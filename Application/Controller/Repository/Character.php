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
		Get Character by Id - getCharById($id)
			@param integer	- character Id
			@return format	- Mixed array
		*/
		public function getCharById($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $db->getRow('tb_character', '*', "id = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get Character by User Id - getCharByUserId($id)
			@param integer	- User Id
			@return format	- Mixed array
		*/
		public function getCharByUserId($id = false) {
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
		Get Non-Combat by Id - getItemById($id)
			@param integer	- Item Id
			@return format	- Mixed array
		*/
		public function getItemById($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query and return
			$return	= ($id !== false) ? $db->getRow('tb_noncombat_item', '*', "id = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get Non-Combat by Id - getNonCombatItemByInventoryId($id)
			@param integer	- Inventory Id
			@return format	- Mixed array
		*/
		public function getNonCombatItemByInventoryId($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query and return
			$return	= ($id !== false) ? $db->getRow('tb_inventory AS i JOIN tb_noncombat_item AS nci ON i.id_item = nci.id', 'nci.*', "i.id = '{$id}'") : false;
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
		Get Character ID by User Id - getCharIdByUserId($id)
			@param integer	- user Id
			@return format	- Mixed array
		*/
		public function getCharIdByUserId($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= $db->getRow('tb_character', 'id', "id_user = '{$id}'");
			$return		= ($return) ? $return['id'] : false;
			// Return
			return $return;
		}

		/*
		Get XP by User and course Id - getXpByCharCourseId($id, id_course)
			@param integer	- char Id
			@param integer	- course Id
			@return format	- Mixed array
		*/
		public function getXpByCharCourseId($id = false, $id_course = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			if (($id) && ($id_course)) {
				// Query set up
				$return	= $db->getRow('tb_course_xp', 'int_xp', "id_character = '{$id}' AND id_course = '{$id_course}'");
				$return	= ($return) ? $return['int_xp'] : false;
			}
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
			$return		= ($id) ? $db->getAllRows_Arr('tb_character', 'id, vc_name, int_xp', "id_user = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get all inventory contents by Char - getAllWoreItems($id)
			@param integer	- char Id
			@return format	- Mixed array
		*/
		public function getAllWoreItems($id = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			// Query set up
			$return		= ($id) ? $db->getRow('tb_wearable', '*', "id_character = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get all Combat Items - getAllCombatItems($ids)
			@param array	- item's id
			@return format	- Mixed array
		*/
		public function getAllCombatItems($ids = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Initialize variables
			$return		= false;
			$condition	= false;
			if ($ids) {
				foreach ($ids as $id) {
					if ($condition) {
						$condition	.= ($id > 0) ? ', '.$id : '';
					} else {
						$condition	.= ($id > 0) ? $id : '';
					}
				}
				// Query set up
				$return				= ($condition) ? $db->getAllRows_Arr('tb_combat_item', '*', "id IN ({$condition})") : false;
			} else {
				// Query set up
				$return				= $db->getAllRows_Arr('tb_combat_item', '*', "1");
			}
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
			$combat		= ($id) ? $db->getAllRows_Arr('tb_inventory AS i JOIN tb_combat_item AS ci ON i.id_item = ci.id', 'i.id AS id_inventory, ci.*, i.boo_bag', "id_character = '{$id}' AND boo_combat = 1 ORDER BY ci.vc_name") : false;
			$noncombat	= ($id) ? $db->getAllRows_Arr('tb_inventory AS i JOIN tb_noncombat_item AS nci ON i.id_item = nci.id', 'i.id AS id_inventory, nci.*, i.boo_bag', "id_character = '{$id}' AND boo_combat = 0 ORDER BY nci.vc_name") : false;
			if (($combat) && ($noncombat)) {
				$return	= array_merge($combat, $noncombat);
			} else if ($combat) {
				$return	= $combat;
			} else if($noncombat) {
				$return	= $noncombat;
			}
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
			$return		= ($id) ? $db->getAllRows_Arr('tb_inventory AS i JOIN tb_noncombat_item AS nci ON i.id_item = nci.id', 'i.id AS id_inventory, nci.*', "id_character = '{$id}' AND boo_bag = 1") : false;
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
			$return		= ($id) ? $db->getAllRows_Arr('tb_inventory AS i JOIN tb_combat_item AS ci ON i.id_item = ci.id', 'ci.*', "id_character = '{$id}' AND boo_combat = 1") : false;
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
			$return		= ($id) ? $db->getAllRows_Arr('tb_inventory AS i JOIN tb_combat_item AS ci ON i.id_item = ci.id', 'ci.*', "id_character = '{$id}' AND boo_bag = 1 AND boo_combat = 1") : false;
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
			$return		= ($id) ? $db->getAllRows_Arr('tb_inventory', 'id, id_item', "id_character = '{$id}' AND AND boo_combat = 0") : false;
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
			$return		= ($id) ? $db->getAllRows_Arr('tb_inventory', 'id, id_item', "id_character = '{$id}' AND boo_bag = 1 AND boo_combat = 0") : false;
			// Return
			return $return;
		}

		/*
		Get all inventory contents by Char - getTalismanInBagIdInventory($id)
			@param integer	- char Id
			@return format	- Mixed array
		*/
		public function getTalismanInBagIdInventory($id_char = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= ($id_char) ? $db->getRow('tb_inventory', 'id', "id_item = 13 AND id_character = '{$id_char}' AND boo_bag = 1 AND boo_combat = 0 ORDER BY id") : false;
			$return	= ($return) ? $return['id'] : false;
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
			$return	= (($vc_name) && ($id_user) && ($db->insertRow('tb_character', array($id_user, $vc_name, $hp), array('id_user', 'vc_name', 'int_hp')))) ? $db->last_id() : false;
			// Return
			return $return;
		}

		/*
		Insert first xp for course - insertXpByCharCourseId($id_char, $id_course, $xp)
			@param integer	- Character ID
			@param integer	- Course id
			@param integer	- xp
			@return format	- Mixed array
		*/
		public function insertXpByCharCourseId($id_char = false, $id_course = false, $xp = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= (($id_char) && ($id_course) && ($xp) && ($db->insertRow('tb_course_xp', array($id_char, $id_course, $xp), array('id_character', 'id_course', 'int_xp')))) ? $xp : false;
			// Return
			return $return;
		}

		/*
		Insert Character - newWearable($id_character)
			@param integer	- Character id
			@return format	- boolean
		*/
		public function newWearable($id_character = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Query set up
			$data[]		= $id_character;
			$data[]		= 0;
			$data[]		= 0;
			$data[]		= 0;
			$data[]		= 0;
			$data[]		= 0;
			$data[]		= 0;
			$data[]		= 0;
			$data[]		= 0;
			$data[]		= 0;
			$data[]		= 0;
			$fields[]	= 'id_character';
			$fields[]	= 'id_combatitem_head';
			$fields[]	= 'id_combatitem_neck';
			$fields[]	= 'id_combatitem_chest';
			$fields[]	= 'id_combatitem_back';
			$fields[]	= 'id_combatitem_mainhand';
			$fields[]	= 'id_combatitem_offhand';
			$fields[]	= 'id_combatitem_rightfinger';
			$fields[]	= 'id_combatitem_leftfinger';
			$fields[]	= 'id_combatitem_legs';
			$fields[]	= 'id_combatitem_feet';
			$return		= ($id_character) ? $db->insertRow('tb_wearable', $data, $fields) : false;
			// Return
			return $return;
		}

		/*
		Insert Item to Inventory - saveItemtoInventory($id_char, $id_item, $boo_bag, $boo_combat)
			@return format	- Mixed array
		*/
		public function saveItemtoInventory($id_char = false, $id_item = false, $boo_bag = 0, $boo_combat = 0) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= (($id_char) && ($id_item)) ? $db->insertRow('tb_inventory', array($id_char, $id_item, $boo_bag, $boo_combat), array('id_character', 'id_item', 'boo_bag', 'boo_combat')) : false;
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
				$return	= $db->updateRow('tb_character', array('id_user', 'vc_name', 'int_hp', 'int_xp', 'int_gold', 'boo_tutorial'), $data, 'id = '.$id);
			}
			// Return
			return $return;
		}

		/*
		update xp points - updateXpByCharCourseId($id_char, $id_course, $xp)
			@param integer	- Character id
			@param integer	- Course id
			@param integer	- xp
			@return format	- Mixed array
		*/
		public function updateXpByCharCourseId($id_char = false, $id_course = false, $xp = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			$return		= false;
			if (($id_char) && ($id_course) && ($xp)) {
				$return	= $db->updateRow('tb_course_xp', array('int_xp'), array($xp), 'id_character = '.$id_char.' AND id_course = '.$id_course);
			}
			// Return
			return $return;
		}

		/*
		Place wearable item - placeWearable($id, $place)
			@param integer	- Character ID
			@param integer	- Combat item id
			@param string	- Place in the body
			@return format	- Mixed array
		*/
		public function placeWearable($id_char = false, $id = false, $place = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			$return		= false;
			if (($id_char !== false) && ($id !== false) && ($place !== false)) {
				$data[]				= $id;
				switch ($place) {
					case 'head' :
						$field[]	= 'id_combatitem_head';
						break;
					case 'neck' :
						$field[]	= 'id_combatitem_neck';
						break;
					case 'chest' :
						$field[]	= 'id_combatitem_chest';
						break;
					case 'back' :
						$field[]	= 'id_combatitem_back';
						break;
					case 'mainhand' :
						$field[]	= 'id_combatitem_mainhand';
						break;
					case 'offhand' :
						$field[]	= 'id_combatitem_offhand';
						break;
					case 'rightfinger' :
						$field[]	= 'id_combatitem_rightfinger';
						break;
					case 'leftfinger' :
						$field[]	= 'id_combatitem_leftfinger';
						break;
					case 'legs' :
						$field[]	= 'id_combatitem_legs';
						break;
					case 'feet' :
						$field[]	= 'id_combatitem_feet';
						break;
					case 'bothhands' :
						$data[]		= $id;
						$field[]	= 'id_combatitem_mainhand';
						$field[]	= 'id_combatitem_offhand';
						break;
					default:
						$field		= false;
						break;
				}
				$return	= $db->updateRow('tb_wearable', $field, $data, 'id_character = '.$id_char);
			}
			// Return
			return $return;
		}

		/*
		Place bag item - placeBag($id)
			@param integer	- Inventory ID
			@return format	- Mixed array
		*/
		public function placeBag($id = false) {
			$db		= $GLOBALS['db'];
			$return	= ($id) ? $db->updateRow('tb_inventory', array('boo_bag'), array(1), 'id = '.$id) : false;
		}

		/*
		Remove bag item - removeBag($id)
			@param integer	- Inventory ID
			@return format	- Mixed array
		*/
		public function removeBag($id = false) {
			$db		= $GLOBALS['db'];
			$return	= ($id) ? $db->updateRow('tb_inventory', array('boo_bag'), array(0), 'id = '.$id) : false;
		}

		/*
		Remove bag item - removeNonCombatInventory($id_char, $id_item)
			@param integer	- Character ID
			@param integer	- Non-combat item id
			@return format	- Mixed array
		*/
		public function removeNonCombatInventory($id_char = false, $id_item = false) {
			$db		= $GLOBALS['db'];
			$return	= (($id_char) && ($id_item)) ? $db->deleteRow('tb_inventory', 'id_character = '.$id_char.' AND id_item = '.$id_item.' AND boo_bag = 1 AND boo_combat = 0') : false;
			return $return;
		}

		/*
		Remove bag item - removeFromInventory($id)
			@param integer	- Inventory id
			@return format	- Mixed array
		*/
		public function removeFromInventory($id = false) {
			$db		= $GLOBALS['db'];
			$return	= ($id) ? $db->deleteRow('tb_inventory', 'id = '.$id) : false;
			return $return;
		}

		/*
		Empty Bag - emptyBag($id_char)
			@param integer	- Character ID
			@return format	- boolean
		*/
		public function emptyBag($id_char = false) {
			$db		= $GLOBALS['db'];
			$return	= ($id_char) ? $db->updateRow('tb_inventory', array('boo_bag'), array(0), 'id_character = '.$id_char) : false;
		}

	}