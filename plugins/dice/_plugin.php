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

class Plugin_dice extends Plugin
{	
	function Plugin_dice()
	{
	}
	
	// Raised when plugin state is changed
	// returns false to activate all calbacks
	// returns a callback array to activate a subset of callbacks
	function OnGetActiveCallbacks(&$burrow,$state)
	{
		if( $state == 'waiting' )
			return array( 'OnSingleClick', 'OnDoubleClick', 'OnApi' );
		else if( $state == 'message' )
			return array( 'OnSingleClick', 'OnDoubleClick', 'OnPingWrite', 'OnApi' );
		else if( $state == 'aftermessage' )
			return array( 'OnSingleClick', 'OnDoubleClick', 'OnEndOfMessage', 'OnApi' );
		else // default state
			return array( 'OnApi' );
	}

	// Raised on single click on head, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnSingleClick(&$burrow,&$pingRequest)
	{
		$lang = $this->GetGlobalData($burrow,'lang');
                //$lang = "en"; // temporary fix
		$value = rand( 1, 6 );
		$id = 1233210 + $value;
		$message = "ID ".$id."\nMU broadcast/vl/plugins/dice/files/".$lang."/get.mp3\nMW\nMU broadcast/vl/plugins/dice/files/".$lang."/".$value.".mp3\nMW\n";
		$this->SetData($burrow,'message',$message);
		$this->SetData($burrow,'messageId',$id);
		$this->SetState($burrow,'message');
		addToLog( 'dice: rolling dice and getting '.$value, 3 );
		return true;
	}

	// Raised on double click on head, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnDoubleClick(&$burrow,&$pingRequest)
	{
		return $this->ExitDiceMode($burrow);
	}

	// Raised when a message that was sent has finished, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnEndOfMessage(&$burrow,&$pingRequest)
	{
		if( $pingRequest->GetCurrentID() != $this->GetData($burrow,'messageId') )
			return false;
		$this->SetData($burrow,'message',ID_DEFAULT_MESSAGE);
		$this->SetState($burrow,'message');
		addToLog( 'dice: end of message ; waiting for another one', 3 );
		return true;
	}


	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$messageBlock = &$pingRequest->Message();
		$messageBlock->Encode( $this->GetData($burrow,'message') );
		$this->SetState($burrow,'aftermessage');
		return true;
	}
	
	function ExitDiceMode(&$burrow)
	{
		$this->ClearData($burrow,'message');
		$this->ClearData($burrow,'messageId');
		$this->SetDefaultState($burrow);
		addToLog( 'dice: leaving dice mode', 3 );
		return false;
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		if( !array_key_exists('dice',$parameters) )
			return false;
		$dice = $parameters['dice'];
		if( $dice == 'on' ) {
			$this->SetState($burrow,'waiting');
			$reply->Add( 'Dice', 'on' );
			addToLog( 'dice: entering dice mode', 3 );
			return true;
		}
		$reply->Add( 'Dice', 'off' );
		return $this->ExitDiceMode($burrow);
	}

}
?>
