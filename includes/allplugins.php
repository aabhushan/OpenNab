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

class AllPlugins extends Plugin
{
	var $plugins;
	
	function AllPlugins()
	{
		global $openNabSettings;
		$this->plugins = array();
		foreach( $openNabSettings['Plugin'] as $pluginName => $pluginSettings ) {
			if( @$pluginSettings['Disabled'] )
				continue;
			require_once(OPENNAB_PLUGINS_DIR.'/'.$pluginName.'/_plugin.php');
			$classname = 'Plugin_'.$pluginName;
			$plugin = new $classname();
			$plugin->cronTab = @$pluginSettings['CronTab'];
			$this->plugins[] = $plugin;
		}
	}

	function OnBoot(&$burrow,&$request)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->IsActive($burrow,'OnBoot') )
				$plugin->OnBoot($burrow,$request);
		}
	}

	function OnPingForward(&$burrow,&$request)
	{
		$detailedCallbacks = array( 0=>'OnTimeout', 3=>'OnSingleClick', 1=>'OnDoubleClick', 5=>'OnSingleClickWhilePlayingMessage', 2=>'OnEndOfMessage', 8=>'OnEarsMove' );
		$currentCallback = $detailedCallbacks[$request->sd[0]];
		foreach( $this->plugins as $plugin ) {
			if( $plugin->IsActive($burrow,$currentCallback) )
				if( $plugin->$currentCallback( $burrow, $request ) )
					return true;
			if( $plugin->IsActive($burrow,'OnPingForward') )
				if( $plugin->OnPingForward($burrow,$request) )
					return true;
		}
		return false;
	}

	function OnPingReadBefore(&$burrow,&$request)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->IsActive($burrow,'OnPingReadBefore') )
				$plugin->OnPingReadBefore($burrow,$request);
		}
	}
	
	function OnPingWrite(&$burrow,&$request)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->IsActive($burrow,'OnPingWrite') )
				if( $plugin->OnPingWrite($burrow,$request) )
					return true;
		}
		return false;
	}
	
	function OnPingReadAfter(&$burrow,&$request)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->IsActive($burrow,'OnPingReadAfter') )
				$plugin->OnPingReadAfter($burrow,$request);
		}
	}
	
	function OnRecord(&$burrow,&$request)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->IsActive($burrow,'OnRecord') )
				if( $plugin->OnRecord($burrow,$request) )
					return true;
		}
		return false;
	}
	
	function OnBroadcast(&$request)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->OnBroadcast($request) )
				return true;
		}
		return false;
	}
	
	function OnApi(&$burrow,$parameters,&$reply)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->IsActive($burrow,'OnApi') )
				if( $plugin->OnApi($burrow,$parameters,$reply) )
					return true;
		}
		return false;
	}
	
	function OnGlobalApi($parameters,&$reply)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->OnGlobalApi($parameters,$reply) )
				return true;
		}
		return false;
	}
	
	function OnCron(&$burrow,&$request)
	{
		$analyzer = new CronAnalyzer(time());
		foreach( $this->plugins as $plugin ) {
			$plugin->ExecuteCron($analyzer,$burrow,$request);
		}
		return false;
	}
	
	function OnRfid(&$burrow,&$request)
	{
		foreach( $this->plugins as $plugin ) {
			if( $plugin->IsActive($burrow,'OnRfid') )
				if( $plugin->OnRfid($burrow,$request) )
					return true;
		}
		return false;
	}

}

?>
