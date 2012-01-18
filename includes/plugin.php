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
define('PLUGIN_STATE_KEY','_state');
define('PLUGIN_ACTIVE_CALLBACKS_KEY','_activeCallbacks');
define('PLUGIN_GLOBAL_DATA','_global');

// Plugins base class
class Plugin
{
	var $cronTab;
	
	function Plugin()
	{
	}
	
	function GetName()
	{
		// Get the name of the plugin : class name except the 7 first chars (which are 'Plugin_')
		return substr(get_class($this),7);
	}
	
	// Get a full filepath for files in the plugin personal folder  <OpenNab root>/vl/plugins/<Plugin name>/files_<Plugin name>/
	function GetFilePath($filename)
	{
		return OPENNAB_PLUGINS_DIR.'/'.$this->GetName().'/files/'.$filename;
	}

	// Get a data from the current set of .ini files
	function GetConfigurationValue($key)
	{
		global $openNabSettings;
		return @$openNabSettings['Plugin'][$this->GetName()][$key];
	}
	
	// Get all global (cross-plugin) data (as an array) for the given burrow
	function GetAllGlobalData(&$burrow)
	{
		return $burrow->GetAllPluginData(PLUGIN_GLOBAL_DATA);
	}
	
	// Get a global (cross-plugin) data for the given burrow
	function GetGlobalData(&$burrow,$key)
	{
		return $burrow->GetPluginData(PLUGIN_GLOBAL_DATA,$key);
	}
	
	// Set a global (cross-plugin) data for the given burrow
	function SetGlobalData(&$burrow,$key,$value)
	{
		$burrow->SetPluginData(PLUGIN_GLOBAL_DATA,$key,$value);
	}
	
	// Clear a global (cross-plugin) data for the given burrow
	function ClearGlobalData(&$burrow,$key)
	{
		$burrow->ClearPluginData(PLUGIN_GLOBAL_DATA,$key);
	}
  
	// Get all data (as an array) specific to the plugin for the given burrow
	function GetAllData(&$burrow)
	{
		return $burrow->GetAllPluginData($this->GetName());
	}
	
	// Get a data specific to the plugin for the given burrow
	function GetData(&$burrow,$key)
	{
		return $burrow->GetPluginData($this->GetName(),$key);
	}
	
	// Set a data specific to the plugin for the given burrow
	function SetData(&$burrow,$key,$value)
	{
		$burrow->SetPluginData($this->GetName(),$key,$value);
	}
	
	// Clear a data specific to the plugin for the given burrow
	function ClearData(&$burrow,$key)
	{
		$burrow->ClearPluginData($this->GetName(),$key);
	}
	
	// Get current plugin state for the given burrow
	function GetState(&$burrow)
	{
		return $this->GetData($burrow,PLUGIN_STATE_KEY);
	}
	
	// Set plugin state for the given burrow
	function SetState(&$burrow,$value)
	{
		addToLog( $this->GetName().': set state ['.$value.']', 4 );
		$this->SetData($burrow,PLUGIN_STATE_KEY,$value);
		$this->SetData($burrow,PLUGIN_ACTIVE_CALLBACKS_KEY,$this->OnGetActiveCallbacks($burrow,$value));
	}
	
	// Put the plugin in default state (initial) for the given burrow
	function SetDefaultState(&$burrow)
	{
		addToLog( $this->GetName().': set default state', 4 );
		$this->ClearData($burrow,PLUGIN_STATE_KEY);
		$this->ClearData($burrow,PLUGIN_ACTIVE_CALLBACKS_KEY);
	}
	
	// Returns true if callback is active, else false
	function IsActive(&$burrow,$callbackName)
	{
		$activeCallbacks = $this->GetData($burrow,PLUGIN_ACTIVE_CALLBACKS_KEY);
		if( $activeCallbacks === false ) {
			$activeCallbacks = $this->OnGetActiveCallbacks($burrow,$this->GetState($burrow));
			$this->SetData($burrow,PLUGIN_ACTIVE_CALLBACKS_KEY,$activeCallbacks);
			if( $activeCallbacks === false )
				return true;
		}
		return in_array( $callbackName, $activeCallbacks );
	}
	
	function ExecuteCron(&$analyzer,&$burrow,&$request)
	{
		if( !$this->IsActive($burrow,'OnCron') )
			return;
		$cronTab = $this->GetData($burrow,'CronTab');
		if( !$cronTab )
			$cronTab = $this->cronTab;
		if( !$cronTab )
			return;
		addToLog( $this->GetName().': using crontab '.$cronTab, 4 );
		$oldStamp = $this->GetData($burrow,'CronStamp');
		$newStamp = $analyzer->LastStamp($cronTab);
		if( $oldStamp >= $newStamp )
			return;
		addToLog( $this->GetName().': executing cron at '.strftime( '%d/%m/%Y %H:%M:%S', $newStamp).', previous was at '.strftime( '%d/%m/%Y %H:%M:%S', $oldStamp), 4 );
		$this->OnCron($burrow,$request);
		$this->SetData($burrow,'CronStamp',$newStamp);
	}
	
	// Raised when plugin state is changed
	// returns false to activate all calbacks
	// returns a callback array to activate a subset of callbacks
	function OnGetActiveCallbacks(&$burrow,$state)
	{
		return false;
	}
	
	// Raised at Rabbit startup (when bc.jsp is called)
	function OnBoot(&$burrow,&$request)
	{
	}

	// Raised on timeout, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnTimeOut(&$burrow,&$pingRequest)
	{
			return false;
	}
	
	// Raised on single click on head, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnSingleClick(&$burrow,&$pingRequest)
	{
			return false;
	}

	// Raised on double click on head, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnDoubleClick(&$burrow,&$pingRequest)
	{
			return false;
	}

	// Raised when a message that was sent has finished, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnEndOfMessage(&$burrow,&$pingRequest)
	{
			return false;
	}

	// Raised when ears have moved, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnEarsMove(&$burrow,&$pingRequest)
	{
			return false;
	}

	// Raised on single click on head whilst playing a message, occurs at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnSingleClickWhilePlayingMessage(&$burrow,&$pingRequest)
	{
			return false;
	}
		
	// Raised at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	// This OnPingForward method is called if the current event was not handled by one of the OnTimeOut, OnSingleClick, OnDoubleClick, OnEndOfMessage, OnEarsMove or OnSingleClickWhilePlayingMessage methods.
	function OnPingForward(&$burrow,&$pingRequest)
	{
		return false;
	}

	// Raised at Rabbit ping (when p4.jsp is called) before any reply update due to the ping
	function OnPingReadBefore(&$burrow,&$pingRequest)
	{
	}

	// Raised at Rabbit ping (when p4.jsp is called) to define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
	}
	
	// Raised at Rabbit ping (when p4.jsp is called) after all reply updates due to the ping
	function OnPingReadAfter(&$burrow,&$pingRequest)
	{
	}
	
	// Raised at Rabbit voice recording (when record.jsp is called)
	// returns true if record operation is handled by the plugin
	// returns false if record operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnRecord(&$burrow,&$request)
	{
		return false;
	}
	
	// Raised at Rabbit broadcasted file request
	// returns true if broadcast operation is handled by the plugin
	// returns false if broadcast operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnBroadcast(&$request)
	{
		return false;
	}
	
  // Utility function to process parameters which are activated by giving a value and deactivated by giving a blank value
	function ProcessParameter(&$burrow,$parameters,&$reply,$paramid,$paramname)
	{
		if( array_key_exists($paramid,$parameters) ) {
			$value = $parameters[$paramid];
			if( $value == '' ) {
				$this->ClearData($burrow,$paramid);
				$reply->Add( $paramname, 'Default' );
			} else {
				$this->SetData($burrow,$paramid,$value);
				$reply->Add( $paramname, $value );
			}
      return true;
		}
    return false;
	}

	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		return false;
	}
	
	// Raised at API call when no serial number is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnGlobalApi($parameters,&$reply)
	{
		return false;
	}
	
	// Raised at cron interval
	function OnCron(&$burrow,&$request)
	{
	}

	// Raised at Rabbit rfid detection (when rfid.jsp is called)
	// returns true if rfid operation is handled by the plugin
	// returns false if rfid operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnRfid(&$burrow,&$request)
	{
		return false;
	}
}

?>
