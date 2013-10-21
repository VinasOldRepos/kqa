<?php
/************************************************************************************
* Name:				Characters Controller											*
* File:				Application\Controller\CharactersController.php 				*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the home page's controller.								*
*																					*
* Creation Date:	17/09/2013														*
* Version:			1.12.0917														*
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
	use Application\Model\Character						as ModCharacter;
	use Application\Model\Combat						as ModCombat;

	// Repository Classes
	//use Application\Controller\Repository\Map			as RepMap;
	//use Application\Controller\Repository\Question	as RepQuestion;
	use Application\Controller\Repository\Character		as RepCharacter;

	// Other Classes
	use Application\Controller\LogInController			as LogIn;

	class CharactersController {

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
				$GLOBALS['this_js']		= '';	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	= '';	// Se não houver, definir como vazio ''
				// Define Menu selection
				//Menu::defineSelected($GLOBALS['controller_name']);
			}
		}

		/*
		Prints out main home page - index()
			@return format	- Render View
		*/
		public static function index() {
			View::render('index');
		}

		/*
		Prints out New character page - newCharacter()
			@return format	- Render View
		*/
		public static function newCharacter() {
			View::render('partial_newCharacter');
		}

		/*
		insert Character - insertCharacter()
			@return format	- Render View
		*/
		public function insertCharacter() {
			// Classes
			$RepCharacter		= new RepCharacter();
			// Variables
			$vc_name			= (isset($_POST['vc_name'])) ? trim($_POST['vc_name']) : false;
			$return				= false;
			if ($vc_name) {
				$id_user		= ($user = Session::getVar('user')) ? $user['id'] : false;
				$id_character	= $RepCharacter->insetCharacter($id_user, $vc_name);
				if ($id_character) {
					Session::setVar('id_character', $id_character);
					$return		= 'ok';
				}
			}
			echo $return;
		}

		public function loadMonetaryInfo() {
			// Classes
			$RepCharacter	= new RepCharacter();
			// Initialize variables
			$user			= Session::getVar('user');
			$id_char		= (isset($_POST['id_char'])) ? trim($_POST['id_char']) : false;
			// If values were sent
			if (($user) && ($id_char)) {
				// Get info
				$tokens				= $user['int_token'];
				$gold				= ($gold = $RepCharacter->getCharGold($id_char)) ? $gold['int_gold'] : '0';
				// Prepare results
				$return['tokens']	= $tokens;
				$return['gold']		= $gold;
			}
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		public function loadCharInfo() {
			// Add Classes
			$RepCharacter		= new RepCharacter();
			$ModCombat			= new ModCombat();
			// Variables
			$user				= Session::getVar('user');
			$return				= false;
			$total_me_bonus		= 0;
			$total_ds			= 0;
			$total_time_bonus	= 0;
			$min_me				= 0;
			$max_me				= 0;
			// If values were sent
			if ($user) {
				// Get character's info
				$character		= $RepCharacter->getById($user['id']);
				if ($character) {
					// Get character's bag contents
					//$combat_bag		= $RepCharacter->getCombatBagContentsByCharId($character['id']);
					$combat_items		= $RepCharacter->getAllWoreItems($character['id']);
					$noncombat_bag		= $RepCharacter->getNonCombatBagContentsByCharId($character['id']);
					// Get Combat Items's data
					if ($combat_items) {
						$ids[]			= ($combat_items['id_combatitem_head'] > 0) ? $combat_items['id_combatitem_head'] : false;
						$ids[]			= ($combat_items['id_combatitem_neck'] > 0) ? $combat_items['id_combatitem_neck'] : false;
						$ids[]			= ($combat_items['id_combatitem_chest'] > 0) ? $combat_items['id_combatitem_chest'] : false;
						$ids[]			= ($combat_items['id_combatitem_back'] > 0) ? $combat_items['id_combatitem_back'] : false;
						$ids[]			= ($combat_items['id_combatitem_mainhand'] > 0) ? $combat_items['id_combatitem_mainhand'] : false;
						$ids[]			= ($combat_items['id_combatitem_offhand'] > 0) ? $combat_items['id_combatitem_offhand'] : false;
						$ids[]			= ($combat_items['id_combatitem_rightfinger'] > 0) ? $combat_items['id_combatitem_rightfinger'] : false;
						$ids[]			= ($combat_items['id_combatitem_leftfinger'] > 0) ? $combat_items['id_combatitem_leftfinger'] : false;
						$ids[]			= ($combat_items['id_combatitem_legs'] > 0) ? $combat_items['id_combatitem_legs'] : false;
						$ids[]			= ($combat_items['id_combatitem_feet'] > 0) ? $combat_items['id_combatitem_feet'] : false;
						$combat_items	= $RepCharacter->getAllCombatItems($ids);
						foreach ($combat_items as $item) {
							$total_me_bonus		= $total_me_bonus + $item['int_magic_me'];
							$total_ds			= $total_ds + $item['int_ds'] + $item['int_magic_ds'];
							$total_time_bonus	= $total_time_bonus + $item['int_time'];
							if (($item['id_type'] == 5) || ($item['id_type'] == 6)) {
								$min_me			= $item['int_me_min'];
								$max_me			= $item['int_me_max'];
							}
						}
					}
					// Prepare return
					$return['character']		= $ModCombat->characterDisplay($character, $combat_items, $noncombat_bag);
					$return['player_hp']		= $character['int_hp'];
					$return['player_min_dmg']	= $min_me;
					$return['player_max_dmg']	= $max_me;
					$return['player_me']		= $total_me_bonus;
					$return['player_ds']		= $total_ds;
					$return['timebonus']		= $total_time_bonus;
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

	}