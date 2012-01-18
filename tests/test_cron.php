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

require_once('../includes/cron.php');

	class TestOfCron extends UnitTestCase {
		function TestOfCron() {
			$this->UnitTestCase();
		}
		
		//====================================================
		
		function checkChoiceIsEqual($expected,$input,$line,$base=false) {
			$cc = new CronChoices($input,$base);
			$helpText = 'expected = '.implode(',',$expected).' vs computed = '.implode(',',$cc->items).' for line '.$line;
			$this->assertEqual( $expected, $cc->items, $helpText );
		}

		function testCronChoices() {
			$this->checkChoiceIsEqual( array(1,2), '*', __LINE__,'1,2' );
			$this->checkChoiceIsEqual( array(1,2,3), '*', __LINE__,'1-3' );
			$this->checkChoiceIsEqual( array(14), '14', __LINE__ );
			$this->checkChoiceIsEqual( array(14,15), '14,15', __LINE__ );
			$this->checkChoiceIsEqual( array(14,15,16), '14,15,16', __LINE__ );
			$this->checkChoiceIsEqual( array(14,15,16), '14-16', __LINE__ );
			$this->checkChoiceIsEqual( array(1,2,3,14,15,16), '1-3,14-16', __LINE__ );
			$this->checkChoiceIsEqual( array(25,35), '25,35', __LINE__ );
			$this->checkChoiceIsEqual( array(25,27,29), '25-29/2', __LINE__ );
			$this->checkChoiceIsEqual( array(25,27,29), '25-30/2', __LINE__ );
			$this->checkChoiceIsEqual( array(25,28), '25-30/3', __LINE__ );
		}
		
		//====================================================

		function checkChoiceSelectionIsEqual($expected,$input,$base,$reference,$line) {
			$cc = new CronChoices($input,$base);
			$computed = $cc->selectLast($reference);
			$helpText = 'expected = '.implode(',',$expected).' vs computed = '.implode(',',$computed).' for line '.$line;
			$this->assertEqual( $expected, $computed, $helpText );
		}

		function testCronChoicesSelection() {
			$this->checkChoiceSelectionIsEqual( array(6,5), '*', '1-12', 6, __LINE__ );
			$this->checkChoiceSelectionIsEqual( array(3,1), '1,3,8', '1-12', 6, __LINE__ );
			$this->checkChoiceSelectionIsEqual( array(6,false), '6', '1-12', 6, __LINE__ );
			$this->checkChoiceSelectionIsEqual( array(7,false), '7', '1-12', 6, __LINE__ );
			$this->checkChoiceSelectionIsEqual( array(25,35), '25,35', '0-59', 30, __LINE__ );
		}
		
		//====================================================

		function checkTimeIsEqual($expected,$last,$cron,$reference,$line) {
			$ct = new CronTime($cron[0],$cron[1]);
			$ct->set($reference[0],$reference[1]);
			$this->assertEqual( $last[0], $ct->lastHour, 'expected = '.$last[0].' vs computed = '.$ct->lastHour.' for line '.$line );
			$this->assertEqual( $last[1], $ct->lastMinute, 'expected = '.$last[1].' vs computed = '.$ct->lastMinute.' for line '.$line );
			$this->assertEqual( $expected[0], $ct->hours, 'expected = '.$expected[0].' vs computed = '.$ct->hours.' for line '.$line );
			$this->assertEqual( $expected[1], $ct->minutes, 'expected = '.$expected[1].' vs computed = '.$ct->minutes.' for line '.$line );
		}

		function testCronTime() {
			$this->checkTimeIsEqual( array(14, 24), array(14, 24), array('14', '24'), array(14, 24), __LINE__ );
			$this->checkTimeIsEqual( array(14, 24), array(14, 59), array('14', '*'), array(14, 24), __LINE__ );
			$this->checkTimeIsEqual( array(14, 24), array(23, 24), array('*', '24'), array(14, 24), __LINE__ );
			$this->checkTimeIsEqual( array(14, 24), array(23, 59), array('*', '*'), array(14, 24), __LINE__ );
			$this->checkTimeIsEqual( array(14, 25), array(14, 25), array('14', '25'), array(14, 30), __LINE__ );
			$this->checkTimeIsEqual( array(14, 25), array(14, 25), array('14', '25'), array(14, 20), __LINE__ );
			$this->checkTimeIsEqual( array(13, 25), array(14, 25), array('13,14', '25'), array(14, 20), __LINE__ );
			$this->checkTimeIsEqual( array(14, 25), array(14, 35), array('14', '25,35'), array(14, 30), __LINE__ );
			$this->checkTimeIsEqual( array(14, 20), array(14, 59), array('13,14', '*'), array(14, 20), __LINE__ );
			$this->checkTimeIsEqual( array(14, 25), array(23, 35), array('*', '25,35'), array(14, 30), __LINE__ );
			$this->checkTimeIsEqual( array(23, 35), array(23, 35), array('*', '25,35'), array(0, 20), __LINE__ );
			$this->checkTimeIsEqual( array(14, 35), array(23, 35), array('*', '25,35'), array(14, 40), __LINE__ );
			$this->checkTimeIsEqual( array(13, 59), array(13, 59), array('13', '*'), array(14, 20), __LINE__ );
			$this->checkTimeIsEqual( array(13, 59), array(13, 59), array('13', '*'), array(12, 20), __LINE__ );
			$this->checkTimeIsEqual( array(13, 35), array(13, 35), array('13', '25,35'), array(14, 30), __LINE__ );
			$this->checkTimeIsEqual( array(13, 35), array(13, 35), array('13', '25,35'), array(12, 30), __LINE__ );
		}
		
		//====================================================

		function checkDateIsEqual($expected,$cron,$reference,$line) {
			$cd = new CronAbsoluteDate($cron[0],$cron[1]);
			$cd->set($reference[0],$reference[1],$reference[2]);
			$this->assertEqual( $expected[0], $cd->days, 'expected = '.$expected[0].' vs computed = '.$cd->days.' for line '.$line );
			$this->assertEqual( $expected[1], $cd->months, 'expected = '.$expected[1].' vs computed = '.$cd->months.' for line '.$line );
			$this->assertEqual( $expected[2], $cd->years, 'expected = '.$expected[2].' vs computed = '.$cd->years.' for line '.$line );
		}

		function testCronDate() {
			$this->checkDateIsEqual( array(06, 01, 1971), array('06', '01'), array(06, 01, 1971), __LINE__ );
			$this->checkDateIsEqual( array(06, 01, 1971), array('06', '*'), array(06, 01, 1971), __LINE__ );
			$this->checkDateIsEqual( array(06, 01, 1971), array('*', '1'), array(06, 01, 1971), __LINE__ );
			$this->checkDateIsEqual( array(06, 01, 1971), array('*', '*'), array(06, 01, 1971), __LINE__ );
			$this->checkDateIsEqual( array(31, 01, 1971), array('*', '1'), array(06, 03, 1971), __LINE__ );
			$this->checkDateIsEqual( array(28, 02, 1971), array('*', '2'), array(06, 03, 1971), __LINE__ );
			$this->checkDateIsEqual( array(31, 12, 1970), array('*', '12'), array(06, 03, 1971), __LINE__ );
			$this->checkDateIsEqual( array(30, 11, 1970), array('*', '11'), array(06, 03, 1971), __LINE__ );
			$this->checkDateIsEqual( array(29, 02, 1972), array('*', '2'), array(06, 01, 1973), __LINE__ );
			$this->checkDateIsEqual( array(25, 12, 1970), array('25', '12'), array(06, 01, 1971), __LINE__ );
			$this->checkDateIsEqual( array(25, 12, 1970), array('25', '*'), array(06, 01, 1971), __LINE__ );
			$this->checkDateIsEqual( array(27, 12, 1970), array('25,27', '12'), array(06, 01, 1971), __LINE__ );
			$this->checkDateIsEqual( array(27, 12, 1970), array('25,27', '8,12'), array(06, 01, 1971), __LINE__ );
			$this->checkDateIsEqual( array(14, 9, 1971), array('13,14', '7,9'), array(06, 11, 1971), __LINE__ );
			$this->checkDateIsEqual( array(14, 10, 1971), array('13,14', '*'), array(06, 11, 1971), __LINE__ );
			$this->checkDateIsEqual( array(30, 9, 1971), array('*', '7,9'), array(06, 11, 1971), __LINE__ );
			$this->checkDateIsEqual( array(30, 9, 1970), array('*', '7,9'), array(1, 5, 1971), __LINE__ );
			$this->checkDateIsEqual( array(31, 7, 1971), array('*', '7,9'), array(6, 8, 1971), __LINE__ );
		}
		
		//====================================================
		
		// Cron format : "minute(0-59) hour(0-23) day(1-31) month(1-12) weekday(0-6)"
		
		function checkIsEqual($expected,$computed,$line) {
			$timeFormat = "%d/%m/%Y %H:%M:%S";
			$helpText = 'expected = '.strftime( $timeFormat, $expected ).' vs computed = '.strftime( $timeFormat, $computed ).' for line '.$line;
			$this->assertEqual( $expected, $computed, $helpText );
		}
		
		function testCronStar() {
			$stamp = mktime( 15, 38, 24, 6, 26, 1994 );
			$ca = new CronAnalyzer($stamp);
			$this->checkIsEqual( mktime(14,24,00,6,26,1994), $ca->LastStamp("24 14 * * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,24,00,6,26,1994), $ca->LastStamp("24 15 * * *"), __LINE__ );
			$this->checkIsEqual( mktime(14,45,00,6,26,1994), $ca->LastStamp("45 14 * * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,45,00,6,25,1994), $ca->LastStamp("45 15 * * *"), __LINE__ );
			$this->checkIsEqual( mktime(16,24,00,6,25,1994), $ca->LastStamp("24 16 * * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,38,00,6,26,1994), $ca->LastStamp("38 15 * * *"), __LINE__ );
			$this->checkIsEqual( mktime(14,59,00,6,26,1994), $ca->LastStamp("* 14 * * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,38,00,6,26,1994), $ca->LastStamp("* 15 * * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,24,00,6,26,1994), $ca->LastStamp("24 * * * *"), __LINE__ );
			$this->checkIsEqual( mktime(14,45,00,6,26,1994), $ca->LastStamp("45 * * * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,38,00,6,26,1994), $ca->LastStamp("38 * * * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,38,00,6,26,1994), $ca->LastStamp("* * * * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,38,00,6,26,1994), $ca->LastStamp("* * 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,6,25,1994), $ca->LastStamp("* * 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,5,27,1994), $ca->LastStamp("* * 27 * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,24,00,6,26,1994), $ca->LastStamp("24 * 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(23,24,00,6,25,1994), $ca->LastStamp("24 * 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(23,24,00,5,27,1994), $ca->LastStamp("24 * 27 * *"), __LINE__ );
			$this->checkIsEqual( mktime(14,45,00,6,26,1994), $ca->LastStamp("45 * 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(23,45,00,6,25,1994), $ca->LastStamp("45 * 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(13,24,00,6,26,1994), $ca->LastStamp("24 13 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(13,24,00,6,25,1994), $ca->LastStamp("24 13 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(13,24,00,5,27,1994), $ca->LastStamp("24 13 27 * *"), __LINE__ );
			$this->checkIsEqual( mktime(13,45,00,6,26,1994), $ca->LastStamp("45 13 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(13,45,00,6,25,1994), $ca->LastStamp("45 13 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(16,24,00,5,26,1994), $ca->LastStamp("24 16 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(16,24,00,6,25,1994), $ca->LastStamp("24 16 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(16,24,00,5,27,1994), $ca->LastStamp("24 16 27 * *"), __LINE__ );
			$this->checkIsEqual( mktime(16,45,00,5,26,1994), $ca->LastStamp("45 16 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(16,45,00,6,25,1994), $ca->LastStamp("45 16 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(13,59,00,6,26,1994), $ca->LastStamp("* 13 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(13,59,00,6,25,1994), $ca->LastStamp("* 13 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(13,59,00,5,27,1994), $ca->LastStamp("* 13 27 * *"), __LINE__ );
			$this->checkIsEqual( mktime(16,59,00,5,26,1994), $ca->LastStamp("* 16 26 * *"), __LINE__ );
			$this->checkIsEqual( mktime(16,59,00,6,25,1994), $ca->LastStamp("* 16 25 * *"), __LINE__ );
			$this->checkIsEqual( mktime(15,38,00,6,26,1994), $ca->LastStamp("* * * 6 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,5,31,1994), $ca->LastStamp("* * * 5 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,4,30,1994), $ca->LastStamp("* * * 4 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,1,31,1994), $ca->LastStamp("* * * 1 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,2,28,1994), $ca->LastStamp("* * * 2 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,12,31,1993), $ca->LastStamp("* * * 12 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,11,30,1993), $ca->LastStamp("* * * 11 *"), __LINE__ );
			$this->checkIsEqual( mktime(17,59,00,11,30,1993), $ca->LastStamp("* 17 * 11 *"), __LINE__ );
		}
		
		function testCronStarDateLimits() {
			$stamp = mktime( 0, 0, 0, 1, 1, 1994 );
			$ca = new CronAnalyzer($stamp);
			$this->checkIsEqual( mktime(23,59,00,12,31,1993), $ca->LastStamp("* * * 12 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,12,31,1993), $ca->LastStamp("* * 31 * *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,12,31,1993), $ca->LastStamp("59 * * * *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,12,31,1993), $ca->LastStamp("* 23 * * *"), __LINE__ );
		}
		
		function testCronListDate() {
			$stamp = mktime( 15, 38, 24, 6, 26, 1994 );
			$ca = new CronAnalyzer($stamp);
			$this->checkIsEqual( mktime(23,59,00,3,31,1994), $ca->LastStamp("* * * 1,3,8 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,5,31,1994), $ca->LastStamp("* * * 1,5,8 *"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,10,31,1993), $ca->LastStamp("* * * 8,10 *"), __LINE__ );
		}
		
		function testCronDayOfWeek() {
			$stamp = mktime( 15, 38, 24, 7, 10, 2007 );
			$this->assertEqual( 2, date('w',$stamp) );
			$ca = new CronAnalyzer($stamp);
			$this->checkIsEqual( mktime(15,38,00,7,10,2007), $ca->LastStamp("* * * * 2"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,7,9,2007), $ca->LastStamp("* * * * 1"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,7,8,2007), $ca->LastStamp("* * * * 0"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,7,7,2007), $ca->LastStamp("* * * * 6"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,7,6,2007), $ca->LastStamp("* * * * 5"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,7,5,2007), $ca->LastStamp("* * * * 4"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,7,4,2007), $ca->LastStamp("* * * * 3"), __LINE__ );
			$this->checkIsEqual( mktime(22,59,00,7,6,2007), $ca->LastStamp("* 22 * * 5"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,6,8,2007), $ca->LastStamp("* * 8 * 5"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,11,3,2006), $ca->LastStamp("* * 3 * 5"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,2,23,2007), $ca->LastStamp("* * * 2 5"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,2,17,2006), $ca->LastStamp("* * 17 2 5"), __LINE__ );
			$this->checkIsEqual( mktime(21,59,00,11,3,2006), $ca->LastStamp("* 21 3 * 5"), __LINE__ );
			$this->checkIsEqual( mktime(21,59,00,2,23,2007), $ca->LastStamp("* 21 * 2 5"), __LINE__ );
			$this->checkIsEqual( mktime(21,59,00,2,17,2006), $ca->LastStamp("* 21 17 2 5"), __LINE__ );
			$this->checkIsEqual( mktime(23,35,00,11,3,2006), $ca->LastStamp("35 * 3 * 5"), __LINE__ );
			$this->checkIsEqual( mktime(23,35,00,2,23,2007), $ca->LastStamp("35 * * 2 5"), __LINE__ );
			$this->checkIsEqual( mktime(23,35,00,2,17,2006), $ca->LastStamp("35 * 17 2 5"), __LINE__ );
			
			$stamp = mktime( 15, 38, 24, 7, 3, 2007 );
			$this->assertEqual( 2, date('w',$stamp) );
			$ca = new CronAnalyzer($stamp);
			$this->checkIsEqual( mktime(23,59,00,7,2,2007), $ca->LastStamp("* * * * 1"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,6,28,2007), $ca->LastStamp("* * * * 4"), __LINE__ );
			$this->checkIsEqual( mktime(23,59,00,6,28,2007), $ca->LastStamp("* * * 6 4"), __LINE__ );
		}
		
		
	}
?>
