<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag� (http://www.nabaztag.com/) electronic pet.
Copyright � 2007 OpenNab team - http://sourceforge.net/project/memberlist.php?group_id=187057

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

class Plugin_quietears extends Plugin
{
	
	function Plugin_quietears()
	{
	}
	
	// Raised at Rabbit startup (when bc.jsp is called)
	function OnBoot(&$burrow,&$request)
	{
            
		if ( $request->bcversion != 65806 ) {
			addToLog( 'Unable to patch quietears, wrong bc version :' . $request->bcversion  ,3);
			return;
		}
		$request->reply[0x8AA0] = chr(0x13);
		$request->reply[0x8BAA] = chr(0x13);
		addToLog( 'patched for quiet ears', 3 );
	}

}

?>
