<?php
/************************************************************************************
* Name:				Character Repository											*
* File:				Application\Controller\Character.php 							*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This contains pre-written functions that execute Database tasks	*
*					related to login information.									*
*																					*
* Creation Date:	17/09/2013														*
* Version:			1.13.0917														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller\Repository;

	//use Application\Controller\Repository\dbFunctions;

	class Character {

		/*
		Get Character by Id - getById($id)
			@param integer	- character Id
			@return format	- Mixed array
		*/
		public function getById($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $db->getRow('tb_character', '*', "id_user = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get Character's gold info  - getCharGold($id)
			@param integer	- character Id
			@return format	- Mixed array
		*/
		public function getCharGold($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $db->getRow('tb_character', 'int_gold', "id = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get Character by User Id - getCharByUserId($id)
			@param integer	- user Id
			@return format	- Mixed array
		*/
		public function getCharByUserId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $return = $db->getRow('tb_character', '*', "id_user = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get All Characters by Id - getAllCharsByUserId($id)
			@param integer	- user Id
			@return format	- Mixed array
		*/
		public function getAllCharsByUserId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $return = $db->getAllRows_Arr('tb_character', 'id, vc_name, int_xp', "id_user = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get all inventory contents by Char - getAllInventoryContentsByCharId($id)
			@param integer	- char Id
			@return format	- Mixed array
		*/
		public function getAllInventoryContentsByCharId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $return = $db->getAllRows_Arr('tb_inventory AS i JOIN tb_combat_item AS ci ON i.id_item = ci.id', 'ci.*', "id_character = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get all bag contents by Char - getAllBagContentsByCharId($id)
			@param integer	- char Id
			@return format	- Mixed array
		*/
		public function getAllBagContentsByCharId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $return = $db->getAllRows_Arr('tb_inventory AS i JOIN tb_combat_item AS ci ON i.id_item = ci.id', 'ci.*', "id_character = '{$id}' AND boo_bag = 1") : false;
			// Return
			return $return;
		}

		/*
		Get inventory combat contents by Char - getCombatInventoryContentsByCharId($id)
			@param integer	- char Id
			@return format	- Mixed array
		*/
		public function getCombatInventoryContentsByCharId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $return = $db->getAllRows_Arr('tb_inventory AS i JOIN tb_combat_item AS ci ON i.id_item = ci.id', 'ci.*', "id_character = '{$id}' AND boo_combat = 1") : false;
			// Return
			return $return;
		}

		/*
		Get bag combat contents by Char - getCombatBagContentsByCharId($id)
			@param integer	- char Id
			@return format	- Mixed array
		*/
		public function getCombatBagContentsByCharId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $return = $db->getAllRows_Arr('tb_inventory AS i JOIN tb_combat_item AS ci ON i.id_item = ci.id', 'ci.*', "id_character = '{$id}' AND boo_bag = 1 AND boo_combat = 1") : false;
			// Return
			return $return;
		}

		/*
		Get inventory non-combat contents by Char - getNonCombatInventoryContentsByCharId($id)
			@param integer	- char Id
			@return format	- Mixed array
		*/
		public function getNonCombatInventoryContentsByCharId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $return = $db->getAllRows_Arr('tb_inventory', 'id, id_item', "id_character = '{$id}' AND AND boo_combat = 0") : false;
			// Return
			return $return;
		}

		/*
		Get bag non-combat contents by Char - getNonCombatBagContentsByCharId($id)
			@param integer	- char Id
			@return format	- Mixed array
		*/
		public function getNonCombatBagContentsByCharId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $return = $db->getAllRows_Arr('tb_inventory', 'id, id_item', "id_character = '{$id}' AND boo_bag = 1 AND boo_combat = 0") : false;
			// Return
			return $return;
		}

		/*
		Find and get Weapon from the bag - findAndGetWeaponFromBagByItemId($combat_bag)
			@param integer	- Item Id
			@return format	- Mixed array
		*/
		public function findAndGetWeaponFromBagByItemId($combat_bag = false) {
			$return				= false;
			$items_ids			= false;
			if ($combat_bag) {
				foreach ($combat_bag as $item) {
					$items_ids	= ($items_ids) ? ','.$item['id'] : $item['id'];
				}
				
			}
			return $return;
		}

		/*
		Insert Character - insetCharacter($vc_name)
			@param integer	- User ID
			@param string	- Character's name
			@param integer	- Character's HP
			@return format	- Mixed array
		*/
		public function insetCharacter($id_user = false, $vc_name = false, $hp = 10) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= (($vc_name) && ($id_user)) ? $db->insertRow('tb_character', array($id_user, $vc_name, $hp), array('id_user', 'vc_name', 'int_hp')) : false;
			// Return
			return $return;
		}

		/*
		update Character - updateCharacter($character)
			@param string	- Character's array info
			@return format	- Mixed array
		*/
		public function updateCharacter($character) {
			// Database Connection
			$db			= $GLOBALS['db'];
			$return		= false;
			if ($character) {
				$id	= $character['id'];
				unset($character['id']);
				foreach ($character as $char) {
					$data[]	= $char;
				}
				$return	= $db->updateRow('tb_character', array('id_user', 'vc_name', 'int_hp', 'int_xp', 'int_gold'), $data, 'id = '.$id);
			}
			// Return
			return $return;
		}

	}