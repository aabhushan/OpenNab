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

require_once('../includes/ping.php');

	class TestOfPing extends UnitTestCase {
		function TestOfPing() {
			$this->UnitTestCase();
			$_REQUEST['sd'] = '0';
			$_REQUEST['tc'] = '7fffffff';
		}
		
		function testPackUnpack() {
			$p = new Ping('some.domain.com','/vl/p');
			$this->assertEqual($p->NumberOfBlocks(),0);
			$bytes = pack('H*','7f0a00014c011e5b4fe1e632cb91741734cd88a80f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b29c5ecc6184abdc05b0f9807fc29526834fe1743f5f6756592126a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd06e292cee6c9c5ecca08c7eb5d6639cf9ee876efd7188244588fe8f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b296849ea9807fc2953ffb4f63a926a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b296849ea9807fc2d1ee876efd7188244588a80f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b29c5ecc6184abdc05b0f9807fc29526834fe1743f5f6756592126a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd06e292cee6c9c5ecca08c7eb5d6639cf9ee876efd71882445040000177fffffff01010e280000000000000000000000000a0a000300000178ff0a');
			$p->reply = $bytes;
			$p->UnpackBlocks();
			$this->assertEqual($p->NumberOfBlocks(),3);
			$b = &$p->Block('03');
			$this->assertEqual($b->Size(),1);
			$b = &$p->Ambient();
			$this->assertEqual($b->Size(),23);
			$b = &$p->Message();
			$this->assertEqual($b->Size(),332);
			$this->assertEqual($p->GetPingInterval(),120);
			$p->SetPingInterval(60);
			$this->assertEqual($p->GetPingInterval(),60);
			$p->PackBlocks();
			$this->assertEqual($p->reply,substr($bytes,0,strlen($bytes)-3).pack('H*','3cff0a'));
		}
		
		function testEmptyBlock() {
			$p = new Ping('some.domain.com','/vl/p');
			$bytes = pack('H*','7f0a00014c011e5b4fe1e632cb91741734cd88a80f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b29c5ecc6184abdc05b0f9807fc29526834fe1743f5f6756592126a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd06e292cee6c9c5ecca08c7eb5d6639cf9ee876efd7188244588fe8f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b296849ea9807fc2953ffb4f63a926a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b296849ea9807fc2d1ee876efd7188244588a80f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b29c5ecc6184abdc05b0f9807fc29526834fe1743f5f6756592126a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd06e292cee6c9c5ecca08c7eb5d6639cf9ee876efd71882445040000177fffffff01010e280000000000000000000000000a0a000300000178ff0a');
			$p->reply = $bytes;
			$p->UnpackBlocks();
			$b = &$p->Block('07');
			$this->assertEqual($b->Size(),0);
			$p->PackBlocks();
			$this->assertEqual($p->reply,$bytes);
		}
		
		function testCreateFromScratch() {
			$p = new Ping('some.domain.com','/vl/p');
			$data = '011e5b4fe1e632cb91741734cd88a80f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b29c5ecc6184abdc05b0f9807fc29526834fe1743f5f6756592126a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd06e292cee6c9c5ecca08c7eb5d6639cf9ee876efd7188244588fe8f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b296849ea9807fc2953ffb4f63a926a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b296849ea9807fc2d1ee876efd7188244588a80f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b29c5ecc6184abdc05b0f9807fc29526834fe1743f5f6756592126a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd06e292cee6c9c5ecca08c7eb5d6639cf9ee876efd71882445';
			$b = &$p->Message();
			$b->data = $data;
			$this->assertEqual($b->Size(),332);
			$p->PackBlocks();
			$this->assertEqual(strlen($p->reply),339);
			$full = '7f0a00014c'.$data;
			$this->assertEqual(implode('',unpack('H*',$p->reply)),$full.'ff0a');
			$this->assertEqual($p->reply,pack('H*',$full.'ff0a'));
			$b = &$p->Ambient();
			$b->data = '7fffffff01010e280000000000000000000000000a0a00';
			$p->PackBlocks();
			$this->assertEqual($b->Size(),23);
			$full .= '04000017'.$b->data;
			$this->assertEqual(implode('',unpack('H*',$p->reply)),$full.'ff0a');
			$this->assertEqual($p->reply,pack('H*',$full.'ff0a'));
			$p->SetPingInterval(120);
			$p->PackBlocks();
			$full .= '0300000178';
			$this->assertEqual(implode('',unpack('H*',$p->reply)),$full.'ff0a');
			$this->assertEqual($p->reply,pack('H*',$full.'ff0a'));
		}
		
		function testEnhancedMessage() {
			$p = new Ping('some.domain.com','/vl/p');
			$data = '011e5b4fe1e632cb91741734cd88a80f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b29c5ecc6184abdc05b0f9807fc29526834fe1743f5f6756592126a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd06e292cee6c9c5ecca08c7eb5d6639cf9ee876efd7188244588fe8f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b296849ea9807fc2953ffb4f63a926a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b296849ea9807fc2d1ee876efd7188244588a80f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd0ac63b62c6c988969b29c5ecc6184abdc05b0f9807fc29526834fe1743f5f6756592126a74f11f9f2eefbfac6c09b860df9f2eefb66ac4881cd3cccd06e292cee6c9c5ecca08c7eb5d6639cf9ee876efd71882445';
			$b = &$p->Message();
			$b->data = $data;
			$this->assertEqual($b->Size(),332);
			$this->assertEqual($b->enhanced,'Message');
			
			$p->PackBlocks();
			$p2 = new Ping('some.domain.com','/vl/p');
			$p2->reply = $p->reply;
			$p2->UnpackBlocks();
			$b2 = &$p2->Message();
			$this->assertEqual($b2->enhanced,'Message');
	
			$b2->Encode('0/1/2/3/4.');
			$this->assertEqual($b2->data,'017fbe9ef43d32dc287b2d');
			$b2->Encode('0/0/2/3/4.');
			$this->assertEqual($b2->data,'017fbeffbe3d32dc287b2d');
		}
		
		function testPackEmptyBlock() {
			$p = new Ping('some.domain.com','/vl/p');
			$bytes = pack('H*','7f040000177fffffff01010e280000000000000000000000000a0a000300000178ff0a');
			$p->reply = $bytes;
			$p->UnpackBlocks();
			$p->PackBlocks();
			$this->assertEqual($p->reply,$bytes);
			$p->UnpackBlocks();
			$p->PackBlocks();
			$this->assertEqual($p->reply,$bytes);
			$p->UnpackBlocks();
			$b = &$p->Message();
			$p->PackBlocks();
			$this->assertEqual($p->reply,$bytes);
			$p->UnpackBlocks();
			$b = &$p->Message();
			$this->assertEqual($b->Size(),0);
			$p->PackBlocks();
			$this->assertEqual($p->reply,$bytes);
		}
		
		function testReboot() {
			$p = new Ping('some.domain.com','/vl/p');
			$bytes = pack('H*','7f040000177fffffff01010e280000000000000000000000000a0a000300000178ff0a');
			$p->reply = $bytes;
			$p->UnpackBlocks();
			$p->Reboot();
			$p->PackBlocks();
			$this->assertEqual(implode('',unpack('H*',$p->reply)),'7f09000000ff0a');
			$this->assertEqual($p->reply,pack('H*','7f09000000ff0a'));
		}
		
		
	}
?>
