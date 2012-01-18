/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag™ (http://www.nabaztag.com/) electronic pet.
Copyright © 2007 OpenNab team - http://opennab.sourceforge.net/team/

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

****************************************************************************

The 'message' plugin allows to test the Nabaztag built-in message syntax by sending a custom message.

This plugin defines the following API :

HTTP POST http://my.domain.com/vl/api.php
sn=<serialNumber>&message=<value>
where <value> is the message content

Examples of messages :

- Weather message :
ID 25357905
MC broadcast/broad/config/weather/fr/signature.mp3
CL 1336759
CH broadcast/broad/config/anim/sig_tete_flash_rapide.chor
MW
MU broadcast/broad/config/weather/fr/tomorrow.mp3
MW
MU broadcast/broad/config/weather/fr/sky/3.mp3
MW
MU broadcast/broad/config/weather/fr/temp/11.mp3
MW
MU broadcast/broad/config/weather/fr/degree.mp3
MW
MC broadcast/broad/config/weather/fr/signature.mp3
CL 1336759
CH broadcast/broad/config/anim/sig_tete_flash_rapide.chor
MW

- Talking clock message :
ID 25358557
MC broadcast/broad/config/clock/fr/signature.mp3
CL 3066796
CH broadcast/broad/config/anim/sig_circle.chor
MW
MU broadcast/broad/config/clock/fr/20/4.mp3
PL 4
CH broadcast/broad/config/clock/fr/20/4.mp3.chor
MW
MC broadcast/broad/config/clock/fr/signature.mp3
CL 3066796
CH broadcast/broad/config/anim/sig_circle.chor
MW

- Streaming mp3 message :
ID 9042998
ST http://213.205.96.91:9915
PL 1
MW

- Violet Text To Speech message :
ID 25275115
ST broadcast/broad/218/250/771.mp3
PL 4
MW



