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

class Plugin_rfid2http extends Plugin
{
	function Plugin_rfid2http()
	{
	}

	function MatchingUrl($settings,$serialNumber,$tagid)
	{
    $separator = substr($settings,0,1);
    if( !preg_match('¶'.$separator.'(.*?)'.$separator.'(.*?)'.$separator.'(.*)¶',$settings,$patternMatches) )
      return false;
    if( !preg_match('¶^'.$patternMatches[1].'$¶',$serialNumber) )
      return false;
    if( !preg_match('¶^'.$patternMatches[2].'$¶',$tagid) )
      return false;
    return str_replace( '$2', $tagid, str_replace( '$1', $serialNumber, $patternMatches[3] ) );
	}

	// Raised at Rabbit rfid detection (when rfid.jsp is called)
	// returns true if rfid operation is handled by the plugin
	// returns false if rfid operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnRfid(&$burrow,&$request)
	{
    for($i=1;;$i++) {
      $urlSettings = $this->GetConfigurationValue('Url'.$i);
      if( !$urlSettings )
        return false;
      $matchingUrl = $this->MatchingUrl($urlSettings,$burrow->sn,$request->tagid);
      addToLog( 'rfid2http: checking tag id '.$request->tagid.' and serial number '.$burrow->sn.' against '.$urlSettings.' => '.$matchingUrl, 3 );
      if( $matchingUrl )
        break;
    }
    $burrow->AfterSave( array('Plugin_rfid2http','AfterBurrowSave'), $request->tagid, $matchingUrl );
		return false;
	}

	function AfterBurrowSave($tagid,$url)
	{
    Request::FileGetContents($url);
		addToLog( 'rfid2http: detected tag id '.$tagid.' and called '.$url, 3 );
	}
}
?>
