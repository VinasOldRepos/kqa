<?php
/************************************************************************************
* Name:				Maps Controller													*
* File:				Application\Controller\MapsController.php 						*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This file controls Map related information.						*
*																					*
* Creation Date:	04/07/2013														*
* Version:			1.13.0704														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller;

	// Framework Classes
	use SaSeed\View;
	use SaSeed\Session;
	//use SaSeed\General;

	// Model Classes
	//use Application\Model\Menu;
	//use Application\Model\Pager;
	use Application\Model\Map						as ModMap;

	// Repository Classes
	use Application\Controller\Repository\Map		as RepMap;
	use Application\Controller\Repository\Question	as RepQuestion;
	use Application\Controller\Repository\Combat	as RepCombat;
	use Application\Controller\Repository\Character	as RepCharacter;

	// Other Classes
	use Application\Controller\LogInController		as LogIn;

	class MapsController{

		public function __construct() {
			// Start session
			Session::start();
			// Check if user is Logged
			$SesUser					= LogIn::checkLogin();
			if (!$SesUser) {
				// Redirect to login area when not
				header('location: '.URL_PATH.'/LogIn/');
			} else {
				// Define JSs e CSSs utilizados por este controller
				$GLOBALS['this_js']		= '<script type="text/javascript" src="'.URL_PATH.'/Application/View/js/scripts/maps.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_js']		.= '<script type="text/javascript" src="'.URL_PATH.'/Application/View/js/scripts/combat.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_js']		.= '<script type="text/javascript" src="'.URL_PATH.'/Application/View/js/libs/jquery.fancybox-1.3.4.pack.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	= '<link rel="stylesheet" href="'.URL_PATH.'/Application/View/css/maps.css" type="text/css" media="screen" />'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	.= '<link rel="stylesheet" href="'.URL_PATH.'/Application/View/css/combat.css" type="text/css" media="screen" />'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	.= '<link href="'.URL_PATH.'/Application/View/css/jquery.fancybox-1.3.4.css" rel="stylesheet">'.PHP_EOL;	// Se não houver, definir como vazio ''
			}
		}

		/*
		Prints out main home page - Sophia()
			@return format	- print
		*/
		public static function Sophia() {
			// Add classes
			$RepMap			= new RepMap();
			$RepCharacter	= new RepCharacter();
			$ModMap			= new ModMap();
			// Initialize variables
			$id_areamap		= (!isset($id_areamap)) ? 45 : $id_areamap;
			$map			= false;
			$area_name		= false;
			$mouseovers		= false;
			$ids			= false;
			$gold			= false;
			$tokens			= false;
			$user			= Session::getVar('user');
			if (($id_areamap) && ($user)) {
				// Get char info
				$character			= $RepCharacter->getCharByUserId($user['id']);
				$character			= ($character) ? $character['vc_name'] : false;
				// Load World Map info
				$map				= $RepMap->getMapById($id_areamap);
				if ($map) {
					// Get info
					//$tokens		= $user['int_token'];
					$tokens			= 0;
					$gold			= ($gold = $RepCharacter->getById($user['id'])) ? $gold['int_gold'] : '0';
					// Load Parent map's info
					$parent			= $RepMap->getParentMapInfoIdByMapId($id_areamap);
					$id_parentmap	= ($parent) ? $parent['id'] : false;
					// Get linking info
					$links			= $RepMap->getLinksIconsByAreaId($id_areamap);
					$navigation		= $RepMap->getNavigationLinkByAreaId($id_areamap);
					$area_name		= $map['vc_name'];
					// Get mouseover texts
					if ($links) {
						foreach ($links as $link) {
							$ids	= ($ids) ? $ids.','.$link['id_map_target'] : $link['id_map_target'];
						}
						$mouseovers	= $RepMap->getAllMouseOversByMapId($ids);
					}
					if ($id_areamap <= 100) {
						// Model world
						$map		= $ModMap->world($map, $links, $mouseovers, $navigation);
					} else {
						// Model world
						$map		= $ModMap->map($map, $id_parentmap, 'world', $links, $mouseovers);
					}
				}
			}
			// Prepare return
			View::set('id_areamap',	$id_areamap);
			View::set('map',		$map);
			View::set('area_name',	$area_name);
			View::set('tokens',		$tokens);
			View::set('gold',		$gold);
			View::set('character',	$character);
			// Return
			View::render('map');
		}

		/*
		Prints out local Map - loadLocalMap()
			@return format	- print json
		*/
		public function loadLocalMap() {
			// Declare Classes
			$RepMap			= new RepMap();
			$ModMap			= new ModMap();
			// Initialize variables
			$return			= false;
			$mouseovers		= false;
			$ids			= false;
			$id_areamap		= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			$id_parentmap	= (isset($_POST['id_parentmap'])) ? trim($_POST['id_parentmap']) : false;
			// If values were sent
			if ($id_areamap) {
				// Load Parent map's info
				if (!$id_parentmap) {
					$parent			= $RepMap->getParentMapInfoIdByMapId($id_areamap);
					$id_parentmap	= ($parent) ? $parent['id'] : false;
				}
				// Load Map info
				$map				= $RepMap->getMapById($id_areamap);
				// Get linking info
				$links				= $RepMap->getLinksIconsByAreaId($id_areamap);
				if ($links) {
					foreach ($links as $link) {
						$ids		= ($ids) ? $ids.','.$link['id_map_target'] : $link['id_map_target'];
					}
					$mouseovers		= $RepMap->getAllMouseOversByMapId($ids);
				}
				// Get Map level
				$level				= ($level = $RepMap->getAreaInfoByMapId($id_areamap)) ? $level['int_level'] : false;
				// Model Return
				if ($map) {
					$return['id_parentmap']	= $id_parentmap;
					$return['area_name']	= $map['vc_name'];
					$return['level']		= $level;
					$return['map']			= $ModMap->map($map, $id_parentmap, 'world', $links, $mouseovers);
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		/*
		Prints out encounter area Map - loadEncounterArea()
			@return format	- print json
		*/
		public function loadEncounterArea() {
			// Declare Classes
			$RepMap				= new RepMap();
			$RepCombat			= new RepCombat();
			$ModMap				= new ModMap();
			// Initialize variables
			$return				= false;
			$vis_tiles			= false;
			$id_areamap			= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			$id_parentmap		= (isset($_POST['id_parentmap'])) ? trim($_POST['id_parentmap']) : false;
			$current_monster	= (isset($_POST['current_monster'])) ? trim($_POST['current_monster']) : 1;
			$step				= (isset($_POST['step'])) ? trim($_POST['step']) : 1;
			// If values were sent
			if (($id_areamap) && ($step)) {
				// Get parent map type
				$parent				= $RepMap->getMapInfoById($id_parentmap);
				$parent_areatype	= (($parent) && ($parent['boo_encounter'] == 1)) ? 'encounter' : 'local';
				// Get visible tiles
				$tiles				= $RepMap->getOrderedTilesByMapId($id_areamap, $step);
				$tot_steps			= ($tot_steps = $RepMap->getTotalStepsByMapId($id_areamap)) ? $tot_steps['tot_steps'] : false;
				foreach ($tiles as $tile) {
					if ($vis_tiles) {
						$vis_tiles	= $vis_tiles.','.$tile['vc_tiles'];
					} else {
						$vis_tiles	= $tile['vc_tiles'];
					}
				}
				$vis_tiles			= ($vis_tiles) ? explode(',', $vis_tiles) : false;
				// Load Dungeon Map
				$map				= $RepMap->getMapById($id_areamap);
				// Get linking info
				$links				= $RepMap->getLinksIconsByAreaId($id_areamap);
				// Get Map level
				$level				= ($level = $RepMap->getAreaInfoByMapId($id_areamap)) ? $level['int_level'] : false;
				// Get all tiles of this step
				$tiles				= ($tiles = $RepCombat->getAllTilesInStep($id_areamap, $step)) ? $tiles['vc_tiles'] : false;
				// Get total of monsters in the current room
				$tot_monsters		= ($tiles) ? $RepCombat->countMonstersInRoom($id_areamap, $tiles) : false;
				// Model world
				if ($map) {
					$return['id_areamap']		= $id_areamap;
					$return['current_monster']	= $current_monster;
					$return['tot_monsters']		= $tot_monsters;
					$return['step']				= $step;
					$return['tot_steps']		= $tot_steps;
					$return['level']			= $level;
					$return['id_parentmap']		= $id_parentmap;
					$return['area_name']		= $map['vc_name'];
					$return['map']				= ($map) ? $ModMap->encounter($map, $id_parentmap, $parent_areatype, $links, $vis_tiles, $step, $tot_steps) : false;
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		/*
		Prints out parent map Map - loadParentMap()
			@return format	- print json
		*/
		public function loadParentMap() {
			// Declare Classes
			$RepMap			= new RepMap();
			$ModMap			= new ModMap();
			// Initialize variables
			$return			= false;
			$mouseovers		= false;
			$ids			= false;
			$id_areamap		= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			// If values were sent
			if ($id_areamap) {
				// Load Parent map's info
				$parent		= $RepMap->getParentMapInfoIdByMapId($id_areamap);
				if ($parent) {
					// If Map is a dungeon, 
					if ($parent['boo_encounter'] == 1) {
						// Get closest parent local map
						while ($parent['boo_encounter'] == 1) {
							$parent		= $RepMap->getParentMapInfoIdByMapId($parent['id']);
						}
					}
					// get world map (parent) info
					$worldmap		= $RepMap->getParentMapInfoIdByMapId($parent['id']);
					// Load Map info
					$map	= $RepMap->getMapById($parent['id']);
					// Get linking info
					$links				= $RepMap->getLinksIconsByAreaId($parent['id']);
					if ($links) {
						foreach ($links as $link) {
							$ids		= ($ids) ? $ids.','.$link['id_map_target'] : $link['id_map_target'];
						}
						$mouseovers		= $RepMap->getAllMouseOversByMapId($ids);
					}
					// Get Map level
					$level				= ($level = $RepMap->getAreaInfoByMapId($parent['id'])) ? $level['int_level'] : false;
					// Model world
					if ($map) {
						$return['id_parentmap']	= $worldmap['id'];
						$return['area_name']	= $map['vc_name'];
						$return['level']		= $level;
						$return['map']			= ($map) ? $ModMap->map($map, $worldmap['id'], 'world', $links, $mouseovers) : false;
					}
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		/*
		Prints out a world - loadWorldMap()
			@return format	- print json
		*/
		public function loadWorldMap() {
			// Declare Classes
			$RepMap		= new RepMap();
			$ModMap		= new ModMap();
			// Initialize variables
			$return		= false;
			$ids		= false;
			$mouseovers	= false;
			$id_areamap	= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : 45;
			// If values were sent
			if ($id_areamap) {
				// Load World Map info
				$map	= $RepMap->getMapById($id_areamap);
				if ($map) {
					// Get linking info
					$links	= $RepMap->getLinksIconsByAreaId($id_areamap);
					if ($links) {
						foreach ($links as $link) {
							$ids	= ($ids) ? $ids.','.$link['id_map_target'] : $link['id_map_target'];
						}
						$mouseovers	= $RepMap->getAllMouseOversByMapId($ids);
					}
					$navigation		= $RepMap->getNavigationLinkByAreaId($id_areamap);
					// Model world and return
					$return['id_areamap']	= $map['id'];
					$return['area_name']	= $map['vc_name'];
					$return['map']			= $ModMap->world($map, $links, $mouseovers, $navigation);
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

	}