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

// Being given a Nabaztag/tag that boots against this OpenNab install,
// these Server classes allows to define the server the Nabaztag is going
// to talk to for pings, records and so on

class Server
{
	var $pingServer, $broadServer;

	function HackBootcode(&$request) {
		// hack the rabbit code so that it connects to all php scripts
		$request->reply = str_replace( '/p4.jsp?', '/p4.php?', $request->reply );
		$request->reply = str_replace( '/record.jsp?', '/record.php?', $request->reply );
    $request->reply = str_replace( '/rfid.jsp?', '/rfid.php?', $request->reply );
	}
	
	function Locate() {
		$cr = "\r\n";
		addToLog( $_SERVER['REQUEST_URI'].' => set to '.$this->pingServer.' / '.$this->broadServer, 2 );
		print 'ping '.$this->pingServer.$cr;
		print 'broad '.$this->broadServer.$cr;
		print $cr;
	}
	
	function TranslatedUri() {
		return str_replace( '.php', '.jsp', $_SERVER['REQUEST_URI'] );
	}
	
	function Forward(&$request) {
		$request->Forward();
	}
	
}

// Nabaztag uses this OpenNab server
// It is the normal execution mode
class ClassicServer extends Server
{
	function ClassicServer() {
		$this->pingServer = $_SERVER['SERVER_NAME'];
		$this->broadServer = $_SERVER['SERVER_NAME'];
	}
}

// Nabaztag uses the Violet server
// It means that the OpenNab install is deactivated
class VioletServer extends Server
{
	function VioletServer() {
		$this->pingServer = TAGTAG_PING_SERVER;
		$this->broadServer = TAGTAG_BROAD_SERVER;
	}
	
	function HackBootcode(&$request) {
		// no hack because it will use Violet's jsp
	}
	
	function TranslatedUri() {
		unexpectedCondition('VioletServer::TranslatedUri');
	}
}

// In the locate call, OpenNab defines ping and broad servers to be another OpenNab that we want to test.
class PingPongServer extends Server
{
	function PingPongServer($serverName) {
		$this->pingServer = $serverName;
		$this->broadServer = $serverName;
	}
}

// This is the server being tested when the Nabaztag boots on a pingpong server
class PongPingServer extends Server
{
	function PongPingServer($serverName) {
		// We forward the requests not to Violet server but to the other OpenNab server
		define('OPENNAB_TAGTAG_PING_SERVER', $serverName);
		define('OPENNAB_TAGTAG_BROAD_SERVER', $serverName);
	}
	
	function TranslatedUri() {
		// no translation because the other server is also an OpenNab
		return $_SERVER['REQUEST_URI'];
	}
}

// OpenNab does not have a connection with Violet
class StandaloneServer extends Server
{
	function StandaloneServer() {
		$this->pingServer = $_SERVER['SERVER_NAME'];
		$this->broadServer = $_SERVER['SERVER_NAME'];
	}
	
	function Forward(&$request) {
		$request->Generate();
	}
}


if( OPENNAB_SERVER_MODE == 'classic' ) {

	$theServer = new ClassicServer();
	
} else if( OPENNAB_SERVER_MODE == 'violet' ) {

	$theServer = new VioletServer();
	
} else if( OPENNAB_SERVER_MODE == 'pingpong' ) {

	if( $_SERVER['SERVER_NAME'] == OPENNAB_PONGPING_SERVER ) {
		$theServer = new PongPingServer(OPENNAB_PINGPONG_SERVER);
	} else {
		$theServer = new PingPongServer(OPENNAB_PONGPING_SERVER);
	}
	
} else if( OPENNAB_SERVER_MODE == 'standalone' ) {

	$theServer = new StandaloneServer();
	
} else{

	unexpectedCondition('Unknown server mode "'.OPENNAB_SERVER_MODE.'"');
	
}

// If ping and broad servers are not defined yet, then point to Violet servers
define('OPENNAB_TAGTAG_PING_SERVER', TAGTAG_PING_SERVER);
define('OPENNAB_TAGTAG_BROAD_SERVER', TAGTAG_BROAD_SERVER);

?>
