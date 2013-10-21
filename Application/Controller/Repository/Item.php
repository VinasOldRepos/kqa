<?php
/************************************************************************************
* Name:				Item Repository													*
* File:				Application\Controller\Item.php 								*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This contains pre-written functions that execute Database tasks	*
*					related to login information.									*
*																					*
* Creation Date:	23/08/2013														*
* Version:			1.13.0823														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller\Repository;

	use Application\Controller\Repository\dbFunctions;

	class Item {
		
		/*
		Get Combat Item by Id - getCombatById($id)
			@param integer	- Item Id
			@return format	- Mixed array
		*/
		public function getCombatById($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= ($id) ? $db->getRow('tb_combat_item', '*', "id = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get Non-Combat Item by Id - getNonCombatById($id)
			@param integer	- Item Id
			@return format	- Mixed array
		*/
		public function getNonCombatById($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= ($id) ? $db->getRow('tb_noncombat_item', '*', "id = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get a random Item by level - getRandItemByLevel($level)
			@param integer	- level
			@return format	- Mixed array
		*/
		public function getRandItemByLevel($level = false) {
			// Database Connection
			$db				= $GLOBALS['db'];
			// Variables
			$return			= false;
			$items			= false;
			$noncombat		= ($level) ? $db->getAllRows_Arr('tb_noncombat_item', 'id', "int_level = '{$level}'") : false;
			$combat			= ($level) ? $db->getAllRows_Arr('tb_combat_item', 'id', "int_level = '{$level}'") : false;
			// Get non combat items
			if ($noncombat) {
				for ($i = 0; $i < count($noncombat); $i++) {
					$items[$i]['type']	= 'noncombat';
					$items[$i]['id']	= $noncombat[$i]['id'];
				}
			}
			// Get combat Items
			$total	= ($items) ? count($items) : 0;
			if ($noncombat) {
				for ($i = 0; $i < count($combat); $i++) {
					$items[$total + $i]['type']	= 'combat';
					$items[$total + $i]['id']	= $combat[$i]['id'];
				}
			} else {
				for ($i = 0; $i < count($combat); $i++) {
					$items[$i]['type']	= 'combat';
					$items[$i]['id']	= $combat[$i]['id'];
				}
			}
			// Get a random item from the list
			if ($items) {
				$tot_items	= count($items);
				$item		= $items[rand(1, $tot_items - 1)];
				// Get item info
				if ($item['type'] == 'combat') {
					$return	= $db->getRow('tb_combat_item', '*', 'id = '.$item['id']);
				} else {
					$return	= $db->getRow('tb_noncombat_item', '*', 'id = '.$item['id']);
				}
			}
			// Return
			return $return;
		}

		/*
		Get All Combat Item Types - getAllCombatItemTypes()
			@return format	- Mixed array
		*/
		public function getAllCombatItemTypes() {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= $db->getAllRows_Arr('tb_combat_item_type', '*', "1 ORDER BY vc_name");
			// Return
			return $return;
		}

		/*
		Get All Non-Combat Item Types - getAllNonCombatItemTypes()
			@return format	- Mixed array
		*/
		public function getAllNonCombatItemTypes() {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= $db->getAllRows_Arr('tb_noncombat_item_type', '*', "1 ORDER BY vc_name");
			// Return
			return $return;
		}

		/*
		Get All Users - getAllCombatItems($max, $num_page, $ordering, $direction)
			@param integer	- Max rows
			@param integer	- Page number
			@param integer	- Ordering
			@param integer	- Ordering direction
			@return format	- Mixed array
		*/
		public function getAllCombatItems($max = 20, $num_page = 1, $ordering = 'i.id', $direction = 'ASC') {
			$dbFunctions	= new dbFunctions();
			// Database Connection
			$db				= $GLOBALS['db'];
			// Initialize variables
			$return			= false;
			// Query set up
			$table			= 'tb_combat_item AS i JOIN tb_combat_item_type AS t ON (i.id_type = t.id)';
			$select_what	= 'i.*, t.vc_name as vc_type';
			$conditions		= "1";
			$return			= $dbFunctions->getPage($select_what, $table, $conditions, $max, $num_page, $ordering, $direction);
			// Return
			return $return;
		}

		/*
		Get All Users - getAllCombatItemsByType($id)
			@param integer	- Combat type ID
			@return format	- Mixed array
		*/
		public function getAllCombatItemsByType($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= ($id) ? $db->getAllRows_Arr('tb_combat_item', '*', "id_type = {$id}") : false;
			// Return
			return $return;
		}

		/*
		Get Searched Combat Items - getSearchedCombat($max, $num_page, $ordering, $direction)
			@param string	- Searched string
			@param integer	- Max rows
			@param integer	- Page number
			@param integer	- Ordering
			@param integer	- Ordering direction
			@return format	- Mixed array
		*/
		public function getSearchedCombat($vc_search = false, $max = 20, $num_page = 1, $ordering = 'i.id', $direction = 'ASC') {
			$dbFunctions		= new dbFunctions();
			// Database Connection
			$db					= $GLOBALS['db'];
			// Initialize variables
			$return				= false;
			if ($vc_search) {
				// Query set up
				$table			= 'tb_combat_item AS i JOIN tb_combat_item_type AS t ON (i.id_type = t.id)';
				$select_what	= 'i.*, t.vc_name as vc_type';
				$conditions		= "i.id LIKE '%{$vc_search}%' OR i.vc_name LIKE '%{$vc_search}%' OR t.vc_name LIKE '%{$vc_search}%'";
				$return			= $dbFunctions->getPage($select_what, $table, $conditions, $max, $num_page, $ordering, $direction);
			}
			// Return
			return $return;
		}

		/*
		Get All Users - getAllNonCombatItems($vc_search, $max, $num_page, $ordering, $direction)
			@param integer	- Max rows
			@param integer	- Page number
			@param integer	- Ordering
			@param integer	- Ordering direction
			@return format	- Mixed array
		*/
		public function getAllNonCombatItems($max = 20, $num_page = 1, $ordering = 'i.id', $direction = 'ASC') {
			$dbFunctions	= new dbFunctions();
			// Database Connection
			$db				= $GLOBALS['db'];
			// Initialize variables
			$return			= false;
			// Query set up
			$table			= 'tb_noncombat_item AS i JOIN tb_noncombat_item_type AS t ON (i.id_type = t.id)';
			$select_what	= 'i.*, t.vc_name as vc_type';
			$conditions		= "1";
			$return			= $dbFunctions->getPage($select_what, $table, $conditions, $max, $num_page, $ordering, $direction);
			// Return
			return $return;
		}

		/*
		Get Searched Non-Combat Items - getSearchedNonCombat($vc_search, $max, $num_page, $ordering, $direction)
			@param string	- Searched string
			@param integer	- Max rows
			@param integer	- Page number
			@param integer	- Ordering
			@param integer	- Ordering direction
			@return format	- Mixed array
		*/
		public function getSearchedNonCombat($vc_search = false, $max = 20, $num_page = 1, $ordering = 'i.id', $direction = 'ASC') {
			$dbFunctions		= new dbFunctions();
			// Database Connection
			$db					= $GLOBALS['db'];
			// Initialize variables
			$return				= false;
			if ($vc_search) {
				// Query set up
				$table			= 'tb_noncombat_item AS i JOIN tb_noncombat_item_type AS t ON (i.id_type = t.id)';
				$select_what	= 'i.*, t.vc_name as vc_type';
				$conditions		= "i.id LIKE '%{$vc_search}%' OR i.vc_name LIKE '%{$vc_search}%' OR t.vc_name LIKE '%{$vc_search}%'";
				$return			= $dbFunctions->getPage($select_what, $table, $conditions, $max, $num_page, $ordering, $direction);
			}
			// Return
			return $return;
		}

	}