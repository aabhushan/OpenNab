<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag™ (http://www.nabaztag.com/) electronic pet.
Copyright © 2007 OpenNab team - http://sourceforge.net/project/memberlist.php?group_id=187057

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

class Plugin_asleep extends Plugin
{
	
	function Plugin_asleep()
	{
	}
	
	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$asleep = $this->GetData($burrow,'asleep');
		if( $asleep === false )
			return false;
		$messageBlock = &$pingRequest->Message();
		if( $asleep == 1 ) { // rabbit should sleep
			if( $pingRequest->GetCurrentID() == ID_ASLEEP )
				$messageBlock->Encode(''); // no message while rabbit is sleeping
			else
				$messageBlock->Encode(ID_ASLEEP_MESSAGE); // ask rabbit to go asleep
			return true;
		} else { // rabbit should not sleep
			if( $pingRequest->GetCurrentID() == ID_ASLEEP )
				$messageBlock->Encode(ID_DEFAULT_MESSAGE); // ask rabbit to wake up
			return false;
		}
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		if( !array_key_exists('asleep',$parameters) )
			return false;
		$asleep = $parameters['asleep'];
		if( $asleep == '' ) {
			$this->ClearData($burrow,'asleep');
			$reply->Add( 'Asleep', 'default' );
			return;
		}
		if( $asleep != 0 ) {
			$this->SetData($burrow,'asleep',1);
			$reply->Add( 'Asleep', 'on' );
		} else {
			$this->SetData($burrow,'asleep',0);
			$reply->Add( 'Asleep', 'off' );
		}
		return false;
	}

}
?>