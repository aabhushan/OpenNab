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

The 'quizz' plugin allows to use the Nabaztag as a dice when playing board games.

This plugin defines the following API :

HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&quizz=<value>
where <value> is :
- 'on' to start quizz mode
- 'off' to exit quizz mode

When the Nabaztag is in quizz mode, a single click on the head starts a new question.
For answer : 
Right Ear Front => A
Right Ear Back  => B
Left Ear Front 	=> C
Leftt Ear Back  => D
 
A double click on the head exits from the quizz mode.


