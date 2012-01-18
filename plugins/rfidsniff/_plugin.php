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

class Plugin_rfidsniff extends Plugin
{
	
	function Plugin_rfidsniff()
	{
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		if( array_key_exists('getlastrfid',$parameters) )
      $reply->Add( 'RfidTagId', $this->GetData($burrow,'tagid') );
		else if( array_key_exists('clearlastrfid',$parameters) ) {
      $this->ClearData($burrow,'tagid');
      $reply->Add( 'RfidTagId', '' );
    }
		return false;
	}

	// Raised at Rabbit rfid detection (when rfid.jsp is called)
	// returns true if rfid operation is handled by the plugin
	// returns false if rfid operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnRfid(&$burrow,&$request)
	{
		$this->SetData($burrow,'tagid',$request->tagid);
		addToLog( 'rfidsniff: detected tag id '.$request->tagid, 3 );
		return false;
	}

}
?>
