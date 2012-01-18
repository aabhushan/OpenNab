<?php

/* * *************************************************************************

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

 * ************************************************************************** */

class Plugin_tts_media {

    var $plugin, $fileId = false, $text, $reference, $message, $realFile = false, $type;

    function Plugin_tts_media(&$plugin, $identifier) {
        $this->plugin = &$plugin;
        $this->text = $identifier;
        $speech = file_get_contents('http://192.168.2.84/tts/converter/' . $this->text);
        $tempFilename = utils::randomString(10);
        $filePath = $_SERVER['DOCUMENT_ROOT'] . '/vl/temp/' . $tempFilename . '.mp3';
        file_put_contents($filePath, $speech);
        $serverFilePath = 'http://' . $_SERVER['SERVER_ADDR'] . '/vl/temp/' . $tempFilename . '.mp3';
        $this->fileId = $serverFilePath;
        $this->reference = hexdec(substr(md5($this->fileId), 26, 6));
        $this->message = "ID " . $this->reference . "\nST " . $this->fileId . "\nPL 1\nMW\n";
        $this->type = 'Stream';
    }

    function SendTo(&$burrow, &$apiReply) {
        $this->plugin->SetData($burrow, 'tts', $this->text);
        $apiReply->Add('TTS ' . $this->type, $this->text);
        addToLog('tts: registering new message to play ' . $this->fileId, 3);
    }

}

class Plugin_tts extends Plugin {

    function Plugin_tts() {
        
    }

    // Raised at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
    // returns true if ping operation is handled by the plugin
    // returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
    // This OnPingForward method is called if the current event was not handled by one of the OnTimeOut, OnSingleClick, OnDoubleClick, OnEndOfMessage, OnEarsMove or OnSingleClickWhilePlayingMessage methods.
    function OnPingForward(&$burrow, &$pingRequest) {
        $text = $this->GetData($burrow, 'tts');
        return ( $text !== false );
    }

    // Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
    function OnPingWrite(&$burrow, &$pingRequest) {
        $textToSpeech = $this->GetData($burrow, 'tts');
        if ($textToSpeech === false)
            return false; // nothing to play for this rabbit
        $media = new Plugin_tts_media($this, $textToSpeech);
        $messageBlock = &$pingRequest->Message();
        $someOtherMessageNeedsToBePlayed = ( $messageBlock->Size() > 0 && !$pingRequest->IsEndOfMessage() );
        $ourMessageHasJustEnded = ( $pingRequest->GetCurrentID() == $media->reference && !$pingRequest->IsTimeout() );
        if ($ourMessageHasJustEnded) {
            $this->ClearData($burrow, 'tts');
            if ($someOtherMessageNeedsToBePlayed) {
                addToLog('tts: ' . $textToSpeech . ' reached the end ; now playing another message', 3);
                return false;
            } else {
                $messageBlock->Encode(ID_DEFAULT_MESSAGE);
                addToLog('tts: ' . $textToSpeech . ' reached the end ; back to default state', 3);
                return true;
            }
        }
        $ourMessageIsBeingPlayed = ( $pingRequest->GetCurrentID() == $media->reference && $pingRequest->IsTimeout() );
        if ($someOtherMessageNeedsToBePlayed && !$ourMessageIsBeingPlayed) {
            addToLog('tts: let another message be played before our message ' . $textToSpeech, 3);
            return false;
        }
        if ($ourMessageIsBeingPlayed) { // then let it go without other notification
            $messageBlock->data = '';
            addToLog('tts: still playing ' . $textToSpeech, 3);
        } else { // send the message notification
            $messageBlock->Encode($media->message);
            addToLog('tts: now playing ' . $textToSpeech, 3);
        }
        return true;
    }

    // Raised at Rabbit broadcasted file request
    // returns true if broadcast operation is handled by the plugin
    // returns false if broadcast operation should be handled elsewhere, and, by default, forwarded to the main server
    function OnBroadcast(&$request) {
        if (!preg_match('#/broad/tts/(.*?)/(.*)#', $request->uri, $params))
            return false;
        $text = urldecode($params[1]);
        addToLog('tts: broadcast: ' . $text, 3);
        $request->reply = '';
        return true;
    }

    // Raised at API call when a serial number identifying the rabbit is present in the API url
    // returns true if API call was handled by the Plugin, else returns false
    function OnApi(&$burrow, $parameters, &$reply) {
        if (!array_key_exists('tts', $parameters))
            return false;
        $this->SetData($burrow, 'tts', $parameters['tts']);
        $reply->Add('TTS', $parameters['tts']);
        if ($parameters['tts'] == 'x') {
            $this->ClearData($burrow, 'tts');
            $reply->Add('TTS', 'cancelled');
        } else {
            $media = new Plugin_tts_media($this, $parameters['tts']);
            $media->SendTo($burrow, $reply);
        }
        return true;
    }

}

?>
