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
require_once('../includes/ambientblock.php');
require_once('../includes/ping.php');
require_once('../includes/plugin.php');
require_once('../plugins/myradio/_plugin.php');

	class TestOfPluginMyRadio extends UnitTestCase {
		function TestOfPluginMyRadio() {
			$this->UnitTestCase();
		}
		
		function makeRadioString($id,$url,$pl) {
			return "ID ".$id."\nST ".$url."\nPL ".$pl."\nMW\n";
		}
		
		function testRadio() {
			$b = new Burrow('0b0b0b0c0c0c');
			$r = new ApiReply('0b0b0b0c0c0c');
			$p = new Plugin_myradio();
			$pr = new Ping('some.domain.com','/vl/p');
			$pr->Generate();
			$pr->UnpackBlocks();
			$mb = &$pr->Message();
			
			$p->OnApi($b, array('radio8'=>'http://my.station.com/','radio9'=>'http://my.other.station.com/') ,$r);
			$this->assertEqual($p->GetData($b,8),'http://my.station.com/');
			$this->assertEqual($p->GetData($b,9),'http://my.other.station.com/');
			
			$pr->sd = '8008';
			$this->assertFalse( $p->OnPingForward($b,$pr) );
			$mb->Encode($this->makeRadioString('25353933','http://213.205.96.91:9915','4'));
			$p->OnPingReadBefore($b,$pr);
			$this->assertEqual($p->GetData($b,'messageFormat'),$this->makeRadioString('25353933','%s','4'));
			$this->assertEqual($p->GetData($b,'currentRadio'),'http://213.205.96.91:9915');
			$this->assertFalse( $p->OnPingWrite($b,$pr) );
			$this->assertEqual($mb->GetText(),$this->makeRadioString('25353933','http://213.205.96.91:9915','4'));

			$pr->sd = '8009';
			$this->assertFalse( $p->OnPingForward($b,$pr) );
			$this->assertEqual($p->GetData($b,'currentRadio'),'http://my.other.station.com/');
			$mb->Encode('');
			$p->OnPingReadBefore($b,$pr);
			$this->assertEqual($p->GetData($b,'currentRadio'),'http://my.other.station.com/');
			$this->assertFalse( $p->OnPingWrite($b,$pr) );
			$this->assertEqual($mb->GetText(),$this->makeRadioString('25353933','http://my.other.station.com/','4'));

			$pr->sd = '8008';
			$this->assertFalse( $p->OnPingForward($b,$pr) );
			$this->assertEqual($p->GetData($b,'currentRadio'),'http://my.station.com/');
			$mb->Encode('');
			$p->OnPingReadBefore($b,$pr);
			$this->assertEqual($p->GetData($b,'currentRadio'),'http://my.station.com/');
			$this->assertFalse( $p->OnPingWrite($b,$pr) );
			$this->assertEqual($mb->GetText(),$this->makeRadioString('25353933','http://my.station.com/','4'));

			$pr->sd = '1';
			$this->assertFalse( $p->OnPingForward($b,$pr) );
			$this->assertFalse($p->GetData($b,'messageFormat'));
			$this->assertFalse($p->GetData($b,'currentRadio'));
			$mb->Encode(ID_DEFAULT_MESSAGE);
			$p->OnPingReadBefore($b,$pr);
			$this->assertFalse($p->GetData($b,'messageFormat'));
			$this->assertFalse($p->GetData($b,'currentRadio'));
			$this->assertFalse( $p->OnPingWrite($b,$pr) );
			
			$pr->sd = '8008';
			$this->assertFalse( $p->OnPingForward($b,$pr) );
			$mb->Encode($this->makeRadioString('25353933','http://213.205.96.91:9915','4'));
			$p->OnPingReadBefore($b,$pr);
			$this->assertEqual($p->GetData($b,'messageFormat'),$this->makeRadioString('25353933','%s','4'));
			$this->assertEqual($p->GetData($b,'currentRadio'),'http://213.205.96.91:9915');
			$this->assertFalse( $p->OnPingWrite($b,$pr) );
			$this->assertEqual($mb->GetText(),$this->makeRadioString('25353933','http://213.205.96.91:9915','4'));

		}
	}
?>
