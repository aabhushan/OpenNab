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

The 'tts' plugin provides text-to-speech functionality (similar to Nabaztag official API).

This plugin defines the following API :
				
HTTP GET http://my.domain.com/vl/api.php?sn=<serialNumber>&speaker=<ttsSpeakerId>&tts=<text>
converts the <text> into audio using the speaker <ttsSpeakerId> and have it played by the Nabaztag

The TTS speakers are defined in .ini configuration files
The syntax is as following:

[TTS:a_speaker_prototype]
;; 'Hide' entry allows to hide the TTS speaker entry in the global list returned by TTS::ListOfAllSpeakers()
Hide=1
;; 'MaxLength' defines the maximum length supported by the speaker
MaxLength=100
;; 'Method' defines the http call to use GET or POST
Method=POST
;; 'Host' defines the http server to connect to
Host=www.someserver.comcom
;; 'Path' defines the http request path
Path="/some/call.php?x=%s&y=2&z=%s"
;; 'Data' defines the data content for a POST request
Data="t=%s&l=%s&z=Go"
;; 'Referer' defines the referer page for the call
Referer=http://www.someserver.comcom/firstpage.html
;; 'Pattern' is the regular expression searched in the http reply to find the actual audio file path
;; if 'Pattern' key is not present, the output of the 1st http call is considered as audio content
Pattern="<embed src=.(.output.*?.mp3)"
;; 'PatternHost' is the http server used to retrieve the audio file identified by the Pattern
;; if 'PatternHost' key is not present, 'Host' key is used
PatternHost=www.someaudioserver.comcom
;; 'ContentType' defines the content type of the returned audio data
ContentType=audio/mp3

[TTS:a_speaker]
;; 'Prototype' is the id of the speaker prototype
Prototype=a_speaker_prototype
;; 'PathParameters' are the substituted strings in the Path expression of the prototype separated by commas
;; In the current example Path="/some/call.php?x=%s&y=2&z=%s" and PathParameters=%s,f then the http call will be "/some/call.php?x=%s&y=2&z=f" where %s will be replaced by the pronounced text
PathParameters=%s,f
;; 'DataParameters' are the substituted strings in the Data expression of the prototype separated by commas
;; In the current example Data="t=%s&l=%s&z=Go" and DataParameters=gg,%s then the http post data will be "t=gg&l=%s&z=Go" where %s will be replaced by the pronounced text
DataParameters=gg,%s


