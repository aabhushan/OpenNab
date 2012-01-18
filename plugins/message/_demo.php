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

class Demo_message extends Demo
{
  var $message = "ID 25195130\nMC broadcast/broad/config/weather/fr/signature.mp3\nCL 9928148\nCH broadcast/broad/config/anim/sig_tete_flash_rapide.chor\nMW\nMU broadcast/broad/config/weather/fr/today.mp3\nMW\nMU broadcast/broad/config/weather/fr/sky/3.mp3\nMW\nMU broadcast/broad/config/weather/fr/temp/4.mp3\nMW\nMU broadcast/broad/config/weather/fr/degree.mp3\nMW\nMC broadcast/broad/config/weather/fr/signature.mp3\nCL 9928148\nCH broadcast/broad/config/anim/sig_tete_flash_rapide.chor\nMW\n";

  function Display() {
?>
Send a 0a message in Nabaztag syntax :<br/>
<textarea name='message' rows='8' cols='80' /><?php print $this->message?></textarea><br/>
<input type='submit' name='s' value='Send' /><br/>
<?php
  }
  
  function Execute() {
		$this->message = str_replace( "\r", "", $_REQUEST['message'] );
    $apiCall = new LocalApiCall();
		return $apiCall->Post( array('message'=>$this->message) );
	}
}

?>