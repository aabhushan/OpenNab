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

The 'ambient' plugin aims at customizing the "ambient" information displayed by the Nabaztag :
- lights on the rabbit belly (weather, stock market, ...)
- ears position
- blinking nose

This plugin defines the following APIs :

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&ambient<id>=<value>
where <id> is the belly/ears service id (weather=1 / market=2 / traffic=3 / email=6 / air=7 / taichi=14)
and <value> is either (1) a number whose meaning depends on the service or (2) empty to clean a previous override.

* weather / id=1
** sun / value=0
** clouds / value=1
** fog / value=2
** rain / value=3
** snow / value=4
** storm / value=5

* market / id=2
** bear++ / value=0
** bear+ / value=1
** bear / value=2
** neutral / value=3
** bull / value=4
** bull+ / value=5
** bull++ / value=6

* traffic / id=3
** the lower the more jammed / value=0 to 6

* email / id=6
** none / value=0
** one / value=1
** two / value=2
** three or more / value=3

* air / id=7
** good / value=1
** medium / value=6
** bad / value=10

* taichi / id=14
** rarely / value=40
** normal / value=80
** often / value=255


HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&ear<id>=<value>
where <id> is the ear id (right=0 / left=1)
and <value> is either (1) the ear position between 0 and 15 or (2) empty to clean a previous override.

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&nose=<value>
where <value> is either (1) the nose blinking mode (0=off / 1=single / 2=double) or (2) empty to clean a previous override.



