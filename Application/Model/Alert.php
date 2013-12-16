<?php
/************************************************************************************
* Name:				Alerts Model													*
* File:				Application\Model\Alert.php 									*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the Alerts' model.										*
*																					*
* Creation Date:	28/11/2013														*
* Version:			1.13.2811														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Model;

	class Alert {

		public function inviteFriendEmail($user = false, $user_email = false, $friend_email = false) {
			$return		= false;
			if (($user) && ($user_email) && ($friend_email)) {
				$return	= 'You have been invited to play Knowledge Adventure Quest. We are still to figure out this text. It was '.$user.' ('.$user_email.') who has invited you.'.PHP_EOL;
				$return	.= 'If you want to play, access the following link http://www.corrastudios.com/kqa/GetStarted/newInvitedUser/'.$friend_email;
			}
			return $return;
		}

	}