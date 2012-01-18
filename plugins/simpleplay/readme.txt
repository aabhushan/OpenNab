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

The 'simpleplay' plugin allows playback of local mp3 files or internet mp3 streams.

This plugin defines the following API :

HTTP PUT http://my.domain.com/vl/api.php?upload=<fileId>
where <fileId> is a number between 0 and 99999
uploads an mp3 file to OpenNab
				
HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&play=<fileId>
where <fileId> is either (1) a number between 0 and 99999 identifying an uploaded file (2) a full URL identifying an internet mp3 stream
starts the playback of the requested mp3 file or stream

