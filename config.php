<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag� (http://www.nabaztag.com/) electronic pet.
Copyright c 2007 OpenNab team - http://opennab.sourceforge.net/team/

This file is part of OpenNab

OpenNab is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

OpenNab is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public
License along with this script; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

****************************************************************************/

$iniFile = OPENNAB_CONFIG_DIR . '/opennab.ini';
$openNabSettings = parse_ini_file($iniFile, true);

// if .ini exists for current serial number, override settings with values from this .ini
if( OPENNAB_SERIAL_NUMBER ) {
  $localIniFile = OPENNAB_CONFIG_DIR . '/'.OPENNAB_SERIAL_NUMBER.'.ini';
  if( file_exists($localIniFile) ) {
    $openNabLocalSettings = parse_ini_file($localIniFile, true);
    foreach( $openNabLocalSettings as $section => $values ) {
      if( !isset($openNabSettings[$section]) )
        $openNabSettings[$section] = array();
      foreach( $values as $key => $value ) {
        $openNabSettings[$section][$key] = $value;
      }
    }
  }
}

$openNabAdvancedSettings = &$openNabSettings['Advanced'];

define('TAGTAG_BOOT_SERVER', $openNabAdvancedSettings['BootServer']);
define('TAGTAG_PING_SERVER', $openNabAdvancedSettings['PingServer']);
define('TAGTAG_BROAD_SERVER', $openNabAdvancedSettings['BroadServer']);

define('LOG_LEVEL', $openNabAdvancedSettings['LogLevel']);
define('LOG_IN_SAME_FILE', ($openNabAdvancedSettings['LogMode'] == 'oneByDay') );

define('OPENNAB_SERVER_MODE', $openNabAdvancedSettings['ServerMode']);
define('OPENNAB_PINGPONG_SERVER', $openNabAdvancedSettings['PingPongServer']);
define('OPENNAB_PONGPING_SERVER', $openNabAdvancedSettings['PongPingServer']);
define('OPENNAB_CODE',$openNabAdvancedSettings['BootCode']);

if( isset($openNabAdvancedSettings['ConnexionProxyAddr']) ) {
	define('OPENNAB_CONNEXION_PROXY_ADDR', $openNabAdvancedSettings['ConnexionProxyAddr']);
	define('OPENNAB_CONNEXION_PROXY_PORT', getValueOrDefault($openNabAdvancedSettings['ConnexionProxyPort'],'8080'));
	define('OPENNAB_CONNEXION_PROXY_USER', getValueOrDefault($openNabAdvancedSettings['ConnexionProxyUser'],''));
	define('OPENNAB_CONNEXION_PROXY_PASS', getValueOrDefault($openNabAdvancedSettings['ConnexionProxyPass'],''));
}

define('TEST_RFID',$openNabAdvancedSettings['TestRfid']);

// process multilevel sections
$openNabSettings['Plugin'] = array();
$openNabSettings['TTS'] = array();
foreach( $openNabSettings as $section => $values ) {
	$sep = strpos($section, ':');
	if( $sep === false )
		continue;
	$s1 = substr($section,0,$sep);
	$openNabSectionSettings = &$openNabSettings[$s1];
	if( !is_array($openNabSectionSettings) )
		continue;
	$s2 = substr($section,$sep+1);
	$openNabSectionSettings[$s2] = $values;
}

// Define visual debug mode : by default, visual debug is activated when the software is called from a user agent (aka web browser) which is not the rabbit itself.
define('VISUAL_DEBUG', $_SERVER['HTTP_USER_AGENT'] != 'MTL' && !isset($_REQUEST['ForceLog']) && !isset($openNabAdvancedSettings['ForceLog']) );


//**************************************************************************

?>
