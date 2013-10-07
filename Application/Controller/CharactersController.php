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
	//use Application\Model\Map							as ModMap;
	use Application\Model\Character						as ModCharacter;

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

	}