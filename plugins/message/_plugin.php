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

class Plugin_message extends Plugin
{
	
	function Plugin_message()
	{
	}
		
	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$message = $this->GetData($burrow,'message');
		if( $message === false )
			return false;
		$messageBlock = &$pingRequest->Message();
		$messageBlock->Encode( $message );
		$this->ClearData($burrow,'message');
		addToLog( 'message: sending custom message', 3 );
		return true;
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		if( !array_key_exists('message',$parameters) )
			return false;
		$message = $parameters['message'];
		$this->SetData($burrow,'message',$message);
		$reply->Add( 'Message', $message );
		return true;
	}

}
?>
