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

class Plugin_pinginterval extends Plugin
{
	
	function Plugin_pinginterval()
	{
	}
	
	function StopTheRequest(&$burrow,&$pingRequest)
	{
		if( !$pingRequest->IsTimeout() )
			return false;
		$countdown = $this->GetData($burrow,'countdown');
		return ( $countdown > 0 );
	}
	
	// Raised at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnPingForward(&$burrow,&$pingRequest)
	{
		return $this->StopTheRequest($burrow,$pingRequest);
	}
	
	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$interval = $this->GetData($burrow,'interval');
		if( $interval === false )
			return false;
		$ambient = &$pingRequest->Ambient();
		if( $this->StopTheRequest($burrow,$pingRequest) ) {
			$countdown = $this->GetData($burrow,'countdown') - $interval;
			$ambient->data = $this->GetData($burrow,'ambient');
		} else {
			addToLog( 'pinginterval: ping to main server', 3 );
			$countdown = $pingRequest->GetPingInterval() - $interval;
			$this->SetData($burrow,'ambient',$ambient->data);
		}
		$this->SetData($burrow,'countdown',$countdown);
		$pingRequest->SetPingInterval($interval);
		return false;
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		if( !array_key_exists('pinginterval',$parameters) )
			return false;
		$interval = $parameters['pinginterval'];
		if( $interval >= $this->GetConfigurationValue('LowerLimit') ) {
			$this->SetData($burrow,'interval',$interval);
			$reply->Add( 'PingInterval', $interval );
		} else {
			$this->ClearData($burrow,'interval');
			$this->ClearData($burrow,'countdown');
			$reply->Add( 'PingInterval', 'off' );
		}
		return false;
	}

}
?>
