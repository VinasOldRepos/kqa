<?php
/************************************************************************************
* Name:				Combat Controller												*
* File:				Application\Controller\CombatController.php 					*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the Combat controller.									*
*																					*
* Creation Date:	19/09/2013														*
* Version:			1.13.0919														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller;

	// Framework Classes
	use SaSeed\View;
	use SaSeed\Session;
	//use SaSeed\General;

	// Model Classes
	use Application\Model\Combat	as ModCombat;

	// Repository Classes
	use Application\Controller\Repository\Map			as RepMap;
	use Application\Controller\Repository\Character		as RepCharacter;
	use Application\Controller\Repository\Combat		as RepCombat;
	use Application\Controller\Repository\Question		as RepQuestion;
	use Application\Controller\Repository\Monster		as RepMonster;

	// Other Classes
	use Application\Controller\LogInController			as LogIn;

	class CombatController {

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
				$GLOBALS['this_js']		= ''.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	= ''.PHP_EOL;	// Se não houver, definir como vazio ''
				// Define Menu selection
				//Menu::defineSelected($GLOBALS['controller_name']);
			}
		}

		public function loadCombat() {
			/*/
			// Classes
			$RepCombat			= new RepCombat();
			$ModCombat			= new ModCombat();
			// Variables
			$id_areamap			= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			$step				= (isset($_POST['step'])) ? trim($_POST['step']) : false;
			$monsters_left		= (isset($_POST['monsters_left'])) ? trim($_POST['monsters_left']) : 0;
			$correct			= false;
			// If data was sent
			if (($id_areamap) && ($step)) {
				// Get all tiles of this step
				$tiles			= ($tiles = $RepCombat->getAllTilesInStep($id_areamap, $step)) ? $tiles['vc_tiles'] : false;
				// Get all monsters in these tiles
				$monsters_left	= ((!$monsters_left) && ($tiles)) ? $RepCombat->countMonstersInRoom($id_areamap, $tiles) : false;
				$monster		= (($monsters_left) && ($tiles)) ? $RepCombat->getAllMonstersInRoom($id_areamap, $tiles) : false;
				$monster		= ($monster) ? $monster[$step - 1] : false;
				// Get question and answers
				$id_course		= ($id_course = $RepCombat->getCourseIdByMapId($id_areamap)) ? $id_course['id_course'] : false;
				$RepQuestion	= new RepQuestion();
				$id_question	= ($id_question = $RepQuestion->getRandomQuestionIdByCourseId($id_course)) ? $id_question['id_question'] : false;
				$question		= ($id_question) ? $RepQuestion->getQuestionById($id_question) : false;
				$answers		= ($id_question) ? $RepQuestion->getAnswersByQuestionId($id_question) : false;
				if ($answers) {
					foreach ($answers as $answer) {
						if ($answer['boo_correct'] == 1) {
							$correct	= $answer['id'];
							break;
						}
					}
					$answers	= $ModCombat->answers($answers);
				}
				// Prepare return
				View::set('question',		$question['tx_question']);
				View::set('answers',		$answers);
				View::set('time_limit',		$question['int_timelimit']);
				View::set('id_question',	$question['id']);
				View::set('correct',		$correct);
				// Return
				View::render('partial_combat');
			}
			/*/


			View::render('partial_combat');
		}

		public function loadMonsterList() {
			// Classes
			$RepCombat			= new RepCombat();
			$ModCombat			= new ModCombat();
			// Variables
			$id_areamap			= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			$step				= (isset($_POST['step'])) ? trim($_POST['step']) : false;
			$tot_monsters		= false;
			$monster			= false;
			$return				= false;
			$monster_treasure	= false;
			// If data was sent
			if (($id_areamap) && ($step)) {
				// Get all monsters in these tiles
				$tiles			= ($tiles = $RepCombat->getAllTilesInStep($id_areamap, $step)) ? $tiles['vc_tiles'] : false;
				$tot_monsters	= ((!$tot_monsters) && ($tiles)) ? $RepCombat->countMonstersInRoom($id_areamap, $tiles) : false;
				$monsters		= (($tot_monsters) && ($tiles)) ? $RepCombat->getAllMonstersInRoom($id_areamap, $tiles) : false;
				// If there are monsters
				if ($monsters) {
					// Model all info
					for ($i = 0; $i < count($monsters); $i++) {
						$monsters[$i]['monster_hp']	= rand($monsters[$i]['int_hits_min'], $monsters[$i]['int_hits_max']);
					}
					$monster			= $monsters[0];
					$monster_treasure	= rand($monster['int_treasure_min'], $monster['int_treasure_max']);
					$tot_monsters		= count($monsters);
					$monsters			= $ModCombat->monsterList($monsters);
					// Prepare return
					$return['total']				= $tot_monsters;
					$return['monsters']				= $monsters;
					$return['id_monster']			= $monster['id'];
					$return['monster_hp']			= $monster['monster_hp'];
					$return['monster_min_dmg']		= $monster['int_damage_min'];
					$return['monster_max_dmg']		= $monster['int_damage_max'];
					$return['monster_me']			= $monster['int_me'];
					$return['monster_ds']			= $monster['int_ds'];
					$return['monster_knowledge']	= $monster['int_knowledge'];
					$return['monster_treasure']		= $monster_treasure;
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		public function CombatSimulator() {
			// Classes
			$RepMap		= new RepMap();
			$ModMap		= new ModMap();

			// Variables
			$level		= 1;
			$id_course	= 1;

			// Get all branches
			$RepQuestion	= new RepQuestion();
			$branches		= $RepQuestion->getAllBranches();
			$branches		= ($branches) ? $ModMap->combo($branches, true) : false;
			// Prepare return
			$GLOBALS['this_js']		= ''.PHP_EOL;	// Se não houver, definir como vazio ''
			$GLOBALS['this_css']	= ''.PHP_EOL;	// Se não houver, definir como vazio ''
			//View::set('monsters',	$monsters);
			View::set('id_course',	$id_course);
			View::set('branches',	$branches);
			// Return
			View::render('combatSimulator');
		}
		
		public function loadMonster() {
			// Add Classes
			$RepMap				= new RepMap();
			$RepCombat			= new RepCombat();
			$ModCombat			= new ModCombat();
			// Variables
			$return				= false;
			$id_areamap			= (isset($_POST['id_areamap'])) ? trim(($_POST['id_areamap'])) : false;
			$step				= (isset($_POST['step'])) ? trim(($_POST['step'])) : false;
			$current_monster	= (isset($_POST['current_monster'])) ? trim(($_POST['current_monster'])) : 1;
			if (($id_areamap) && ($step) && ($current_monster)) {
				// Get monster's info
				$tiles			= ($tiles = $RepCombat->getAllTilesInStep($id_areamap, $step)) ? $tiles['vc_tiles'] : false;
				$monsters		= ($tiles) ? $RepCombat->getAllMonstersInRoom($id_areamap, $tiles) : false;
				$monster		= ($monsters) ? $monsters[$current_monster - 1] : false;
				if ($monster) {
					/*/
					// Get a random question from the course
					$RepQuestion		= new RepQuestion();
					$id_question		= ($id_question = $RepQuestion->getRandomQuestionIdByCourseId($id_course)) ? $id_question['id_question'] : false;
					$question			= ($id_question) ? $RepQuestion->getQuestionById($id_question) : false;
					$answers			= ($id_question) ? $RepQuestion->getAnswersByQuestionId($id_question) : false;
					// Calculate Monster's treasure drop
					// Model Data
					if ($question) {
						$time_limit		= $question['int_timelimit'];
						$id_question	= $question['id'];
						$question		= $question['tx_question'];
					}
					$answers		= ($answers) ? $ModCombat->answers($answers) : false;
					/*/
					$monster_treasure			= rand($monster['int_treasure_min'], $monster['int_treasure_max']);
					// Prepare return
					$return['monster_hp']		= rand($monster['int_hits_min'], $monster['int_hits_max']);
					$return['id_monster']		= $monster['id'];
					$return['monster_min_dmg']	= $monster['int_damage_min'];
					$return['monster_max_dmg']	= $monster['int_damage_max'];
					$return['int_ds']			= $monster['int_ds'];
					$return['int_knowledge']	= $monster['int_knowledge'];
					$return['int_me']			= $monster['int_me'];
					$return['monster_treasure']	= $monster_treasure;
					//$return['question']			= $question;
					//$return['answers']			= $answers;
					//$return['time_limit']		= $time_limit;
					//$return['id_question']		= $id_question;
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		public function loadCharInfo() {
			// Add Classes
			$RepCharacter		= new RepCharacter();
			$ModCombat			= new ModCombat();
			// Variables
			$user			= Session::getVar('user');
			$return			= false;
			$helmet			= false;
			$amulet			= false;
			$armor			= false;
			$cloak			= false;
			$magic_weapon	= false;
			$weapon			= false;
			$shield			= false;
			$ring			= false;
			$leggings		= false;
			$boots			= false;
			$player_ds		= 0;
			// If values were sent
			if ($user) {
				// Get character's info
				$character			= $RepCharacter->getById($user['id']);
				if ($character) {
					// Get character's bag contents
					$combat_bag		= $RepCharacter->getCombatBagContentsByCharId($character['id']);
					$noncombat_bag	= $RepCharacter->getNonCombatBagContentsByCharId($character['id']);
					// Get Combat Items's data
					if ($combat_bag) {
						foreach ($combat_bag as $item) {
							if ($item['id_type'] == 1) {
								$helmet			= $item;
							} else if ($item['id_type'] == 2) {
								$amulet			= $item;
							} else if ($item['id_type'] == 3) {
								$armor			= $item;
							} else if ($item['id_type'] == 4) {
								$cloak			= $item;
							} else if ($item['id_type'] == 5) {
								$magic_weapon	= $item;
							} else if ($item['id_type'] == 6) {
								$weapon			= $item;
							} else if ($item['id_type'] == 7) {
								$shield			= $item;
							} else if ($item['id_type'] == 8) {
								$ring			= $item;
							} else if ($item['id_type'] == 9) {
								$leggings		= $item;
							} else if ($item['id_type'] == 10) {
								$boots			= $item;
							}
						}
						$weapon			= (($magic_weapon) && ($weapon)) ? $magic_weapon : $weapon;
						$player_ds		= ($helmet) ? $player_ds + $helmet['int_ds'] + $helmet['int_magic_ds'] : $player_ds;
						$player_ds		= ($amulet) ? $player_ds + $amulet['int_ds'] + $amulet['int_magic_ds'] : $player_ds;
						$player_ds		= ($armor) ? $player_ds + $armor['int_ds'] + $armor['int_magic_ds'] : $player_ds;
						$player_ds		= ($cloak) ? $player_ds + $cloak['int_ds'] + $cloak['int_magic_ds'] : $player_ds;
						$player_ds		= ($magic_weapon) ? $player_ds + $magic_weapon['int_ds'] + $magic_weapon['int_magic_ds'] : $player_ds;
						$player_ds		= ($shield) ? $player_ds + $shield['int_ds'] + $shield['int_magic_ds'] : $player_ds;
						$player_ds		= ($ring) ? $player_ds + $ring['int_ds'] + $ring['int_magic_ds'] : $player_ds;
						$player_ds		= ($leggings) ? $player_ds + $leggings['int_ds'] + $leggings['int_magic_ds'] : $player_ds;
						$player_ds		= ($boots) ? $player_ds + $boots['int_ds'] + $boots['int_magic_ds'] : $player_ds;
					}
					// Prepare return
					$return['character']		= $ModCombat->characterDisplay($character);
					$return['player_hp']		= $character['int_hp'];
					$return['player_min_dmg']	= $weapon['int_me_min'];
					$return['player_max_dmg']	= $weapon['int_me_max'];
					$return['player_me']		= $weapon['int_magic_me'];
					$return['player_ds']		= $player_ds;
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		public function checkAnswer() {
			$RepQuestion	= new RepQuestion();
			$id_answer		= (isset($_POST['id_answer'])) ? trim($_POST['id_answer']) : false;
			$return			= ($RepQuestion->checkAnswerById($id_answer)) ? 'ok' : false;
			echo $return;
		}

		public function loadQuestion() {
			// Classes
			$RepCombat			= new RepCombat();
			$ModCombat			= new ModCombat();
			// Variables
			$return				= false;
			$id_course			= (isset($_POST['id_course'])) ? trim($_POST['id_course']) : false;
			$id_areamap			= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			$time_limit			= false;
			$id_question		= false;
			$correct			= false;
			if ((!$id_course) || ($id_course == 'false')) {
				$id_course		= $RepCombat->getCourseIdByMapId($id_areamap);
				$id_course		= ($id_course) ? $id_course['id_course'] : false;
			}
			if ($id_course) {
				// Get question and Answers
				$RepQuestion	= new RepQuestion();
				$id_question	= ($id_question = $RepQuestion->getRandomQuestionIdByCourseId($id_course)) ? $id_question['id_question'] : false;
				$question		= ($id_question) ? $RepQuestion->getQuestionById($id_question) : false;
				$answers		= ($id_question) ? $RepQuestion->getAnswersByQuestionId($id_question) : false;
				shuffle($answers);
				// Model Data
				if ($question) {
					$time_limit			= $question['int_timelimit'];
					//$id_question		= $question['id'];
					$question			= $question['tx_question'];
				}
				if ($answers) {
					foreach ($answers as $answer) {
						if ($answer['boo_correct'] == 1) {
							$correct	= $answer['id'];
							break;
						}
					}
					$answers			= $ModCombat->answers($answers);
				}
				// Prepare return
				$return['question']		= $question;
				$return['answers']		= $answers;
				$return['time_limit']	= $time_limit;
				$return['id_question']	= $id_question;
				$return['correct']		= $correct;
				$return['id_course']	= $id_course;
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		public function loadMonsters() {
			// Classes
			$RepQuestion	= new RepQuestion();
			$ModMap			= new ModMap();
			$id_course		= (isset($_POST['id_course'])) ? trim($_POST['id_course']) : false;
			$return			= false;
			if ($id_course) {
				$course		= $RepQuestion->getCourseById($id_course);
				// Get all Monsters
				$RepMap		= new RepMap();
				$monsters	= ($course) ? $RepMap->getminAllMonstersByLevel($course['int_level']) : false;
				// Model all monster
				$return		= ($monsters) ? $ModMap->listMonsters($monsters) : false;
			}
			// Return
			echo $return;
		}

		public function saveXP() {
			// Class
			$RepCharacter	= new RepCharacter();
			// Variables
			$user			= Session::getVar('user');
			$tot_xp			= (isset($_POST['tot_xp'])) ? trim($_POST['tot_xp']) : false;
			$return			= false;
			if (($tot_xp) && ($user)) {
				// Save XP
				$character	= $RepCharacter->getById($user['id']);
				if ($character) {
					$character['int_xp']	= $character['int_xp'] + $tot_xp;
					$return	= (($RepCharacter->updateCharacter($character))) ? $character['int_xp'] : false;
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
				$character		= $RepCharacter->getById($user['id']);
				if ($character) {
					$character['int_gold']	= $character['int_gold'] + $monster_treasure;
					$return		= (($RepCharacter->updateCharacter($character))) ? $character['int_gold'] : false;
				}
			}
			// Return
			echo $return;
		}

	}