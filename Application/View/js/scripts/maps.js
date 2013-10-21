$('document').ready(function() {

	// What happens when user clicks on world map tile
	$(".world_map_tile").live("click", function() {
		cursorWait(".world_map_tile");
		clearInterval(window.tileinfo);
		contentHide("#tile_info");
		$target_map				= $(this).attr('map');
		$id_areamap				= $("#id_areamap").val();
		if (($target_map) && ($id_areamap)) {
			$.post('/kqa/Maps/loadLocalMap/', {
				id_areamap:		$target_map,
				id_parentmap:	$id_areamap
			}, function($return) {
				if ($return) {
					$("#id_parentmap").val($id_areamap);
					$("#id_areamap").val($return.id_areamap);
					contentHide("#area_name");
					contentHide("#map_area");
					contentShowData("#area_name",	$return.area_name);
					contentShowData("#map_area",	$return.map);
					contentShowData("#level",		'Level '+$return.level);
				}
				cursorDefault(".world_map_tile");
				return false;
			});
		} else {
			cursorDefault(".world_map_tile");
		}
	}).live("mouseover", function() {
		$text		= $(this).attr('text');
		$pos		= $(this).position();
		if (($text) && ($pos)) {
			window.tileinfo	= showFlyingBox("#tile_info", 1000, $text, $pos.left+25, $pos.top+25);
		}
	}).live("mouseout", function() {
		clearInterval(window.tileinfo);
		contentHide("#tile_info");
	});

	// What happens when user clicks on a Local map tile
	$(".local_map_tile").live("click", function() {
		cursorWait(".local_map_tile");
		clearInterval(window.tileinfo);
		contentHide("#tile_info");
		$target_map				= $(this).attr('target');
		$id_areamap				= $("#id_areamap").val();
		// If data was sent
		if (($target_map) && ($id_areamap)) {
			// If it's a town
			if ($target_map < 0) {
				$.post('/kqa/Maps/loadTown/', {
					id_parentmap:	$id_areamap
				}, function($return) {
					$("#center").html($return);
					cursorDefault(".local_map_tile");
				});
			// If it's an encounter area
			} else {
				$.post('/kqa/Maps/loadEncounterArea/', {
					id_areamap:		$target_map,
					id_parentmap:	$id_areamap
				}, function($return) {
					if ($return) {
						$("#id_parentmap").val($id_areamap);
						$("#id_areamap").val($return.id_areamap);
						$("#tot_monsters").val($return.tot_monsters);
						$("#current_monster").val($return.current_monster);
						$("#step").val($return.step);
						$("#tot_steps").val($return.tot_steps);
						contentHide("#area_name");
						contentHide("#map_area");
						contentShowData("#area_name",	$return.area_name);
						contentShowData("#map_area",	$return.map);
						contentShowData("#level",		'Level '+$return.step);
						contentShowData("#room",		'Room '+$return.step+' of '+$return.tot_steps);
					}
					cursorDefault(".local_map_tile");
				});
			}
		} else {
			cursorDefault(".local_map_tile");
		}
		return false;
	}).live("mouseover", function() {
		$text		= $(this).attr('text');
		$pos		= $(this).position();
		$target_map	= $(this).attr('target');
		if ($target_map) {
			document.body.style.cursor	= 'pointer';
		}
		if (($text) && ($pos)) {
			window.tileinfo	= showFlyingBox("#tile_info", 1000, $text, $pos.left+25, $pos.top+25);
		}
	}).live("mouseout", function() {
		clearInterval(window.tileinfo);
		contentHide("#tile_info");
		document.body.style.cursor	= 'default';
	});

	// What happens when user navigates through world maps
	$(".go").live("click", function() {
		cursorWait(".go");
		$id_areamap			= $(this).attr('key');
		$parent_areatype	= $(this).attr('type');
		if ($parent_areatype == 'local') {
			$url			= '/kqa/Maps/loadLocalMap/';
		} else if ($parent_areatype == 'encounter') {
			$url			= '/kqa/Maps/loadEncounterArea/';
		} else {
			$("#level").html('');
			$url			= '/kqa/Maps/loadWorldMap/';
		}
		if ($id_areamap) {
			$.post($url, {
				id_areamap:	$id_areamap
			}, function($return) {
				if ($return) {
					contentShowData("#id_areamap",	$return.area_name);
					contentHide("#area_name");
					contentHide("#map_area");
					contentShowData("#area_name",	$return.area_name);
					contentShowData("#map_area",	$return.map);
				}
				cursorDefault(".go");
				return false;
			});
		} else {
			cursorDefault(".go");
		}
		return false;
	}).live("mouseover", function() {
		document.body.style.cursor	= 'pointer';
	}).live("mouseout", function() {
		document.body.style.cursor	= 'default';
	});

	$(".opt_town").live("click", function() {
		$id_parentmap	= $("#town_id_parentmap").val();
		$target			= $(this).attr('key');
		if (($id_parentmap) && ($target)) {
			$("#id_parentmap").val($id_parentmap);
			if ($target == 'tutor') {
				$.post('/kqa/Maps/loadTutor/', {
					id_parentmap:	$id_parentmap
				}, function($return) {
					if ($return) {
						$("#center").html($return);
					}
				});
			} else if ($target == 'shop') {
				$.post('/kqa/Maps/loadShop/', {}, function($return) {
					if ($return) {
						$("#center").html($return);
					}
				});
			}
		}
	});

	$("#next_tutor_text").live("click", function() {
		$id_parentmap	= $("#town_id_parentmap").val();
		if ($id_parentmap) {
			cursorWait("#center");
			$.post('/kqa/Maps/loadTutor/', {
				id_parentmap:	$id_parentmap
			}, function($return) {
				if ($return) {
					$("#center").html($return);
				}
				cursorDefault("#center");
			});
		}
	});

	$("#back_localmap").live("click", function() {
		$id_areamap	= $("#town_id_parentmap").val();
		if ($id_areamap) {
			$.post('/kqa/Maps/loadLocalMap/', {
				id_areamap:	$id_areamap
			}, function($return) {
				if ($return) {
					$("#center").html('');
					$("#turn").html('');
					$("#boxright").html('');
					$("#room").html('');
					$("#id_parentmap").val($id_areamap);
					$("#id_areamap").val($return.id_areamap);
					contentHide("#area_name");
					contentHide("#map_area");
					contentShowData("#area_name",	$return.area_name);
					contentShowData("#center",		'<div class="map_area" id="map_area" style="margin-left: 130px;">'+$return.map+'</div>');
					contentShowData("#level",		'Level '+$return.level);
				} else {
					alert("Sorry,\n\nWe weren't able to redirect you to the local map you were before this dungeon.\nWe are working on the problem.\n\nMeanhile, please, try reloading the world of Sophia.\n\nThank you.");
				}
			});
		}
	});
});