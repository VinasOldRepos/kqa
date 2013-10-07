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

		public function world($world = false, $links = false, $mouseovers = false, $navigation = false) {
			$return				= false;
			if ($links) {
				foreach ($links as $link) {
					$id_target[$link['int_pos']]	= $link['id_map_target'];
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
							$return	.= '<div class="go_up go pointer" key="'.$link['id_map_target'].'">up</div>'.PHP_EOL;
						} else if ($link['vc_direction'] == 'left') {
							$return	.= '<div class="go_left go pointer" key="'.$link['id_map_target'].'">&lt;-</div>'.PHP_EOL;
						} else if ($link['vc_direction'] == 'right') {
							$return	.= '<div class="go_right go pointer" key="'.$link['id_map_target'].'">-&gt;</div>'.PHP_EOL;
						} else if ($link['vc_direction'] == 'down') {
							$return	.= '<div class="go_down go pointer" key="'.$link['id_map_target'].'">down</div>'.PHP_EOL;
						}
					}
				}
				for ($i = 1; $i <= 100; $i++) {
					if ($i == 1) {
						$return	.= '<div class="map_row">'.PHP_EOL;
					}
					$pos		= sprintf('%03d', $i);
					if ($world['vc_coord_'.$pos] == '0') {
						$return	.= '<img class="map_tile world_map_tile" text="The Veil of Ignorance" pos="'.$pos.'" key="NewLocalMap" src="/gamemaster/Application/View/img/textures/bk_veil_of_ignorance.gif" width="32" height="32" border="0" alt="" title="" >';
					} else {
						$map	= (isset($id_target[$i])) ? $id_target[$i] : false;
						$text	= ($id_target[$i]) ? $tile_txts[$id_target[$i]] : false;
						$return	.= '<img class="map_tile world_map_tile" text="'.$text.'" pos="'.$pos.'" key="EditLocalMap" map="'.$map.'" src="/gamemaster/Application/View/img/textures/'.$world['vc_coord_'.$pos].'" width="32" height="32" border="0" alt="" title="" >';
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

		public function map($map = false, $id_parentmap = false, $parent_areatype = false, $links = false, $mouseovers = false) {
			$return				= false;
			if ($links) {
				foreach ($links as $link) {
					$targets[$link['int_pos']][0]	= $link['id_icon'];
					$targets[$link['int_pos']][1]	= $link['vc_path'];
					$targets[$link['int_pos']][2]	= ($link['id_map_target'] > 0) ? $link['id_map_target'] : false;
				}
			}
			if ($mouseovers) {
				foreach ($mouseovers as $mouseover) {
					$tile_txts[$mouseover['id']]	= $mouseover['vc_mouseover'];
				}
			}
			if (($map) && ($id_parentmap) && ($parent_areatype)) {
				//$return	.= '<input type="hidden" name="id_areamap" id="id_areamap" value="'.$map['id'].'">'.PHP_EOL;
				$return	.= '<div class="go_left go pointer" key="'.$id_parentmap.'" type="'.$parent_areatype.'" style="margin-left: -20px;">&lt;-</div>'.PHP_EOL;
				for ($i = 1; $i <= 100; $i++) {
					if ($i == 1) {
						$return	.= '<div class="map_row">'.PHP_EOL;
					}
					$pos		= sprintf('%03d', $i);
					if (isset($targets[$i])) {
						$text		= (isset($tile_txts[$targets[$i][2]])) ? $tile_txts[$targets[$i][2]] : false;
						$return		.= '<div class="map_tile local_map_tile" id="'.$pos.'" icon="'.$targets[$i][0].'" text="'.$text.'" status="unselected" bkgrnd="'.$map['vc_coord_'.$pos].'" target="'.$targets[$i][2].'" image="" style="background-image:url(/gamemaster/Application/View/img/textures/'.$map['vc_coord_'.$pos].');">'.PHP_EOL;
						$return		.= '	<img src="/gamemaster/Application/View/img/textures/'.$targets[$i][1].'" width="32" height="32" />'.PHP_EOL;
						$return		.= '</div>'.PHP_EOL;
					} else {
						$return		.= '<div class="map_tile local_map_tile" id="'.$pos.'" icon="" status="unselected" bkgrnd="'.$map['vc_coord_'.$pos].'" image="" style="background-image:url(/gamemaster/Application/View/img/textures/'.$map['vc_coord_'.$pos].');"></div>'.PHP_EOL;
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

		public function encounter($map = false, $id_parentmap = false, $parent_areatype = false, $links = false, $vis_tiles = false) {
			$return				= false;
			if ($links) {
				foreach ($links as $link) {
					$id_target[$link['int_pos']]	= $link['id_map_target'];
				}
			}
			if (($map) && ($vis_tiles) && ($id_parentmap)) {
				//$return			.= '<div class="go_left go pointer" key="'.$id_parentmap.'" type="'.$parent_areatype.'" style="margin-left: -20px;">&lt;-</div>'.PHP_EOL;
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

	}