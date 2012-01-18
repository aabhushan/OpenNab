<?php
/***************************************************************************

OpenNab - An open PHP-based proxy framework for the Nabaztag� (http://www.nabaztag.com/) electronic pet.
Copyright c 2007 OpenNab team - http://opennab.sourceforge.net/team/

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

class Plugin_quizz extends Plugin
{	
	
	var $form_fr;
	
	function Plugin_quizz()
	{
		$this->form = array(
			'fr' => array("0" => "B", "1" => "A", "2" => "C", "3" => "B"),
			//'en' => array("0" => "B", "1" => "A", "2" => "C", "3" => "B")
			);
		//addToLog('quizz: nabazquizz loaded', 3);
	}
		
	// Raised at Rabbit ping (when p4.jsp is called) before the ping request is forwarded to the main server
	// returns true if ping operation is handled by the plugin
	// returns false if ping operation should be handled elsewhere, and, by default, forwarded to the main server
	function OnPingForward(&$burrow,&$pingRequest)
	{
		$quizz = $this->GetData($burrow,'quizz');
		if( $quizz === false )
			return false;
		if( $pingRequest->IsDoubleClick() ) {
			$this->ClearData($burrow,'quizz');
			$this->ClearData($burrow,'messageId');
			$this->ClearData($burrow,'cptquestion');
			$this->ClearData($burrow,'bonnereponse');
			addToLog( 'quizz: leaving quizz mode', 3 );
			return false;
		}
		$quizz = $this->GetGlobalData($burrow,'lang');
                $quizz = "fr"; //temporary fix
		if( $pingRequest->IsSingleClick() ) {
			$cpt_question = $this->GetData($burrow,'cptquestion');
			if( $cpt_question === false )
				$cpt_question = -1;
			$cpt_question = $cpt_question + 1;
			if ($cpt_question == 0) {
				// presentation of NabazQuizz
				$value = 0;
				$id = 1233210 + $value;
				$message = "ID ".$id."\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value.".mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_a.mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_b.mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_c.mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_d.mp3 \nMW\n";
			}
			else {
				if ($cpt_question == count($this->form[$quizz])) {
					$value = $cpt_question;
					$id = 3213210 + $value;
					$bonnereponse = $this->GetData($burrow, 'bonnereponse');
					$message = "ID ".$id."\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value.".mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/".$bonnereponse.".mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_1.mp3 \nMW\n";
				}
				else {
					$value = $cpt_question;
					$id = 3213210 + $value;
					$message = "ID ".$id."\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value.".mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_a.mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_b.mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_c.mp3 \nMW\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_d.mp3 \nMW\n";
				}
			}
			$this->SetData($burrow, 'cptquestion', $cpt_question);
			$this->SetData($burrow,'message',$message);
			$this->SetData($burrow,'messageId',$id);
			addToLog( 'quizz: now we ask the question : '.$value, 3 );
			return true;
		}
		if($pingRequest->IsEarsMove()) {
			$reponse = "";
			$oreille_gauche = $pingRequest->GetEarMove(1);
			$oreille_droite = $pingRequest->GetEarMove(0);
			if (($oreille_droite > 0) && ($oreille_droite <= 7)) {
				$reponse = "A";
			}
			elseif (($oreille_droite > 8) && ($oreille_droite <= 15)) {
				$reponse = "B";
			}
			elseif (($oreille_gauche > 0) && ($oreille_gauche <= 7)) {
				$reponse = "C";
			}
			elseif (($oreille_gauche > 8) && ($oreille_gauche <= 15)) {
				$reponse = "D";
			}
			if ($reponse != "") {
				$cpt_question = $this->GetData($burrow,'cptquestion');
				if ($this->form[$quizz][$cpt_question] == $reponse) {
					// bonne reponse
					$bonnereponse = $this->GetData($burrow,'bonnereponse');
					if ($bonnereponse === false)
						$bonnereponse = 0;
					$bonnereponse++;
					$this->SetData($burrow,'bonnereponse',$bonnereponse);
				}
				else {
					// mauvaise reponse
					$bonnereponse = $this->GetData($burrow,'bonnereponse');
					if ($bonnereponse === false)
						$bonnereponse = 0;
					$this->SetData($burrow,'bonnereponse',$bonnereponse);
				}
				$value = $cpt_question;
				$id = 3213210 + $value;
				$message = "ID ".$id."\nMU broadcast/vl/plugins/quizz/files/".$quizz."/q_".$value."_".strtolower($reponse).".mp3 \nMW\n";
				$this->SetData($burrow,'message',$message);
				$this->SetData($burrow,'messageId',$id);
				addToLog( 'quizz: question : '.$value.' ; answer : '.$reponse, 3 );
			}
			return true;
		}
	
		return false;
	}
	
	// Raised at Rabbit ping (when p4.jsp is called) for updating burrow and define the reply to the rabbit
	function OnPingWrite(&$burrow,&$pingRequest)
	{
		$message = $this->GetData($burrow,'message');
		if( $message === false )
			return false;
		$ambientBlock = &$pingRequest->ambient();
		$ambientBlock->SetEarValue(0,0);
		$ambientBlock->SetEarValue(1,0);
		$messageBlock = &$pingRequest->Message();
		$messageBlock->Encode( $message );
		$this->ClearData($burrow,'message');
		return true;
	}
	
	// Raised at API call when a serial number identifying the rabbit is present in the API url
	// returns true if API call was handled by the Plugin, else returns false
	function OnApi(&$burrow,$parameters,&$reply)
	{
		if( !array_key_exists('quizz',$parameters) )
			return false;
		$quizz = $parameters['quizz'];
		if( $quizz == 'on' ) {
			$this->SetData($burrow,'quizz',$quizz);
			$reply->Add( 'Quizz', $quizz );
			addToLog( 'quizz: entering quizz mode', 3 );
			return true;
		}
		$this->ClearData($burrow,'quizz');
		$this->ClearData($burrow,'messageId');
		$reply->Add( 'Quizz', 'off' );
		addToLog( 'quizz: leaving quizz mode by API call', 3 );
		return false;
	}

}
?>
