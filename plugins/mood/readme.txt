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

The 'mood' plugin allows playback of local mp3 files randomly, with a predefined
frequency and set of language(s).

Files used by this plugin should be located, as usual in the 'files' subdirectory, with 
the following structure:
- <language>/XXX.mp3 (and <language>/XXX.mp3.chor, not implemented yet )

Where language is a 2-letter code like "fr" or "uk" (or anything else you'd like...)
If XXX.mp3.chor exists, it is added to the message. The latter is not implemented yet.

Note: this plugin needs to have a Cron associated to it, otherwise it will never trigger
      new messages. The Cron is in the sample opennab.ini file and should call the plugin
      every minute or so.

This plugin defines the following API :

- Set Frequency: from 0 to 10 (least to most frequent). 0 disables the mood
- Set Language: comma-separated list of languages to use, empty will default to the global
  language defaults of opennab

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&mood=<frequency>
where <frequency> is a number between 1 and 10 indicating how frequently the rabbit will
say something

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber&moodlanguage=<languages>
where <languages> is a comma-separated list of languages to enable for the mood plugin

