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

require_once('request.php');
require_once('block.php');
require_once('ambientblock.php');
require_once('messageblock.php');

class Ping extends Request
{
	var $packetHead = '7f', $packetTail = 'ff0a', $blocks, $blockIndexes, $sd, $tc;
	
	function Ping($host,$uri)
	{
		$this->Request($host,$uri);
		$this->blocks = array();
		$this->blockIndexes = array();
		$this->sd = $_REQUEST['sd'];
		$this->tc = hexdec($_REQUEST['tc']);
	}

	function NumberOfBlocks()
	{
		return count($this->blocks);
	}

	function EnhanceBlock(&$block)
	{
		if( $block->type == '0a' )
			return new MessageBlock($block->data);
		else if( $block->type == '04' )
			return new AmbientBlock($block->data);
		else
			return $block;
	}

	function UnpackBlocks()
	{
		$this->blocks = array();
		$this->blockIndexes = array();
		$this->packetHead = implode( '', unpack( 'H2', $this->reply ) );
		$remainder = substr( $this->reply, 1 );
		for(;;) {
			if( strlen($remainder) < 4 )
				break;
			$block = new Block();
			$remainder = $block->Unpack( $remainder );
			$block = &$this->EnhanceBlock($block);
			$this->blockIndexes[$block->type] = $this->NumberOfBlocks();
			$this->blocks[] = $block;
		}
		$this->packetTail = implode( '', unpack( 'H*', $remainder ) );
	}

	function &Block($type)
	{
		if( isset($this->blockIndexes[$type]) )
			return $this->blocks[$this->blockIndexes[$type]];
		$b = new Block($type);
		$b = &$this->EnhanceBlock($b);
		$this->blockIndexes[$type] = $this->NumberOfBlocks();
		$this->blocks[] = &$b;
		return $b;
	}

	function &Ambient()
	{
		$b = &$this->Block('04');
		return $b;
	}

	function &Message()
	{
		$b = &$this->Block('0a');
		return $b;
	}

	function Reboot()
	{
		$this->blocks = array();
		$b = &$this->Block('09');
	}

	function SetPingInterval($seconds)
	{
		$b = &$this->Block('03');
		$b->data = sprintf('%02x',$seconds);
	}

	function GetPingInterval()
	{
		$b = &$this->Block('03');
		return hexdec($b->data);
	}

	function PackBlocks()
	{
		$this->reply = pack( 'H*', $this->packetHead );
		foreach( $this->blocks as $block ) {
			if( $block->Size() == 0 && $this->NumberOfBlocks() > 1 )
				continue;
			$this->reply .= $block->Pack();
		}
		$this->reply .= pack( 'H*', $this->packetTail );
	}

	function Generate()
	{
		if( $this->GetCurrentID() == 0 )
			$this->reply = pack( 'H*', '7f0a00000f011e5b4fe10c6bce3ba71289b3cec5040000177fffffff000000000000000000000000000000000000000300000178ff0a' );
		else
			$this->reply = pack( 'H*', '7f040000177fffffff000000000000000000000000000000000000000300000178ff0a' );
	}

	function RawLog()
	{
		addToLog( $data.' '.$this->reply.' '.implode( '', unpack( 'H*', $this->reply ) ), 2 );
	}

	function Log()
	{
		$data = $this->uri.' '.$this->packetHead;
		foreach( $this->blocks as $block ) {
			$data .= ' '.$block->ToString();
		}
		addToLog( $data.' '.$this->packetTail, 2 );
	}

	//================================================
	
	function GetCurrentID() {
		return $this->tc;
	}
	
	function IsTimeout() {
		return $this->sd == 0;
	}
	
	function IsSingleClick() {
		return $this->sd == 3;
	}
	
	function IsDoubleClick() {
		return $this->sd == 1;
	}
	
	function IsSingleClickWhilePlayingMessage() {
		return $this->sd == 5;
	}
	
	function IsEndOfMessage() {
		return $this->sd == 2;
	}
	
	function IsEarsMove() {
		return $this->sd[0] == '8';
	}
	
	function GetEarMove($earId) { // right ear id = 0 / left ear id = 1
		return hexdec($this->sd[2*$earId+1]);
	}

}

?>
