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

define('ID_DEFAULT',2147483647);
define('ID_DEFAULT_MESSAGE',"ID ".ID_DEFAULT."\n");

define('ID_ASLEEP',2147483646);
define('ID_ASLEEP_MESSAGE',"ID ".ID_ASLEEP."\n");

class MessageBlock extends Block
{	
	var $text = false, $invtable = array( 1, 171, 205, 183, 57, 163, 197, 239, 241, 27, 61, 167, 41, 19, 53, 223, 225, 139, 173, 151, 25, 131, 165, 207, 209, 251, 29, 135, 9, 243, 21, 191, 193, 107, 141, 119, 249, 99, 133, 175, 177, 219, 253, 103, 233, 211, 245, 159, 161, 75, 109, 87, 217, 67, 101, 143, 145, 187, 221, 71, 201, 179, 213, 127, 129, 43, 77, 55, 185, 35, 69, 111, 113, 155, 189, 39, 169, 147, 181, 95, 97,11, 45, 23, 153, 3, 37, 79, 81, 123, 157, 7, 137, 115, 149, 63, 65, 235, 13, 247, 121, 227, 5, 47, 49, 91, 125, 231, 105, 83, 117, 31, 33, 203, 237, 215, 89, 195, 229, 15, 17, 59, 93, 199, 73, 51, 85, 255);
			
	function MessageBlock($data)
	{
		$this->Block('0a');
		$this->data = $data;
		$this->binary = pack( 'H*', $this->data );
		$this->enhanced = 'Message';
	}
	
	function GetText() {
		if( !$this->text )
			$this->DecodeText();
		return $this->text;
	}
	
	function DecodeText() {
		// Deobfuscating algorithm by Sache
		$this->text = '';
		$currentChar = 35;
		for($i=1;$i<strlen($this->binary);$i++) {
			$code = ord($this->binary[$i]);
			$currentChar = (($code-47)*(1+2*$currentChar))%256;
			$this->text .= chr($currentChar);
		}
	}

	function Encode($text) {
		if( strlen($text) == 0 ) {
			$this->text = '';
			$this->binary = '';
			$this->data = '';
			return;
		}
		// Obfuscating algorithm by Sache
		$this->text = $text;
		$this->binary = chr(1);
		$previousChar = 35;
		for($i=0;$i<strlen($text);$i++) {
			$currentChar = ord($text[$i]);
			$code = ($this->invtable[$previousChar % 128]*$currentChar+47) % 256;
			$previousChar = $currentChar;
			$this->binary .= chr($code);
		}
		$this->data = implode( '', unpack( 'H*', $this->binary) );
	}

	function GetID()
	{
		$txt = $this->GetText();
		if( preg_match( "#^ID ([0-9]+)\n#", $txt, $matches ) )
			return $matches[1];
		else
			return false;
	}

	function ToString()
	{
		if( $this->Size() <= 0 )
			return '';
		return '['.$this->type.','.$this->Size().']'.str_replace( "\n", ";", $this->GetText() );
	}
}

?>