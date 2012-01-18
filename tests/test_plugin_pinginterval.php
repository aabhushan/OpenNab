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

$openNabSettings = array( 'Plugin' => array( 'pinginterval' => array( 'LowerLimit' => 10 ) ) );
require_once('../includes/burrow.php');
require_once('../includes/apireply.php');
require_once('../includes/ping.php');
require_once('../includes/ambientblock.php');
require_once('../includes/plugin.php');
require_once('../plugins/pinginterval/_plugin.php');

class TestOfPluginPingInterval extends UnitTestCase {
	function TestOfPluginPingInterval() {
		$this->UnitTestCase();
	}
	
	function forward(&$pr) {

	}
	
	function testPingInterval() {
		$serverAmbientData = '7fffffff01010202000000000000000000000000000000';
		$serverPingReply = pack( 'H*', '7f04000017'.$serverAmbientData.'0300000178ff0a' );
		$sn = '0b0b0b0c0c0c';
		$b = new Burrow($sn);
		$p = new Plugin_pinginterval();
		$_REQUEST['sd'] = 0;
		
		// empty api call => not taken into account
		$r = new ApiReply($sn);
		$this->assertFalse( $p->OnApi($b,array(),$r) );
		$this->assertEqual($r->Xml(),"<OpenNab>\r\n  <Error>Unknown api query</Error>\r\n</OpenNab>");
		
		// other api call => not taken into account
		$r = new ApiReply($sn);
		$this->assertFalse( $p->OnApi($b,array('toto'=>'titi'),$r) );
		$this->assertEqual($r->Xml(),"<OpenNab>\r\n  <Error>Unknown api query</Error>\r\n</OpenNab>");

		// ping happens => ping interval left untouched
		$pr = new Ping('some.domain.com','/vl/p');
		$this->assertFalse( $p->OnPingForward($b,$pr) );
		$pr->reply = $serverPingReply;
		$pr->UnpackBlocks();
		$this->assertEqual( $pr->GetPingInterval(), 120 );
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual( $pr->GetPingInterval(), 120 );

		// ping interval api call => save interval
		$r = new ApiReply($sn);
		$this->assertFalse( $p->OnApi($b,array('pinginterval'=>'40'),$r) );
		$this->assertEqual($r->Xml(),"<OpenNab>\r\n  <PingInterval>40</PingInterval>\r\n</OpenNab>");

		// ping happens => ping to main server + interval modified
		$pr = new Ping('some.domain.com','/vl/p');
		$this->assertFalse( $p->OnPingForward($b,$pr) );
		$pr->reply = $serverPingReply;
		$pr->UnpackBlocks();
		$this->assertEqual( $pr->GetPingInterval(), 120 );
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual( $pr->GetPingInterval(), 40 );
		$a = &$pr->Ambient();
		$this->assertEqual( $a->data, $serverAmbientData );

		// ping happens => no ping to main server
		$pr = new Ping('some.domain.com','/vl/p');
		$this->assertTrue( $p->OnPingForward($b,$pr) );
		$pr->Generate();
		$pr->UnpackBlocks();
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual( $pr->GetPingInterval(), 40 );
		$a = &$pr->Ambient();
		$this->assertEqual( $a->data, $serverAmbientData );

		// ping happens => no ping to main server
		$pr = new Ping('some.domain.com','/vl/p');
		$this->assertTrue( $p->OnPingForward($b,$pr) );
		$pr->Generate();
		$pr->UnpackBlocks();
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual( $pr->GetPingInterval(), 40 );
		$a = &$pr->Ambient();
		$this->assertEqual( $a->data, $serverAmbientData );

		// ping happens => ping to main server + interval modified
		$pr = new Ping('some.domain.com','/vl/p');
		$this->assertFalse( $p->OnPingForward($b,$pr) );
		$pr->reply = $serverPingReply;
		$pr->UnpackBlocks();
		$this->assertEqual( $pr->GetPingInterval(), 120 );
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual( $pr->GetPingInterval(), 40 );
		$a = &$pr->Ambient();
		$this->assertEqual( $a->data, $serverAmbientData );

		// ping happens with button => ping to main server + interval modified
		$_REQUEST['sd'] = 1;
		$pr = new Ping('some.domain.com','/vl/p');
		$this->assertFalse( $p->OnPingForward($b,$pr) );
		$pr->reply = $serverPingReply;
		$pr->UnpackBlocks();
		$this->assertEqual( $pr->GetPingInterval(), 120 );
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual( $pr->GetPingInterval(), 40 );
		$a = &$pr->Ambient();
		$this->assertEqual( $a->data, $serverAmbientData );

		// small ping interval api call => clear interval
		$r = new ApiReply($sn);
		$this->assertFalse( $p->OnApi($b,array('pinginterval'=>'5'),$r) );
		$this->assertEqual($r->Xml(),"<OpenNab>\r\n  <PingInterval>off</PingInterval>\r\n</OpenNab>");
		
		// ping happens => ping interval left untouched
		$pr = new Ping('some.domain.com','/vl/p');
		$this->assertFalse( $p->OnPingForward($b,$pr) );
		$pr->reply = $serverPingReply;
		$pr->UnpackBlocks();
		$this->assertEqual( $pr->GetPingInterval(), 120 );
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual( $pr->GetPingInterval(), 120 );
		$a = &$pr->Ambient();
		$this->assertEqual( $a->data, $serverAmbientData );
	}
}
?>



