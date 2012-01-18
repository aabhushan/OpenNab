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

require_once('../includes/burrow.php');
require_once('../includes/apireply.php');
require_once('../includes/ping.php');
require_once('../includes/ambientblock.php');
require_once('../includes/plugin.php');
require_once('../plugins/myweather/_plugin.php');

	class TestOfPluginMyWeather extends UnitTestCase {
		function TestOfPluginMyWeather() {
			$this->UnitTestCase();
		}
		
		function makeWeatherString($lang,$sky,$temperature) {
			return "ID 25195130\nMC broadcast/broad/config/weather/".$lang."/signature.mp3\nCL 9928148\nCH broadcast/broad/config/anim/sig_tete_flash_rapide.chor\nMW\nMU broadcast/broad/config/weather/".$lang."/today.mp3\nMW\nMU broadcast/broad/config/weather/".$lang."/sky/".$sky.".mp3\nMW\nMU broadcast/broad/config/weather/".$lang."/temp/".$temperature.".mp3\nMW\nMU broadcast/broad/config/weather/".$lang."/degree.mp3\nMW\nMC broadcast/broad/config/weather/".$lang."/signature.mp3\nCL 9928148\nCH broadcast/broad/config/anim/sig_tete_flash_rapide.chor\nMW\n";
		}
		
		function testWeather() {
			$b = new Burrow('0b0b0b0c0c0c');
			$p = new Plugin_myweather();
			$pr = new Ping('some.domain.com','/vl/p');
			$mb = &$pr->Message();
			
			$weather1 = $this->makeWeatherString('fr','3','4');
			$mb->Encode($weather1);
			$this->assertFalse( $p->OnPingWrite($b,$pr) );
			$this->assertEqual($mb->GetText(),$weather1);
			
			$r = new ApiReply('0b0b0b0c0c0c');
			$weather2 = $this->makeWeatherString('fr','2','5');
			$p->OnApi($b, array('temperature'=>'5', 'sky'=>'2') ,$r);
			$this->assertTrue( $p->OnPingWrite($b,$pr) );
			$this->assertEqual($mb->GetText(),$weather2);
		}
		
		function testSelectedWeatherDay() {
			$p = new Plugin_myweather();
			$this->assertEqual($p->SelectedDay(mktime( 11, 38, 24, 6, 26, 1994 )),26);
			$this->assertEqual($p->SelectedDay(mktime( 20, 38, 24, 6, 26, 1994 )),27);
			$this->assertEqual($p->SelectedDay(mktime( 20, 38, 24, 6, 30, 1994 )),1);
		}
		
    function checkWeatherData($dayData,$sky,$temperature,$line)
    {
			$this->assertEqual($sky,$dayData['sky'],'sky expected='.$sky.' vs computed='.$dayData['sky'].' for line '.$line);
			$this->assertEqual($temperature,$dayData['temperature'],'temperature expected='.$temperature.' vs computed='.$dayData['temperature'].' for line '.$line);
    }
		
		function testDataForWeatherDay() {
			$p = new Plugin_myweather();
      $html = file_get_contents('test_plugin_myweather.html');
			$this->checkWeatherData($p->DayData($html,27),3,11,__LINE__);
			$this->checkWeatherData($p->DayData($html,28),0,18,__LINE__);
			$this->checkWeatherData($p->DayData($html,29),0,26,__LINE__);
			$this->checkWeatherData($p->DayData($html,30),0,30,__LINE__);
			$this->checkWeatherData($p->DayData($html,1),1,26,__LINE__);
			$this->checkWeatherData($p->DayData($html,2),1,27,__LINE__);
			$this->checkWeatherData($p->DayData($html,3),1,24,__LINE__);
		}
    
	}
?>
