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

define( 'OPENNAB_BASE_DIR', preg_replace( '#/vl.*#', '', $_SERVER['SCRIPT_FILENAME'] ) );
define( 'OPENNAB_PLUGINS_DIR', OPENNAB_BASE_DIR.'/vl/tests' );
define( 'VISUAL_DEBUG', true );

require_once('../includes/plugin.php');
require_once('../includes/allplugins.php');
require_once('../includes/burrow.php');
require_once('../includes/misc.php');

class TestOfPlugin extends UnitTestCase {
	function TestOfPlugin() {
		$this->UnitTestCase();
		
		global $openNabSettings;
		$openNabSettings = array( 'Plugin' => array( 'test' => array() ) );
	}

	function testName() {
		$ap = new AllPlugins();
		$p = $ap->plugins[0];
		$this->assertEqual($p->GetName(),'test');
		$this->assertEqual($p->GetFilePath('xxx'),OPENNAB_BASE_DIR.'/vl/tests/test/files/xxx');
	}
	
	function testExecute() {
		global $globalTest;
		$ap = new AllPlugins();
		$burrow = new Burrow('010101');
		$burrow->SetPluginData('test','p',0);
		$x = new stdClass();
		$x->sd = '0';
		$x->val = 1;
		$ap->OnBoot($burrow,$x);
		$this->assertEqual($burrow->GetPluginData('test','last'),'OnBoot01');
		$x->val = 2;
		$this->assertTrue( $ap->OnPingForward($burrow,$x) );
		$this->assertEqual($burrow->GetPluginData('test','last'),'OnPingForward02');
		$x->val = 3;
		$ap->OnPingReadBefore($burrow,$x);
		$this->assertEqual($globalTest,'OnPingReadBefore03');
		$x->val = 4;
		$ap->OnPingWrite($burrow,$x);
		$this->assertEqual($burrow->GetPluginData('test','last'),'OnPingWrite04');
		$x->val = 5;
		$ap->OnPingReadAfter($burrow,$x);
		$this->assertEqual($globalTest,'OnPingReadAfter05');
		$x->val = 6;
		$this->assertTrue( $ap->OnRecord($burrow,$x) );
		$this->assertEqual($burrow->GetPluginData('test','last'),'OnRecord06');
		$x->val = 7;
		$this->assertTrue( $ap->OnBroadcast($x) );
		$this->assertEqual($x->val,'OnBroadcast7');
		$x->val = 8;
		$this->assertTrue( $ap->OnApi($burrow,'4',$x) );
		$this->assertEqual($burrow->GetPluginData('test','last'),'OnApi048');
		$this->assertTrue( $ap->OnGlobalApi('5',$x) );
		$this->assertEqual($globalTest,'OnGlobalApi58');
	}
	
	function testSingleClick() {
		global $globalTest;
		$ap = new AllPlugins();
		$burrow = new Burrow('010101');
		$burrow->SetPluginData('test','p',0);
		$x = new stdClass();
		$x->sd = '3';
		$x->val = 2;
		$this->assertTrue( $ap->OnPingForward($burrow,$x) );
		$this->assertEqual($burrow->GetPluginData('test','last'),'OnSingleClick02');
	}
	
	function testActive() {
		global $globalTest;
		$ap = new AllPlugins();
		$burrow = new Burrow('010101');
		$burrow->SetPluginData('test','p',0);
		$x = new stdClass();
		$x->sd = '0';
		$x->val = 1;
		
		$p = $ap->plugins[0];
		$p->SetState($burrow,'somestate');
		$burrow->SetPluginData('test','last','a');
		$ap->OnBoot($burrow,$x);
		$this->assertEqual($burrow->GetPluginData('test','last'),'a');
	}

}


?>
