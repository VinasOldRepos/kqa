<?php
/************************************************************************************
* Name:				Character Model													*
* File:				Application\Model\User.php 										*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the Character's model.									*
*																					*
* Creation Date:	15/11/2012														*
* Version:			1.12.1115														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Model;

	class Character {

		public function charList($char_list = false) {
			$return	= false;
			if ($char_list) {
				$return		.= "<div>Click on your character's name to continue.</div><br /><br />";
				$return		.= '<div>'.PHP_EOL;
				foreach ($char_list as $char) {
					$return	.= '<div class="char_name" key="'.$char['id'].'">'.$char['vc_name'].'</div>'.PHP_EOL;
				}
				$return		.= '</div>'.PHP_EOL;
			}
			return $return;
		}

		public function listInventory($inventory = false, $wore = false){
			$return	= '(inventory empty)';
			if ($inventory) {
				foreach ($inventory as $item) {
					if (isset($item['vc_wearable'])) {
						if ($wore) {
							$show			= true;
							foreach ($wore as $weareable) {
								if (($show) && ($weareable['id'] == $item['id'])){
									$show	=  false;
									break;
								} else {
									$show	= true;
								}
							}
							$return			.= ($show) ? '<div class="item_name" key="'.$item['id'].'" place="'.$item['vc_wearable'].'">'.$item['vc_name'].'</div>'.PHP_EOL : false;
						} else {
							$return			.= '<div class="item_name" key="'.$item['id'].'" place="'.$item['vc_wearable'].'">'.$item['vc_name'].'</div>'.PHP_EOL;
						}
					} else {
						$return				.= '<div class="item_name" key="'.$item['id_inventory'].'">'.$item['vc_name'].'</div>'.PHP_EOL;
					}
				}
			}
			return $return;
		}

		public function listWore($wore = false) {
			$return			= false;
			if ($wore) {
				if ($wore['head']) {
					$return	.= '<div class="place" id="head" key="'.$wore['head']['id'].'">'.$wore['head']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="head">-</div>'.PHP_EOL;
				}
				if ($wore['neck']) {
					$return	.= '<div class="place" id="neck" key="'.$wore['neck']['id'].'">'.$wore['neck']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="neck">-</div>'.PHP_EOL;
				}
				if ($wore['chest']) {
					$return	.= '<div class="place" id="chest" key="'.$wore['chest']['id'].'">'.$wore['chest']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="chest">-</div>'.PHP_EOL;
				}
				if ($wore['back']) {
					$return	.= '<div class="place" id="back" key="'.$wore['back']['id'].'">'.$wore['back']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="back">-</div>'.PHP_EOL;
				}
				if ($wore['mainhand']) {
					$return	.= '<div class="place" id="mainhand" key="'.$wore['mainhand']['id'].'">'.$wore['mainhand']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="mainhand">-</div>'.PHP_EOL;
				}
				if ($wore['offhand']) {
					$return	.= '<div class="place" id="offhand" key="'.$wore['offhand']['id'].'">'.$wore['offhand']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="offhand">-</div>'.PHP_EOL;
				}
				if ($wore['rightfinger']) {
					$return	.= '<div class="place" id="rightfinger" key="'.$wore['rightfinger']['id'].'">'.$wore['rightfinger']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="rightfinger">-</div>'.PHP_EOL;
				}
				if ($wore['leftfinger']) {
					$return	.= '<div class="place" id="leftfinger" key="'.$wore['leftfinger']['id'].'">'.$wore['leftfinger']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="leftfinger">-</div>'.PHP_EOL;
				}
				if ($wore['legs']) {
					$return	.= '<div class="place" id="legs" key="'.$wore['legs']['id'].'">'.$wore['legs']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="legs">-</div>'.PHP_EOL;
				}
				if ($wore['feet']) {
					$return	.= '<div class="place" id="feet" key="'.$wore['feet']['id'].'">'.$wore['feet']['vc_name'].'</div>'.PHP_EOL;
				} else{
					$return	.= '<div class="place" id="feet">-</div>'.PHP_EOL;
				}
			}
			return $return;
		}

		public function listBag($bag = false) {
			$return	= false;
			if ($bag) {
				foreach ($bag as $item) {
					$return	.= '<div class="bagplace" key="'.$item['id_inventory'].'">'.$item['vc_name'].'</div>'.PHP_EOL;
				}
			}
			return $return;
		}

		public function listBagItems($bag = false) {
			$return	= false;
			if ($bag) {
				foreach ($bag as $item) {
					$return	.= '<div class="bag_item" key="'.$item['id_inventory'].'">'.$item['vc_name'].'</div>'.PHP_EOL;
				}
			} else {
				$return	.= '<div class="bag_item" style="text-align: center; width: 100%;">(no items in your bag)</div>'.PHP_EOL;
			}
			return $return;
		}

	}