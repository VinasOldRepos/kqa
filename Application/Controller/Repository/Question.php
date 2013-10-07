<?php
/************************************************************************************
* Name:				Question Repository												*
* File:				Application\Controller\Questionphp 								*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This contains pre-written functions that execute Database tasks	*
*					related to login information.									*
*																					*
* Creation Date:	11/07/2013														*
* Version:			1.13.0713														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Controller\Repository;

	use Application\Controller\Repository\dbFunctions;
	use SaSeed\Database;

	class Question {


		public function __construct() {
			// 	Question Database Connection
			if (DB_NAME_Q) {
				$GLOBALS['db_q']	= new Database();
				$GLOBALS['db_q']->DBConnection(DB_HOST_Q, DB_USER_Q, DB_PASS_Q, DB_NAME_Q);
			}
		}

		/*
		Check Answer by id  - checkAnswerById($id)
			@param integer	- Answer id
			@return format	- Mixed array
		*/
		public function checkAnswerById($id = false) {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Variables
			$return			= false;
			// Query set up	
			$row			= ($id) ? $db->getRow('tb_answer', 'boo_correct', "id = {$id}") : false;
			if ($row['boo_correct'] == 1) {
				$return		= true;
			}
			// Return
			return $return;
		}

		/*
		Get Field by id  - getFieldById($id)
			@param integer	- Field id
			@return format	- Mixed array
		*/
		public function getFieldById($id = false) {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Query set up	
			$return			= ($id) ? $db->getRow('tb_field', '*', "id = {$id}") : false;
			// Return
			return $return;
		}

		/*
		get random question by course id  - getRandomQuestionIdByCourseId($id)
			@param integer	- Course id
			@return format	- Mixed array
		*/
		public function getRandomQuestionIdByCourseId($id = false) {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Query set up
			$return			= ($id) ? $db->getRow('tb_question_course', 'id_question', "id_course = {$id} ORDER BY RAND() LIMIT 1") : false;
			// Return
			return $return;
		}

		/*
		Get question by id - getQuestionById($id)
			@param integer	- Course id
			@return format	- Mixed array
		*/
		public function getQuestionById($id = false) {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Query set up
			$return			= ($id) ? $db->getRow('tb_question', '*', "id = {$id}") : false;
			// Return
			return $return;
		}

		/*
		Get question by id - getAnswersByQuestionId($id, $num_answers)
			@param integer	- Question id
			@param integer	- Num of answers
			@return format	- Mixed array
		*/
		public function getAnswersByQuestionId($id = false, $num_answers = 4) {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Query set up
			$num_answers	= $num_answers - 1;
			$correct		= ($id) ? $db->getAllRows_Arr('tb_answer', '*', "id_question = {$id} AND boo_correct = 1") : false;
			$incorrect		= ($id) ? $db->getAllRows_Arr('tb_answer', '*', "id_question = {$id} AND boo_correct = 0 LIMIT {$num_answers}") : false;
			$return			= (($correct) && ($incorrect)) ? array_merge($correct, $incorrect) : false;
			// Return
			return $return;
		}

		/*
		Get Branch by Field Id - getBranchFieldId($id)
			@param integer	- Field ID
			@return format	- Mixed array
		*/
		public function getBranchFieldId($id = false) {
			// Database Connection
			$db					= $GLOBALS['db_q'];
			// Initialize variables
			$return				= false;
			if ($id) {
				// Query set up	
				$table			= 'tb_field AS f JOIN tb_branch AS b ON f.id_branch = b.id';
				$select_what	= 'b.*';
				$conditions		= "f.id = {$id}";
				$return			= $db->getRow($table, $select_what, $conditions);
			}
			// Return
			return $return;
		}

		/*
		Get All Branches - getAllBranches()
			@return format	- Mixed array
		*/
		public function getAllBranches() {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Initialize variables
			$return			= false;
			// Query set up	
			$table			= 'tb_branch AS b';
			$select_what	= 'b.id, b.vc_branch AS vc_name';
			$conditions		= "1 ORDER BY vc_branch ASC";
			$return			= $db->getAllRows_Arr($table, $select_what, $conditions);
			// Return
			return $return;
		}

		/*
		Get All Fields  - getAllFields()
			@return format	- Mixed array
		*/
		public function getAllFields() {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Initialize variables
			$return			= false;
			// Query set up	
			$table			= 'tb_field';
			$select_what	= 'id, vc_field AS vc_name';
			$conditions		= "1 ORDER BY vc_field ASC";
			$return			= $db->getAllRows_Arr($table, $select_what, $conditions);
			// Return
			return $return;
		}

		/*
		Get All Courses By Field Id  - getCoursesByFieldId($id)
			@param integer	- Field ID
			@return format	- Mixed array
		*/
		public function getCoursesByFieldId($id = false) {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Initialize variables
			$return			= false;
			// Query set up	
			$table			= 'tb_course';
			$select_what	= 'id, vc_course AS vc_name';
			$conditions		= "id_field = {$id} ORDER BY vc_course ASC";
			$return			= ($id) ? $db->getAllRows_Arr($table, $select_what, $conditions) : false;
			// Return
			return $return;
		}

		/*
		Get Courses By Id  - getCourseById($id)
			@param integer	- ID
			@return format	- Mixed array
		*/
		public function getCourseById($id = false) {
			// Database Connection
			$db				= $GLOBALS['db_q'];
			// Initialize variables
			$return			= false;
			// Query set up	
			$return			= ($id) ? $db->getRow('tb_course', '*', "id = {$id}") : false;
			// Return
			return $return;
		}

		/*
		Get Fields by Branch Id - getFieldsBranchId($id)
			@param integer	- Branch id ID
			@return format	- Mixed array
		*/
		public function getFieldsBranchId($id = false) {
			// Database Connection
			$db					= $GLOBALS['db_q'];
			// Initialize variables
			$return				= ($id) ? $db->getAllRows_Arr('tb_field', 'id, vc_field as vc_name', "id_branch = {$id}") :  false;
			// Return
			return $return;
		}

		/*
		Insert Question into Database - insertQuestion($courses, $id_status, $tx_question, $tx_tutor)
			@param array	- Corses' IDs
			@param integer	- Status ID
			@param integer	- Time limit
			@param text		- Question's text
			@param text		- Tutor's text
			@return integer	- Question's ID
		*/
		public function insertQuestion($courses, $id_status, $int_timelimit, $tx_question, $tx_tutor) {
			// Initialize variables
			$return				= false;
			// Database Connection
			$db					= $GLOBALS['db'];
			// Validate sent information
			if (($courses) && ($id_status)  && ($int_timelimit) && ($tx_question) && ($tx_tutor)) {
				// Prepare values
				$values[]		= $id_status;
				$values[]		= $int_timelimit;
				$values[]		= $tx_question;
				$values[]		= $tx_tutor;
				// Add Question to Database
				$db->insertRow('tb_question', $values, '');
				$question_id	= $db->last_id();
				foreach ($courses as $course) {
					$db->insertRow('tb_question_course', array($course, $question_id), '');
				}
				$return			= $question_id;
			}
			return $return;
		}

		/*
		Insert Answer into Database - insertAnswer($id_question, $vc_answer, $boo_correct)
			@param integer	- Question ID
			@param string	- Answer
			@param boolean	- If answer is correct
			@return boolean
		*/
		public function insertAnswer($id_question = false, $vc_answer = false, $boo_correct = 0) {
			// Initialize variables
			$return				= false;
			// Database Connection
			$db					= $GLOBALS['db'];
			// Validate sent information
			if (($id_question) && ($vc_answer)) {
				if ($boo_correct == 1) {
					$db->updateRow('tb_answer', array('boo_correct'), array(0), 'id_question = '.$id_question);
				}
				// Prepare values
				$values[]		= $id_question;
				$values[]		= $vc_answer;
				$values[]		= $boo_correct;
				// Add Branch to Database
				$return			= $db->insertRow('tb_answer', $values, '');
			}
			return $return;
		}

	}