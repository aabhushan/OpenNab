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

class Plugin_saveboot extends Plugin
{
	
	function Plugin_saveboot()
	{
	}
	
	// Raised at Rabbit startup (when bc.jsp is called)
	function OnBoot(&$burrow,&$request)
	{
		// save the rabbit code (maybe for future use : violet server down...)
		$filepath = $this->GetFilePath('bootcode_'.$request->bcversion.'.bin');
		if( file_exists($filepath) )
			return;
		fileWrite( $filepath, $request->reply );
		addToLog( 'Saved bootcode to file '.$filepath, 3 );
	}

}

?>
