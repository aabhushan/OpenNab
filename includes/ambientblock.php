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

require_once('block.php');
define('RIGHT_EAR',0);
define('LEFT_EAR',1);

class AmbientBlock extends Block
{
	function AmbientBlock($data)
	{
		$this->Block('04');
		$this->data = $data;
		$this->enhanced = 'Ambient';
	}
	
	function GetServicePosition($serviceId, $allocate=false) {
		$idStr = sprintf('%02x',$serviceId);
		for($i=8; $i<=36; $i+=4) {
			if( substr($this->data,$i,2) == $idStr )
				return $i+2;
			if( $allocate && substr($this->data,$i,4) == '0000' ) {
				$this->data = substr($this->data,0,$i).$idStr.substr($this->data,$i+2);
				return $i+2;
			}
		}
		return false;
	}
	
	function GetServiceValue($serviceId) {
		$pos = $this->GetServicePosition($serviceId,false);
		if( $pos === false )
			return false;
		return $this->GetValue($pos);
	}
	
	function SetServiceValue($serviceId,$value) {
		$pos = $this->GetServicePosition($serviceId,true);
		if( $pos === false )
			return false;
		addToLog( 'Override ambient '.$serviceId.' to '.$value, 4 );
		return $this->SetValue($pos,$value);
	}

	function GetEarValue($earId) { // right ear id = 0 / left ear id = 1
		return $this->GetValue(40 + 2*$earId);
	}
	
	function SetEarValue($earId,$value) {
		addToLog( 'Override ear '.$earId.' to '.$value, 4 );
		return $this->SetValue(40 + 2*$earId,$value);
	}

	function GetNoseValue() { // 0 = no blinking / 1 = single blinking / 2 = double blinking
		return strlen($this->data)/2 - 23;
	}
	
	function SetNoseValue($value) {
		addToLog( 'Override nose to '.$value, 4 );
		$this->data = substr($this->data,0,44).str_repeat('05',$value).'00';
		return true;
	}

	function GetValue($pos) {
		return hexdec(substr($this->data,$pos,2));
	}
	
	function SetValue($pos,$value) {
		$this->data = substr($this->data,0,$pos).sprintf('%02x',$value).substr($this->data,$pos+2);
		return true;
	}
}

?>
