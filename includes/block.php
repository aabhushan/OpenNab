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

class Block
{
	var $type, $binary, $data = '', $enhanced = false;
	
	function Block($type='00')
	{
		$this->type = $type;
	}

	function Unpack($bytes)
	{
		$infos = unpack( 'H2type/H6size', $bytes );
		$this->type = $infos['type'];
		$size = hexdec($infos['size']);
		$this->binary = substr( $bytes, 4, $size );
		$this->data = implode( '', unpack( 'H*', $this->binary) );
		$remainder = substr( $bytes, 4+$size );
		return $remainder;
	}

	function Size()
	{
		return strlen($this->data) / 2;
	}

	function Pack()
	{
		$pack = pack( 'H*', $this->type );
		$pack .= pack( 'H*', sprintf('%06x',$this->Size()) );
		if( strlen($this->data) <= 0 )
			return $pack;
		$this->binary = pack( 'H*', $this->data );
		$pack .= $this->binary;
		return $pack;
	}

	function ToString()
	{
		return '['.$this->type.','.$this->Size().']'.$this->data;
	}
}

?>
