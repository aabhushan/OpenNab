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

class Demo_ambient extends Demo
{
  function Display() {
?>
Override ambient services :<br/>
<?php
for($i=1; $i<=4; $i++) {
	print "Service Id:<input type='text' name='services[$i][id]' value='' />";
	print " Value:<input type='text' name='services[$i][value]' value='' /><br />";
}
?>
Right ear: <input type='text' name='others[ear0]' value='' /><br/>
Left ear: <input type='text' name='others[ear1]' value='' /><br/>
Nose: <input type='text' name='others[nose]' value='' /><br/>
<input type='submit' name='s' value='Override' /><br/>
<?php
  }
  
  function Execute() {
		$parameters = array();
		foreach( $_REQUEST['services'] as $service ) {
			if( $service['id']!='' )
				$parameters['ambient'.$service['id']] = $service['value'];
		}
		foreach( $_REQUEST['others'] as $name => $value ) {
			$parameters[$name] = $value;
		}
    $apiCall = new LocalApiCall();
		return $apiCall->Get($parameters);
	}
}

?>