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

	class TestOfMisc extends UnitTestCase {
		function TestOfMisc() {
			$this->UnitTestCase();
		}
		
    var $k = 'a', $l;
    
		function testGetValueOrDefault() {
			$this->assertEqual('a',getValueOrDefault('a','b'));
      $x = 'a';
			$this->assertEqual('a',getValueOrDefault($x,'b'));
			$this->assertEqual('b',getValueOrDefault($thisVarIsNotDefined,'b'));
      $y = array( 'u', 'v' );
			$this->assertEqual('v',getValueOrDefault($y[1],'b'));
			$this->assertEqual('b',getValueOrDefault($y[2],'b'));
			$this->assertEqual('a',getValueOrDefault($this->k,'b'));
			$this->assertEqual('b',getValueOrDefault($this->l,'b'));
      list($a, $b)= split(':', 'a');
			$this->assertEqual('a',getValueOrDefault($a,'b'));
			$this->assertEqual('b',getValueOrDefault($b,'b'));
		}
	}
?>
