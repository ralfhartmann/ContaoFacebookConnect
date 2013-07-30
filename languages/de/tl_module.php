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
$GLOBALS['TL_LANG']['tl_module']['fb_settings_legend']	= 'Moduleinstellungen';

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['fb_changeFeMessage']                  = array('Eigene Frontend-Ausgabe definieren', 'Bestimmen Sie einen eigenen Text für das Frontend.');
$GLOBALS['TL_LANG']['tl_module']['fb_feMessage']                        = array('Frontend-Ausgabe', 'Geben Sie den Text ein, welcher im Frontend ausgegeben werden soll.');
$GLOBALS['TL_LANG']['tl_module']['fb_feCssAppearance']                  = array('Frontend-Ausgabe stylen', 'Wählen Sie aus, ob Sie die Ausgabe im Frontend mit CSS3 stylen wollen.');
$GLOBALS['TL_LANG']['tl_module']['fb_dontUpdateDatabse']                = array('Bereits registrierten Benutzer nicht updaten', 'Wählen Sie dies, wenn Sie nicht wollen, dass der bereits registrierte Benuzter aktualisiert wird (Benutzername wird generell nicht aktualisiert).');
$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']            = array('Weitere Felder von Facebook laden', 'Wählen Sie aus, welche Felder zusätzlich von Facebook geladen werden sollen. Dies erfordert zusätzliche Erlaubnis. Siehe <a href="https://developers.facebook.com/docs/reference/login/#permissions" target="_blank">https://developers.facebook.com/docs/reference/login/#permissions</a>');
$GLOBALS['TL_LANG']['tl_module']['fb_additionalPermissions']['fields']  = array('email'=>'E-Mail-Adresse', 'user_website'=>'Webseite', 'user_birthday'=>'Geburtsdatum', 'user_location'=>'Wohnort und -land');
