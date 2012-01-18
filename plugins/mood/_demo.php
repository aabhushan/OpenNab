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

//===================================================

class Demo_mood extends Demo
{
  function Display() {
?>
Mood settings :<br/>
Mood frequency (between 1 and 10): <input type='text' name='mood_frequency' value='' /><br/>
Mood language (comma-separated list of country names) : <input type='text' name='mood_language' value='' /><br/>
<input type='submit' name='s' value='Submit' /><br/>
<?php
  }
  
  function Execute() {
    $apiCall = new LocalApiCall();
    return $apiCall->Get( array('mood_frequency'=>urlencode($_REQUEST['mood_frequency']), 'mood_language'=>urlencode($_REQUEST['mood_language'])) );
	}
  
}

?>
