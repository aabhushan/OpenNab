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
require_once('../plugins/ambient/_plugin.php');

	class TestOfPluginAmbient extends UnitTestCase {
		function TestOfPluginAmbient() {
			$this->UnitTestCase();
		}
		
		function testAmbient() {
			$b = new Burrow('0b0b0b0c0c0c');
			$pr = new Ping('some.domain.com','/vl/p');
			$ab = &$pr->Ambient();
			$ab->data = '7fffffff0103030207010e2802050000000000000b0d00';
			
			$p = new Plugin_ambient();
			$p->OnPingWrite($b,$pr);
			$this->assertEqual($ab->data,'7fffffff0103030207010e2802050000000000000b0d00');
			
			$r = new ApiReply('0b0b0b0c0c0c');
			$p->OnApi($b, array('ambient3'=>5, 'ambient14'=>42, 'ambient8'=>1) ,$r);
			$p->OnPingWrite($b,$pr);
			$this->assertEqual($ab->data,'7fffffff0103030507010e2a02050801000000000b0d00');
		}
	}
?>
