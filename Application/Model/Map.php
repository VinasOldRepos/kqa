<?php
/************************************************************************************
* Name:				Map Model														*
* File:				Application\Model\Map.php 										*
* Author(s):		Vinas de Andrade												*
*																					*
* Description: 		This is the Map's model.										*
*																					*
* Creation Date:	15/11/2012														*
* Version:			1.12.1115														*
* License:			http://www.opensource.org/licenses/bsd-license.php BSD			*
*************************************************************************************/

	namespace Application\Model;

	class Map {

		public function world($world = false, $links = false, $mouseovers = false, $navigation = false, $boo_tutorial = false) {
			$return				= false;
			$blocked			= false;
			$id_target			= false;
			$color_branch		= false;
			if ($links) {
				foreach ($links as $link) {
					$id_target[$link['int_pos']]			= $link['id_map_target'];
					if ($boo_tutorial) {
						if ($link['id_branch'] == 1) {
							$color_branch[$link['int_pos']]	= 'field_blue.png';
							$blocked[$link['int_pos']]		= 0;
						} else if ($link['id_branch'] == 2) {
							$color_branch[$link['int_pos']]	= 'field_green.png';
							$blocked[$link['int_pos']]		= 0;
						} else if ($link['id_branch'] == 3) {
							$color_branch[$link['int_pos']]	= 'field_purple.png';
							$blocked[$link['int_pos']]		= 0;
						} else if ($link['id_branch'] == 4) {
							$color_branch[$link['int_pos']]	= 'field_red.png';
							$blocked[$link['int_pos']]		= 0;
						} else if ($link['id_branch'] == 5) {
							$color_branch[$link['int_pos']]	= 'field_yellow.png';
							$blocked[$link['int_pos']]		= 0;
						} else if ($link['id_branch'] == 6) {
							$color_branch[$link['int_pos']]	= 'field_gray.png';
							$blocked[$link['int_pos']]		= 0;
						} else if ($link['id_branch'] == 7) {
							$color_branch[$link['int_pos']]	= 'field_white.png';
							$blocked[$link['int_pos']]		= 0;
						} else if ($link['id_branch'] == 8) {
							$color_branch[$link['int_pos']]	= 'field_orange.png';
							$blocked[$link['int_pos']]		= 0;
						} else {
							$color_branch[$link['int_pos']]	= 'pixel.gif';
							$blocked[$link['int_pos']]		= 1;
						}
					} else {
						if ($link['id_branch'] == 8) {
							$color_branch[$link['int_pos']]	= 'field_orange.png';
							$blocked[$link['int_pos']]		= 0;
						} else {
							$color_branch[$link['int_pos']]	= 'pixel.gif';
							$blocked[$link['int_pos']]		= 1;
						}
					}
				}
			}
			if ($mouseovers) {
				foreach ($mouseovers as $mouseover) {
					$tile_txts[$mouseover['id']]	= $mouseover['vc_mouseover'];
				}
			}
			if ($world) {
				$return	.= '<input type="hidden" name="id_areamap" id="id_areamap" value="'.$world['id'].'">'.PHP_EOL;
				if ($navigation) {
					foreach ($navigation as $link) {
						if ($link['vc_direction'] == 'up') {
							$return	.= '<div class="go_up go pointer" key="'.$link['id_map_target'].'"><img src="/kqa/Application/View/img/img_arrow_up.gif" width="19" height="17" /></div>'.PHP_EOL;
						} else if ($link['vc_direction'] == 'left') {
							$return	.= '<div class="go_left go pointer" key="'.$link['id_map_target'].'"><img src="/kqa/Application/View/img/img_arrow_left.gif" width="17" height="19" /></div>'.PHP_EOL;
						} else if ($link['vc_direction'] == 'right') {
							$return	.= '<div class="go_right go pointer" key="'.$link['id_map_target'].'"><img src="/kqa/Application/View/img/img_arrow_right.gif" width="17" height="19" /></div>'.PHP_EOL;
						} else if ($link['vc_direction'] == 'down') {
							$return	.= '<div class="go_down go pointer" key="'.$link['id_map_target'].'"><img src="/kqa/Application/View/img/img_arrow_down.gif" width="19" height="17" /></div>'.PHP_EOL;
						}
					}
				}
				for ($i = 1; $i <= 100; $i++) {
					if ($i == 1) {
						$return	.= '<div class="map_row">'.PHP_EOL;
					}
					$pos		= sprintf('%03d', $i);
					if ($world['vc_coord_'.$pos] == '0') {
						//$return	.= '<img class="map_tile world_map_tile" text="The Veil of Ignorance" pos="'.$pos.'" key="NewLocalMap" src="/gamemaster/Application/View/img/textures/bk_veil_of_ignorance.gif" width="32" height="32" border="0" alt="" title="" >';
						$return	.= '<div class="map_tile world_map_tile" pos="'.$pos.'" text="The Veil of Ignorance" bkgrnd="bk_veil_of_ignorance.gif" target="" style="background-image:url(/gamemaster/Application/View/img/textures/bk_veil_of_ignorance.gif);"></div>'.PHP_EOL;
					} else {
						$map	= (isset($id_target[$i])) ? $id_target[$i] : false;
						$text	= ($id_target[$i]) ? $tile_txts[$id_target[$i]] : false;
						//$return	.= '<img class="map_tile world_map_tile" text="'.$text.'" pos="'.$pos.'" key="EditLocalMap" map="'.$map.'" src="/gamemaster/Application/View/img/textures/'.$world['vc_coord_'.$pos].'" width="32" height="32" border="0" alt="" title="" >';
						$return	.= '<div class="map_tile world_map_tile" pos="'.$pos.'" text="'.$text.'" map="'.$map.'" bkgrnd="'.$world['vc_coord_'.$pos].'" blocked="'.$blocked[$i].'" style="background-image:url(/gamemaster/Application/View/img/textures/'.$world['vc_coord_'.$pos].');">'.PHP_EOL;
						$return	.= '	<img src="/gamemaster/Application/View/img/textures/'.$color_branch[$i].'" width="32" height="32" />'.PHP_EOL;
						$return	.= '</div>'.PHP_EOL;
					}
					if ($i % 10 == 0) {
						$return	.= '</div>'.PHP_EOL;
						$return	.= '<div class="map_row">'.PHP_EOL;
					}
				}
				$return			.= '</div>'.PHP_EOL;
				$return			.= '<br />'.PHP_EOL;
			}
			return $return;
		}

		public function map($map = false, $id_parentmap = false, $parent_areatype = false, $links = false, $mouseovers = false, $gonetrhu = false) {
			$return				= false;
			$gone				= false;
			if ($links) {
				foreach ($links as $link) {
					if ($gonetrhu) {
						foreach ($gonetrhu as $areamap) {
							if ($link['id_map_target'] == $areamap['id_areamap']) {
								$gone						= true;
								break;
							}
						}
						if (!$gone) {
							$targets[$link['int_pos']][0]	= $link['id_icon'];
							$targets[$link['int_pos']][1]	= $link['vc_path'];
							$targets[$link['int_pos']][2]	= ($link['id_map_target'] !== false) ? $link['id_map_target'] : false;
						} else {
							$targets[$link['int_pos']][0]	= $link['id_icon'];
							$targets[$link['int_pos']][1]	= $link['vc_path'];
							$targets[$link['int_pos']][2]	= false;
							$gone							= false;
						}
					} else {
						$targets[$link['int_pos']][0]	= $link['id_icon'];
						$targets[$link['int_pos']][1]	= $link['vc_path'];
						$targets[$link['int_pos']][2]	= ($link['id_map_target'] !== false) ? $link['id_map_target'] : false;
					}
				}
			}
			if ($mouseovers) {
				foreach ($mouseovers as $mouseover) {
					$tile_txts[$mouseover['id']]	= $mouseover['vc_mouseover'];
				}
			}
			if (($map) && ($id_parentmap) && ($parent_areatype)) {
				//$return	.= '<input type="hidden" name="id_areamap" id="id_areamap" value="'.$map['id'].'">'.PHP_EOL;
				$return				.= '<div class="go_left go pointer" key="'.$id_parentmap.'" type="'.$parent_areatype.'" style="margin-top: 156px;"><img src="/kqa/Application/View/img/img_arrow_left.gif" width="17" height="19" /></div>'.PHP_EOL;
				for ($i = 1; $i <= 100; $i++) {
					if ($i == 1) {
						$return		.= '<div class="map_row">'.PHP_EOL;
					}
					$pos			= sprintf('%03d', $i);
					if (isset($targets[$i])) {
						$text		= (isset($tile_txts[$targets[$i][2]])) ? $tile_txts[$targets[$i][2]] : false;
						if ($targets[$i][2] > 0) {
							$return	.= '<div class="map_tile local_map_tile" id="'.$pos.'" icon="'.$targets[$i][0].'" text="'.$text.'" status="unselected" bkgrnd="'.$map['vc_coord_'.$pos].'" target="'.$targets[$i][2].'" image="" style="background-image:url(/gamemaster/Application/View/img/textures/'.$map['vc_coord_'.$pos].');">'.PHP_EOL;
						} else if ($targets[$i][2] < 0) {
							$return	.= '<div class="map_tile local_map_tile" id="'.$pos.'" icon="'.$targets[$i][0].'" text="Town" status="unselected" bkgrnd="'.$map['vc_coord_'.$pos].'" target="'.$targets[$i][2].'" image="" style="background-image:url(/gamemaster/Application/View/img/textures/'.$map['vc_coord_'.$pos].');">'.PHP_EOL;
						} else {
							$return	.= '<div class="map_tile local_map_tile" id="'.$pos.'" icon="'.$targets[$i][0].'" status="unselected" bkgrnd="'.$map['vc_coord_'.$pos].'" target="'.$targets[$i][2].'" image="" style="background-image:url(/gamemaster/Application/View/img/textures/'.$map['vc_coord_'.$pos].');">'.PHP_EOL;
						}
						$return		.= '	<img src="/gamemaster/Application/View/img/textures/'.$targets[$i][1].'" width="32" height="32" />'.PHP_EOL;
						$return		.= '</div>'.PHP_EOL;
					} else {
						$return		.= '<div class="map_tile local_map_tile" id="'.$pos.'" icon="" status="unselected" bkgrnd="'.$map['vc_coord_'.$pos].'" image="" style="background-image:url(/gamemaster/Application/View/img/textures/'.$map['vc_coord_'.$pos].');"></div>'.PHP_EOL;
					}
					if ($i % 10 == 0) {
						$return		.= '</div>'.PHP_EOL;
						$return		.= '<div class="map_row">'.PHP_EOL;
					}
				}
				$return				.= '</div>'.PHP_EOL;
			}
			return $return;
		}

		public function encounter($map = false, $id_parentmap = false, $parent_areatype = false, $links = false, $vis_tiles = false) {
			$return				= false;
			if ($links) {
				foreach ($links as $link) {
					$id_target[$link['int_pos']]	= $link['id_map_target'];
				}
			}
			if (($map) && ($vis_tiles) && ($id_parentmap)) {
				for ($i = 1; $i <= 100; $i++) {
					$visible	= false;
					if ($i == 1) {
						$return	.= '<div class="map_row">'.PHP_EOL;
					}
					$pos		= sprintf('%03d', $i);
					$target_map	= (isset($id_target[$i])) ? $id_target[$i] : false;
					foreach ($vis_tiles as $tile) {
						if ($tile == $pos) {
							$visible	= true;
						}
					}
					if ($visible) {
						$return		.= '<div class="map_tile encounter_map_tile" id="'.$pos.'" icon="" target="'.$target_map.'" status="unselected" bkgrnd="'.$map['vc_coord_'.$pos].'" image="" style="background-image:url(/gamemaster/Application/View/img/textures/'.$map['vc_coord_'.$pos].');"></div>'.PHP_EOL;
					} else {
						$return		.= '<div class="map_tile encounter_map_tile" id="'.$pos.'" icon="" target="'.$target_map.'" status="unselected" bkgrnd="'.$map['vc_coord_'.$pos].'" image="" style="background-image:url(/gamemaster/Application/View/img/textures/blank_01.png);"></div>'.PHP_EOL;
					}
					if ($i % 10 == 0) {
						$return	.= '</div>'.PHP_EOL;
						$return	.= '<div class="map_row">'.PHP_EOL;
					}
				}
				$return			.= '</div>'.PHP_EOL;
			}
			return $return;
		}

		public function courseList($courses = false) {
			$return		= false;
			$sorter		= array();
			$ret		= array();
			if ($courses) {
				// Order Courses by Course name
				reset($courses);
				foreach ($courses as $ii => $va) {
					$sorter[$ii]	= $va['vc_name'];
				}
				asort($sorter);
				foreach ($sorter as $ii => $va) {
					$ret[$ii]		= $courses[$ii];
				}
				$courses	= false;
				foreach ($ret as $course) {
					$courses[]	= $course;
				}
				// Prepare return
				$return		.= '<div class="monster_row">'.PHP_EOL;
				$return		.= '	<div class="monsters_tit">SEARCH</div>'.PHP_EOL;
				$return		.= '</div>'.PHP_EOL;
				$return		.= 'Find map: <input type="text" name="mapsearch" id="mapsearch" value="" class="tiny_text_input" />'.PHP_EOL;
				$return		.= '<div class="monster_row">'.PHP_EOL;
				$return		.= '	<div class="monster_tit_name">Course</div>'.PHP_EOL;
				$return		.= '	<div class="monster_tit_hp" >Maps</div>'.PHP_EOL;
				$return		.= '</div>'.PHP_EOL;
				$return		.= '<div class="course_list">'.PHP_EOL;
				for ($i = 0; $i < count($courses); $i++) {
					$course	= $i + 1;
					$return	.= '	<div class="course_row" key="'.$courses[$i]['id_course'].'">'.PHP_EOL;
					if (strlen($courses[$i]['vc_name']) < 23) {
						$return	.= '		<div class="course_maps">'.$courses[$i]['vc_name'].'</div>'.PHP_EOL;
					} else {
						$return	.= '		<div class="course_maps">'.substr($courses[$i]['vc_name'], 0, 20).' (...)</div>'.PHP_EOL;
					}
					$return	.= '		<div class="total_maps">'.$courses[$i]['total_maps'].'</div>'.PHP_EOL;
					$return	.= '	</div>'.PHP_EOL;
				}
				$return		.= '</div>'.PHP_EOL;
			}
			return $return;
		}

		public function listMaps($maps = false) {
			$return		= false;
			if ($maps) {
				$return	.= '<div class="maps_list">'.PHP_EOL;
				$return	.= '	<div class="title">MAPS FOUND</div><br />'.PHP_EOL;
				foreach ($maps as $map) {
					$return	.= '	<div class="maps_row" key="'.$map['id'].'">'.$map['vc_name'].' - '.$map['vc_mouseover'].'</div>'.PHP_EOL;
				}
				$return	.='<br /><br />'.PHP_EOL;
				$return	.= '<div class="pointer" id="back_localmap">Return to map</div>'.PHP_EOL;
				$return	.= '</div>'.PHP_EOL;
			}
			return $return;
		}

	}