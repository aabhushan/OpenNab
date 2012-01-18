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

The 'myweather' plugin allows to get custom weather information displayed and announced by the Nabaztag.

This plugin defines the following API :

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&sky=<skyvalue>&temperature=<tempvalue>
where <skyvalue> is the sky type ( 0=sun; 1=clouds; 2=fog; 3=rain; 4=snow; 5=storm )
and <tempvalue> is the temperature value in celsius degrees.

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&weatherurl=<urlvalue>
where <urlvalue> is an url from which is retrieved the weather data (works well with hamweather.net).

Use of this plugin gives :
- customized weather color code on the rabbit belly (no need to subscribe to Violet weather service)
- customized audio weather announcement on programmed hours or by voice recognition (NEED to subscribe to Violet weather service -- see below)

The audio weather announcement feature is built on top of the Violet weather service.
Basically, this plugins catches any downstream weather audio message and hacks it to announce the customized sky and temperature values.
