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

define('OPENNAB_BASE_DIR','../..');
require_once('../includes/burrow.php');

	class TestOfBurrow extends UnitTestCase {
		function TestOfBurrow() {
			$this->UnitTestCase();
		}
		
		function testPlugin() {
			$b = new Burrow('test_to_delete');
			$this->assertFalse($b->GetPluginData('p','k1'));
			$this->assertEqual(count($b->GetAllPluginData('p')),0);
			$b->SetPluginData('p','k1','v1');
			$this->assertEqual($b->GetPluginData('p','k1'),'v1');
			$this->assertEqual(count($b->GetAllPluginData('p')),1);
			$this->assertFalse($b->GetPluginData('p','k2'));
			$this->assertFalse($b->GetPluginData('pp','k1'));
			$this->assertEqual(count($b->GetAllPluginData('pp')),0);
			$b->SetPluginData('p','k2','v2');
			$this->assertEqual($b->GetPluginData('p','k2'),'v2');
			$this->assertEqual($b->GetPluginData('p','k1'),'v1');
			$this->assertEqual(count($b->GetAllPluginData('p')),2);
			$b->ClearPluginData('p','k1');
			$this->assertFalse($b->GetPluginData('p','k1'));
			$this->assertEqual($b->GetPluginData('p','k2'),'v2');
			$this->assertEqual(count($b->GetAllPluginData('p')),1);
			$b->ClearPluginData('p','k2');
			$this->assertFalse($b->GetPluginData('p','k1'));
			$this->assertFalse($b->GetPluginData('p','k2'));
			$this->assertEqual(count($b->GetAllPluginData('p')),0);
		}
		
    var $afterSaveValue;
		function assignAfterSave($v) {
      $this->afterSaveValue = $v;
		}
		function addAfterSave($v) {
      $this->afterSaveValue += $v;
		}
		function addAndMulAfterSave($v1,$v2) {
      $this->afterSaveValue += $v1;
      $this->afterSaveValue *= $v2;
		}
		function byrefAfterSave(&$v) {
      $v = 4;
		}
		
		function testSave() {
			$b = new Burrow('test_to_delete');

      $b->AfterSave( array(&$this,'assignAfterSave'), 15 );
      $b->Save();
			$this->assertEqual($this->afterSaveValue,15);
      
      $b->AfterSave( array(&$this,'addAfterSave'), 16 );
      $b->Save();
			$this->assertEqual($this->afterSaveValue,31); // 15+16=31
      
      $b->AfterSave( array(&$this,'addAndMulAfterSave'), 4, 2 );
      $b->Save();
			$this->assertEqual($this->afterSaveValue,70); // (15+16+4)*2=70
      
      $filepath = OPENNAB_BASE_DIR.'/vl/burrows/test_to_delete';
			$this->assertTrue(is_file($filepath));
      @unlink($filepath);
		}

	}
?>
