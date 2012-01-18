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

if (! defined('SIMPLE_TEST')) {
			define('SIMPLE_TEST', 'simpletest/');
	}
	require_once(SIMPLE_TEST . 'unit_tester.php');
	require_once(SIMPLE_TEST . 'reporter.php');

	define( 'OPENNAB_BASE_DIR', preg_replace( '#/vl.*#', '', $_SERVER['SCRIPT_FILENAME'] ) );
  define( 'OPENNAB_SCRIPTS_DIR', OPENNAB_BASE_DIR.'/vl' );
  define( 'OPENNAB_PLUGINS_DIR', OPENNAB_SCRIPTS_DIR.'/plugins' );
	define('VISUAL_DEBUG', true);
	
	require_once('test_misc.php');
	require_once('test_request.php');
	require_once('test_block.php');
	require_once('test_ping.php');
	require_once('test_burrow.php');
	require_once('test_ambientblock.php');
	require_once('test_messageblock.php');
	require_once('test_apireply.php');
  
  require_once('../includes/plugin.php');
	require_once('test_plugin_ambient.php');
	require_once('test_plugin_myweather.php');
	require_once('test_plugin_myradio.php');
	require_once('test_plugin_plsm3u.php');
	require_once('test_plugin_pinginterval.php');
	require_once('test_plugin_asleep.php');
	require_once('test_cron.php');

  loadPluginFiles(OPENNAB_PLUGINS_DIR,'_test.php');
  
	$test = &new GroupTest('All tests');

  // create test classes
  foreach( get_declared_classes() as $classname ) {
    if( strtolower(substr($classname,0,6)) == 'testof' ) {
      $test->addTestCase(new $classname());
    }
  }

  $test->run(new HtmlReporter());
?>
