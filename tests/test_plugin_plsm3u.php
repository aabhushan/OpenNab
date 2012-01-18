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
require_once('../plugins/plsm3u/_plugin.php');

class My_plsm3u extends Plugin_plsm3u {

	var $contents;
	
	function GetContents($url)
	{
		return $this->contents;
	}
	
}

class TestOfPluginPlsM3u extends UnitTestCase {
	function TestOfPluginPlsM3u() {
		$this->UnitTestCase();
	}
	
	function makeRadioString($url) {
		return "ID 123456\nST ".$url."\nPL 3\nMW\n";
	}
	
	function testChangeContents() {
		$b = new Burrow('0b0b0b0c0c0c');
		$p = new My_plsm3u();
		$pr = new Ping('some.domain.com','/vl/p');
		$mb = &$pr->Message();
		
		$radio1 = $this->makeRadioString('http://some.server.com/radio.mp3');
		$mb->Encode($radio1);
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),$radio1);
		
		$radio2 = $this->makeRadioString('http://irc1.ax-proxima.net:8000/listen.pls');
		$mb->Encode($radio2);
		$p->contents = "[playlist]\nNumberOfEntries=1\nFile1=http://ufg.impek.tv:80/\n";
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),$this->makeRadioString('http://ufg.impek.tv:80/'));
		$p->contents = "[playlist]\nNumberOfEntries=2\nFile1=http://ufg.impek.tv:80/\nFile2=http://ukg.imtek.tv:81/\n";
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),$this->makeRadioString('http://ufg.impek.tv:80/'));
		
		$radio3 = $this->makeRadioString('http://libre-attitude.org/radio/RLA.m3u');
		$mb->Encode($radio3);
		$p->contents = "#EXTM3U\n#EXTINF:-1,Radio Libre Attitude\nhttp://rla-stream.libre-attitude.org:8000/rla-hq.ogg\n";
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),$this->makeRadioString('http://rla-stream.libre-attitude.org:8000/rla-hq.ogg'));
		$p->contents = "#EXTM3U\n#EXTINF:-1,Radio Libre Attitude\nhttp://rla-stream.libre-attitude.org:8000/rla-hq.ogg\n#EXTINF:-1,Radio Libre Attitude, serveur 1\nhttp://rla-slave1.libre-attitude.org:8000/rla-hq.ogg\n#EXTINF:-1,Radio Libre Attitude, serveur 2\nhttp://rla-slave2.libre-attitude.org:8000/rla-hq.ogg\n";
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),$this->makeRadioString('http://rla-stream.libre-attitude.org:8000/rla-hq.ogg'));
		
		$radio4 = $this->makeRadioString('http://www.jamzine.net:8000/stream.m3u');
		$mb->Encode($radio4);
		$p->contents = "http://www.jamzine.net:8000/stream";
		$this->assertFalse( $p->OnPingWrite($b,$pr) );
		$this->assertEqual($mb->GetText(),$this->makeRadioString('http://www.jamzine.net:8000/stream'));

	}
}
?>



