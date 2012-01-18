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

require_once('_plugin.php');

class SubclassDouble_for_Plugin_downtime extends Plugin_downtime
{
  var $pingInterval, $currentTime;
  var $burrowDatas = array(), $configValues = array(), $apiReply = array();
  
	function GetConfigurationValue($key) {
    return $this->configValues[$key];
	}
  
	function GetData($burrow,$key) {
    return $this->burrowDatas[$key];
	}
  
	function SetData($burrow,$key,$value) {
    $this->burrowDatas[$key] = $value;
	}
  
	function GetCurrentTime() {
    return $this->currentTime;
	}
  
	function GetPingInterval() {
    return $this->pingInterval;
	}
  
	function Add($key,$value) {
    $this->apiReply[$key] = $value;
	}
  
	function ExecuteOnPingReadAfter($currentTime,$pingInterval) {
    $this->currentTime = $currentTime;
    $this->pingInterval = $pingInterval;
    $this->OnPingReadAfter($this,$this);
	}
  
	function ExecuteOnApi($parameters,$currentTime,$threshold) {
    $this->currentTime = $currentTime;
    $this->configValues['Threshold'] = $threshold;
    $this->OnApi($this,$parameters,$this);
	}
}

class TestOfPluginDowntime extends UnitTestCase {

	function TestOfPluginDowntime() {
		$this->UnitTestCase();
	}
	
	function testDowntime() {
    $p = new SubclassDouble_for_Plugin_downtime();
    $p->ExecuteOnApi(array(),123456789,0);
		$this->assertFalse( isset($p->apiReply['Downtime']), 'Downtime without getdowntime api param - %s' );
		$p->ExecuteOnApi(array('getdowntime'=>1),123456789,0);
		$this->assertEqual( 123456789, $p->apiReply['Downtime'], 'Downtime=currenttime when no nextcall is known - %s' );
		$p->ExecuteOnPingReadAfter(1000000,120);
		$this->assertEqual( 1000120, $p->burrowDatas['nextcall'], 'Nextcall set on ping read after - %s' );
		$p->ExecuteOnApi(array('getdowntime'=>1),1000060,0);
		$this->assertEqual( 0, $p->apiReply['Downtime'], 'Downtime=0 before nextcall - %s' );
		$p->ExecuteOnApi(array('getdowntime'=>1),1000122,0);
		$this->assertEqual( 2, $p->apiReply['Downtime'], 'Downtime=2 at 2 after nextcall - %s' );
		$p->ExecuteOnApi(array('getdowntime'=>1),1000122,5);
		$this->assertEqual( 0, $p->apiReply['Downtime'], 'Downtime=0 after nextcall within threshold - %s' );
		$p->ExecuteOnApi(array('getdowntime'=>1),1000128,5);
		$this->assertEqual( 8, $p->apiReply['Downtime'], 'Downtime=8 at 8 after nextcall which is beyond threshold - %s' );
	}
}
?>



