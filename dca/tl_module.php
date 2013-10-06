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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][]			= 'fb_changeFeMessage';
$GLOBALS['TL_DCA']['tl_module']['palettes']['FacebookConnect']			= 'name,headline,type;{account_legend},reg_groups,reg_assignDir;{fb_settings_legend},fb_changeFeMessage,fb_feCssAppearance,fb_dontUpdateDatabase,fb_additionalPermissions;{redirect_legend},jumpTo,redirectBack;{protected_legend},protected;align,space,cssID';

/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['fb_changeFeMessage']	= 'fb_feMessage';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['fb_changeFeMessage'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['fb_changeFeMessage'],
	'inputType'		=> 'checkbox',
	'exclude'		=> true,
	'eval'			=> array('submitOnChange'=>true),
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fb_feMessage'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['fb_feMessage'],
	'inputType'		=> 'text',
	'exclude'		=> true,
	'eval'			=> array('mandatory'=>true, 'tl_class'=>'long'),
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fb_feCssAppearance'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['fb_feCssAppearance'],
	'inputType'		=> 'checkbox',
	'exclude'		=> true,
	'eval'			=> array('tl_class'=>'w50'),
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fb_dontUpdateDatabase'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['fb_dontUpdateDatabse'],
	'inputType'		=> 'checkbox',
	'exclude'		=> true,
	'eval'			=> array('tl_class'=>'w50'),
);
$GLOBALS['TL_DCA']['tl_module']['fields']['fb_additionalPermissions'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions'],
	'inputType'		=> 'checkbox',
	'exclude'		=> true,
	'options'		=> array('email','user_website','user_birthday','user_location'),
	'reference'		=> $GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']['fields'],
	'eval'			=> array('multiple'=>true, 'tl_class'=>'clr'),
);
