<?php
/************************************************************************************
* Name:				Combat Repository												*
* File:				Application\Controller\Combat.php 								*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This contains pre-written functions that execute Database tasks	*
*					related to login information.									*
*																					*
* Creation Date:	19/09/2013														*
* Version:			1.13.0919														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller\Repository;

	//use Application\Controller\Repository\dbFunctions;

	class Combat {
		
		/*
		Get Monster by Id - getMonsterById($id)
			@param integer	- Monster Id
			@return format	- Mixed array
		*/
		public function getMonsterById($id = false) {
			// Database Connection
			$db					= $GLOBALS['db'];
			// Query and return
			$return	= ($id !== false) ? $db->getRow('tb_monster', '*', "id = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get Course Id by Map Id - getCourseIdByMapId($id)
			@param integer	- Map Id
			@return format	- Mixed array
		*/
		public function getCourseIdByMapId($id = false) {
			// Database Connection
			$db					= $GLOBALS['db'];
			// Query and return
			$return	= ($id !== false) ? $db->getRow('tb_areamap', 'id_course', "id = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get All Tiles in one step - getAllTilesInStep($id_areamap, $step)
			@param integer	- Monster Id
			@param integer	- Step
			@return format	- Mixed array
		*/
		public function getAllTilesInStep($id_areamap = false, $step = false) {
			// Database Connection
			$db					= $GLOBALS['db'];
			// Query and return
			$return	= (($id_areamap) && ($step)) ? $db->getRow('tb_encounter_areaorder', '*', "id_areamap = '{$id_areamap}' AND int_order = '{$step}'") : false;
			// Return
			return $return;
		}

		/*
		Get All Monsters in one step - getAllMonstersInRoom($id_areamap, $positions)
			@param integer	- Monster Id
			@param integer	- positions
			@return format	- Mixed array
		*/
		public function getAllMonstersInRoom($id_areamap = false, $positions = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Query and return
			$return		= (($id_areamap) && ($positions)) ? $db->getAllRows_Arr('tb_area_pos_monster AS apm JOIN tb_monster AS m ON apm.id_monster = m.id', 'm.*', "id_areamap = '{$id_areamap}' AND vc_pos IN ({$positions}) ORDER BY apm.id") : false;
			// Return
			return $return;
		}

		/*
		Count All Monsters in one step - countMonstersInRoom($id_areamap, $positions)
			@param integer	- Monster Id
			@param integer	- positions
			@return format	- Mixed array
		*/
		public function countMonstersInRoom($id_areamap = false, $positions = false) {
			// Database Connection
			$db			= $GLOBALS['db'];
			// Query and return
			$return		= (($id_areamap) && ($positions)) ? $db->getRow('tb_area_pos_monster', 'COUNT(*) as total', "id_areamap = '{$id_areamap}' AND vc_pos IN ({$positions})") : false;
			$return		= ($return) ? $return['total'] : false;
			// Return
			return $return;
		}

		/*
		Encounter Area Log - encounterLog($id_areamap, $id_character)
			@param integer	- Area Map id
			@param integer	- Character ID
			@return format	- boolean
		*/
		public function encounterLog($id_areamap = false, $id_character = false) {
			// Database Connection
			$db				= $GLOBALS['db'];
			if (($id_areamap) && ($id_character)) {
				// Query and return
				$encounter	= $db->getRow('tb_encounter_log', 'id, int_visits', "id_areamap = {$id_areamap} AND id_character = {$id_character}");
				if ($encounter) {
					$return	= ($db->updateRow('tb_encounter_log', array('id_areamap', 'id_character', 'int_visits'), array($id_areamap, $id_character, $encounter['int_visits'] + 1), 'id = '.$encounter['id'])) ? true : false;
				} else {
					$return	= ($db->insertRow('tb_encounter_log', array($id_areamap, $id_character, 1), array('id_areamap', 'id_character', 'int_visits'))) ? true : false;
				};
			}
			// Return
			return $return;
		}

	}