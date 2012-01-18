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

class Plugin_helloworld extends Plugin
{
	
	function Plugin_helloworld()
	{
	}
	
	function GetPingEvent(&$pingRequest) {
		if($pingRequest->IsTimeout())
			return 'timeout';
		else if($pingRequest->IsSingleClick())
			return 'single click';
		else if($pingRequest->IsDoubleClick())
			return 'double click';
		else if($pingRequest->IsSingleClickWhilePlayingMessage())
			return 'single click while playing message';
		else if($pingRequest->IsEndOfMessage())
			return 'end of message';
		else if($pingRequest->IsEarsMove())
			return 'ears move';
	}
	
	// Raised at Rabbit startup (when bc.jsp is called)
	function OnBoot(&$burrow,&$request)
	{
		addToLog( 'Hello World from boot for '.$burrow->GetSerialNumber(), 3 );
	}

	// Raised at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnPingForward(&$burrow,&$pingRequest)
	{
		addToLog( 'Hello World from ping forward for '.$burrow->GetSerialNumber().' on '.$this->GetPingEvent(&$pingRequest), 3 );
		return false;
	}

	// Raised at Rabbit ping (when p4.jsp is called) before any burrow update due to the ping
	function OnPingReadBefore(&$burrow,&$pingRequest)
	{
		addToLog( 'Hello World from ping read before for '.$burrow->GetSerialNumber(), 3 );
	}

	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		addToLog( 'Hello World from ping write for '.$burrow->GetSerialNumber(), 3 );
		return false;
	}
	
	// Raised at Rabbit ping (when p4.jsp is called) after all burrow updates due to the ping
	function OnPingReadAfter(&$burrow,&$pingRequest)
	{
		addToLog( 'Hello World from ping read after for '.$burrow->GetSerialNumber(), 3 );
	}
	
	// Raised at Rabbit voice recording (when record.jsp is called)
	// returns true if record operation is handled by the plugin
	// returns false if record operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnRecord(&$burrow,&$request)
	{
		addToLog( 'Hello World from record for '.$burrow->GetSerialNumber(), 3 );
		return false;
	}
	
	// Raised at Rabbit broadcasted file request
	// returns true if broadcast operation is handled by the plugin
	// returns false if broadcast operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnBroadcast(&$request)
	{
		addToLog( 'Hello World from broadcast of resource '.$request->uri, 3 );
		return false;
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		addToLog( 'Hello World from API call for '.$burrow->GetSerialNumber(), 3 );
		return false;
	}
	
	// Raised at API call when no serial number is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnGlobalApi($parameters,&$reply)
	{
		addToLog( 'Hello World from global API call', 3 );
		return false;
	}

	// Raised at cron interval
	function OnCron(&$burrow,&$request)
	{
		addToLog( 'Hello World from cron', 3 );
	}
}

?>
