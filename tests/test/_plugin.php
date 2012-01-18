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

class Plugin_test extends Plugin
{
	
	function Plugin_test()
	{
	}

	function OnGetActiveCallbacks(&$burrow,$state)
	{
		if( $state == 'somestate' )
			return array( 'OnApi' );
		else
			return false;
	}
	
	function OnBoot(&$burrow,&$request)	{
		$this->SetData($burrow,'last','OnBoot'.$this->GetData($burrow,'p').$request->val);
	}

	function OnSingleClick(&$burrow,&$pingRequest)	{
		$this->SetData($burrow,'last','OnSingleClick'.$this->GetData($burrow,'p').$pingRequest->val);
		return true;
	}

	function OnPingForward(&$burrow,&$pingRequest)	{
		$this->SetData($burrow,'last','OnPingForward'.$this->GetData($burrow,'p').$pingRequest->val);
		return true;
	}

	function OnPingReadBefore(&$burrow,&$pingRequest)	{
		global $globalTest;
		$globalTest = 'OnPingReadBefore'.$this->GetData($burrow,'p').$pingRequest->val;
	}
	
	function OnPingWrite(&$burrow,&$pingRequest)	{
		$this->SetData($burrow,'last','OnPingWrite'.$this->GetData($burrow,'p').$pingRequest->val);
	}
	
	function OnPingReadAfter(&$burrow,&$pingRequest)	{
		global $globalTest;
		$globalTest = 'OnPingReadAfter'.$this->GetData($burrow,'p').$pingRequest->val;
	}
	
	function OnRecord(&$burrow,&$request) {
		$this->SetData($burrow,'last','OnRecord'.$this->GetData($burrow,'p').$request->val);
		return true;
	}
	
	function OnBroadcast(&$request) {
		$request->val = 'OnBroadcast'.$request->val;
		return true;
	}
	
	function OnApi(&$burrow,$parameters,&$reply) {
		$this->SetData($burrow,'last','OnApi'.$this->GetData($burrow,'p').$parameters.$reply->val);
		return true;
	}
	
	function OnGlobalApi($parameters,&$reply)	{
		global $globalTest;
		$globalTest = 'OnGlobalApi'.$parameters.$reply->val;
		return true;
	}

}

?>
