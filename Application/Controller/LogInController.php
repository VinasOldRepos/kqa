<?php
/************************************************************************************
* Name:				Login Controller												*
* File:				Application\Controller\LongInController.php 					*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This files holds general functions that relate to user session,	*
*					and can be accessed from anywhere.								*
*																					*
* Creation Date:	24/04/2013														*
* Version:			1.13.0424														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller;

	// Framework Classes
	use SaSeed\View;
	use SaSeed\Session;

	// Repository Classes
	use Application\Controller\Repository\LogIn	as RepLogIn;

	Final class LogInController {

		public function __construct() {
			// Start session
			Session::start();
			// Define JSs e CSSs utilizados por este controller
			$GLOBALS['this_js']		= '<script type="text/javascript" src="/kqa/Application/View/js/scripts/login.js"></script>'.PHP_EOL;	// Se não houver, definir como vazio ''
			$GLOBALS['this_css']	= '<link href="'.URL_PATH.'/Application/View/css/login.css" rel="stylesheet">'.PHP_EOL;	// Se não houver, definir como vazio ''
		}

		/*
		Prints out main login page - start()
			@return format	- print
		*/
		public static function index() {
			View::render('login');
 		}

		/*
		Try to log user in - in()
			@return format	- print
		*/
		public static function in() {
			// Call in classes
			$RepLogIn		= new RepLogIn();
			// Initialize variables
			$return			= 'false';
			// Get form data
			$email			= (isset($_POST['email'])) ? trim($_POST['email']) : false;
			$password		= (isset($_POST['password'])) ? md5(trim($_POST['password'])) : false;
			// If data was sent
			if (($email) && ($password)) {
				// Get user info from DB and check password
				$user		= $RepLogIn->checkRightsLogin($email, $password);
				// If user is found and password match
				if ($user) {
					// Create session with user info
					Session::setVar('user', $user);
					// prepare return
					$return	= 'true';
				// If user not found and/or password don't match
				} else {
					// prepare return
					$return	= 'false';
				}
			}
			// Print return on screen
			echo $return;
		}

		/*
		Checks if user is logged and return boolean - start()
			@return format	- boolean
		*/
		public static function checkLogin() {
			$user	= Session::getVar('user');
			return ($user['rights']['boo_kqa']) ? true : false;
		}

		/*
		Checks if user is logged and return its data - start()
			@return format	- false/array
		*/
		public static function getUserInfo() {
			return (Session::getVar('user')) ? Session::getVar('user') : false;
		}
	}