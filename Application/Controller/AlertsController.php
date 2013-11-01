<?php
/************************************************************************************
* Name:				Alerts Controller												*
* File:				Application\Controller\AlertsController.php 					*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the Alerts controller.									*
*																					*
* Creation Date:	03/10/2013														*
* Version:			1.13.1003														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller;

	// Framework Classes
	use SaSeed\View;
	use SaSeed\Session;
	//use SaSeed\General;

	// Model Classes
	//use Application\Model\Combat	as ModCombat;

	// Repository Classes
	use Application\Controller\Repository\Map			as RepMap;
	use Application\Controller\Repository\Character		as RepCharacter;

	// Other Classes
	use Application\Controller\LogInController			as LogIn;

	class AlertsController {

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
				$GLOBALS['this_js']		= '<script type="text/javascript" src="'.URL_PATH.'/Application/View/js/scripts/combat.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_js']		.= '<script type="text/javascript" src="'.URL_PATH.'/Application/View/js/libs/jquery.fancybox-1.3.4.pack.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	= '<link rel="stylesheet" href="'.URL_PATH.'/Application/View/css/alerts.css" type="text/css" media="screen" />'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	.= '<link href="'.URL_PATH.'/Application/View/css/jquery.fancybox-1.3.4.css" rel="stylesheet">'.PHP_EOL;	// Se não houver, definir como vazio ''
				// Define Menu selection
				//Menu::defineSelected($GLOBALS['controller_name']);
			}
		}

		public function MonsterDies() {
			View::render('partial_modalMonsterDies');
		}

		public function TimeIsUp() {
			View::render('partial_modalTimeIsUp');
		}

		public function DungeonFinished() {
			// Initialize variables
			$tot_gold	= (isset($GLOBALS['params'][1])) ? trim(($GLOBALS['params'][1])) : false;
			$tot_xp		= (isset($GLOBALS['params'][2])) ? trim(($GLOBALS['params'][2])) : false;
			$name_item1	= ((isset($GLOBALS['params'][3])) && ($GLOBALS['params'][3] != 'false')) ? str_replace('%20', ' ', trim($GLOBALS['params'][3])) : '-';
			$name_item2	= ((isset($GLOBALS['params'][4])) && ($GLOBALS['params'][4] != 'false')) ? str_replace('%20', ' ', trim($GLOBALS['params'][4])) : '-';
			if ($tot_xp) {
				View::set('tot_xp',		$tot_xp);
				View::set('tot_gold',	$tot_gold);
				View::set('name_item1', $name_item1);
				View::set('name_item2',	$name_item2);
				View::render('partial_modalDungeonFinished');
			}
		}

		public function PlayerDies() {
			// Classes
			$RepMap			= new RepMap();
			$RepCharacter	= new RepCharacter();
			// Initialize variables
			$id_areamap		= (isset($GLOBALS['params'][1])) ? trim(($GLOBALS['params'][1])) : false;
			$user			= Session::getVar('user');
			// If values were sent
			if ($id_areamap) {
				// Get local parentmap id
				$id_parentmap	= ($id_parentmap = $RepMap->getLocalParentMapIdByMapId($id_areamap)) ? $id_parentmap : false;
				// Check if the the user has a Talisman of Resurrection
				$id_char		= $RepCharacter->getCharIdByUserId($user['id']);
				$id_inventory	= ($id_char) ? $RepCharacter->getTalismanInBagIdInventory($id_char) : false;
				// Model return
				$talisman		= ($id_inventory) ? '<a id="use_talisman" key="'.$id_inventory.'" href="#">USE</a> one of your Talimans of Resurrection<br><br>' : '';
				// Prepare Return
				View::set('id_parentmap',	$id_parentmap);
				View::set('talisman',		$talisman);
				// Return
				View::render('partial_modalPlayerDies');
			}
		}

	}