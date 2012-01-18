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

class Plugin_mood_media
{
	var $plugin, $fileId = false, $reference, $message, $realFile = false, $type;
	
	// Create a mood message: $identifier is the filename to play
	//
	function Plugin_mood_media(&$plugin,$identifier) {
		$this->plugin = &$plugin;
		$this->fileId = $identifier;
		$this->reference = hexdec(substr(md5($this->fileId),26,6));
		$this->realFile = new File($this->plugin->GetFilePath($identifier));
		$this->message = "ID ".$this->reference."\nST broadcast/broad/mood".$identifier."\nPL 1\nMW\n";
		$this->type = 'File';
	}
	
	function SendTo(&$burrow)
	{
		if( $this->realFile && !$this->realFile->Exists() ) {
			addToLog( 'mood: Error, Unknown file: '.$this->realFile->filepath,2 );
		} else {
			$this->plugin->SetData($burrow,'fileToPlay',$this->fileId);
			addToLog( 'mood: registering new message to play '.$this->fileId, 3 );
		}
	}
}


class Plugin_mood extends Plugin
{
	var $topfolder="";
	
	function Plugin_mood()
	{
	}

	function GetFileId($fieldName) {
		$fileId = $parameters['upload'];
		$fileId = $fileId % 100000;
	}
		
	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$fileToPlay = $this->GetData($burrow,'fileToPlay');
		if( $fileToPlay === false )
			return false; // nothing to play for this rabbit
		$media = new Plugin_mood_media($this,$fileToPlay);
		$messageBlock = &$pingRequest->Message();
		$someOtherMessageNeedsToBePlayed = ( $messageBlock->Size() > 0 && !$pingRequest->IsEndOfMessage() );
		$ourMessageHasJustEnded = ( $pingRequest->GetCurrentID() == $media->reference && !$pingRequest->IsTimeout() );
		if( $ourMessageHasJustEnded ) {
			$this->ClearData($burrow,'fileToPlay');
			if( $someOtherMessageNeedsToBePlayed ) {
				addToLog( 'mood: '.$fileToPlay.' reached the end ; now playing another message', 3 );
				return false;
			} else {
				$messageBlock->Encode( ID_DEFAULT_MESSAGE );
				addToLog( 'mood: '.$fileToPlay.' reached the end ; back to default state', 3 );
				return false;
			}
		}
		$ourMessageIsBeingPlayed = ( $pingRequest->GetCurrentID() == $media->reference && $pingRequest->IsTimeout() );
		if( $someOtherMessageNeedsToBePlayed && !$ourMessageIsBeingPlayed ) {
			addToLog( 'mood: let another message be played before our message '.$fileToPlay, 3 );
			return false;
		}
		if( $ourMessageIsBeingPlayed ) { // then let it go without other notification
			$messageBlock->data = '';
			addToLog( 'mood: still playing '.$fileToPlay, 3 );
		} else { // send the message notification
			$messageBlock->Encode( $media->message );
			addToLog( 'mood: now playing '.$fileToPlay, 3 );
		}
		return true;
	}

        // Raised at Rabbit broadcasted file request
        // returns true if broadcast operation is handled by the plugin
        // returns false if broadcast operation should be handled elsewhere, and, by default, forwarded to the main server
        function OnBroadcast(&$request)
        {
                $header = '/broad/mood/';
                if( strpos($request->uri,$header) === false )
                        return false; // the broadcast request is not for us
                $filename = substr($request->uri,strlen($header));
                addToLog( 'mood: sending file '.$filename, 3 );
                @readfile($this->GetFilePath($filename));
                $request->reply = '';
                return true;
        }

	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		$this->ProcessParameter($burrow,$parameters,$reply,'mood_frequency','Frequency');
		$this->ProcessParameter($burrow,$parameters,$reply,'mood_language','Language');
                return false;
        }

	// Scans a folder & subfolder for mp3 files
        function ScanMP3Folder($foldername) {
    		$contents = array();
    		$folderHandle = @opendir($foldername);
    		if( !$folderHandle )
      			return false;
    		while( false !== ($filename = @readdir($folderHandle)) ) {
      			if( substr($filename,0,1) == '.' )
        			continue;
			if (substr($filename,-3,3) != 'mp3')
				continue;
      			$fullfilename = $foldername.'/'.$filename;
      				if( $subdirs = File::ScanFolder($fullfilename) )
        		$contents = array_merge( $contents, $subdirs );
      			else
        			$contents[] = $fullfilename;
    		}
    		return $contents;
        }



        // Raised at cron interval: use the 'frequency' parameter to randomly calculate
	// whether we should send a message or not.
        function OnCron(&$burrow,&$request)
        { 
		addToLog('mood: cron request',3);
                $frequency = $this->GetData($burrow,'mood_frequency');
		$language = $this->GetData($burrow,'mood_language');
		$shallIPlay = rand(0,10);
		if ($shallIPlay < $frequency) {
			addToLog('mood: I should play something',3);
			// Decide on what to play:
			// Choose the language:
	                // Transform the language parameter into an array:
       		        $languages = split(",",$this->GetData($burrow,'mood_language'));
			$lang = $languages[array_rand($languages)];
			addToLog('mood: language: ' . $lang,3);
      			$basepath = $this->GetFilePath($this->topfolder) . '/' . $lang;
      			$offset = strlen($basepath)-strlen($this->topfolder.'/'.$lang);
      			$files = $this->ScanMP3Folder($basepath);
			if ( $files ) {
				$file = $files[array_rand($files)];
				addToLog('mood: file to play: '.substr($file,$offset),3);
				$media = new Plugin_mood_media($this,substr($file,$offset));
				$media->SendTo($burrow);
      			}

		} else {
			addToLog('mood: nope, I won\'t play',3);
		}
	}
	
}
?>
