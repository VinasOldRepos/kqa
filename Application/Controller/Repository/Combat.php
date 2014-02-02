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
			$local			= false;
			$id_localmap	= false;
			if (($id_areamap) && ($id_character)) {
				// Query and return
				$encounter	= $db->getRow('tb_encounter_log', 'id, int_visits', "id_areamap = {$id_areamap} AND id_character = {$id_character}");
				if ($encounter) {
					$return	= ($db->updateRow('tb_encounter_log', array('id_areamap', 'id_character', 'int_visits'), array($id_areamap, $id_character, $encounter['int_visits'] + 1), 'id = '.$encounter['id'])) ? true : false;
				} else {
					$id		= $id_areamap;
					while (!$local) {
						// Query
						$res	= $db->getRow('tb_map_link_icon AS mli JOIN tb_areamap AS am ON mli.id_map_orign = am.id', 'am.id, am.boo_encounter', "id_map_target = {$id}");
						if (($res) && ($res['boo_encounter'] == 0)) {
							$id_localmap	= $res['id'];
							$local			= true;
						} else {
							$id				= $res['id'];
						}
					}
					$return	= ($db->insertRow('tb_encounter_log', array($id_areamap, $id_character, 1), array('id_areamap', 'id_character', 'int_visits'))) ? true : false;
					// If playe is in the start area
					if ($id_localmap == 101) {
						$parent	= $db->getRow('tb_map_link_icon AS mli JOIN tb_areamap AS am ON mli.id_map_orign = am.id', 'am.id, am.boo_encounter', "id_map_target = {$id_areamap}");
						if ($parent['boo_encounter'] == 1) {
							$db->insertRow('tb_encounter_log', array($parent['id'], $id_character, 1), array('id_areamap', 'id_character', 'int_visits'));
						}
						$links	= $this->getLinksIconsByAreaId($id_localmap);
						foreach ($links as $link) {
							$childmaps[]	= $link['id_map_target'];
						}
						$gonethru			= $this->getGoneThruFromList($id_character, $childmaps);
						// If player has gone thru all start area dungeons
						if ((count($links) == count($gonethru))) {
							// Flag user
							$db->updateRow('tb_character', array('boo_tutorial'), array(1), 'id = '.$id_character);
						}
					}
				};
			}
			// Return
			return $return;
		}

		/*
		Get links and icons by id - getLinksIconsByAreaId($id)
			@param integer	- Area Id
			@return format	- Mixed array
		*/
		public function getLinksIconsByAreaId($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query
			$return	= ($id) ? $db->getAllRows_Arr('tb_map_link_icon AS mli LEFT JOIN tb_icon AS i ON mli.id_icon = i.id', 'mli.*, i.vc_path', "id_map_orign = {$id}") : false;
			// Return
			return $return;
		}

		/*
		Get the Maps the user has gone thru from a given list - getGoneThruFromList($id_char, $childmaps)
			@param integer	- Character ID
			@param array	- Child maps list Id
			@return format	- Mixed array
		*/
		public function getGoneThruFromList($id_char = false, $childmaps = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			$return	= false;
			if (($id_char) && ($childmaps)) {
				$childmaps	= implode(', ', $childmaps);
				// Query
				$return		= $db->getAllRows_Arr('tb_encounter_log', 'id_areamap', "id_character = {$id_char} AND id_areamap IN ({$childmaps})");
			}
			// Return
			return $return;
		}

	}