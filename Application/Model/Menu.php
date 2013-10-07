<?php
/************************************************************************************
* Name:				Menu Model														*
* File:				Application\Model\Menu.php 										*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the Menu's model.										*
*																					*
* Creation Date:	03/05/2012														*
* Version:			1.12.1115														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Model;

	Final class Menu {

		public static function defineSelected ($controller = false) {
			if ($controller) {
				$controller						= str_replace('Controller', '', $controller);
				$GLOBALS['menu']['blank']['display']			= 'none';
				if ($controller == 'Maps') {
					$GLOBALS['menu']['maps']['css']				= 'item_on';
					$GLOBALS['menu']['maps']['display']			= 'block';
					$GLOBALS['menu']['maps']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['items']['css']			= 'item_off';
					$GLOBALS['menu']['items']['display']		= 'none';
					$GLOBALS['menu']['items']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['textures']['css']			= 'item_off';
					$GLOBALS['menu']['textures']['display']		= 'none';
					$GLOBALS['menu']['textures']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt2_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt3_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt4_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt5_css']	= 'details_item_off';

					$GLOBALS['menu']['monsters']['css']			= 'item_off';
					$GLOBALS['menu']['monsters']['display']		= 'none';
					$GLOBALS['menu']['monsters']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['monsters']['opt2_css']	= 'details_item_off';

					$GLOBALS['menu']['users']['css']			= 'item_off';
					$GLOBALS['menu']['users']['display']		= 'none';
					$GLOBALS['menu']['users']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['users']['opt2_css']		= 'details_item_off';
				} else if ($controller == 'Items') {
					$GLOBALS['menu']['maps']['css']				= 'item_off';
					$GLOBALS['menu']['maps']['display']			= 'none';
					$GLOBALS['menu']['maps']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['items']['css']			= 'item_on';
					$GLOBALS['menu']['items']['display']		= 'block';
					$GLOBALS['menu']['items']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['textures']['css']			= 'item_off';
					$GLOBALS['menu']['textures']['display']		= 'none';
					$GLOBALS['menu']['textures']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt2_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt3_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt4_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt5_css']	= 'details_item_off';

					$GLOBALS['menu']['monsters']['css']			= 'item_off';
					$GLOBALS['menu']['monsters']['display']		= 'none';
					$GLOBALS['menu']['monsters']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['monsters']['opt2_css']	= 'details_item_off';

					$GLOBALS['menu']['users']['css']			= 'item_off';
					$GLOBALS['menu']['users']['display']		= 'none';
					$GLOBALS['menu']['users']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['users']['opt2_css']		= 'details_item_off';
				} else if ($controller == 'Textures') {
					$GLOBALS['menu']['maps']['css']				= 'item_off';
					$GLOBALS['menu']['maps']['display']			= 'none';
					$GLOBALS['menu']['maps']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['items']['css']			= 'item_off';
					$GLOBALS['menu']['items']['display']		= 'none';
					$GLOBALS['menu']['items']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['textures']['css']			= 'item_on';
					$GLOBALS['menu']['textures']['display']		= 'block';
					$GLOBALS['menu']['textures']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt2_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt3_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt4_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt5_css']	= 'details_item_off';

					$GLOBALS['menu']['monsters']['css']			= 'item_off';
					$GLOBALS['menu']['monsters']['display']		= 'none';
					$GLOBALS['menu']['monsters']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['monsters']['opt2_css']	= 'details_item_off';

					$GLOBALS['menu']['users']['css']			= 'item_off';
					$GLOBALS['menu']['users']['display']		= 'none';
					$GLOBALS['menu']['users']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['users']['opt2_css']		= 'details_item_off';
				} else if ($controller == 'Monsters') {
					$GLOBALS['menu']['maps']['css']				= 'item_off';
					$GLOBALS['menu']['maps']['display']			= 'none';
					$GLOBALS['menu']['maps']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['items']['css']			= 'item_off';
					$GLOBALS['menu']['items']['display']		= 'none';
					$GLOBALS['menu']['items']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['textures']['css']			= 'item_off';
					$GLOBALS['menu']['textures']['display']		= 'none';
					$GLOBALS['menu']['textures']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt2_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt3_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt4_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt5_css']	= 'details_item_off';

					$GLOBALS['menu']['monsters']['css']			= 'item_on';
					$GLOBALS['menu']['monsters']['display']		= 'block';
					$GLOBALS['menu']['monsters']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['monsters']['opt2_css']	= 'details_item_off';

					$GLOBALS['menu']['users']['css']			= 'item_off';
					$GLOBALS['menu']['users']['display']		= 'none';
					$GLOBALS['menu']['users']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['users']['opt2_css']		= 'details_item_off';
				} else if ($controller == 'Users') {
					$GLOBALS['menu']['maps']['css']				= 'item_off';
					$GLOBALS['menu']['maps']['display']			= 'none';
					$GLOBALS['menu']['maps']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['items']['css']			= 'item_off';
					$GLOBALS['menu']['items']['display']		= 'none';
					$GLOBALS['menu']['items']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['textures']['css']			= 'item_off';
					$GLOBALS['menu']['textures']['display']		= 'none';
					$GLOBALS['menu']['textures']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt2_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt3_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt4_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt5_css']	= 'details_item_off';

					$GLOBALS['menu']['monsters']['css']			= 'item_off';
					$GLOBALS['menu']['monsters']['display']		= 'none';
					$GLOBALS['menu']['monsters']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['monsters']['opt2_css']	= 'details_item_off';

					$GLOBALS['menu']['users']['css']			= 'item_on';
					$GLOBALS['menu']['users']['display']		= 'block';
					$GLOBALS['menu']['users']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['users']['opt2_css']		= 'details_item_off';
				} else {
					$GLOBALS['menu']['blank']['display']		= 'block';

					$GLOBALS['menu']['maps']['css']				= 'item_off';
					$GLOBALS['menu']['maps']['display']			= 'none';
					$GLOBALS['menu']['maps']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['maps']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['items']['css']			= 'item_off';
					$GLOBALS['menu']['items']['display']		= 'none';
					$GLOBALS['menu']['items']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt2_css']		= 'details_item_off';
					$GLOBALS['menu']['items']['opt3_css']		= 'details_item_off';

					$GLOBALS['menu']['textures']['css']			= 'item_off';
					$GLOBALS['menu']['textures']['display']		= 'none';
					$GLOBALS['menu']['textures']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt2_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt3_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt4_css']	= 'details_item_off';
					$GLOBALS['menu']['textures']['opt5_css']	= 'details_item_off';

					$GLOBALS['menu']['monsters']['css']			= 'item_off';
					$GLOBALS['menu']['monsters']['display']		= 'none';
					$GLOBALS['menu']['monsters']['opt1_css']	= 'details_item_off';
					$GLOBALS['menu']['monsters']['opt2_css']	= 'details_item_off';

					$GLOBALS['menu']['users']['css']			= 'item_off';
					$GLOBALS['menu']['users']['display']		= 'none';
					$GLOBALS['menu']['users']['opt1_css']		= 'details_item_off';
					$GLOBALS['menu']['users']['opt2_css']		= 'details_item_off';
				}
			}
		}

	}