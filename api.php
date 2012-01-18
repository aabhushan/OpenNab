<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag™ (http://www.nabaztag.com/) electronic pet.
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

class Destination
{
  var $serialNumber;
  
  function Destination()
  {
    if( isset($_REQUEST['dst']) ) {
      $userSettings = @parse_ini_file('users/'.$_REQUEST['dst'].'.ini', true);
      $sn = @$userSettings['Bunny']['SerialNumber'];
      if( $sn )
        $_REQUEST['sn'] = $sn;
      unset($_REQUEST['dst']);
    }
    $this->serialNumber = isset($_REQUEST['sn']) ? $_REQUEST['sn'] : 'unknown';
  }
}

$dst = new Destination();
define('OPENNAB_SERIAL_NUMBER', $dst->serialNumber);
require_once('utilities.php');

$ap = new AllPlugins();
$reply = new ApiReply($_REQUEST['sn']);
$parameters = $_REQUEST;
//$reply->Add( 'globals', getVarString($GLOBALS) );

if( $reply->hasSerialNumber ) {

	$burrow = new Burrow(OPENNAB_SERIAL_NUMBER);
	$burrow->Load();
  unset($parameters['sn']);
  unset($parameters['dst']);
	$ap->OnApi($burrow,$parameters,$reply);
	$burrow->Save();
	
} else {

	$ap->OnGlobalApi($parameters,$reply);
	
}

$reply->Execute();

?>
