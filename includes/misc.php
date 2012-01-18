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


function dbg($txt,$name=false) {
	if(!VISUAL_DEBUG)
		return;
	if($name)
		print $name.' = ';
	print_r($txt);
	print "\r\n";
}

function unexpectedCondition($txt) {
	addToLog( 'Unexpected : '.$txt, 1 );
	die('We should not reach this point. See log for details.');
}

function headerCase(&$item, $key) {
	$item = ucfirst(strtolower($item));
}

function getVarString($var) {
	ob_start();
	print_r($var);
	return ob_get_clean();
}

function getValueOrDefault($var,$default) {
  return isset($var) ? $var : $default;
}

function fileWrite($path,$data) {
	$fh = fopen($path, 'w');
	fwrite($fh, $data);
	fclose($fh);
}

function fileAppend($path,$data) {
	$fh = fopen($path, 'a');
	fwrite($fh, $data);
	fclose($fh);
}

function fileName($prefix,$suffix,$onebyday=false) {
	if( $onebyday )
		$datePattern = 'Ymd';
	else
		$datePattern = 'YmdHis';
  if( LOG_IN_SAME_FILE )
    return OPENNAB_BASE_DIR.'/vl/'.$prefix.'_'.date($datePattern).$suffix;
  else
    return OPENNAB_BASE_DIR.'/vl/'.$prefix.'_'.OPENNAB_SERIAL_NUMBER.'_'.date($datePattern).$suffix;
}

define( 'LOG_FILENAME', fileName('logs/actions','.log',true) );

function addToLog($data,$level)
{
	if( VISUAL_DEBUG )
		return;
	if( LOG_LEVEL < $level )
		return;
	fileAppend( LOG_FILENAME, date('Y-m-d H:i:s').' '.$data."\r\n" );
}

function loadPluginFiles($pluginsFolder,$fileName) {
  $pluginsFolderHandle = opendir($pluginsFolder); // cannot use 'glob' because inhibited by some web host providers
  while( $pluginFolder = readdir($pluginsFolderHandle) ) {
    if( $pluginFolder == '.' || $pluginFolder == '..' || !is_dir($pluginsFolder.'/'.$pluginFolder) )
      continue;
    $demoFile = $pluginsFolder.'/'.$pluginFolder.'/'.$fileName;
    if( file_exists($demoFile) )
      require_once($demoFile);
  }
  closedir($pluginsFolderHandle);
}

?>
