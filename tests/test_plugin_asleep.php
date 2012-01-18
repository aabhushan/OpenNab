<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag™ (http://www.nabaztag.com/) electronic pet.
Copyright © 2007 OpenNab team - http://sourceforge.net/project/memberlist.php?group_id=187057

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

require_once('../includes/burrow.php');
require_once('../includes/apireply.php');
require_once('../includes/ping.php');
require_once('../includes/messageblock.php');
require_once('../includes/plugin.php');
require_once('../plugins/asleep/_plugin.php');

class TestOfPluginAsleep extends UnitTestCase {
	function TestOfPluginAsleep() {
		$this->UnitTestCase();
	}
	
	function testSleep() {
		$sn = '0b0b0b0c0c0c';
		$b = new Burrow($sn);
		$p = new Plugin_asleep();

		// set asleep default mode
		$r = new ApiReply($sn);
		$this->assertFalse( $p->OnApi($b,array('asleep'=>''),$r) );
		$this->assertEqual($r->Xml(),"<OpenNab>\r\n  <Asleep>default</Asleep>\r\n</OpenNab>");

		$_REQUEST['tc'] = dechex(ID_DEFAULT);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode('aaa');
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),'aaa');

		$_REQUEST['tc'] = dechex(ID_DEFAULT);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode(ID_ASLEEP_MESSAGE);
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),ID_ASLEEP_MESSAGE);

		$_REQUEST['tc'] = dechex(ID_ASLEEP);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode(ID_DEFAULT_MESSAGE);
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),ID_DEFAULT_MESSAGE);

		// set asleep mode on
		$r = new ApiReply($sn);
		$this->assertFalse( $p->OnApi($b,array('asleep'=>'1'),$r) );
		$this->assertEqual($r->Xml(),"<OpenNab>\r\n  <Asleep>on</Asleep>\r\n</OpenNab>");

		$_REQUEST['tc'] = dechex(ID_DEFAULT);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode('aaa');
		$this->assertTrue( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),ID_ASLEEP_MESSAGE);

		$_REQUEST['tc'] = dechex(ID_ASLEEP);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode('aaa');
		$this->assertTrue( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),'');

		$_REQUEST['tc'] = dechex(ID_ASLEEP);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode(ID_DEFAULT_MESSAGE);
		$this->assertTrue( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),'');

		// set asleep mode off
		$r = new ApiReply($sn);
		$this->assertFalse( $p->OnApi($b,array('asleep'=>'0'),$r) );
		$this->assertEqual($r->Xml(),"<OpenNab>\r\n  <Asleep>off</Asleep>\r\n</OpenNab>");

		$_REQUEST['tc'] = dechex(ID_ASLEEP);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode('aaa');
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),ID_DEFAULT_MESSAGE);

		$_REQUEST['tc'] = dechex(ID_DEFAULT);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode('aaa');
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),'aaa');
		
		// back to asleep default mode
		$r = new ApiReply($sn);
		$this->assertFalse( $p->OnApi($b,array('asleep'=>''),$r) );
		$this->assertEqual($r->Xml(),"<OpenNab>\r\n  <Asleep>default</Asleep>\r\n</OpenNab>");

		$_REQUEST['tc'] = dechex(ID_DEFAULT);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode('aaa');
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),'aaa');

		$_REQUEST['tc'] = dechex(ID_DEFAULT);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode(ID_ASLEEP_MESSAGE);
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),ID_ASLEEP_MESSAGE);

		$_REQUEST['tc'] = dechex(ID_ASLEEP);
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		$mb->Encode(ID_DEFAULT_MESSAGE);
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),ID_DEFAULT_MESSAGE);	}
}
?>



