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

require_once('../includes/misc.php');
require_once('../includes/ambientblock.php');

	class TestOfAmbientBlock extends UnitTestCase {
		function TestOfAmbientBlock() {
			$this->UnitTestCase();
		}
		
		function testGetServiceValue() {
			$ab = new AmbientBlock('7fffffff0103030207010e2802050000000000000b0d00');
			$this->assertEqual($ab->type,'04');
			$this->assertEqual($ab->enhanced,'Ambient');
			$this->assertEqual($ab->GetServiceValue(1),3);
			$this->assertEqual($ab->GetServiceValue(3),2);
			$this->assertEqual($ab->GetServiceValue(7),1);
			$this->assertEqual($ab->GetServiceValue(14),40);
			$this->assertEqual($ab->GetServiceValue(2),5);
		}
		
		function testSetServiceValue() {
			$ab = new AmbientBlock('7fffffff0103030207010e2802050000000000000b0d00');
			$this->assertEqual($ab->SetServiceValue(1,2),true);
			$this->assertEqual($ab->data,'7fffffff0102030207010e2802050000000000000b0d00');
			$this->assertEqual($ab->SetServiceValue(3,5),true);
			$this->assertEqual($ab->data,'7fffffff0102030507010e2802050000000000000b0d00');
			$this->assertEqual($ab->SetServiceValue(7,10),true);
			$this->assertEqual($ab->data,'7fffffff01020305070a0e2802050000000000000b0d00');
			$this->assertEqual($ab->SetServiceValue(14,42),true);
			$this->assertEqual($ab->data,'7fffffff01020305070a0e2a02050000000000000b0d00');
			$this->assertEqual($ab->SetServiceValue(2,0),true);
			$this->assertEqual($ab->data,'7fffffff01020305070a0e2a02000000000000000b0d00');
			$this->assertEqual($ab->SetServiceValue(8,1),true);
			$this->assertEqual($ab->data,'7fffffff01020305070a0e2a02000801000000000b0d00');
			$this->assertEqual($ab->SetServiceValue(9,2),true);
			$this->assertEqual($ab->data,'7fffffff01020305070a0e2a02000801090200000b0d00');
			$this->assertEqual($ab->SetServiceValue(255,254),true);
			$this->assertEqual($ab->data,'7fffffff01020305070a0e2a020008010902fffe0b0d00');
			$this->assertEqual($ab->SetServiceValue(44,33),false);
			$this->assertEqual($ab->data,'7fffffff01020305070a0e2a020008010902fffe0b0d00');
		}
	}
?>
