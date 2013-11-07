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
	use Application\Model\Character	as ModCharacter;

	// Repository Classes
	use Application\Controller\Repository\Map			as RepMap;
	use Application\Controller\Repository\Character		as RepCharacter;
	use Application\Controller\Repository\Combat		as RepCombat;
	use Application\Controller\Repository\Question		as RepQuestion;
	use Application\Controller\Repository\Monster		as RepMonster;
	use Application\Controller\Repository\Item			as RepItem;

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
				$GLOBALS['this_js']		= '<script type="text/javascript" src="'.URL_PATH.'/Application/View/js/scripts/combat.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	= '<link rel="stylesheet" href="'.URL_PATH.'/Application/View/css/combat.css" type="text/css" media="screen" />'.PHP_EOL;	// Se não houver, definir como vazio ''
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

		public function loadBagContents() {
			// Classes
			$RepCharacter	= new RepCharacter();
			$ModCharacter	= new ModCharacter();
			// Initialze variables
			$user			= Session::getVar('user');
			$id_char		= $RepCharacter->getCharIdByUserId($user['id']);
			$return			= false;
			if ($id_char) {
				// Get all bag contents
				$bag		= $RepCharacter->getAllBagContentsByCharId($id_char);
				// Model return
				$return		= $ModCharacter->listBagItems($bag);;
			}
			// Prepare return
			View::set('bag',	$return);
			// Return
			View::render('partial_modalBagContents');
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
				$character	= $RepCharacter->getCharByUserId($user['id']);
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
				$character		= $RepCharacter->getCharByUserId($user['id']);
				if ($character) {
					$character['int_gold']	= $character['int_gold'] + $monster_treasure;
					$return		= (($RepCharacter->updateCharacter($character))) ? $character['int_gold'] : false;
				}
			}
			// Return
			echo $return;
		}

		public function calculateTreasureDrop() {
			// Classes
			$RepMap			= new RepMap();
			$RepItem		= new RepItem();
			$RepCharacter	= new RepCharacter();
			// Initialize variables
			$return			= false;
			$level1			= false;
			$level2			= false;
			$user			= Session::getVar('user');
			$id_areamap		= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			$sorted			= rand(1, 100);
			// If data was sent
			if ($id_areamap) {
				// Get area level
				$level		= $RepMap->getAreaInfoByMapId($id_areamap);
				$level 		= ($level) ? $level['int_level'] : false;
				// Calculate treasure drop according to level
				switch ($level) {
					// If it's a level 1 dungeon
					case 1:
						$gold				= rand(1, 10);
						if ($sorted <= 30) {
							$sorted			= rand(1, 100);
							if ($sorted <= 5) {
								$level1 	= 0;
							} else if (($sorted >= 6) && ($sorted <= 53)) {
								$level1 	= 1;
							} else if (($sorted >= 54) && ($sorted <= 67)) {
								$level1 	= 2;
							} else if (($sorted >= 68) && ($sorted <= 82)) {
								$level1 	= 3;
							} else if (($sorted >= 83) && ($sorted <= 92)) {
								$level1 	= 4;
							} else if (($sorted >= 93) && ($sorted <= 98)) {
								$level1 	= 5;
							} else if ($sorted == 99) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted <= 5) {
									$level2 = 0;
								} else if (($sorted >= 6) && ($sorted <= 53)) {
									$level2 = 1;
								} else if (($sorted >= 54) && ($sorted <= 67)) {
									$level2 = 2;
								} else if (($sorted >= 68) && ($sorted <= 82)) {
									$level2 = 3;
								} else if (($sorted >= 83) && ($sorted <= 92)) {
									$level2 = 4;
								} else if (($sorted >= 93) && ($sorted <= 98)) {
									$level2 = 5;
								} else if ($sorted >= 99) {
									$level2 = rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 2 dungeon
					case 2:
						$gold				= rand(10, 50);
						if ($sorted <= 35) {
							$sorted			= rand(1, 100);
							if ($sorted <= 4) {
								$level1 	= 0;
							} else if (($sorted >= 5) && ($sorted <= 52)) {
								$level1 	= 1;
							} else if (($sorted >= 53) && ($sorted <= 66)) {
								$level1 	= 2;
							} else if (($sorted >= 67) && ($sorted <= 81)) {
								$level1 	= 3;
							} else if (($sorted >= 82) && ($sorted <= 91)) {
								$level1 	= 4;
							} else if (($sorted >= 92) && ($sorted <= 97)) {
								$level1 	= 5;
							} else if (($sorted >= 98) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted <= 4) {
									$level2 = 0;
								} else if (($sorted >= 5) && ($sorted <= 52)) {
									$level2 = 1;
								} else if (($sorted >= 53) && ($sorted <= 66)) {
									$level2 = 2;
								} else if (($sorted >= 67) && ($sorted <= 81)) {
									$level2 = 3;
								} else if (($sorted >= 82) && ($sorted <= 91)) {
									$level2 = 4;
								} else if (($sorted >= 92) && ($sorted <= 97)) {
									$level2 = 5;
								} else if ($sorted >= 98) {
									$level2 = rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 3 dungeon
					case 3:
						$gold				= rand(50, 100);
						if ($sorted <= 40) {
							$sorted			= rand(1, 100);
							if ($sorted <= 3) {
								$level1 	= 0;
							} else if (($sorted >= 4) && ($sorted <= 50)) {
								$level1 	= 1;
							} else if (($sorted >= 51) && ($sorted <= 64)) {
								$level1 	= 2;
							} else if (($sorted >= 65) && ($sorted <= 79)) {
								$level1 	= 3;
							} else if (($sorted >= 80) && ($sorted <= 89)) {
								$level1 	= 4;
							} else if (($sorted >= 90) && ($sorted <= 97)) {
								$level1 	= 5;
							} else if (($sorted >= 98) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted <= 3) {
									$level2 = 0;
								} else if (($sorted >= 4) && ($sorted <= 50)) {
									$level2 = 1;
								} else if (($sorted >= 51) && ($sorted <= 64)) {
									$level2 = 2;
								} else if (($sorted >= 65) && ($sorted <= 79)) {
									$level2 = 3;
								} else if (($sorted >= 80) && ($sorted <= 89)) {
									$level2 = 4;
								} else if (($sorted >= 90) && ($sorted <= 97)) {
									$level2 = 5;
								} else if ($sorted >= 98) {
									$level2 = rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 4 dungeon
					case 4:
						$gold				= rand(100, 200);
						if ($sorted <= 45) {
							$sorted			= rand(1, 100);
							if ($sorted <= 2) {
								$level1 	= 0;
							} else if (($sorted >= 3) && ($sorted <= 47)) {
								$level1 	= 1;
							} else if (($sorted >= 48) && ($sorted <= 63)) {
								$level1 	= 2;
							} else if (($sorted >= 64) && ($sorted <= 78)) {
								$level1 	= 3;
							} else if (($sorted >= 79) && ($sorted <= 88)) {
								$level1 	= 4;
							} else if (($sorted >= 89) && ($sorted <= 96)) {
								$level1 	= 5;
							} else if (($sorted >= 97) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted <= 2) {
									$level2	= 0;
								} else if (($sorted >= 3) && ($sorted <= 47)) {
									$level2	= 1;
								} else if (($sorted >= 48) && ($sorted <= 63)) {
									$level2	= 2;
								} else if (($sorted >= 64) && ($sorted <= 78)) {
									$level2	= 3;
								} else if (($sorted >= 79) && ($sorted <= 88)) {
									$level2	= 4;
								} else if (($sorted >= 89) && ($sorted <= 96)) {
									$level2	= 5;
								} else if ($sorted >= 97) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 5 dungeon
					case 5:
						$gold				= rand(200, 400);
						if ($sorted <= 50) {
							$sorted			= rand(1, 100);
							if ($sorted == 1) {
								$level1 	= 0;
							} else if (($sorted >= 2) && ($sorted <= 45)) {
								$level1 	= 1;
							} else if (($sorted >= 46) && ($sorted <= 61)) {
								$level1 	= 2;
							} else if (($sorted >= 62) && ($sorted <= 78)) {
								$level1 	= 3;
							} else if (($sorted >= 79) && ($sorted <= 87)) {
								$level1 	= 4;
							} else if (($sorted >= 88) && ($sorted <= 95)) {
								$level1 	= 5;
							} else if (($sorted >= 96) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted == 1) {
									$level2	= 0;
								} else if (($sorted >= 2) && ($sorted <= 45)) {
									$level2	= 1;
								} else if (($sorted >= 46) && ($sorted <= 61)) {
									$level2	= 2;
								} else if (($sorted >= 62) && ($sorted <= 78)) {
									$level2	= 3;
								} else if (($sorted >= 79) && ($sorted <= 87)) {
									$level2	= 4;
								} else if (($sorted >= 88) && ($sorted <= 95)) {
									$level2	= 5;
								} else if ($sorted >= 96) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 6 dungeon
					case 6:
						$gold				= rand(400, 800);
						if ($sorted <= 55) {
							$sorted			= rand(1, 100);
							if ($sorted == 1) {
								$level1 	= 0;
							} else if (($sorted >= 2) && ($sorted <= 44)) {
								$level1 	= 1;
							} else if (($sorted >= 45) && ($sorted <= 59)) {
								$level1 	= 2;
							} else if (($sorted >= 60) && ($sorted <= 77)) {
								$level1 	= 3;
							} else if (($sorted >= 78) && ($sorted <= 86)) {
								$level1 	= 4;
							} else if (($sorted >= 87) && ($sorted <= 95)) {
								$level1 	= 5;
							} else if (($sorted >= 96) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted == 1) {
									$level2	= 0;
								} else if (($sorted >= 2) && ($sorted <= 44)) {
									$level2	= 1;
								} else if (($sorted >= 45) && ($sorted <= 59)) {
									$level2	= 2;
								} else if (($sorted >= 60) && ($sorted <= 77)) {
									$level2	= 3;
								} else if (($sorted >= 78) && ($sorted <= 86)) {
									$level2	= 4;
								} else if (($sorted >= 87) && ($sorted <= 95)) {
									$level2	= 5;
								} else if ($sorted >= 96) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 7 dungeon
					case 7:
						$gold				= rand(800, 1600);
						if ($sorted <= 60) {
							$sorted			= rand(1, 100);
							if ($sorted == 1) {
								$level1 	= 0;
							} else if (($sorted >= 2) && ($sorted <= 40)) {
								$level1 	= 1;
							} else if (($sorted >= 41) && ($sorted <= 57)) {
								$level1 	= 2;
							} else if (($sorted >= 58) && ($sorted <= 75)) {
								$level1 	= 3;
							} else if (($sorted >= 76) && ($sorted <= 84)) {
								$level1 	= 4;
							} else if (($sorted >= 85) && ($sorted <= 94)) {
								$level1 	= 5;
							} else if (($sorted >= 95) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted == 1) {
									$level2	= 0;
								} else if (($sorted >= 2) && ($sorted <= 40)) {
									$level2	= 1;
								} else if (($sorted >= 41) && ($sorted <= 57)) {
									$level2	= 2;
								} else if (($sorted >= 58) && ($sorted <= 75)) {
									$level2	= 3;
								} else if (($sorted >= 76) && ($sorted <= 84)) {
									$level2	= 4;
								} else if (($sorted >= 85) && ($sorted <= 94)) {
									$level2	= 5;
								} else if ($sorted >= 95) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 8 dungeon
					case 8:
						$gold				= rand(1600, 3200);
						if ($sorted >= 75) {
							$sorted			= rand(1, 100);
							if ($sorted == 1) {
								$level1 	= 0;
							} else if (($sorted >= 2) && ($sorted <= 38)) {
								$level1 	= 1;
							} else if (($sorted >= 39) && ($sorted <= 55)) {
								$level1 	= 2;
							} else if (($sorted >= 56) && ($sorted <= 72)) {
								$level1 	= 3;
							} else if (($sorted >= 73) && ($sorted <= 82)) {
								$level1 	= 4;
							} else if (($sorted >= 83) && ($sorted <= 93)) {
								$level1 	= 5;
							} else if (($sorted >= 94) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted == 1) {
									$level2	= 0;
								} else if (($sorted >= 2) && ($sorted <= 38)) {
									$level2	= 1;
								} else if (($sorted >= 39) && ($sorted <= 55)) {
									$level2	= 2;
								} else if (($sorted >= 56) && ($sorted <= 72)) {
									$level2	= 3;
								} else if (($sorted >= 73) && ($sorted <= 82)) {
									$level2	= 4;
								} else if (($sorted >= 83) && ($sorted <= 93)) {
									$level2	= 5;
								} else if ($sorted >= 94) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 9 dungeon
					case 9:
						$gold				= rand(3200, 6400);
						if ($sorted <= 80) {
							$sorted			= rand(1, 100);
							if ($sorted == 1) {
								$level1 	= 0;
							} else if (($sorted >= 2) && ($sorted <= 34)) {
								$level1 	= 1;
							} else if (($sorted >= 35) && ($sorted <= 52)) {
								$level1 	= 2;
							} else if (($sorted >= 53) && ($sorted <= 69)) {
								$level1 	= 3;
							} else if (($sorted >= 70) && ($sorted <= 80)) {
								$level1 	= 4;
							} else if (($sorted >= 81) && ($sorted <= 92)) {
								$level1 	= 5;
							} else if (($sorted >= 93) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted == 1) {
									$level2	= 0;
								} else if (($sorted >= 2) && ($sorted <= 34)) {
									$level2	= 1;
								} else if (($sorted >= 35) && ($sorted <= 52)) {
									$level2	= 2;
								} else if (($sorted >= 53) && ($sorted <= 69)) {
									$level2	= 3;
								} else if (($sorted >= 70) && ($sorted <= 80)) {
									$level2	= 4;
								} else if (($sorted >= 81) && ($sorted <= 92)) {
									$level2	= 5;
								} else if ($sorted >= 93) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 10 dungeon
					case 10:
						$gold				= rand(6400, 12800);
						if ($sorted >= 85) {
							$sorted			= rand(1, 100);
							if ($sorted == 1) {
								$level1 	= 0;
							} else if (($sorted >= 2) && ($sorted <= 30)) {
								$level1 	= 1;
							} else if (($sorted >= 31) && ($sorted <= 47)) {
								$level1 	= 2;
							} else if (($sorted >= 48) && ($sorted <= 65)) {
								$level1 	= 3;
							} else if (($sorted >= 66) && ($sorted <= 78)) {
								$level1 	= 4;
							} else if (($sorted >= 79) && ($sorted <= 91)) {
								$level1 	= 5;
							} else if (($sorted >= 92) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted == 1) {
									$level2	= 0;
								} else if (($sorted >= 2) && ($sorted <= 30)) {
									$level2	= 1;
								} else if (($sorted >= 31) && ($sorted <= 47)) {
									$level2	= 2;
								} else if (($sorted >= 48) && ($sorted <= 65)) {
									$level2	= 3;
								} else if (($sorted >= 66) && ($sorted <= 78)) {
									$level2	= 4;
								} else if (($sorted >= 79) && ($sorted <= 91)) {
									$level2	= 5;
								} else if ($sorted >= 92) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 11 dungeon
					case 11:
						$gold				= rand(12800, 25600);
						if ($sorted <= 90) {
							$sorted			= rand(1, 100);
							if ($sorted == 1) {
								$level1 	= 0;
							} else if (($sorted >= 2) && ($sorted <= 27)) {
								$level1 	= 1;
							} else if (($sorted >= 28) && ($sorted <= 43)) {
								$level1 	= 2;
							} else if (($sorted >= 44) && ($sorted <= 59)) {
								$level1 	= 3;
							} else if (($sorted >= 60) && ($sorted <= 74)) {
								$level1 	= 4;
							} else if (($sorted >= 75) && ($sorted <= 88)) {
								$level1 	= 5;
							} else if (($sorted >= 89) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted == 1) {
									$level2	= 0;
								} else if (($sorted >= 2) && ($sorted <= 27)) {
									$level2	= 1;
								} else if (($sorted >= 28) && ($sorted <= 43)) {
									$level2	= 2;
								} else if (($sorted >= 44) && ($sorted <= 59)) {
									$level2	= 3;
								} else if (($sorted >= 60) && ($sorted <= 74)) {
									$level2	= 4;
								} else if (($sorted >= 75) && ($sorted <= 88)) {
									$level2	= 5;
								} else if ($sorted >= 89) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					// If it's a level 12 dungeon
					case 12:
						$gold				= rand(25600, 51200);
						if ($sorted <= 95) {
							$sorted			= rand(1, 100);
							if ($sorted == 1) {
								$level1 	= 0;
							} else if (($sorted >= 2) && ($sorted <= 21)) {
								$level1 	= 1;
							} else if (($sorted >= 22) && ($sorted <= 39)) {
								$level1 	= 2;
							} else if (($sorted >= 40) && ($sorted <= 55)) {
								$level1 	= 3;
							} else if (($sorted >= 56) && ($sorted <= 70)) {
								$level1 	= 4;
							} else if (($sorted >= 71) && ($sorted <= 85)) {
								$level1 	= 5;
							} else if (($sorted >= 86) && ($sorted <= 99)) {
								$level1 	= rand(6, 12);
							} else if ($sorted == 100) {
								$level1 	= rand(6, 12);
								$sorted		= rand(1, 100);
								if ($sorted == 1) {
									$level2	= 0;
								} else if (($sorted >= 2) && ($sorted <= 21)) {
									$level2	= 1;
								} else if (($sorted >= 22) && ($sorted <= 39)) {
									$level2	= 2;
								} else if (($sorted >= 40) && ($sorted <= 55)) {
									$level2	= 3;
								} else if (($sorted >= 56) && ($sorted <= 70)) {
									$level2	= 4;
								} else if (($sorted >= 71) && ($sorted <= 85)) {
									$level2	= 5;
								} else if ($sorted >= 86) {
									$level2	= rand(6, 12);
								}
							}
						}
						break;
					default:
						$gold	= 0;
						$level1	= 0;
						$level2	= false;
						break;
				}
				// Load items
				$item1	= ($level1) ? $RepItem->getRandItemByLevel($level1) : false;
				$item2	= ($level2) ? $RepItem->getRandItemByLevel($level2) : false;
				// If there is item 1
				if ($level1) {
					// Get item type
					$type				= (isset($item['int_bonus_min'])) ? 0 : 1;
					// Get character id
					$id_character		= $RepCharacter->getCharIdByUserId($user['id']);
					// Save Item
					$RepCharacter->saveItemtoInventory($id_character, $item1['id'], 0, $type);
				}
				if ($level2) {
					// Get item type
					$type				= (isset($item['int_bonus_min'])) ? 0 : 1;
					// Get character id
					$id_character		= $RepCharacter->getCharIdByUserId($user['id']);
					// Save Item
					$RepCharacter->saveItemtoInventory($id_character, $item2['id'], 0, $type);
				}
				// Prepare return
				$return['gold']			= $gold;
				$return['name_item1']	= ($item1) ? $item1['vc_name'] : false;
				$return['name_item2']	= ($item2) ? $item2['vc_name'] : false;
				$return['level']		= $level;
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		public function actionOptions() {
			View::render('partial_actionOptions');
		}

		public function useItem() {
			// Classes
			$RepCharacter	= new RepCharacter();
			// Initialize variables
			$return			= false;
			$id_inventory	= (isset($_POST['id_item'])) ? trim($_POST['id_item']) : false;
			$user			= Session::getVar('user');
			// If value were sent
			if ($id_inventory) {
				// Get item info
				$item		= $RepCharacter->getNonCombatItemByInventoryId($id_inventory);
				// If item was found
				if ($item) {
					// Get item data
					$bonus_min	= $item['int_bonus_start'];
					$bonus_max	= $item['int_bonus_end'];
					$type		= $item['id_type'];
					// Calculate Bonus
					$bonus		= rand($bonus_min, $bonus_max);
					// Get char's max hp if it's a healing item
					$max_hp		= ((($type == 1) || ($type == 5)) && ($char = $RepCharacter->getCharByUserId($user['id']))) ? $char['int_hp'] : false;
					// Prepare return
					$return['bonus']	= $bonus;
					$return['type']		= $type;
					$return['max_hp']	= $max_hp;
				}
			}
			// Return
			header('Content-Type: application/json');
			echo json_encode($return);
		}

		public function encounterLog() {
			// Classes
			$RepCombat		= new RepCombat();
			$RepCharacter	= new RepCharacter();
			// Initialize variables
			$return			= false;
			$user			= Session::getVar('user');
			$id_areamap		= (isset($_POST['id_areamap'])) ? trim($_POST['id_areamap']) : false;
			// If values were sent
			if ($id_areamap) {
				// Get Char Id
				$id_char	= $RepCharacter->getCharIdByUserId($user['id']);
				// Make the log and prepare return
				$return		= ($RepCombat->encounterLog($id_areamap, $id_char)) ? 'ok' : false;
			}
			// Return
			echo $return;
		}

	}