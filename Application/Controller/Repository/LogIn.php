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

	use SaSeed\Database;

	class LogIn {
		
		public function __construct() {
			// 	Question Database Connection
			if (DB_NAME_Q) {
				$GLOBALS['db_q']	= new Database();
				$GLOBALS['db_q']->DBConnection(DB_HOST_Q, DB_USER_Q, DB_PASS_Q, DB_NAME_Q);
			}
		}

		/*
		Check if user has access and gets its rights - checkRightsLogin($email, $password)
			@param string	- User's email
			@param string	- User's password
			@return format	- Mixed array
		*/
		public function checkRightsLogin($email = false, $password = false) {
			// Database Connection
			$db							= $GLOBALS['db_q'];
			// Initialize variables
			$return						= false;
			$user						= false;
			$permissions				= false;
			// if data was sent
			if (($email) && ($password)) {
				// Query set up
				$table					= 'tb_user';
				$select_what			= 'id, vc_user, vc_email';
				$conditions				= "vc_email = '{$email}' AND vc_password = '{$password}' AND boo_active = 1";
				// Run query
				$return					= $db->getRow($table, $select_what, $conditions);
				// If user was found
				if ($return) {
					// Get user's permissions
					$permissions		= $this->getUserPermissions($return['id']);
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
			$db					= $GLOBALS['db_q'];
			$return				= false;
			if ($id) {
				// Query set up
				$table			= 'tb_user_permissions';
				$select_what	= 'boo_kqa, boo_questionmaster, boo_gamemaster';
				$conditions		= "id_user = {$id}";
				// Run query
				$return			= $db->getRow($table, $select_what, $conditions);
			}
			return $return;
		}

	}