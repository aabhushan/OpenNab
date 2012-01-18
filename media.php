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

define('OPENNAB_SERIAL_NUMBER','unknown');
require_once('utilities.php');
$ap = new AllPlugins();
// broad server could not be stored into a session variable or anything else because we do not know which rabbit is calling
$r = new Request(OPENNAB_TAGTAG_BROAD_SERVER,$_SERVER['REQUEST_URI']);
if( !$ap->OnBroadcast($r) ) // if no plugin handles the broadcast request
	$theServer->Forward($r); // then forward it to the main server
$r->Log();
$r->ReplyToNabaztag();
?>
