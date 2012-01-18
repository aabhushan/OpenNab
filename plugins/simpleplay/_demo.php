<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag� (http://www.nabaztag.com/) electronic pet.
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

class Demo_upload extends Demo
{
  function Display() {
?>
Upload mp3 file :<br/>
<input type='file' name='fileData' value='' /><br/>
File Id: <input type='text' name='fileId' value='' /><br/>
<input type='submit' name='s' value='Upload' /><br/>
<?php
  }
  
  function UseSerialNumber() {
    return false;
  }
  
  function Execute() {
    $apiCall = new LocalApiCall();
		if( isset($_FILES['fileData']['tmp_name']) )
      return $apiCall->Put( array('upload'=>$_REQUEST['fileId']), file_get_contents('P:'.$_FILES['fileData']['tmp_name']));
		else
      return false;
  }
}

//===================================================

class Demo_play extends Demo
{
  function Display() {
?>
Play mp3 file :<br/>
File Id (or full mp3 streaming url): <input type='text' name='fileId' value='' /><br/>
<input type='submit' name='s' value='Play' /><br/>
<?php
  }
  
  function Execute() {
    $apiCall = new LocalApiCall();
		return $apiCall->Get( array('play'=>urlencode($_REQUEST['fileId'])) );
	}
}

?>