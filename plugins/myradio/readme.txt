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

The 'myradio' plugin turns the rabbit into a multi-channel ear-controlled radio receiver.

This plugin defines the following API :

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&radio<channel>=<url>
where <channel> is an ear position between 0 and 15 and <url> is the streaming mp3 url corresponding to the channel
defines a radio channel

HTTP GET http://my.domain.com/vl/api.php?radiolist=1
retrieves the list of the currently defined channels

When some mp3 stream is played by the rabbit (for example, when the radio is activated by voice recognition), a single click on the rabbit head pauses the audio playback.
At this time, if the 'myradio' plugin is loaded, manually moving the rabbit left ear to some position causes the respective mp3 url to be streamed.
Then, another single click on the head pauses the playback and another channel can be selected.
A double click on the head exits the ear-controlled radio mode.

If the ear is moved to a position for which no radio channel was defined with the API, an hardcoded default (French) channel for the given position is streamed.
