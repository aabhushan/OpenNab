<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag� (http://www.nabaztag.com/) electronic pet.
Copyright � 2007 OpenNab team - http://sourceforge.net/project/memberlist.php?group_id=187057

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

parse_str($_SERVER['REDIRECT_QUERY_STRING'],$vars);
$_REQUEST = $vars;
define('OPENNAB_SERIAL_NUMBER',str_replace(':','',$_REQUEST['m']));
require_once('utilities.php');

if( !strpos($_SERVER['REQUEST_URI'],'/bc.jsp') ) {
	addToLog( 'Unexpected: '.$_SERVER['REQUEST_URI'], 1 );
	print 'ERROR 404 from OpenNab';
	exit;
}
$ap = new AllPlugins();
$b = new Burrow( OPENNAB_SERIAL_NUMBER );
$b->Load();
$r = new Boot(TAGTAG_BOOT_SERVER,$_SERVER['REQUEST_URI']);
$theServer->Forward($r);

$ap->OnBoot($b,$r);

$b->Save();
$r->Log();
$r->ReplyToNabaztag();

?>
