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

class ApiReply
{
	var $fields = array(), $serialNumber, $hasSerialNumber, $cancel;
	
	function ApiReply($serialNumber) {
		$this->serialNumber = $serialNumber;
		$this->hasSerialNumber = ($serialNumber != '');
		$this->cancel = false;
	}
	
	function Add( $fieldName, $fieldValue ) {
    if( isset($this->fields[$fieldName]) ) {
      if( is_array($this->fields[$fieldName]) ) {
        array_push( $this->fields[$fieldName], $fieldValue );
      } else {
        $this->fields[$fieldName] = array( $this->fields[$fieldName], $fieldValue );
      }
    } else {
      $this->fields[$fieldName] = $fieldValue;
    }
	}
	
	function Cancel() {
    $this->cancel = true;
	}
	
	function Execute() {
    if( $this->cancel )
      return;
		header('Content-type: text/xml');
		print $this->Xml();
	}
	
	function Xml() {
		$cr = "\r\n";
		$txt = '<OpenNab>'.$cr;
		if( count($this->fields) == 0 )
			$txt .= '  <Error>Unknown api query</Error>'.$cr;
		foreach( $this->fields as $name => $value ) {
      if( is_array($value) ) {
        foreach( $value as $subvalue )
          $txt .= '  <'.$name.'>'.$subvalue.'</'.$name.'>'.$cr;
      } else {
        $txt .= '  <'.$name.'>'.$value.'</'.$name.'>'.$cr;
      }
		}
		$txt .= '</OpenNab>';
		return $txt;
	}
}


?>
