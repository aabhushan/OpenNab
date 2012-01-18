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

// This file is a wrapper for all the files that have to be included by the client-called scripts

define( 'OPENNAB_BASE_DIR', str_repeat( '../', substr_count( substr($_SERVER['SCRIPT_FILENAME'],strpos( $_SERVER['SCRIPT_FILENAME'], '/vl/' ) + 1 ), '/' ) ) );
define( 'OPENNAB_SCRIPTS_DIR', OPENNAB_BASE_DIR.'vl' );
define( 'OPENNAB_INCLUDES_DIR', OPENNAB_SCRIPTS_DIR.'/includes' );
define( 'OPENNAB_PLUGINS_DIR', OPENNAB_SCRIPTS_DIR.'/plugins' );
define( 'OPENNAB_CONFIG_DIR', OPENNAB_SCRIPTS_DIR.'/config' );
define( 'OPENNAB_TTS_DIR', OPENNAB_SCRIPTS_DIR.'/tts' );

require_once( OPENNAB_INCLUDES_DIR.'/http.inc' ); // GuinuX Advanced HTTP Client

require_once(OPENNAB_INCLUDES_DIR.'/misc.php');
require_once(OPENNAB_SCRIPTS_DIR.'/config.php');
require_once(OPENNAB_INCLUDES_DIR.'/server.php');

require_once(OPENNAB_INCLUDES_DIR.'/request.php');
require_once(OPENNAB_INCLUDES_DIR.'/hcprequest.php');
require_once(OPENNAB_INCLUDES_DIR.'/ping.php');
require_once(OPENNAB_INCLUDES_DIR.'/boot.php');
require_once(OPENNAB_INCLUDES_DIR.'/burrow.php');
require_once(OPENNAB_INCLUDES_DIR.'/file.php');
require_once(OPENNAB_INCLUDES_DIR.'/apireply.php');
require_once(OPENNAB_INCLUDES_DIR.'/plugin.php');
require_once(OPENNAB_INCLUDES_DIR.'/allplugins.php');
require_once(OPENNAB_INCLUDES_DIR.'/cron.php');
require_once(OPENNAB_SCRIPTS_DIR.'/utils.php');
?>
