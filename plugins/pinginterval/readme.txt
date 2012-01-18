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

The 'pinginterval' plugin allows to force the Nabaztag to ping the OpenNab server at a given frequency while still pinging the main 'r.nabaztag.com' server using the same initial period.

This plugin defines the following API :

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&pinginterval=<value>
where <value> is the duration in seconds of the period of OpenNab server ping.

A 0 value stops the frequency modification.
