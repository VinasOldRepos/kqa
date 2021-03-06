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
	use Application\Controller\Repository\Combat		as RepCombat;
	use Application\Controller\Repository\Item			as RepItem;

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
				$GLOBALS['this_js']		= '<script type="text/javascript" src="'.URL_PATH.'/Application/View/js/scripts/character.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_js']		.= '<script type="text/javascript" src="'.URL_PATH.'/Application/View/js/libs/jquery.fancybox-1.3.4.pack.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	= '<link rel="stylesheet" href="'.URL_PATH.'/Application/View/css/character.css" type="text/css" media="screen" />'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	.= '<link href="'.URL_PATH.'/Application/View/css/jquery.fancybox-1.3.4.css" rel="stylesheet">'.PHP_EOL;	// Se não houver, definir como vazio ''
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
					$RepCharacter->newWearable($id_character);
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
			$last_id			= 0;
			// If values were sent
			if ($user) {
				// Get character's info
				$character		= $RepCharacter->getCharByUserId($user['id']);
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
						if ($combat_items) {
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

		/*
		Display Page where player selects gear to be taken to the dungeon - selectGear()
			@return format	- Render View
		*/
		public function selectGear() {
			// Classes
			$RepCharacter	= new RepCharacter();
			$RepItem		= new RepItem();
			$ModCharacter	= new ModCharacter();
			// Initialize variables
			$return			= false;
			$user			= Session::getVar('user');
			$target_map		= (isset($GLOBALS['params'][1])) ? trim(($GLOBALS['params'][1])) : false;
			$id_areamap		= (isset($GLOBALS['params'][2])) ? trim(($GLOBALS['params'][2])) : false;
			// If there are values
			if (($target_map !== false) && ($id_areamap !== false)) {
				$id_char	= $RepCharacter->getCharIdByUserId($user['id']);
				// Empty Player's bag
				$RepCharacter->emptyBag($id_char);
				// Load Player's inventory
				$inventory				= ($id_char) ? $RepCharacter->getAllInventoryContentsByCharId($id_char) : false;
				// Get wore items names
				$items					= ($id_char) ? $RepCharacter->getAllWoreItems($id_char) : false;
				$wore['head']			= ($items['id_combatitem_head'] > 0) ? $RepItem->getCombatById($items['id_combatitem_head']) : false;
				$wore['neck']			= ($items['id_combatitem_neck'] > 0) ? $RepItem->getCombatById($items['id_combatitem_neck']) : false;
				$wore['chest']			= ($items['id_combatitem_chest'] > 0) ? $RepItem->getCombatById($items['id_combatitem_chest']) : false;
				$wore['back']			= ($items['id_combatitem_back'] > 0) ? $RepItem->getCombatById($items['id_combatitem_back']) : false;
				$wore['mainhand']		= ($items['id_combatitem_mainhand'] > 0) ? $RepItem->getCombatById($items['id_combatitem_mainhand']) : false;
				$wore['offhand']		= ($items['id_combatitem_offhand'] > 0) ? $RepItem->getCombatById($items['id_combatitem_offhand']) : false;
				$wore['rightfinger']	= ($items['id_combatitem_rightfinger'] > 0) ? $RepItem->getCombatById($items['id_combatitem_rightfinger']) : false;
				$wore['leftfinger']		= ($items['id_combatitem_leftfinger'] > 0) ? $RepItem->getCombatById($items['id_combatitem_leftfinger']) : false;
				$wore['legs']			= ($items['id_combatitem_legs'] > 0) ? $RepItem->getCombatById($items['id_combatitem_legs']) : false;
				$wore['feet']			= ($items['id_combatitem_feet'] > 0) ? $RepItem->getCombatById($items['id_combatitem_feet']) : false;
				// Model results and prepare return
				View::set('inventory',	$ModCharacter->listInventory($inventory, $wore));
				View::set('wore',		$ModCharacter->listWore($wore));
				View::set('target_map',	$target_map);
				View::set('id_areamap',	$id_areamap);
			}
			// Return
			View::render('partial_modalSelectGear');
		}

		/*
		Place item in the player's body - placeWearable()
			@return format	- jquery print
		*/
		public function placeWearable() {
			// Classes
			$RepCharacter	= new RepCharacter();
			$RepItem		= new RepItem();
			$ModCharacter	= new ModCharacter();
			// Initialize variables
			$return			= false;
			$user			= Session::getVar('user');
			$id_item		= (isset($_POST['id_item'])) ? trim($_POST['id_item']): 1;
			$place			= (isset($_POST['place'])) ? trim($_POST['place']): 'mainhand';
			// if values were sent
			if (($id_item) && ($place)) {
				// Get Character id
				$id_char				= $RepCharacter->getCharIdByUserId($user['id']);
				// Get wore items
				$items					= ($id_char) ? $RepCharacter->getAllWoreItems($id_char) : false;
				// If item is a ring
				if ($place == 'finger') {
					// Define target finger
					if ($items['id_combatitem_rightfinger'] > 0) {
						if ($items['id_combatitem_leftfinger'] > 0) {
							$place		= 'rightfinger';
						} else {
							$place		= 'leftfinger';
						}
					} else {
						$place			= 'rightfinger';
					}
				// If it's a main hand or off hand item
				} else if (($place == 'mainhand') || ($place == 'offhand')) {
					// If wore item uses both hands
					if ($items['id_combatitem_mainhand'] == $items['id_combatitem_offhand']) {
						// Empty both hands
						$RepCharacter->placeWearable($id_char, 0, "mainhand");
						$RepCharacter->placeWearable($id_char, 0, "offhand");
					}
				}
				// Save item position in the database
				$RepCharacter->placeWearable($id_char, $id_item, $place);
				// Get wore items
				$items					= ($id_char) ? $RepCharacter->getAllWoreItems($id_char) : false;
				// Load Player's inventory
				$inventory				= ($id_char) ? $RepCharacter->getAllInventoryContentsByCharId($id_char) : false;
				$wore['head']			= ($items['id_combatitem_head'] > 0) ? $RepItem->getCombatById($items['id_combatitem_head']) : false;
				$wore['neck']			= ($items['id_combatitem_neck'] > 0) ? $RepItem->getCombatById($items['id_combatitem_neck']) : false;
				$wore['chest']			= ($items['id_combatitem_chest'] > 0) ? $RepItem->getCombatById($items['id_combatitem_chest']) : false;
				$wore['back']			= ($items['id_combatitem_back'] > 0) ? $RepItem->getCombatById($items['id_combatitem_back']) : false;
				$wore['mainhand']		= ($items['id_combatitem_mainhand'] > 0) ? $RepItem->getCombatById($items['id_combatitem_mainhand']) : false;
				$wore['offhand']		= ($items['id_combatitem_offhand'] > 0) ? $RepItem->getCombatById($items['id_combatitem_offhand']) : false;
				$wore['rightfinger']	= ($items['id_combatitem_rightfinger'] > 0) ? $RepItem->getCombatById($items['id_combatitem_rightfinger']) : false;
				$wore['leftfinger']		= ($items['id_combatitem_leftfinger'] > 0) ? $RepItem->getCombatById($items['id_combatitem_leftfinger']) : false;
				$wore['legs']			= ($items['id_combatitem_legs'] > 0) ? $RepItem->getCombatById($items['id_combatitem_legs']) : false;
				$wore['feet']			= ($items['id_combatitem_feet'] > 0) ? $RepItem->getCombatById($items['id_combatitem_feet']) : false;
				// Model results and prepare return
				$return					= ($return = $ModCharacter->listInventory($inventory, $wore)) ? $return : 'no items';
				
			}
			// Return
			echo $return;
		}

		/*
		Place item in the player's bag - placeBag()
			@return format	- jquery print
		*/
		public function placeBag() {
			// Classes
			$RepCharacter	= new RepCharacter();
			$ModCharacter	= new ModCharacter();
			// Initialize variables
			$return			= false;
			$user			= Session::getVar('user');
			$id_inventory	= (isset($_POST['id_item'])) ? trim($_POST['id_item']): false;
			// If values were sent
			if ($id_inventory) {
				// Get Character id
				$id_char	= $RepCharacter->getCharIdByUserId($user['id']);
				// Add this item to the bag
				$RepCharacter->placeBag($id_inventory);
				// Get all bag Items
				$bag		= ($id_char) ? $RepCharacter->getAllBagContentsByCharId($id_char) : false;
				// Model return
				$return		= ($bag) ? $ModCharacter->listBag($bag) : false;
			}
			// Return
			echo $return;
		}

		/*
		Remove item from the player's body - removeWearable()
			@return format	- jquery print
		*/
		public function removeWearable() {
			// Classes
			$RepCharacter	= new RepCharacter();
			$RepItem		= new RepItem();
			$ModCharacter	= new ModCharacter();
			// Initialize variables
			$return			= false;
			$user			= Session::getVar('user');
			$place			= (isset($_POST['place'])) ? trim($_POST['place']): false;
			$id_item		= (isset($_POST['id_item'])) ? trim($_POST['id_item']): false;
			$main_hand		= (isset($_POST['main_hand'])) ? trim($_POST['main_hand']): false;
			$off_hand		= (isset($_POST['off_hand'])) ? trim($_POST['off_hand']): false;
			// if values were sent
			if ($place) {
				// Get Character id
				$id_char	= $RepCharacter->getCharIdByUserId($user['id']);
				// Save item position in the database
				if (($id_item) && ($id_item == $main_hand) && ($id_item == $off_hand)) {
					$RepCharacter->placeWearable($id_char, 0, "mainhand");
					$RepCharacter->placeWearable($id_char, 0, "offhand");
				} else {
					$RepCharacter->placeWearable($id_char, 0, $place);
				}
				// Load Player's inventory
				$inventory				= ($id_char) ? $RepCharacter->getAllInventoryContentsByCharId($id_char) : false;
				// Get wore items names
				$items					= ($id_char) ? $RepCharacter->getAllWoreItems($id_char) : false;
				$wore['head']			= ($items['id_combatitem_head'] > 0) ? $RepItem->getCombatById($items['id_combatitem_head']) : false;
				$wore['neck']			= ($items['id_combatitem_neck'] > 0) ? $RepItem->getCombatById($items['id_combatitem_neck']) : false;
				$wore['chest']			= ($items['id_combatitem_chest'] > 0) ? $RepItem->getCombatById($items['id_combatitem_chest']) : false;
				$wore['back']			= ($items['id_combatitem_back'] > 0) ? $RepItem->getCombatById($items['id_combatitem_back']) : false;
				$wore['mainhand']		= ($items['id_combatitem_mainhand'] > 0) ? $RepItem->getCombatById($items['id_combatitem_mainhand']) : false;
				$wore['offhand']		= ($items['id_combatitem_offhand'] > 0) ? $RepItem->getCombatById($items['id_combatitem_offhand']) : false;
				$wore['rightfinger']	= ($items['id_combatitem_rightfinger'] > 0) ? $RepItem->getCombatById($items['id_combatitem_rightfinger']) : false;
				$wore['leftfinger']		= ($items['id_combatitem_leftfinger'] > 0) ? $RepItem->getCombatById($items['id_combatitem_leftfinger']) : false;
				$wore['legs']			= ($items['id_combatitem_legs'] > 0) ? $RepItem->getCombatById($items['id_combatitem_legs']) : false;
				$wore['feet']			= ($items['id_combatitem_feet'] > 0) ? $RepItem->getCombatById($items['id_combatitem_feet']) : false;
				// Model results and prepare return
				$return					= $ModCharacter->listInventory($inventory, $wore);
			}
			// Return
			echo $return;
		}

		/*
		Remove item from player's bag on selection fancybox - removeBag()
			@return format	- jquery print
		*/
		public function removeBag() {
			// Classes
			$RepCharacter	= new RepCharacter();
			$RepItem		= new RepItem();
			$ModCharacter	= new ModCharacter();
			// Initialize variables
			$return			= false;
			$inventory		= false;
			$user			= Session::getVar('user');
			$id_inventory	= (isset($_POST['id_item'])) ? trim($_POST['id_item']): false;
			// If values were sent
			if ($id_inventory) {
				// Get Character id
				$id_char					= $RepCharacter->getCharIdByUserId($user['id']);
				// Remove this item from the bag
				$RepCharacter->removeBag($id_inventory);
				// Load Player's inventory
				$invent_items				= ($id_char) ? $RepCharacter->getAllInventoryContentsByCharId($id_char) : false;
				// Remove bag items from the list
				if ($invent_items) {
					foreach ($invent_items as $item) {
						if ($item['boo_bag'] != 1) {
							$inventory[]	= $item;
						}
					}
				}
				// Get wore items names
				$items					= ($id_char) ? $RepCharacter->getAllWoreItems($id_char) : false;
				$wore['head']			= ($items['id_combatitem_head'] > 0) ? $RepItem->getCombatById($items['id_combatitem_head']) : false;
				$wore['neck']			= ($items['id_combatitem_neck'] > 0) ? $RepItem->getCombatById($items['id_combatitem_neck']) : false;
				$wore['chest']			= ($items['id_combatitem_chest'] > 0) ? $RepItem->getCombatById($items['id_combatitem_chest']) : false;
				$wore['back']			= ($items['id_combatitem_back'] > 0) ? $RepItem->getCombatById($items['id_combatitem_back']) : false;
				$wore['mainhand']		= ($items['id_combatitem_mainhand'] > 0) ? $RepItem->getCombatById($items['id_combatitem_mainhand']) : false;
				$wore['offhand']		= ($items['id_combatitem_offhand'] > 0) ? $RepItem->getCombatById($items['id_combatitem_offhand']) : false;
				$wore['rightfinger']	= ($items['id_combatitem_rightfinger'] > 0) ? $RepItem->getCombatById($items['id_combatitem_rightfinger']) : false;
				$wore['leftfinger']		= ($items['id_combatitem_leftfinger'] > 0) ? $RepItem->getCombatById($items['id_combatitem_leftfinger']) : false;
				$wore['legs']			= ($items['id_combatitem_legs'] > 0) ? $RepItem->getCombatById($items['id_combatitem_legs']) : false;
				$wore['feet']			= ($items['id_combatitem_feet'] > 0) ? $RepItem->getCombatById($items['id_combatitem_feet']) : false;
				// Model results and prepare return
				$return					= $ModCharacter->listInventory($inventory, $wore);
			}
			// Return
			echo $return;
		}

		/*
		Remove non combat item from player's Inventory - removeNonCombatInventory()
			@return format	- jquery print
		*/
		public function removeNonCombatInventory() {
			// Classes
			$RepCharacter	= new RepCharacter();
			// Initialize variables
			$return			= false;
			$inventory		= false;
			$user			= Session::getVar('user');
			$id_inventory	= (isset($_POST['id_item'])) ? trim($_POST['id_item']): false;
			// If values were sent
			if ($id_inventory) {
				// Remove this item from the bag
				$return		= ($RepCharacter->removeNonCombatInventory($id_inventory)) ? 'ok' : false;
			}
			// Return
			echo $return;
		}

		/*
		Remove item from player's Inventory - removeFromInventory()
			@return format	- jquery print
		*/
		public function removeFromInventory() {
			// Classes
			$RepCharacter	= new RepCharacter();
			// Initialize variables
			$return			= false;
			$inventory		= false;
			$id_inventory	= (isset($_POST['id_inventory'])) ? trim($_POST['id_inventory']) : false;
			$id_inventory	= ((!$id_inventory) && (isset($_POST['id_item']))) ? trim($_POST['id_item']) : false;
			// If values were sent
			if ($id_inventory) {
				// Remove this item from the inventory
				$return		= ($RepCharacter->removeFromInventory($id_inventory)) ? 'ok' : false;
			}
			// Return
			echo $return;
		}

		public function saveXP() {
			// Class
			$RepCharacter		= new RepCharacter();
			$RepCombat			= new RepCombat();
			// Variables
			$user				= Session::getVar('user');
			$tot_xp				= (isset($_POST['tot_xp'])) ? trim($_POST['tot_xp']) : false;
			$id_areamap			= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			$character			= $RepCharacter->getCharByUserId($user['id']);
			$return				= false;
			if (($tot_xp) && ($id_areamap) && ($character)) {
				// Get Course id
				$id_course			= ($id_course = $RepCombat->getCourseIdByMapId($id_areamap)) ? $id_course['id_course'] : false;
				// Save XP
				if ($id_course) {
					$xp				= $RepCharacter->getXpByCharCourseId($character['id'], $id_course);
					if ($xp) {
						$prev_xp	= $xp;
						$xp			= $xp + $tot_xp;
						$return		= (($RepCharacter->updateXpByCharCourseId($character['id'], $id_course, $xp))) ? $xp : false;
					} else {
						$prev_xp	= 0;
						$xp			= $tot_xp;
						$return		= (($RepCharacter->insertXpByCharCourseId($character['id'], $id_course, $xp))) ? $xp : false;
					}
					if (($prev_xp < 100) && ($xp >= 100)) {
						$character				= $RepCharacter->getCharByUserId($user['id']);
						$character['int_hp']	= $character['int_hp'] + 1;
						$return					= (($RepCharacter->updateCharacter($character))) ? $character['int_gold'] : false;
					} else if (($prev_xp >= 100) && ($xp > 100)) {
						$stop		= false;
						$min_hp		= 100;
						$add_factor	= 500;
						while (!$stop) {
							$next_hp			= $min_hp + $add_factor;
							if (($prev_xp >= $min_hp) && ($prev_xp < $next_hp) && ($xp >= $next_hp)) {
								$character				= $RepCharacter->getCharByUserId($user['id']);
								$character['int_hp']	= $character['int_hp'] + 1;
								$return					= (($RepCharacter->updateCharacter($character))) ? $character['int_gold'] : false;
								$stop					= false;
								break;
							} else if (($prev_xp >= $min_hp) && ($prev_xp < $next_hp) && ($xp < $next_hp)) {
								$stop		= false;
								break;
							} else {
								$min_hp		= $next_hp;
							}
						}
					}
				}
			}
			// Return
			echo $return;
		}

		public function saveGold() {
			// Class
			$RepCharacter		= new RepCharacter();
			// Variables
			$user				= Session::getVar('user');
			$monster_treasure	= (isset($_POST['monster_treasure'])) ? trim($_POST['monster_treasure']) : false;
			$return				= false;
			if (($monster_treasure) && ($user)) {
				// Save XP
				$character		= $RepCharacter->getCharByUserId($user['id']);
				if ($character) {
					$character['int_gold']	= $character['int_gold'] + $monster_treasure;
					$return		= (($RepCharacter->updateCharacter($character))) ? $character['int_gold'] : false;
				}
			}
			// Return
			echo $return;
		}

	}