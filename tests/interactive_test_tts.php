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

define( 'OPENNAB_SERIAL_NUMBER', 'unknown' );
define( 'OPENNAB_BASE_DIR', preg_replace( '#/vl.*#', '/', $_SERVER['SCRIPT_FILENAME'] ) );
define( 'OPENNAB_SCRIPTS_DIR', OPENNAB_BASE_DIR.'vl' );
define( 'OPENNAB_INCLUDES_DIR', OPENNAB_SCRIPTS_DIR.'/includes' );
define( 'OPENNAB_CONFIG_DIR', OPENNAB_SCRIPTS_DIR.'/config' );
define( 'OPENNAB_TTS_DIR', OPENNAB_SCRIPTS_DIR.'/tts' );
define( 'VISUAL_DEBUG', true );
require_once(OPENNAB_SCRIPTS_DIR.'/config.php');
require_once(OPENNAB_INCLUDES_DIR.'/misc.php');
require_once(OPENNAB_INCLUDES_DIR.'/request.php');
require_once(OPENNAB_INCLUDES_DIR.'/TTS.php');

if( isset($_REQUEST['s']) ) {
  
  $audiourl = $GLOBALS['SCRIPT_NAME'].'?speaker='.$_REQUEST['speaker'].'&message='.$_REQUEST['message'];
  $eaudiourl = urlencode($audiourl);
  
} else if( isset($_REQUEST['message']) ) {

  $tts = new TTS($_REQUEST['speaker']);
  $tts->Say($_REQUEST['message']);
  exit;
  
}

?>
Test TTS
<hr />
<form method='POST' action='interactive_test_tts.php'>
Speaker:
<select name='speaker'>
<?php
  foreach( TTS::ListOfAllSpeakers() as $speaker )
    if( $speaker == $_REQUEST['speaker'] )
      print '<option selected>'.$speaker.'</option>';
    else
      print '<option>'.$speaker.'</option>';
?>
</select><br/>
<textarea name='message' rows='8' cols='80' /><?php print $_REQUEST['message']?></textarea><br/>
<input type='submit' name='s' value='To speech' /><br/>
</form><br/>
<a href='<?php print $audiourl; ?>'><?php print $audiourl; ?></a><br/>
<object type="application/x-shockwave-flash" data="dewplayer.swf?son=<?php print $eaudiourl; ?>" width="200" height="20"> <param name="movie" value="dewplayer.swf?son=<?php print $eaudiourl; ?>" />
</object><br/>
<object type="application/x-shockwave-flash" data="player_mp3.swf?mp3=<?php print $eaudiourl; ?>" width="200" height="20">
	<param name="movie" value="player_mp3.swf?mp3=<?php print $eaudiourl; ?>" />
	<param name="wmode" value="transparent" />
</object><br/>
<hr />
