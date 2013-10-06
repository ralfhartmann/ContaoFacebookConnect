<?php if (!defined('TL_ROOT'))
	die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * @package FacebookConnect
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Handle Facebook registration; update, login, create new members
 *
 * @package   FacebookConnect
 * @author    Mark Sturm
 * @author    Richard Henkenjohann
 * @copyright Mark Sturm 2013
 * Special thanks to Andreas Schempp for autoregistration.
 */
require_once(TL_ROOT . '/system/modules/FacebookConnect/assets/src/facebook.php');

class ModuleFacebookConnect extends Module
{

	protected $strTemplate = 'mod_facebookconnect';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### USER FACEBOOK-CONNECT ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate the module
	 */
	protected function compile()
	{
		global $objPage;
		$root = $this->Database->prepare('SELECT fb_feed, fb_appid, fb_secret FROM tl_page WHERE id=?')
							   ->execute($objPage->rootId);

		// Module is configured in root page
		if ($root->fb_feed && strlen($root->fb_appid) > 0 && strlen($root->fb_secret) > 0)
		{
			// Set the last page visited
			if ($this->redirectBack)
			{
				$this->Session->set('LAST_PAGE_VISITED', $this->getReferer());
			}

			// Redirect to the last page visited
			if ($this->redirectBack && strlen($this->Session->get('LAST_PAGE_VISITED')))
			{
				$strRedirect = $this->Session->get('LAST_PAGE_VISITED');
			}
			else
			{
				// Redirect to the jumpTo page
				if (strlen($this->jumpTo))
				{
					$objNextPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
										->limit(1)
										->execute($this->jumpTo);

					if ($objNextPage->numRows)
					{
						$strRedirect = $this->generateFrontendUrl($objNextPage->fetchAssoc());
					}
				}
			}

			// Create application instance
			$this->facebook = new Facebook(array
			(
				'appId' => $root->fb_appid,
				'secret' => $root->fb_secret,
				'cookie' => true
			));

			// Get Facebook user ID and generate login url
			$fb_user_id = $this->facebook->getUser();
			$arrRequestedFbData = deserialize($this->fb_additionalPermissions);
			$this->Template->fb_user_id = $fb_user_id;
			$this->Template->fb_getapp = $this->facebook->getAppID();

			// Extended Profile and/or e-mail permissions required
			if ($arrRequestedFbData)
			{
				$this->Template->fb_login_url = $this->facebook->getLoginUrl(array
				(
					'scope' => implode(',', $arrRequestedFbData), /* email,user_website,user_birthday */
					'display' => 'popup'
				));
			}
			// Basic Information will do
			else
			{
				$this->Template->fb_login_url = $this->facebook->getLoginUrl(array
				(
					'display' => 'popup'
				));
			}

			// Proceed with an authenticated user
			if ($fb_user_id && !$this->Session->get('TL_USER_LOGGED_IN'))
			{
				try
				{
					// Set first user information for database update
					$fb_me = $this->facebook->api('/me');
					$arrData['tstamp'] = time();
					$arrData['activation'] = md5(uniqid(mt_rand(), true));
					$arrData['dateAdded'] = $arrData['tstamp'];
					$arrData['firstname'] = $fb_me['first_name']; /* John */
					$arrData['lastname'] = $fb_me['last_name']; /* Smith */
					$arrData['gender'] = $fb_me['gender']; /* male */
					$arrData['username'] = strtolower($fb_me['first_name']{0} . '.' . $fb_me['last_name']);
					$arrData['fb_user_id'] = $fb_user_id; /* 1000012345678912 */
					$arrData['login'] = '1';
					$arrData['groups'] = $this->reg_groups;
					$arrData['language'] = substr($fb_me['locale'], 0, 2); /* en_US */

					// Additional fields (extended profile properties)
					foreach ($arrRequestedFbData as $strRequestedFbData)
					{
						switch ($strRequestedFbData)
						{
							case 'email':
								if ($fb_me['email']) // Prevent issue with NULL
								{
									$arrData['fb_email'] = $fb_me['email']; /* j.smith@example.com */
								}
								break;
							case 'user_website':
								if ($fb_me['website']) // Prevent issue with NULL
								{
									$arrData['website'] = $fb_me['website']; /* www.example.com */
								}
								break;
							case 'user_birthday':
								if ($fb_me['birthiday']) // Prevent issue with NULL
								{
									$strDateOfBirth = new Date($fb_me['birthday'], 'm/d/Y'); /* 02/22/1990 */
									$arrData['dateOfBirth'] = $strDateOfBirth->dayBegin;
								}
								break;
							case 'user_location':
								if ($fb_me['location']) // Prevent issue with NULL
								{
									$strLocation = explode(', ', $fb_me['location']['name']);
									if (count($strLocation) == 3) /* Berlin, Berlin, Germany */
									{
										require_once(TL_ROOT . '/system/config/countries.php');
										$arrData['city'] = $strLocation[0];
										$arrData['state'] = $strLocation[1];
										$arrData['country'] = str_replace($strLocation[2], array_search($strLocation[2], $countries), $strLocation[2]);
									}
									elseif (count($strLocation) == 2) /* Berlin, Germany */
									{
										require_once(TL_ROOT . '/system/config/countries.php');
										$arrData['city'] = $strLocation[0];
										$arrData['country'] = str_replace($strLocation[1], array_search($strLocation[1], $countries), $strLocation[1]);
									}
									elseif ($fb_me['location']['name'])
									{
										$arrData['city'] = $fb_me['location']['name'];
									}
								}
								break;
						}
					}
				}
				catch (FacebookApiException $e)
				{
					error_log($e);
					$fb_user_id = null;
				}

				// Update Database
				if ($this->Input->get('code'))
				{
					// Check if member already exists (but maybe without Facebook ID)
					if ($arrData['fb_email'])
					{
						$objMemberInit = $this->Database->prepare("SELECT id,username,fb_user_id FROM tl_member WHERE fb_user_id=? OR email=?")
														->execute($fb_user_id, $arrData['fb_email']);
					}
					else
					{
						$objMemberInit = $this->Database->prepare("SELECT id,username,fb_user_id FROM tl_member WHERE fb_user_id=?")
														->execute($fb_user_id);
					}

					// Create a new user if the Facebook member isn't in the database
					if ($objMemberInit->numRows == 0)
					{
						// Use email from Facebook as user email
						$arrData['email'] = $arrData['fb_email'];
						$objNewUser = $this->Database->prepare("INSERT INTO tl_member %s")
													 ->set($arrData)
													 ->execute();

						$insertId = $objNewUser->insertId;
						$this->log('User with Facebook ID "' . $fb_user_id . '" was created', get_class($this) . ' activateAccount()', TL_ACCESS);

						// Assign home directory
						if ($this->reg_assignDir && is_dir(TL_ROOT . '/' . $this->reg_homeDir))
						{
							$this->import('Files');
							$strUserDir = strlen($arrData['username']) ? $arrData['username'] : 'user_' . $insertId;

							// Add the user ID if the directory exists
							if (is_dir(TL_ROOT . '/' . $this->reg_homeDir . '/' . $strUserDir))
							{
								$strUserDir .= '_' . $insertId;
							}
							new Folder($this->reg_homeDir . '/' . $strUserDir);

							$this->Database->prepare("UPDATE tl_member SET homeDir=?, assignDir=1 WHERE id=?")
										   ->execute($this->reg_homeDir . '/' . $strUserDir, $insertId);
							$this->log('Facebook user directory "' . $strUserDir . '" was created', get_class($this) . ' activateAccount()', TL_ACCESS);
						}
					}
					// Update the user if the Facebook member already exists
					elseif (!$this->fb_dontUpdateDatabase)
					{
						// Do not update the username, maybe it changed by hand
						unset($arrData['username']);
						// Do not update the registration date
						unset($arrData['dateAdded']);

						$this->Database->prepare("UPDATE tl_member %s WHERE id=?")
									   ->set($arrData)
									   ->execute($objMemberInit->id);
						$this->log('Facebook user "' . $objMemberInit->username . '" was updated', get_class($this) . ' activateAccount()', TL_ACCESS);
					}

					// Update the user if the e-mail address already exists
					if ($objMemberInit->numRows > 0 && !$objMemberInit->fb_user_id && $fb_me['email'])
					{
						$this->Database->prepare("UPDATE tl_member SET fb_user_id=? WHERE id=?")
									   ->execute($fb_user_id, $objMemberInit->id);
						$this->log('User "' . $objMemberInit->username . '" gained Facebook ID', get_class($this) . ' activateAccount()', TL_ACCESS);
					}

					// Create new database object to make sure the current member is selected
					$objMember = $this->Database->prepare("SELECT id,username FROM tl_member WHERE fb_user_id=?")
												->execute($fb_user_id);

					// Login user
					if ($fb_user_id)
					{
						// Set time variable
						$time = time();

						// Generate the cookie hash
						$strHash = sha1(session_id() . (!$GLOBALS['TL_CONFIG']['disableIpCheck'] ? $this->Environment->ip : '') . 'FE_USER_AUTH');

						// Clean up old sessions
						$this->Database->prepare("DELETE FROM tl_session WHERE tstamp<? OR hash=?")
									   ->execute(($time - $GLOBALS['TL_CONFIG']['sessionTimeout']), $strHash);

						// Save the session in the database
						$this->Database->prepare("INSERT INTO tl_session (pid, tstamp, name, sessionID, ip, hash) VALUES (?, ?, ?, ?, ?, ?)")
									   ->execute($objMember->id, $time, 'FE_USER_AUTH', session_id(), $this->Environment->ip, $strHash);

						// Set the authentication cookie
						$this->setCookie('FE_USER_AUTH', $strHash, ($time + $GLOBALS['TL_CONFIG']['sessionTimeout']), $GLOBALS['TL_CONFIG']['websitePath']);

						// Save the login status
						$this->Session->set('TL_USER_LOGGED_IN', true);
						$this->log('Facebook user "' . $objMember->username . '" was logged in', get_class($this) . ' activateAccount()', TL_ACCESS);

						// Close Popup and redirect
						$GLOBALS['TL_HEAD'][] = '<script>window.close(); window.opener.location.href = "' . $strRedirect . '"</script>';
					}
				}
			}
			// If error or user abort close the Popup
			elseif ($this->Input->get('error'))
			{
				$GLOBALS['TL_HEAD'][] = '<script>window.close();</script>';
			}
		}
	}
}
