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

class Plugin_myradio extends Plugin
{
	function Plugin_myradio()
	{
	}
  
  function DefaultStation($i)
  {
    return $this->GetConfigurationValue('DefaultRadio'.$i);
  }
		
	// Raised at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnPingForward(&$burrow,&$pingRequest)
	{
		$radioModeIsOn = ($this->GetData($burrow,'messageFormat') !== false);
		if( !$radioModeIsOn )
			return false;
		if( $pingRequest->IsEarsMove() ) {
			$earpos = $pingRequest->GetEarMove(LEFT_EAR);
			$newStation = $this->GetData($burrow,$earpos);
			if( $newStation === false )
				$newStation = $this->DefaultStation($earpos);
			$this->SetData($burrow,'currentRadio',$newStation);
			addToLog( 'myradio: change station to ['.$earpos.'] '.$newStation, 3 );
		} else if( $pingRequest->IsEndOfMessage() ) {
			$this->ClearData($burrow,'currentRadio');
			addToLog( 'myradio: end of stream ; move left ear to change station', 3 );
		} else if( $pingRequest->IsSingleClickWhilePlayingMessage() ) {
			$this->ClearData($burrow,'currentRadio');
			addToLog( 'myradio: pause radio mode ; move left ear to change station', 3 );
		} else if( $pingRequest->IsDoubleClick() ) {
			$this->ClearData($burrow,'messageFormat');
			$this->ClearData($burrow,'currentRadio');
			addToLog( 'myradio: exit radio mode', 3 );
		}
		return false;
	}
	
	// Raised at Rabbit ping (when p4.jsp is called) before any burrow update due to the ping
	function OnPingReadBefore(&$burrow,&$pingRequest)
	{
		$messageBlock = &$pingRequest->Message();
		$text = $messageBlock->GetText();
		$enteringRadioMode = preg_match( "#^(ID [0-9]+\nST )(.*?)(\nPL [0-9]\nMW\n)$#", $text, $matches );
		if( $enteringRadioMode ) {
			$this->SetData($burrow,'messageFormat',$matches[1].'%s'.$matches[3]);
			$this->SetData($burrow,'currentRadio',$matches[2]);
			addToLog( 'myradio: entering radio mode', 3 );
		}
	}
	


	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$format = $this->GetData($burrow,'messageFormat');
		$currentRadio = $this->GetData($burrow,'currentRadio');
		$playRadioStation = ( ( $format !== false ) && ( $currentRadio !== false ) );
		if( $playRadioStation ) {
			$messageBlock = &$pingRequest->Message();
			$messageBlock->Encode( sprintf($format,$currentRadio) );
			addToLog( 'myradio: now playing '.$currentRadio, 3 );
		}
		return false;
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		if( array_key_exists('radiolist',$parameters) ) {
			for( $i=0; $i<16; $i++ ) {
				$station = $this->GetData($burrow,$i);
				if( $station === false ) {
					$reply->Add( 'DefaultRadio'.$i, $this->DefaultStation($i) );
				} else {
					$reply->Add( 'Radio'.$i, $station );
				}
			}
			return true;
		}
		foreach($parameters as $key => $value) {
			if( substr($key,0,5) == 'radio' ) {
				$earpos = substr($key,5);
				$url = $value;
				if( $url == '' ) {
					$this->ClearData($burrow,$earpos);
					$reply->Add( 'DefaultRadio'.$earpos, $this->DefaultStation($earpos) );
				} else {
					$this->SetData($burrow,$earpos,$url);
					$reply->Add( 'Radio'.$earpos, $url );
				}
			}
		}
		return false;
	}

}
?>
