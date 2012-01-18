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

class TestOfPluginRfid2Http extends UnitTestCase {

	function TestOfPluginRfid2Http() {
		$this->UnitTestCase();
	}
	
	function testChangeContents() {
		$this->assertFalse( Plugin_rfid2http::MatchingUrl('anything','mysn','mytagid') );
		$this->assertEqual( 'myurl', Plugin_rfid2http::MatchingUrl('/mysn/mytagid/myurl','mysn','mytagid') );
		$this->assertFalse( Plugin_rfid2http::MatchingUrl('/anothersn/mytagid/myurl','mysn','mytagid') );
		$this->assertFalse( Plugin_rfid2http::MatchingUrl('/mysn/anothertagid/myurl','mysn','mytagid') );
		$this->assertEqual( 'myurl-mysn', Plugin_rfid2http::MatchingUrl('/mysn/mytagid/myurl-$1','mysn','mytagid') );
		$this->assertEqual( 'myurl-mytagid', Plugin_rfid2http::MatchingUrl('/mysn/mytagid/myurl-$2','mysn','mytagid') );
		$this->assertEqual( 'myurl-mysn-mytagid', Plugin_rfid2http::MatchingUrl('/mysn/mytagid/myurl-$1-$2','mysn','mytagid') );
		$this->assertEqual( 'myurl', Plugin_rfid2http::MatchingUrl('/.*/mytagid/myurl','mysn','mytagid') );
		$this->assertEqual( 'myurl', Plugin_rfid2http::MatchingUrl('/mysn/.*/myurl','mysn','mytagid') );
		$this->assertEqual( 'myurl', Plugin_rfid2http::MatchingUrl('/.*/.*/myurl','mysn','mytagid') );
		$this->assertEqual( 'myurl', Plugin_rfid2http::MatchingUrl('/m.*/m.*/myurl','mysn','mytagid') );
		$this->assertFalse( Plugin_rfid2http::MatchingUrl('/n.*/.*/myurl','mysn','mytagid') );
		$this->assertFalse( Plugin_rfid2http::MatchingUrl('/.*/n.*/myurl','mysn','mytagid') );
		$this->assertEqual( 'myurl-mysn-mytagid', Plugin_rfid2http::MatchingUrl('/m.*/m.*/myurl-$1-$2','mysn','mytagid') );
	}
}
?>



