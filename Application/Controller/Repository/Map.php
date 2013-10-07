<?php
/************************************************************************************
* Name:				Map Repository													*
* File:				Application\Controller\Map.php 									*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This contains pre-written functions that execute Database tasks	*
*					related to login information.									*
*																					*
* Creation Date:	04/07/2013														*
* Version:			1.13.0606														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller\Repository;

	//use Application\Controller\Repository\dbFunctions;

	class Map {

		/*
		Get Map by Id - getMapById($id)
			@param integer	- World Id
			@return format	- Mixed array
		*/
		public function getMapById($id = false) {
			// Database Connection
			$db					= $GLOBALS['db'];
			// Initialize variables
			$return				= false;
			// Query set up
			$return			= ($id) ? $return = $db->getRow('tb_areamap', '*', "id = '{$id}'") : false;
			// Return
			return $return;
		}

		/*
		Get map info by Id - getMapInfoById($id)
			@param integer	- Area Id
			@return format	- Mixed array
		*/
		public function getMapInfoById($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query set up
			$return	= ($id) ? $db->getRow('tb_areamap', 'id, boo_encounter, id_areatype, vc_name', "id = '{$id}'") : false;
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
		Get Map by Id - getAllMouseOversByMapId($ids)
			@param string	- Maps Ids
			@return format	- Mixed array
		*/
		public function getAllMouseOversByMapId($ids = false) {
			// Database Connection
			$db					= $GLOBALS['db'];
			// Initialize variables
			$return				= false;
			// Query set up
			$return			= ($ids) ? $return = $db->getAllRows_Arr('tb_areamap', 'id, vc_mouseover', "id IN ({$ids})") : false;
			// Return
			return $return;
		}

		/*
		Get Ordered tiles by map id - getOrderedTilesByMapId($id_areamap, $step)
			@param integer	- Area Id
			@param integer	- Step
			@return format	- Mixed array
		*/
		public function getOrderedTilesByMapId($id_areamap = false, $step = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query
			$return	= (($id_areamap) && ($step)) ? $db->getAllRows_Arr('tb_encounter_areaorder', '*', "id_areamap = {$id_areamap} AND int_order <= {$step}") : false;
			// Return
			return $return;
		}

		/*
		Get encounter area total steps by map id - getTotalStepsByMapId($id_areamap)
			@param integer	- Area Id
			@return format	- Mixed array
		*/
		public function getTotalStepsByMapId($id_areamap = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query
			$return	= ($id_areamap) ? $db->getRow('tb_encounter_areaorder', 'MAX(int_order) as tot_steps', "id_areamap = {$id_areamap}") : false;
			// Return
			return $return;
		}

		/*
		Get navigation Links By Area Id - getNavigationLinkByAreaId($id)
			@param integer	- Area ID
			@return format	- Mixed array
		*/
		public function getNavigationLinkByAreaId($id = false) {
			// Database Connection
			$db				= $GLOBALS['db'];
			// Query set up
			$return			= ($id) ? $db->getAllRows_Arr('tb_world_navigation', '*', 'id_map_orign = '.$id) : false;
			// Return
			return $return;
		}

		/*
		Get Area Info By Map Id - getAreaInfoByMapId($id)
			@param integer	- Map ID
			@return format	- Mixed array
		*/
		public function getAreaInfoByMapId($id = false) {
			// Database Connection
			$db				= $GLOBALS['db'];
			// Query set up
			$return			= ($id) ? $db->getRow('tb_area', '*', 'id_areamap = '.$id) : false;
			// Return
			return $return;
		}

		/*
		Get parent map id and type by map id- getParentMapInfoIdByMapId($id)
			@param integer	- Area Id
			@return format	- Mixed array
		*/
		public function getParentMapInfoIdByMapId($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			// Query
			$return	= ($id) ? $db->getRow('tb_map_link_icon AS mli JOIN tb_areamap AS am ON mli.id_map_orign = am.id', 'am.id, am.boo_encounter', "id_map_target = {$id}") : false;
			// Return
			return $return;
		}

		/*
		Get local parent map id by map id- getLocalParentMapIdByMapId($id)
			@param integer	- Area Id
			@return format	- Mixed array
		*/
		public function getLocalParentMapIdByMapId($id = false) {
			// Database Connection
			$db		= $GLOBALS['db'];
			$local	= false;
			$return	= false;
			if ($id) {
				while (!$local) {
					// Query
					$res		= $db->getRow('tb_map_link_icon AS mli JOIN tb_areamap AS am ON mli.id_map_orign = am.id', 'am.id, am.boo_encounter', "id_map_target = {$id}");
					if (($res) && ($res['boo_encounter'] == 0)) {
						$return	= $res['id'];
						$local	= true;
					} else {
						$id		= $res['id'];
					}
				}
			}
			// Return
			return $return;
		}

	}