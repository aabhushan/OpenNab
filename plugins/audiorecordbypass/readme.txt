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

The 'audiorecordbypass' plugin allows a client application to bypass the Nabaztag audio recording mechanism

This plugin defines the following API:

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&audiorecordbypass=1
activates the bypass.
All following audio records (long click on bunny head) will not reach Violet speech recognition system. 

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&getaudiorecord=1
retrieves the last audio record or returns a 404 error if no record is available. 

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&audiorecordbypass=
stops the bypass.
All following audio records (long click on bunny head) will go back to Violet speech recognition system again. 

