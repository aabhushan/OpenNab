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

class Plugin_simpleplay_media
{
	var $plugin, $fileId = false, $reference, $message, $realFile = false, $type;
	
	function Plugin_simpleplay_media(&$plugin,$identifier) {
                addToLog("I AM AT SIMPLE PLAY MEDIA", 3);
		$this->plugin = &$plugin;
		$this->fileId = $identifier;
		if( substr($this->fileId,0,7) == 'http://' ) {
			$this->reference = hexdec(substr(md5($this->fileId),26,6));
			$this->message = "ID ".$this->reference."\nST ".$this->fileId."\nPL 1\nMW\n";
			$this->type = 'Stream';
		} else {
			$this->reference = $this->fileId % 100000;
			$filename = $this->GetFilenameFromId($this->reference);
			$this->realFile = new File($this->plugin->GetFilePath($filename));
			$this->message = "ID ".$this->reference."\nST broadcast/broad/simpleplay".$filename."\nPL 1\nMW\n";
			$this->type = 'File';
		}
	}
	
	function GetFilenameFromId($id) {
            addToLog("I AM AT FILENAME", 3);
		$idstr = sprintf('%05d',$id);
		$filename = '';
		while( strlen($idstr) > 0 ) {
			$filename .= '/'.substr($idstr,0,1);
			$idstr = substr($idstr,1);
		}
		$filename .= '.mp3';
		return $filename;
	}

	function SendTo(&$burrow,&$apiReply)
	{
                addToLog("I AM AT SENDTO", 3);
		if( $this->realFile && !$this->realFile->Exists() ) {
			$apiReply->Add( 'Error', 'Unknown file '.$this->fileId );
		} else {
			$this->plugin->SetData($burrow,'fileToPlay',$this->fileId);
			$apiReply->Add( 'Play'.$this->type, $this->fileId );
			addToLog( 'simpleplay: registering new message to play '.$this->fileId, 3 );
		}
	}
}


class Plugin_simpleplay extends Plugin
{
	
	function Plugin_simpleplay()
	{
	}

	function GetFileId($fieldName) {
            addToLog("I AM AT GET FILE FROM ID", 3);
		$fileId = $parameters['upload'];
		$fileId = $fileId % 100000;
	}
		
	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
                addToLog("I AM AT PING WRITE", 3);
		$fileToPlay = $this->GetData($burrow,'fileToPlay');
		if( $fileToPlay === false )
			return false; // nothing to play for this rabbit
		$media = new Plugin_simpleplay_media($this,$fileToPlay);
		$messageBlock = &$pingRequest->Message();
		$someOtherMessageNeedsToBePlayed = ( $messageBlock->Size() > 0 && !$pingRequest->IsEndOfMessage() );
		$ourMessageHasJustEnded = ( $pingRequest->GetCurrentID() == $media->reference && !$pingRequest->IsTimeout() );
		if( $ourMessageHasJustEnded ) {
			$this->ClearData($burrow,'fileToPlay');
			if( $someOtherMessageNeedsToBePlayed ) {
				addToLog( 'simpleplay: '.$fileToPlay.' reached the end ; now playing another message', 3 );
				return false;
			} else {
				$messageBlock->Encode( ID_DEFAULT_MESSAGE );
				addToLog( 'simpleplay: '.$fileToPlay.' reached the end ; back to default state', 3 );
				return true;
			}
		}
		$ourMessageIsBeingPlayed = ( $pingRequest->GetCurrentID() == $media->reference && $pingRequest->IsTimeout() );
		if( $someOtherMessageNeedsToBePlayed && !$ourMessageIsBeingPlayed ) {
			addToLog( 'simpleplay: let another message be played before our message '.$fileToPlay, 3 );
			return false;
		}
		if( $ourMessageIsBeingPlayed ) { // then let it go without other notification
			$messageBlock->data = '';
			addToLog( 'simpleplay: still playing '.$fileToPlay, 3 );
		} else { // send the message notification
			$messageBlock->Encode( $media->message );
			addToLog( 'simpleplay: now playing '.$fileToPlay, 3 );
		}
		return true;
	}
	
	// Raised at Rabbit broadcasted file request
	// returns true if broadcast operation is handled by the plugin
	// returns false if broadcast operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnBroadcast(&$request)
	{
                addToLog("I AM AT BROADCAST", 3);
		$header = '/broad/simpleplay/';
		if( strpos($request->uri,$header) === false )
			return false; // the broadcast request is not for us
		$filename = substr($request->uri,strlen($header));
		addToLog( 'simpleplay: sending file '.$filename, 3 );
		@readfile($this->GetFilePath($filename));
		$request->reply = '';
		return true;
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
                addToLog("I AM AT ONAPI", 3);
		if( !array_key_exists('play',$parameters) )
			return false;
		if( $parameters['play'] == 'x' ) {
			$this->ClearData($burrow,'fileToPlay');
			$reply->Add( 'PlayFile', 'cancelled' );
		} else {
			$media = new Plugin_simpleplay_media($this,$parameters['play']);
			$media->SendTo($burrow,$reply);
		}
		return true;
	}
	
	// Raised at API call when no serial number is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnGlobalApi($parameters,&$reply)
	{
                addToLog("I AM AT ONGLOBALAPI", 3);
		if( !array_key_exists('upload',$parameters) )
			return false;
		$media = new Plugin_simpleplay_media($this,$parameters['upload']);
		$media->realFile->Write(file_get_contents('php://input'));
		addToLog( 'Simple play uploaded file '.$media->realFile->filepath, 3 );
		$reply->Add( 'UploadFile', $parameters['upload'] );
		return true;
	}

}
?>
