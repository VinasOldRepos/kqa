<?php
/************************************************************************************
* Name:				Combat Model													*
* File:				Application\Model\Combat.php 									*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the Combat's model.										*
*																					*
* Creation Date:	04/09/2012														*
* Version:			1.12.0904														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Model;

	class Combat {

		public function answers($answers = false) {
			$return		= false;
			$correct	= false;
			if ($answers) {
				foreach ($answers as $answer) {
					$correct	= ((!$correct) && ($answer['boo_correct'] == 1)) ? $answer['id'] : $correct;
					$return		.= '<input type="radio" name="answer_opt" id="opt_'.$answer['id'].'" value="'.$answer['id'].'" caption="'.$answer['vc_answer'].'" class="radio_answer_opt pointer" /><label for="opt_'.$answer['id'].'" class="pointer">'.$answer['vc_answer'].'</label><br />'.PHP_EOL;
				}
				//$return			.= '<input type="hidden" name="correct" id="correct" value="'.md5($correct).'" />'.PHP_EOL;
			}
			return $return;
		}

		public function monsterList($monsters = false) {
			$return		= false;
			if ($monsters) {
				$return	.= '<div class="monster_row">'.PHP_EOL;
				$return	.= '	<div class="monsters_tit">MONSTERS</div>'.PHP_EOL;
				$return	.= '</div>'.PHP_EOL;
				$return	.= '<div class="monster_row">'.PHP_EOL;
				$return	.= '	<div class="monster_tit_name">NAME</div>'.PHP_EOL;
				$return	.= '	<div class="monster_tit_hp" >Hit Points</div>'.PHP_EOL;
				$return	.= '</div>'.PHP_EOL;
				for ($i = 0; $i < count($monsters); $i++) {
					$monster	= $i + 1;
					$return	.= '<div class="monster_row" id="row_'.$monster.'">'.PHP_EOL;
					$return	.= '	<div class="monster_name">'.$monsters[$i]['vc_name'].'</div>'.PHP_EOL;
					$return	.= '	<div class="monster_hits" id="hp_'.$monster.'" hp="'.$monsters[$i]['monster_hp'].'">'.$monsters[$i]['monster_hp'].'</div>'.PHP_EOL;
					$return	.= '</div>'.PHP_EOL;
				}
			}
			return $return;
		}

		public function characterDisplay($character = false, $combat_items = false, $noncombat_bag = false) {
			$return		= false;
			if ($character) {
				$return	.= '<div class="char_spacer">&nbsp;</div>'.PHP_EOL;
				$return	.= '<div class="monster_row">'.PHP_EOL;
				$return	.= '	<div class="monsters_tit">CHARACTER</div>'.PHP_EOL;
				$return	.= '</div>'.PHP_EOL;
				$return	.= '<div class="char_spacer">&nbsp;</div>'.PHP_EOL;
				$return	.= '<div class="monster_row">'.PHP_EOL;
				$return	.= '	<div class="char_detail">Name:</div>'.PHP_EOL;
				$return	.= '	<div class="char_info">'.$character['vc_name'].'</div>'.PHP_EOL;
				$return	.= '</div>'.PHP_EOL;
				$return	.= '<div class="char_spacer">&nbsp;</div>'.PHP_EOL;
				$return	.= '<div class="monster_row">'.PHP_EOL;
				$return	.= '	<div class="char_detail">HP:</div>'.PHP_EOL;
				$return	.= '	<div class="char_info" id="current_hp">'.$character['int_hp'].'</div>'.PHP_EOL;
				$return	.= '</div>'.PHP_EOL;
				$return	.= '<div class="monster_row">'.PHP_EOL;
				$return	.= '	<div class="char_detail">XP in this course:</div>'.PHP_EOL;
				//$return	.= '	<div class="char_info" id="xp">'.$character['int_xp'].'</div>'.PHP_EOL;
				$return	.= '	<div class="char_info" id="xp"></div>'.PHP_EOL;
				$return	.= '</div>'.PHP_EOL;
			}
			$return		.= '<div class="char_spacer">&nbsp;</div>'.PHP_EOL;
			if ($combat_items) {
				$return	.= '<div class="monster_row">'.PHP_EOL;
				$return	.= '	<div class="monsters_tit">WEARING</div>'.PHP_EOL;
				$return	.= '</div>'.PHP_EOL;
				$return	.= '<div class="char_spacer">&nbsp;</div>'.PHP_EOL;
				foreach ($combat_items as $item) {
					$return	.= '<div class="monster_row">'.PHP_EOL;
					$return	.= '	<div class="char_detail">'.$item['vc_name'].'</div>';
					/*if (($item['id_type'] == 5) || ($item['id_type'] == 6)) {
						$return	.= '	<div class="char_info" id="xp">('.$item['int_me_min'].' - '.$item['int_me_max'];
						if ($item['int_magic_me'] > 0) {
							$return	.=  ') + '.$item['int_magic_me'];
						} else {
							$return	.=  ')';
						}
						$return	.=  '</div>'.PHP_EOL;
					} else if ($item['int_time'] > 0) {
						$return	.= '	<div class="char_info">(Time bonus: '.$item['int_time'].'%)</div>'.PHP_EOL;
					} else if ($item['int_ds'] > 0) {
						$return	.= '	<div class="char_info">(DS +'.$item['int_ds'];
						if ($item['int_magic_ds'] > 0) {
							$return	.=  ') + '.$item['int_magic_ds'];
						} else {
							$return	.=  ')';
						}
						$return	.=  '</div>'.PHP_EOL;
					} else if ($item['int_magic_ds'] > 0) {
						$return	.= '	<div class="char_info">(DS +'.$item['int_magic_ds'].')</div>'.PHP_EOL;
					} else if ($item['int_magic_me'] > 0) {
						$return	.= '	<div class="char_info">(ME +'.$item['int_magic_me'].')</div>'.PHP_EOL;
					}*/
					$return	.= '</div>'.PHP_EOL;
				}
			}
			return $return;
		}

	}