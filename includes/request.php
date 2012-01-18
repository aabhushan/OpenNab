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

class Request
{
	var $host, $server, $port, $uri, $type, $reply, $bcversion, $tagid, $referer, $sentHeaders, $returnedHeaders;
	
	function Request($host,$uri,$type='GET')
        {
                addToLog( "BC VERSION:" . $_REQUEST['v']);
                addToLog( "Tag ID:" . $_REQUEST['t']);
                
		$this->host = $host;
    list($this->server, $this->port)= split(':', $host);
    $this->port = getValueOrDefault( $this->port, 80 );
		$this->uri = $uri;
		$this->type = $type;
		$this->bcversion = @$_REQUEST['v'];
		$this->tagid = @$_REQUEST['t'];
		
		// set http headers incoming from Nabaztag
		$this->sentHeaders = array();
		foreach( $_SERVER as $key => $value )
		{
			if( $key != 'HTTP_HOST' && substr($key,0,5) == 'HTTP_' )
			{
				$keyWords = explode('_',substr($key,5));
				array_walk($keyWords, 'headerCase');
				$key = implode('-',$keyWords);
				$this->sentHeaders[] = array( $key, $value );
				addToLog( 'Client header : '.$key.' = '.$value, 4 );
			}
		}
  }
	
	function FileGetContents($filepath)
	{
    $urlItems = parse_url($filepath);
    $host = $urlItems['host'];
    if( isset($urlItems['port']) )
      $host .= ':'.$urlItems['port'];
    $path = substr($filepath,strlen($host)+7);
    $r = new Request($host,$path);
    $r->Forward();
    return $r->reply;
  }
	
	function Forward()
	{
		$reply_code = HTTP_STATUS_NOT_IMPLEMENTED;
		$http_client = new http( HTTP_V10, false);
		$url = 'http://'.$this->host.':'.$this->port.$this->uri;
    foreach( $this->sentHeaders as $header )
      $http_client->set_request_header($header[0], $header[1]);
		$referer = getValueOrDefault($this->referer,'');
		$http_client->host = $this->server;
		$http_client->port = $this->port;

		if( defined('OPENNAB_CONNEXION_PROXY_ADDR') )
			$http_client->use_proxy(OPENNAB_CONNEXION_PROXY_ADDR, OPENNAB_CONNEXION_PROXY_PORT, OPENNAB_CONNEXION_PROXY_USER, OPENNAB_CONNEXION_PROXY_PASS);

		addToLog( $this->type.' '.$url, 4 );
		if( $this->type == 'POST' ) {
			if( isset($GLOBALS['HTTP_RAW_POST_DATA']) )
				$contents = $GLOBALS['HTTP_RAW_POST_DATA'];
			else
				$contents = file_get_contents('php://input');
			$reply_code = @$http_client->raw_post($this->uri,$contents,true,$referer);
		} else {
			$reply_code = @$http_client->get($this->uri,true,$referer);
		}
    
		if( $reply_code != HTTP_STATUS_OK ) {
      $requestLogLevel = 1;
			$this->Generate();
		} else {
      $requestLogLevel = 4;
			$this->reply = $http_client->get_response_body();
    }

		if( LOG_LEVEL >= $requestLogLevel ) {
  		addToLog( 'HTTP status : '.$reply_code.' for '.$url, $requestLogLevel );
			foreach ( $http_client->_response->_headers as $name => $value)
        addToLog( '*** Header: '.$name.': '.$value, $requestLogLevel );
      $trunkAt = 200;
      $replySize = strlen($this->reply);
      addToLog( '*** Reply ('.min($trunkAt,$replySize).'/'.$replySize.'): '.substr($this->reply,0,$trunkAt), $requestLogLevel );
    }
		
		// close the connection
		unset( $http_client );
	}

	function Generate()
	{
		$this->reply = '';
	}

	function Log()
	{
		addToLog( $this->uri, 2 );
	}

	function ReplyToNabaztag()
	{
		print $this->reply;
	}
}	

?>
