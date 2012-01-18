<?php

/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag™ (http://www.nabaztag.com/) electronic pet.
Copyright © 2007 OpenNab team - http://sourceforge.net/project/memberlist.php?group_id=187057

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

require_once('request.php');

class Boot extends Request
{
	function Boot($host,$uri)
	{
		$this->Request($host,$uri);
	}
	
	function ComputeVersion()
	{
		$this->bcversion=ord($this->reply[175])/2+128*(ord($this->reply[176])+256*(ord($this->reply[177])+256*ord($this->reply[178])));
		addToLog('version number is '.$this->bcversion , 4);
	}
	
	function Forward()
	{
		Request::Forward();
		$this->ComputeVersion();
	}

	function Generate()
	{
		$this->reply = file_get_contents(OPENNAB_BASE_DIR.OPENNAB_CODE);
		$this->ComputeVersion();
	}

	function ReplyToNabaztag()
	{
		// hack the rabbit code so that it connects to php locate
		$this->reply = str_replace( '/locate.jsp?', '/locate.php?', $this->reply );

		// hack other calls only if needed
		global $theServer;
		$theServer->HackBootcode($this);

		Request::ReplyToNabaztag();
	}
}

?>