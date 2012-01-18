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

require_once('../includes/block.php');

	class TestOfBlock extends UnitTestCase {
		function TestOfBlock() {
			$this->UnitTestCase();
		}
		
		function testPackUnpack() {
			$b = new Block();
			$this->assertEqual($b->Pack(),pack('H*','00000000'));
			$bytes = pack('H*','05000011abcdef01234567899876543210fedcba1acccc');
			$remainder = $b->Unpack($bytes);
			$this->assertEqual($b->type,'05');
			$this->assertEqual($b->data,'abcdef01234567899876543210fedcba1a');
			$this->assertEqual($b->Size(),17);
			$this->assertEqual($remainder,pack('H*','cccc'));
			$this->assertEqual($b->Pack(),substr($bytes,0,21));
			$this->assertEqual($b->ToString(),'[05,17]abcdef01234567899876543210fedcba1a');
		}
	}
?>
