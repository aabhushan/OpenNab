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

****************************************************************************/

2007/02/18: 
===========
Update for bc.jsp version 65806. The new offsets are 0x8BAA and 0x8AA0.
Also, the call we are replacing is now a call to SUB_009F.

Original text 
=============
If you are like me, then you don't like that your bunny does some ear rotations both *before* and *after* giving you a message. I find it irritating.

If you want to stop this movements (and, seeing how many reports of broken ears there are it's probably a good idea to enlarge their life). The bytes you have to patch in bc.jsp version 65800 are those at positions 0x7BF9 (corresponds to initial movement) and 0x7AEF (corresponds to final movement.
Both bytes have an original value  of 0x0 and you have to change them to 0x13.

This OpenNab plugin does the trick

[For those interested in the technical details, what we are doing is replacing:

00000000 push 00FF0000
00000005 push 18
00000007 CALL // SUB_0018 (set all leds)
00000008 drop
00000009 push 9B
0000000B CALL // SUB_009B (rotates ears)
0000000C drop

by

00000000 push 00FF0000
00000005 push 18
00000007 CALL // SUB_0018 (set all leds)
00000008 drop
00000009 push 9B
0000000B change_sign
0000000C drop

The opcode 00 (CALL) has been replaced bye 0x13 (change sign). This has the same length, disables the call, and does not alter the stack (in the first case you drop the result of the call to 9b, in the second case you drop the negative 9B)
