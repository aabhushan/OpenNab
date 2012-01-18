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

class CronChoices
{
	var $text, $items, $size, $follow;
	
	function CronChoices($text,$base) {
		$this->text = $text;
		if( $text == '*' )
			$text = $base;
		$this->items = array();
		foreach( explode(',',$text) as $v ) {
      $step = 1;
      $withsteps = explode('/',$v);
      if( count($withsteps) == 2 ) {
        $v = $withsteps[0];
        $step = $withsteps[1];
      }
			$bounds = explode('-',$v);
			if( count($bounds) == 2 ) {
				for($i=$bounds[0];$i<=$bounds[1];$i += $step)
					$this->items[] = $i;
			} else {
				$this->items[] = $v;
			}
		}
	}
	
	function findKey($reference) {
		for(;;) {
			if( $reference < $this->items[0] )
				return count($this->items)-1;
			$k = array_search($reference,$this->items);
			if( $k !== false )
				return $k;
			$reference--;
		}
	}
	
	function min() {
		return $this->items[0];
	}
	
	function max() {
		return $this->items[count($this->items)-1];
	}
	
	function selectLast($reference) {
		if( count($this->items) == 1 )
			return array($this->items[0],false);
		$k1 = $this->findKey($reference);
		if( $k1 == 0 )
			$k2 = count($this->items)-1;
		else
			$k2 = $k1-1;
		return array($this->items[$k1],$this->items[$k2]);
	}

}

class CronTime
{
	var $hours, $minutes;
	var $lastHour, $lastMinute;
	var $hourChoices, $minuteChoices;
	
	function CronTime($hourText,$minuteText)
	{
		$this->hourChoices =	new CronChoices($hourText,'0-23');
		$this->minuteChoices =	new CronChoices($minuteText,'0-59');
		$this->hour =	0;
		$this->minute =	0;
		$this->lastHour =	$this->hourChoices->max();
		$this->lastMinute =	$this->minuteChoices->max();
	}
	
	function set($refHour,$refMinute) {
		list($selectedHour,$previousHour) = $this->hourChoices->selectLast($refHour);
		list($selectedMinute,$previousMinute) = $this->minuteChoices->selectLast($refMinute);
		$this->hours = $selectedHour;
		$this->minutes = $selectedMinute;
		if( $selectedMinute > $refMinute && $previousHour !== false )
			$this->hours = $previousHour;
		if( $selectedHour < $refHour || $refHour < $this->hourChoices->min() ) {
			list($selectedMinute,$previousMinute) = $this->minuteChoices->selectLast(59);
			$this->minutes = $selectedMinute;
		}
	}
	
}

class CronDate
{
	var $days, $months, $years, $allDays, $resultStamp;
	
	function CronDate($allDays)
	{
		$this->allDays = $allDays;
		$this->days =	0;
		$this->months =	0;
		$this->years =	0;
		$this->resultStamp =	-1;
	}

	function IsAny() {
		return $this->allDays;
	}

	function set($refDay,$refMonth,$refYear) {
		$this->setResult($refDay,$refMonth,$refYear);
	}
	
	function setStampResult($stamp) {
		$this->setResult(	date('d',$stamp), date('m',$stamp), date('Y',$stamp) );
	}
	
	function setResult($day,$month,$year) {
		$this->days =	$day;
		$this->months =	$month;
		$this->years =	$year;
		$this->resultStamp = mktime(0,0,0,$month,$day,$year);
	}
}

class CronAbsoluteDate extends CronDate
{
	var $dayChoices, $monthChoices;
	
	function CronAbsoluteDate($dayText,$monthText)
	{
		$this->CronDate( $dayText == "*" && $monthText == "*" );
		$this->dayChoices =	new CronChoices($dayText,'1-31');
		$this->monthChoices =	new CronChoices($monthText,'1-12');
	}

	function set($refDay,$refMonth,$refYear) {

		list($selectedDay,$previousDay) = $this->dayChoices->selectLast($refDay);
		list($selectedMonth,$previousMonth) = $this->monthChoices->selectLast($refMonth);
		$selectedYear = $refYear;
		$previousYear = $refYear - 1;
		if( $selectedMonth == $refMonth && $selectedDay > $refDay )
			$selectedMonth =	$previousMonth;
		if( $selectedMonth > $refMonth )
			$selectedYear =	$previousYear;
		if( $selectedMonth < $refMonth || $refMonth < $this->monthChoices->min() ) {
			$numberOfDays = date('d',mktime(0,0,0,$selectedMonth+1,1,$selectedYear)-1);
			list($selectedDay,$previousDay) = $this->dayChoices->selectLast($numberOfDays);
		}
		$this->setResult(	$selectedDay, $selectedMonth, $selectedYear );
	}
}

class CronDayOfWeek extends CronDate
{
	var $dowChoices;
	
	function CronDayOfWeek($dayOfWeekText) // 0=sunday / 6=saturday
	{
		$this->CronDate( $dayOfWeekText == "*" );
		$this->dowChoices =	new CronChoices($dayOfWeekText,'0-6');
	}
	
	function set($refDay,$refMonth,$refYear) {
		$refStamp = mktime(0,0,0,$refMonth,$refDay,$refYear);
		$refDow = date('w',$refStamp);
		list($selectedDow,$previousDow) = $this->dowChoices->selectLast($refDow);
		$deltaDays = (7+$refDow-$selectedDow)%7;
		//print $refMonth."-".$refDay."-".$refYear." ".$refDow." ".$selectedDow." ".$deltaDays."<br/>";
		$lastDowStamp = $refStamp - 86400*$deltaDays;
		$this->setStampResult($lastDowStamp);
	}
}

class CronDateMix extends CronDate
{
	var $date1, $date2;
	
	function CronDateMix($date1,$date2)
	{
		$this->CronDate( false );
		$this->date1 = $date1;
		$this->date2 = $date2;
	}
	
	function set($refDay,$refMonth,$refYear) {
		$this->date1->set($refDay,$refMonth,$refYear);
		$this->date2->set($refDay,$refMonth,$refYear);
    for(;;) {
      if( $this->date1->resultStamp == $this->date2->resultStamp ) {
        $this->setResult(	$this->date1->days, $this->date1->months, $this->date1->years );
        break;
      }
      if( $this->date1->resultStamp > $this->date2->resultStamp )
        $this->date1->set( $this->date2->days, $this->date2->months, $this->date2->years );
      else
        $this->date2->set( $this->date1->days, $this->date1->months, $this->date1->years );
    }
	}
}

class CronAnalyzer
{
	var $data, $refYear, $refMonth, $refDay, $refHour, $refMinute;
	
	function CronAnalyzer($stamp=false)
	{
		if($stamp)
			$this->data = getdate($stamp);
		else
			$this->data = getdate();
		$this->refYear = $this->data['year'];
		$this->refMonth = $this->data['mon'];
		$this->refDay = $this->data['mday'];
		$this->refHour = $this->data['hours'];
		$this->refMinute = $this->data['minutes'];
	}

	function LastStamp($text)
	{
		$items = explode(" ",$text);
		$time = new CronTime($items[1],$items[0]);
		$adate = new CronAbsoluteDate($items[2],$items[3]);
		$dow = new CronDayOfWeek($items[4]);
		$time->set($this->refHour,$this->refMinute);
		if( $adate->IsAny() ) {
			if( $dow->IsAny() ) {
				$date = new CronDate(true);
				$date->setResult($this->refDay,$this->refMonth,$this->refYear);
			} else {
				$dow->set($this->refDay,$this->refMonth,$this->refYear);
				$date = $dow;
			}
		} else {
			if( $dow->IsAny() ) {
				$adate->set($this->refDay,$this->refMonth,$this->refYear);
				$date = $adate;
			} else {
				$date = new CronDateMix($adate,$dow);
				$date->set($this->refDay,$this->refMonth,$this->refYear);
			}
		}
		$referenceStamp = mktime($this->refHour,$this->refMinute,0,$this->refMonth,$this->refDay,$this->refYear);
		$computedStamp = mktime($time->hours,$time->minutes,0,$date->months,$date->days,$date->years);
		$referenceMinutesInDay = $this->refHour*60 + $this->refMinute;
		$computedMinutesInDay = $time->hours*60 + $time->minutes;
		$dayBefore = $referenceStamp - 86400; 
		if( $computedStamp > $dayBefore && ($computedStamp == $referenceStamp || $computedMinutesInDay < $referenceMinutesInDay) )
				return $computedStamp;
		$time->set(23,59);
		$date->set( date('d',$dayBefore), date('m',$dayBefore), date('Y',$dayBefore) );
		return mktime($time->hours,$time->minutes,0,$date->months,$date->days,$date->years);
	}

}

?>
