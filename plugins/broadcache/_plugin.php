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

class Plugin_broadcache extends Plugin
{
	var $topfolder = '/broad';
  
	function Plugin_broadcache()
	{
	}
	
	// Raised at Rabbit broadcasted file request
	// returns true if broadcast operation is handled by the plugin
	// returns false if broadcast operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnBroadcast(&$request)
	{
    if( !preg_match('¶'.$this->topfolder.$this->GetConfigurationValue('Pattern').'¶', $request->uri) )
      return false;
		$cacheFile = new File($this->GetFilePath($request->uri));
    if( $cacheFile->Exists() ) {
      $request->reply = $cacheFile->Read();
      return true;
    }
    global $theServer;
    $theServer->Forward($request);
    if( strlen($request->reply) > 0 ) {
      $cacheFile->Write($request->reply);
  		addToLog( 'broadcache: cached '.$request->uri, 3 );
    }
    return true;
	}

  function ScanDirectories($basepath)
  {
  }
  
	// Raised at API call when no serial number is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnGlobalApi($parameters,&$reply)
	{
		if( array_key_exists('listbroadcache',$parameters) ) {
      $basepath = $this->GetFilePath($this->topfolder);
      $offset = strlen($basepath)-strlen($this->topfolder);
      $files = File::ScanFolder($basepath);
      if( $files ) {
        $reply->Add( 'CachedFilesCount', count($files) );
        foreach( $files as $filename )
          $reply->Add( 'CachedFile', substr($filename,$offset) );
      } else {
        $reply->Add( 'CachedFilesCount', 0 );
      }
			return true;
    }
		if( array_key_exists('clearbroadcache',$parameters) ) {
      File::DelFolder($this->GetFilePath($this->topfolder));
      $reply->Add( 'CacheCleared', '' );
			return true;
    }
		return false;
	}
}

?>
