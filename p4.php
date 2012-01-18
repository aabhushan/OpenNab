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

define('OPENNAB_SERIAL_NUMBER',$_REQUEST['sn']);
require_once('utilities.php');

// $_REQUEST variables :
// sn : serial number of the rabbit
// sd : type of ping
//       0 : normal ping (every 2 minutes)
//       1 : ping activated by double pushing on the rabbit's head (typically for archiving messages)
//      2 : ping at the end of a previous audio message
//      3 : ping activated by pushing the button on the rabbit's head
//      5 : ping activated by pushing the button on the rabbit's head while audio is playing
//     8x0y : the rabbit's ears were moved manually ; x is the position of the right hand ear, y is the position of the left hand one
// v : rabbits boot code version
// st : always equal to 1 (hardcoded)
// tc : id of current message being played
// h : always equal to 4 ???

$ap = new AllPlugins();

$b = new Burrow(OPENNAB_SERIAL_NUMBER);
$b->Load();

$p = new Ping(OPENNAB_TAGTAG_PING_SERVER,$theServer->TranslatedUri());

if( $ap->OnPingForward($b,$p) ) // if some plugin handles the ping request
	$p->Generate(); // then generate an empty reply
else
	$theServer->Forward($p); // else forward the request to the main server

$p->UnpackBlocks();
$ap->OnPingReadBefore($b,$p); // give the unpacked information to all plugins

$ap->OnPingWrite($b,$p); // give all plugins the opportunity to update the data

$p->PackBlocks();
$ap->OnPingReadAfter($b,$p); // give the ready-to-be-sent information to all plugins

$p->Log();
$p->ReplyToNabaztag();

$ap->OnCron($b,$p);
$b->Save();
?>
