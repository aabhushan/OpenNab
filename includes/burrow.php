<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag� (http://www.nabaztag.com/) electronic pet.
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

class BurrowData
{
	var $plugins;
	
	function BurrowData()
	{
		$this->plugins = array();
	}
}

class AfterSave
{
  var $callee, $args;
  
  function AfterSave($callee, $args)
  {
    $this->callee = $callee;
    $this->args = $args;
    array_shift($this->args);
  }
  
	function Call()
	{
    call_user_func_array($this->callee,$this->args);
	}
}

class Burrow
{
	var $data, $fileName, $sn, $afterSaves;
	
	function Burrow($serialNumber)
	{
		$this->data = new BurrowData();
		$this->sn = strtolower($serialNumber);
		$this->fileName = OPENNAB_BASE_DIR.'/vl/burrows/'.$this->sn;
		$this->afterSaves = array();
	}

	function GetSerialNumber()
	{
		return $this->sn;
	}

	function Load()
	{
		if( is_file($this->fileName) ) {
			$previousData = unserialize(file_get_contents($this->fileName));
			foreach( get_object_vars($previousData) as $name => $value ) {
				$this->data->$name = $value;
			}
		}
	}

  // to be used by plugins which need reeantrant calls on API : calls in AfterSave will be executed *after* the burrow data for current API call is complete
	function AfterSave($callee)
	{
    $args = func_get_args();
    $this->afterSaves[] = new AfterSave($callee,$args);
	}

	function Save()
	{
		fileWrite($this->fileName,serialize($this->data));
    foreach( $this->afterSaves as $afterSave )
      $afterSave->Call();
	}

	function GetAllPluginData($pluginName)
	{
		if( !array_key_exists($pluginName,$this->data->plugins) )
			return array();
		return $this->data->plugins[$pluginName];
	}

	function GetPluginData($pluginName,$key)
	{
		if( !array_key_exists($pluginName,$this->data->plugins) )
			return false;
		if( !array_key_exists($key,$this->data->plugins[$pluginName]) )
			return false;
		return $this->data->plugins[$pluginName][$key];
	}

	function SetPluginData($pluginName,$key,$value)
	{
		if( !array_key_exists($pluginName,$this->data->plugins) )
			$this->data->plugins[$pluginName] = array();
		$this->data->plugins[$pluginName][$key] = $value;
	}

	function ClearPluginData($pluginName,$key)
	{
		if( !array_key_exists($pluginName,$this->data->plugins) )
			return;
		unset($this->data->plugins[$pluginName][$key]);
	}

}

?>
