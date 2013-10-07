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

	}