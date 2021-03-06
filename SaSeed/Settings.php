<?php
/************************************************************************************
* Name:				General Settings												*
* File:				Application\FramworkCore\Settings.php 							*
* Author(s):		Vinas de Andrade, Raphael Pawlik e Leandro Menezes				*
*																					*
* Description: 		This file holds basic settings for the whole web-site.			*
*																					*
* Creation Date:	14/11/2012														*
* Version:			1.13.0523														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/


	// General Settings
	$pathinfo	= pathinfo(dirname(__FILE__));
	define('APP_PATH',	$pathinfo['dirname'].DIRECTORY_SEPARATOR);
	define('VIEW_PATH',	APP_PATH.'/Application/View'.DIRECTORY_SEPARATOR);
	define('URL_PATH',	'/kqa'); // DEV
	//define('URL_PATH',	''); // LIVE
	define('ENV',		'DEV');
	//define('ENV',		'LIVE');

	// Timezone and regional settings
	date_default_timezone_set('America/Sao_Paulo');
	setlocale(LC_MONETARY,	'pt_BR');
	setlocale(LC_ALL,		'Portuguese_Brazil.1252 ');

	// Database Settings - GameMaster DB
	$conection_config = parse_ini_file(APP_PATH.'/SaSeed/Config/database_gm.ini', true);
	define('DB_HOST', $conection_config['DATABASE_'.ENV]['local']);
	define('DB_USER', $conection_config['DATABASE_'.ENV]['user']);
	define('DB_PASS', $conection_config['DATABASE_'.ENV]['password']);
	define('DB_NAME', $conection_config['DATABASE_'.ENV]['dbname']);
	//define('DB_DRIVER', $conection_config['DATABASE_'.ENV]['driver']); // to be used with PDO

	// Database Settings - QuestionMaster DB
	$conection_config = parse_ini_file(APP_PATH.'/SaSeed/Config/database_qm.ini', true);
	define('DB_HOST_Q', $conection_config['DATABASE_'.ENV]['local']);
	define('DB_USER_Q', $conection_config['DATABASE_'.ENV]['user']);
	define('DB_PASS_Q', $conection_config['DATABASE_'.ENV]['password']);
	define('DB_NAME_Q', $conection_config['DATABASE_'.ENV]['dbname']);
	//define('DB_DRIVER', $conection_config['DATABASE_'.ENV]['driver']); // to be used with PDO

	// XSS String List
	$xss_strings	= parse_ini_file(APP_PATH.'/SaSeed/Config/xss.ini');