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

require_once('misc.php');

class HandcraftedPostRequest
{
	var $cr = "\r\n", $header, $host, $uri, $connectTimeout = 30, $status, $replyRows, $reply;
	
	function HandcraftedPostRequest($host,$uri)
	{
		$this->host = $host;
		$this->uri = $uri;
	}
	
	function Forward()
	{
		if( isset($GLOBALS['HTTP_RAW_POST_DATA']) )
			$contents = $GLOBALS['HTTP_RAW_POST_DATA'];
		else
			$contents = file_get_contents('php://input');

		$header = 'POST '.$this->uri.' HTTP/1.0'.$this->cr;
		$header .= 'User-Agent: MTL'.$this->cr;
		$header .= 'Pragma: no-cache'.$this->cr;
		$header .= 'Icy-MetaData:1'.$this->cr;
		$header .= 'Host: '.$this->host.$this->cr;
		$header .= 'Content-length: '.strlen($contents).$this->cr.$this->cr;

		// Open a socket to main server
		$sock = @fsockopen($this->host, 80, $errno, $errstr, $this->connectTimeout);
		if( !$sock )
			return false;
		
		// Send HTTP header & contents
		$sent = fwrite($sock, $header);
		$sent = $sent && fwrite($sock, $contents);
		
		// Read the server reply
		while (!feof($sock)) {
				$this->reply .= fgets($sock, 1024);
		}
		fclose($sock);
		if( !$sent )
			return false;
			
		// Parse the reply header and content
		$this->replyRows = explode($this->cr,$this->reply);
		return true;
	}

	function ReplyHeadersToNabaztag()
	{
		if(VISUAL_DEBUG)
			return;
    if( !$this->replyRows )
      return;
		foreach( $this->replyRows as $headerRow )
			header($headerRow);
	}

	function Log()
	{
		addToLog($this->uri,2);
	}

	function ReplyToNabaztag()
	{
		$this->ReplyHeadersToNabaztag();
	}

}	

?>
