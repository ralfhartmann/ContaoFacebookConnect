<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package   FacebookConnect
 * @author    Mark Sturm
 * @author    Richard Henkenjohann
 * @copyright Mark Sturm 2013
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_module']['fb_settings_legend']	= 'Module settings';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['fb_changeFeMessage']                  = array('Define front end message', 'Define your own front end message.');
$GLOBALS['TL_LANG']['tl_module']['fb_feMessage']                        = array('Front end message', 'Enter the text which is shown in the front end.');
$GLOBALS['TL_LANG']['tl_module']['fb_feCssAppearance']                  = array('Style front end message', 'Use CSS3 styles to improve the button in the front end.');
$GLOBALS['TL_LANG']['tl_module']['fb_dontUpdateDatabse']                = array('Don\'t update registered members', 'Don\'t update the database if the member still exists (username will never be updated).');
$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']            = array('Load additional fields', 'Choose the fields which should be recieved from Facebook. This requires different permissions. See <a href="https://developers.facebook.com/docs/reference/login/#permissions" target="_blank">https://developers.facebook.com/docs/reference/login/#permissions</a>');
$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']['fields']  = array('email'=>'E-mail adress', 'user_website'=>'Website', 'user_birthday'=>'Birthday', 'user_location'=>'Location');
