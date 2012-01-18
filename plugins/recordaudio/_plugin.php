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

class Plugin_recordaudio extends Plugin
{
	
	function Plugin_recordaudio()
	{
	}
	
	// Raised at Rabbit voice recording (when record.jsp is called)
	// returns true if record operation is handled by the plugin
	// returns false if record operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnRecord(&$burrow,&$request)
	{
		// Save the recorded wave file
		$filepath = $this->GetFilePath('record_'.date('YmdHis').'.wav');
		fileWrite( $filepath, $GLOBALS['HTTP_RAW_POST_DATA'] );
		addToLog( 'Saved audio recording to file '.$filepath, 3 );
		return false;
	}

}

?>
