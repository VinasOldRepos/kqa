<?php
/************************************************************************************
* Name:				Get Started Controller											*
* File:				Application\Controller\GetStartedController.php 				*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This files holds general functions that relate to adding new	*
*					users to the system.											*
*					and can be accessed from anywhere.								*
*																					*
* Creation Date:	02/12/2013														*
* Version:			1.13.0212														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller;

	// Framework Classes
	use SaSeed\View;
	use SaSeed\Session;

	// Repository Classes
	//use Application\Controller\Repository\Character	as RepCharacter;
	use Application\Controller\Repository\Question	as RepQuestion;
	use Application\Controller\Repository\LogIn		as RepLogIn;

	// Model Classes
	use Application\Model\Alert						as ModAlert;

	// Other Classes
	use Application\Controller\LogInController		as LogIn;

	class GetStartedController {

		public function __construct() {
			// Start session
			Session::start();
			/*/
			// Check if user is Logged
			$SesUser					= LogIn::checkLogin();
			if (!$SesUser) {
				// Redirect to login area when not
				header('location: '.URL_PATH.'/LogIn/');
			} else {
				// Define JSs e CSSs utilizados por este controller
				$GLOBALS['this_js']		= ''.PHP_EOL;	// Se não houver, definir como vazio ''
				$GLOBALS['this_css']	= ''.PHP_EOL;	// Se não houver, definir como vazio ''
			}
			/*/
			// Define JSs e CSSs utilizados por este controller
			$GLOBALS['this_js']		= ''.PHP_EOL;	// Se não houver, definir como vazio ''
			$GLOBALS['this_css']	= ''.PHP_EOL;	// Se não houver, definir como vazio ''
		}

		/*
		User invite a friend - inviteFriend()
			@return format	- print
		*/
		public static function inviteFriend() {
			// Add Classes
			$RepQuestion	= new RepQuestion();	
			$ModAlert		= new ModAlert();
			// Initialize variables
			$email			= (isset($_POST['email'])) ? trim($_POST['email']) : false;
			$user			= Session::getVar('user');
			// If values were sent
			if (($user) && ($email)) {
				// Fetch content
				$email_content	= $ModAlert->inviteFriendEmail($user['vc_user'], $user['vc_email'], $email);
				// If email was sent
				if (mail($email, 'You were invited to play Knowledge Question Adventure', $email_content, "From:".$user['vc_email'])) {
					// Register it on database
					$return		= ($RepQuestion->logInviteFriend($user['id'], $email)) ? 'ok' :  'Most likely you have invited that person already.';
					// Prepare return
				// If email was not sent
				} else {
					// Prepare return
					$return		= 'mail object error: email not sent';
				}
			}
			// Print return on screen
			echo $return;
		}

		/*
		New invited user clicks link on email - newInvitedUser()
			@return format	- render view
		*/
		public function newInvitedUser() {
			// Inicialize variables
			$email			= (isset($GLOBALS['params'][1])) ? trim(($GLOBALS['params'][1])) : false;
			// If values were sent
			if ($email) {
				// Prepare reuturn
				View::set('email', $email);
				// Return
				View::render('newInvitedUser');
			}
		}

		/*
		Create new user - newInvitedUser()
			@return format	- render view
		*/
		public function newUser() {
			// Add Clases
			$RepQuestion	= new RepQuestion();
			$RepLogin		= new RepLogin();
			// Initialize variables
			$return			= false;
			$name			= (isset($_POST['name'])) ? trim($_POST['name']) : false;
			$email			= (isset($_POST['email'])) ? trim($_POST['email']) : false;
			$password		= (isset($_POST['password'])) ? md5(trim($_POST['password'])) : false;
			// If values were sent
			if (($name) && ($email) && ($password)) {
				// If email doesn exist in database
				$user			= $RepLogin->checkEmail($email);
				if (!$user) {
					// Add user to the database and prepare return
					$return		= ($RepQuestion->newUser($name, $email, $password)) ? 'ok' : false;
					// Log user in
					$user		= $RepLogin->checkRightsLogin($email, $password);
					if ($user) {
						// Create session with user info
						Session::setVar('user', $user);
					}
				} else {
					$return		= 'Email taken';
				}
			}
			// Return
			echo $return;
		}

	}