<?php
/************************************************************************************
* Name:				Login Repository												*
* File:				Application\Controller\LogIn.php 								*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This contains pre-written functions that execute Database tasks	*
*					related to login information.									*
*																					*
* Creation Date:	28/04/2013														*
* Version:			1.12.1127														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller\Repository;

	class LogIn {
		
		/*
		Check if user has access and gets its rights - checkRightsLogin($email, $password)
			@param string	- User's email
			@param string	- User's password
			@return format	- Mixed array
		*/
		public function checkRightsLogin($email = false, $password = false) {
			// Database Connection
			$db							= $GLOBALS['db'];
			// Initialize variables
			$return						= false;
			$user						= false;
			$permissions				= false;
			// if data was sent
			if (($email) && ($password)) {
				// Query set up
				$table					= 'tb_user';
				$select_what			= 'id, vc_user, vc_email, int_token';
				$conditions				= "vc_email = '{$email}' AND vc_password = '{$password}' AND boo_active = 1";
				// Run query
				$return					= $db->getRow($table, $select_what, $conditions);
				// If user was found
				if ($return) {
					// Get user's permissions
					$permissions		= $this->getUserPermissions($user['id']);
					$return['rights']	= $permissions;
				}
			}
			// Return
			return $return;
		}

		/*
		Get User's access rights by Id - getUserPermissions($id)
			@param integer	- User's id
			@return format	- Mixed array
		*/
		function getUserPermissions($id = false) {
			$return				= false;
			if ($id) {
				// Query set up
				$table			= 'tb_user_game_profile';
				$select_what	= 'id_game, id_profile';
				$conditions		= "id_user = {$id}";
				// Run query
				$return			= $db->getRow($table, $select_what, $conditions);
			}
			return $return;
		}

	}