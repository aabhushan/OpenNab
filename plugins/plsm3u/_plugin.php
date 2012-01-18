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

class Plugin_plsm3u extends Plugin
{
	
	function Plugin_plsm3u()
	{
	}
		
	function GetContents($url)
	{
		return Request::FileGetContents($url);
	}
		
	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$messageBlock = &$pingRequest->Message();
		if( $messageBlock->Size() == 0 )
			return false;
		$instructions = explode( "\n", $messageBlock->GetText() );
		foreach( $instructions as $key => $instruction ) {
			if( !preg_match( "#(.*)(http:.*(pls|m3u))#", $instruction, $matches ) )
				continue;
			$oldUrl = $matches[2];
			$contents = $this->GetContents($oldUrl);
			if( !preg_match( "#.*(http:.*)$#m", $contents, $matchContents ) )
				continue;
			$newUrl = $matchContents[1];
			$instructions[$key] = $matches[1].$newUrl;
			addToLog( 'plsm3u: replaced '.$oldUrl.' by '.$newUrl, 3 );
		}
		$messageBlock->Encode( implode( "\n", $instructions ) );
		return false;
	}

}
?>
