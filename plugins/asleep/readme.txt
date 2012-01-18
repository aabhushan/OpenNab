/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag™ (http://www.nabaztag.com/) electronic pet.
Copyright © 2007 OpenNab team - http://sourceforge.net/project/memberlist.php?group_id=187057

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

The 'asleep' plugin allows to put the rabbit in sleep mode.

This plugin defines the following API :

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&asleep=<value>
where <value> is :
- '1' to put the Nabaztag in sleep mode
- '0' to put the Nabaztag in awake mode
- '' (empty) to put the Nabaztag in default mode, that is comply to main server orders for sleep/wake up



