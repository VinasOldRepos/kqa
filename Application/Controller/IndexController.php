<?php
/************************************************************************************
* Name:				Index Controller												*
* File:				Application\Controller\IndexController.php 						*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the home page's controller.								*
*																					*
* Creation Date:	30/08/2013														*
* Version:			1.12.0830														*
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
	use Application\Controller\Repository\Character		as RepCharacter;
	//use Application\Controller\Repository\Question	as RepQuestion;

	// Other Classes
	use Application\Controller\LogInController			as LogIn;

	class IndexController {

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
				$GLOBALS['this_css']	= '<link rel="stylesheet" href="'.URL_PATH.'/Application/View/css/maps.css" type="text/css" media="screen" />'.PHP_EOL;	// Se não houver, definir como vazio ''
				// Define Menu selection
				//Menu::defineSelected($GLOBALS['controller_name']);
			}
		}

		/*
		Prints out main home page - index()
			@return format	- Render View
		*/
		public static function index() {
			// Classes
			$RepCharacter	= new RepCharacter();
			$ModCharacter	= new ModCharacter();
			// Variables
			$char_list	= false;
			// Get all user's characters
			$user			= Session::getVar('user');
			if ($user) {
				$char_list 	= $RepCharacter->getAllCharsByUserId($user['id']);
				$char_list	= ($char_list) ? $ModCharacter->charList($char_list) : '<br /><br /><div class="partial_link" target="/Characters/newCharacter/">New character</a></div>';
			}
			// Return
			View::set('char_list', $char_list);
			View::render('index');
		}

		public function model() {
			View::render('model');
		}

	}