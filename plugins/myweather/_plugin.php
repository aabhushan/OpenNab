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

class Plugin_myweather extends Plugin
{
	
	function Plugin_myweather()
	{
	}
		
	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$sky = $this->GetData($burrow,'sky');
		$temperature = $this->GetData($burrow,'temperature');
		if( $sky === false && $temperature===false )
			return false;
		if( $sky !== false ) {
			$ambientBlock = &$pingRequest->Ambient();
			$ambientBlock->SetServiceValue(1,$sky);
		}
		$messageBlock = &$pingRequest->Message();
		$text = $messageBlock->GetText();
		if( strpos( $text, 'broad/config/weather' ) === false )
			return false; // We only process weather messages
		if( $sky !== false )
			$text = preg_replace( '#/sky/([0-9]+).mp3#', '/sky/'.$sky.'.mp3', $text );
		if( $temperature !== false )
			$text = preg_replace( '#/temp/([0-9]+).mp3#', '/temp/'.$temperature.'.mp3', $text );
		$messageBlock->Encode( $text );
		addToLog( 'myweather: sending custom weather (sky='.$sky.',temperature='.$temperature.')', 3 );
		return true;
	}

	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
    $this->ProcessParameter($burrow,$parameters,$reply,'sky','Sky');
    $this->ProcessParameter($burrow,$parameters,$reply,'temperature','Temperature');
    if( $this->ProcessParameter($burrow,$parameters,$reply,'weatherurl','WeatherUrl') && $this->UpdateWeatherFromUrl($burrow) )
    {
			$reply->Add( 'Sky', $this->GetData($burrow,'sky') );
			$reply->Add( 'Temperature', $this->GetData($burrow,'temperature') );
    }
		return false;
	}

	function SelectedDay($timestamp)
	{
    return date('d',$timestamp+43200);
	}
  
	function DayData($html,$day)
	{
    if( !preg_match('#<div id="forecastRow">.*?</div>#s', $html, $forecastRow) )
      return false;
    if( !preg_match_all('#<td align="center" valign="top" class="Title">.*?([0-9]+)</td>#s', $forecastRow[0], $days) )
      return false;
    if( !preg_match_all('#<img src=.*?"http://www.hamweather.net/hw3/images/fcicons/(.*?).gif"#s', $forecastRow[0], $images) )
      return false;
    if( !preg_match_all('#<span  class="high">Hi: ([0-9]+)#s', $forecastRow[0], $temperatures) )
      return false;
    $index = array_search($day, $days[1]);
    $temperature = $temperatures[1][$index];
    $sky = $images[1][$index];
    $skies = array(
      'sunny' => 0,
      'sunnyn' => 0,
      'fair' => 0,
      'pcloudy' => 0,
      'pcloudyn' => 0,
      'mcloudy' => 1,
      'mcloudyn' => 1,
      'cloudy' => 1,
      'cloudyn' => 1,
      'mcloudy' => 1,
      'fog' => 2,
      'hazy' => 2,
      'smoke' => 2,
      'rain' => 3,
      'drizzle' => 3,
      'fdrizzle' => 3,
      'showers' => 3,
      'mcloudyr' => 3,
      'mcloudyrn' => 3,
      'mcloudynr' => 3,
      'pcloudyr' => 3,
      'pcloudyrn' => 3,
      'pcloudynr' => 3,
      'flurries' => 4,
      'snowshowers' => 4,
      'rainandsnow' => 4,
      'freezingrain' => 4,
      'blizzard' => 4,
      'blowingsnow' => 4,
      'snow' => 4,
      'mcloudys' => 4,
      'mcloudyns' => 4,
      'sleet' => 4,
      'tstorm' => 5,
      'tstormn' => 5,
      'wind' => 5,
      'wswarning' => 5,
      'wswatch' => 5,
      'chancetstorm' => 5,
      'chancetstormn' => 5
    );
    return array( 'sky' => $skies[$sky], 'temperature' => $temperature );
	}

	function UpdateWeatherFromUrl(&$burrow)
	{
		$weatherurl = $this->GetData($burrow,'weatherurl');
		if( $weatherurl === false )
			return false;
    $selectedDay = $this->SelectedDay(time());
    $weatherdata = Request::FileGetContents($weatherurl);
    $weather = $this->DayData($weatherdata,$selectedDay);
    if( !weather ) {
      addToLog( 'myweather: failed to get weather from url', 3 );
      return false;
    }
		addToLog( 'myweather: get weather from url (sky='.$weather['sky'].',temperature='.$weather['temperature'].')', 3 );
    $this->SetData($burrow,'temperature',$weather['temperature']);
    $this->SetData($burrow,'sky',$weather['sky']);
    return true;
  }

	// Raised at cron interval
	function OnCron(&$burrow,&$request)
	{
    $this->UpdateWeatherFromUrl($burrow);
	}
}
?>
