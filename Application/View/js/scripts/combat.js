$('document').ready(function() {

	// What happens when user clicks on an encounter map tile
	$(".encounter_map_tile").live("click", function() {
		cursorWait(".encounter_map_tile");
		$id_areamap		= $("#id_areamap").val();
		$step			= $("#step").val();
		$monsters_left	= $("#monsters_left").val();
		$target			= $("#target").val();
		$turn			= $("#turn").val();
		$tot_steps		= $("#tot_steps").val();
		if ($tot_steps >= $step) {
			// if it's next floor
			if (($tot_steps == $step) && ($target > 0)) {
				// Load Floor
				$("#id_areamap").val($target);
				$("#step").val('0');
				loadEncounterMap();
				cursorDefault("#center");
			// if it's combat
			} else {
				if (($id_areamap) && ($step)) {
					$.post('/kqa/Combat/loadCombat/', {
					}, function($return) {
						if ($return) {
							$("#turn").html("Turn: Player");
							$("#current_monster").val('1');
							if ($step == 1) {
								//loadCharacterInfo;
								$.post('/kqa/Characters/loadCharInfo/', {}, function($player) {
									contentShowData("#boxleft", $player.character);
									$("#player_hp").val($player.player_hp);
									$("#player_min_dmg").val($player.player_min_dmg);
									$("#player_max_dmg").val($player.player_max_dmg);
									$("#player_me").val($player.player_me);
									$("#player_ds").val($player.player_ds);
									$("#timebonus").val($player.timebonus);

									//loadMonsterList();
									$.post('/kqa/Combat/loadMonsterList/', {
										id_areamap: $id_areamap,
										step:		$step
									}, function($monsters) {
										contentShowData("#boxright", $monsters.monsters);
										//$("#boxright").html($monsters.monsters);
										$("#id_monster").val($monsters.id_monster);
										$("#monster_hp").val($monsters.monster_hp);
										$("#monster_min_dmg").val($monsters.monster_min_dmg);
										$("#monster_max_dmg").val($monsters.monster_max_dmg);
										$("#monster_me").val($monsters.monster_me);
										$("#monster_ds").val($monsters.monster_ds);
										$("#monster_knowledge").val($monsters.monster_knowledge);
										$("#monster_treasure").val($monsters.monster_treasure);
									});

									contentShowData("#center", $return.trim());
									loadQuestion(false, $id_areamap);
									cursorDefault("#center");
								});
							} else {
								//loadMonsterList();
								$.post('/kqa/Combat/loadMonsterList/', {
									id_areamap: $id_areamap,
									step:		$step
								}, function($monsters) {
									contentShowData("#boxright", $monsters.monsters);
									//$("#boxright").html($monsters.monsters);
									$("#id_monster").val($monsters.id_monster);
									$("#monster_hp").val($monsters.monster_hp);
									$("#monster_min_dmg").val($monsters.monster_min_dmg);
									$("#monster_max_dmg").val($monsters.monster_max_dmg);
									$("#monster_me").val($monsters.monster_me);
									$("#monster_ds").val($monsters.monster_ds);
									$("#monster_knowledge").val($monsters.monster_knowledge);
									$("#monster_treasure").val($monsters.monster_treasure);
								});

								contentShowData("#center", $return.trim());
								loadQuestion(false, $id_areamap);
								cursorDefault("#center");
							}
						}
					});
				}
			}
			return false;
		}
	}).live("mouseover", function() {
		document.body.style.cursor	= 'pointer';
	}).live("mouseout", function() {
		document.body.style.cursor	= 'default';
	});

	$("#box_run_round").live("click", function() {
		$action				= false;
		$id_answer			= $('input[name=answer_opt]:checked').val()
		$correct			= $("#correct").val();
		$id_course			= $("#id_course").val();
		$monster_treasure	= $("#monster_treasure").val();
		$turn				= $("#turn").val();
		$player_hp			= $("#player_hp").val();
		$monster_xp			= 1;
		$answer				= $('#opt_'+$correct).attr('caption');
		clearInterval(window.counter);
		if (($id_course) && ($turn)) {
			//contentHide("#box_rightanswer");
			// If it's player's turn
			if ($turn == 'player') {
				// if answer is correct
				$("#id_answer").val($id_answer);
				if ($correct == $id_answer) {
					// Display message
					contentShowData("#box_rightanswer", 'Answer was correct!.<br /><br />It was: "'+$answer+'"');
					setTimeout(function(){contentHide("#box_rightanswer")},5000);
					// Player hits monster
					$action		= playerHits();
				// If the Player got it wrong
				} else {
					// Display message
					contentHide("#box_run_round");
					setTimeout(function(){contentShow("#box_gotwrong")},2000);
					contentShowData("#box_rightanswer", 'You were wrong.<br />The right answer was: "'+$answer+'"');
				}
			// If it's monster's turn
			} else {
				$action		= monstersTurn();
			}
			performAction($action);
		}
		return false;
	});

	$("#box_gotwrong").live("click", function() {
		$id_course	= $("#id_course").val();
		loadQuestion($id_course);
		contentHide("#box_gotwrong");
		setTimeout(function(){contentShow("#box_run_round")},2000);
	});

	$("#back_to_localmap").live("click", function() {
		$id_areamap	= $(this).attr('key');
		loadLocalMap($id_areamap, 'modal');
		return false;
	});

	/*/
	$(".radio_answer_opt").live("click", function() {
		alert($("#correct").val());
		return false;
	});
	/*/

});

function loadQuestion($id_course, $id_areamap) {
	// Initialize variables
	$turn		= $("#turn").val();
	$timebonus	= parseInt($("#timebonus").val());
	$action		= false;
	// IF values were sent
	if (($id_course) || ($id_areamap)) {
		// Set whose turn is
		$turn	= ($turn == 'player') ? 'monster' : 'player';
		$("#turn").val($turn);
		// Load question
		$.post('/kqa/Combat/loadQuestion/', {
			id_course:	$id_course,
			id_areamap:	$id_areamap
		}, function($return) {
			// If wuestion was loaded
			if ($return) {
				// Set and display Question values
				$("#id_answer").val('');
				//contentHide("#box_rightanswer");
				//setTimeout(function(){contentHide("#box_rightanswer")}, 2000);
				$("#box_question").html($return.question);
				$("#box_answers").html($return.answers);
				$("#time_limit").val($return.time_limit);
				$("#id_question").val($return.id_question);
				$("#correct").val($return.correct);
				$("#id_course").val($return.id_course);
				$("#box_counter").html();
				$id_question		= $return.id_question;
				// If it's player's turn
				if ($turn == 'player') {
					// Display captions, time and activate timer
					$("#turn").html("Turn: Player");
					contentHide("#box_rightanswer");
					$time_limit		= parseInt($return.time_limit) * 1000;
					if ($timebonus > 0) {
						$time_limit	= $time_limit + $time_limit * ($timebonus / 100);
					}
					clearInterval(window.counter);
					window.counter	= displayCounter($time_limit);
				// If it's monter's turn
				} else {
					// Display captions
					setTimeout(function(){contentHide("#box_rightanswer")}, 2000);
					$("#turn").html("Turn: Monster");
					$("#box_counter").html("");
				}
			}
		});
	}
}

function playerHits() {
	// Initialize variables
	$player_min_dmg		= parseInt($("#player_min_dmg").val());
	$player_max_dmg		= parseInt($("#player_max_dmg").val());
	$player_hp			= parseInt($("#player_hp").val());
	$player_me			= parseInt($("#player_me").val());
	$player_ds			= parseInt($("#player_ds").val());
	$monster_hp			= parseInt($("#monster_hp").val());
	$monster_ds			= parseInt($("#monster_ds").val());
	$current_monster	= $("#current_monster").val();
	$tot_xp				= parseInt($("#tot_xp").val());
	$return				= false;
	// If values are set
	// Add XP point to temp storage
	$tot_xp				= (($tot_xp) || 0) + 1;
	$("#tot_xp").val($tot_xp);
	// If player uses no weapon
	if (($player_max_dmg == 0) && ($player_min_dmg == 0)) {
		// Set fist damage
		$player_min_dmg	= 1;
		$player_max_dmg	= 2;
	}
	// Calcutale damage
	$player_dmg			= Math.floor(Math.random() * ($player_max_dmg - $player_min_dmg + 1)) + $player_min_dmg;
	$player_dmg			= ($player_dmg + $player_me) - $monster_ds;
	$player_dmg			= ($player_dmg < 1) ? 1 : $player_dmg;
	// Apply damage
	$monster_hp			= $monster_hp - $player_dmg;
	$("#monster_hp").val($monster_hp);
	// If monster died
	if ($monster_hp <= 0) {
		// Display message and hide monsgter from list
		contentHide("#row_"+$current_monster);
		// Prepare return action
		$return			= 'player_won';
	// If monster didn't die
	} else {
		// Update monster hitpoint on list
		contentShowData("#hp_"+$current_monster, $monster_hp);
		// Prepare return action
		$return			= 'loadQuestion';
	}
	// Return
	return $return;
}

function monstersTurn() {
	$player_hp			= parseInt($("#player_hp").val());
	$monster_min_dmg	= parseInt($("#monster_min_dmg").val());
	$monster_max_dmg	= parseInt($("#monster_max_dmg").val());
	$monster_hp			= parseInt($("#monster_hp").val());
	$monster_me			= parseInt($("#monster_me").val());
	$monster_ds			= parseInt($("#monster_ds").val());
	$monster_knowledge	= parseInt($("#monster_knowledge").val());
	$player_ds			= parseInt($("#player_ds").val());
	$return				= false;
	$number				= Math.floor(Math.random() * 100) + 1
	if ($number <= $monster_knowledge) {
		contentShow("#monster_hit");
		//setTimeout(function(){contentHide("#monster_hit")}, 2000);
		$monster_dmg	= Math.floor(Math.random() * ($monster_max_dmg - $monster_min_dmg + 1)) + $monster_min_dmg;
		$monster_dmg	= ($monster_dmg + $monster_me) - $player_ds;
		$monster_dmg	= ($monster_dmg < 1) ? 1 : $monster_dmg;
		$return			= playerDamage($monster_dmg);
	} else {
		contentShow("#monster_missed");
		contentHide("#box_run_round");
		setTimeout(function(){contentHide("#monster_missed")}, 2000);
		setTimeout(function(){contentShow("#box_gotwrong")}, 2000);
		contentShowData("#box_rightanswer", 'The monster didn'+"'"+'t reply correctly.<br />Right answer was: "'+$answer+'".');
	}
	//clearInterval(window.counter);
	return $return;
}

function playerDamage($damage, $cancel_timecount) {
	$player_hp		= parseInt($("#player_hp").val());
	$player_hp		= $player_hp - $damage;
	$("#player_hp").val($player_hp);
	contentShow("#monster_hit");
	setTimeout(function(){contentHide("#monster_hit")}, 2000);
	if ($player_hp <= 0) {
		$return		= 'player_lost';
	} else {
		contentShowData("#current_hp", $player_hp);
		$return		= 'monster_hit';
	}
	clearInterval(window.counter);
	return $return;
}

function dimAnswer($id_answer) {
	if ($id_answer) {
		$("#opt_"+$id_answer).attr("disabled", "disabled");
	}
}

function restartCombat() {
	// Reset values
	$("#id_answer").val('');
	$("#id_monster").val('');
	$("#monster_hp").val('');
	$("#monster_min_dmg").val('');
	$("#monster_max_dmg").val('');
	//$("#player_hp").val('');
	$("#box_question").html('');
	$("#box_answers").html('');
	$("#turn").val('');
	// Variables
	$step				= parseInt($("#step").val());
	$tot_steps			= $("#tot_steps").val();
	$tot_monsters		= $("#tot_monsters").val();
	$current_monster	= parseInt($("#current_monster").val()) + 1;
	// If this step is over
	if (($current_monster > $tot_monsters) && ($step != $tot_steps)) {
		//$("#step").val('1')
		loadEncounterMap();
	// If dungeon is over
	} else if (($current_monster > $tot_monsters) && ($step == $tot_steps)) {
		dungeonEnd();
	// If it's next monster
	} else {
		$("#step").val($step);
		$("#current_monster").val($current_monster);
		loadMonster();
	}
}

function loadMonster() {
	$id_course			= $("#id_course").val();
	$id_areamap			= $("#id_areamap").val();
	$step				= $("#step").val();
	$current_monster	= $("#current_monster").val();
	if (($id_areamap) && ($step) && ($current_monster)&& ($id_course)) {
		$monster_hp		= $("#hp_"+$current_monster).attr('hp');
		$.post('/kqa/Combat/loadMonster/', {
			id_areamap:			$id_areamap,
			step:				$step,
			current_monster:	$current_monster
		}, function($return) {
			if ($return) {
				$("#turn").val('');
				$("#id_monster").val($return.id_monster);
				$("#monster_hp").val(($monster_hp) ? $monster_hp : $return.monster_hp);
				//$("#monster_hp").val($return.monster_hp);
				$("#monster_min_dmg").val($return.monster_min_dmg);
				$("#monster_max_dmg").val($return.monster_max_dmg);
				$("#monster_treasure").val($return.treasure);
				$("#monster_ds").val($return.int_ds);
				$("#monster_me").val($return.int_me);
				$("#monster_knowledge").val($return.int_knowledge);
				$("#monster_treasure").val($return.monster_treasure);
				//$("#turn").val('player');
				loadQuestion($id_course);
			}
		});
	}
	return false;
}

function loadLocalMap($id_areamap, $mode) {
	cursorWait(this);
	clearInterval(window.tileinfo);
	contentHide("#tile_info");
	$id_areamap				= (!$id_areamap) ? $("#id_areamap").val() : $id_areamap;
	if ($id_areamap) {
		$.post('/kqa/Maps/loadLocalMap/', {
			id_areamap:		$id_areamap
		}, function($return) {
			if ($return) {
				if ($mode == 'modal') {
					parent.$("#room").html('');
					parent.$("#turn").html('');
					parent.$("#boxright").html('');
					parent.$("#boxleft").html('');
					parent.$("#center").html('<div id="map_area" class="map_area" style="margin-left: 130px; display: block;">'+$return.map+'</div>');
					parent.$("#id_parentmap").val($return.id_parentmap);
					parent.$("#area_name").html($return.area_name);
					//parent.$("#map_area").html($return.map);
					parent.$("#level").html('Level '+$return.level);
					parent.$.fancybox.close();
				} else {
					$("#id_parentmap").val($return.id_parentmap);
					contentHide("#area_name");
					contentHide("#map_area");
					contentShowData("#area_name",	$return.area_name);
					contentShowData("#map_area",	$return.map);
					contentShowData("#level",		'Level '+$return.level);
				}
			}
			cursorDefault(this);
		});
	} else {
		cursorDefault(this);
	}
}

function loadEncounterMap() {
	$id_areamap		= $("#id_areamap").val();
	$step			= $("#step").val();
	if (($id_areamap) && ($step)) {
		$step++;
		$("#step").val($step);
		$.post('/kqa/Maps/loadEncounterArea/', {
			id_areamap:		$id_areamap,
			step:			$step
		}, function($return) {
			if ($return) {
				$("#id_parentmap").val($return.$id_parentmap);
				$("#id_areamap").val($return.id_areamap);
				$("#target").val($return.target);
				$("#tot_monsters").val($return.tot_monsters);
				$("#current_monster").val($return.current_monster);
				$("#step").val($return.step);
				$("#tot_steps").val($return.tot_steps);
				contentHide("#area_name");
				contentHide("#map_area");
				contentShowData("#area_name",	$return.area_name);
				contentShowData("#center",	'<div id="map_area" class="map_area" style="margin-left: 130px; display: block;">'+$return.map+'</div>');
				contentShowData("#room",		'Room '+$return.step+' of '+$return.tot_steps);
			}
			cursorDefault(".local_map_tile");
		});
	}
}

function addTreasure($treasure) {
	if ($treasure) {
		$tot_treasure	= $("#tot_treasure").val();
		$tot_treasure	= (parseInt($tot_treasure) || 0) + parseInt($treasure);
		$("#tot_treasure").val($tot_treasure);
	}
}

function timeLimit($id_question) {
	// Checa se houve resposta
	$id_answer			= $("#id_answer").val();
	$curr_id_question	= $("#id_question").val();
	if (($id_question == $curr_id_question) && (!$id_answer)) {
		$action			= monstersTurn();
		performAction($action);
		/*/
		if ($id_answer) {
			dimAnswer($id_answer);
		}
		/*/
	}
}

function displayCounter($period) {
	$seconds	= $period / 1000;
	$remaining	= $seconds;
	$i			= 0;
	$("#box_counter").html($seconds);
	return setInterval(function($remaining) {
		$remaining	= parseInt($("#box_counter").html());
		$remaining--;
		if ($remaining >= 0) {
			$("#box_counter").html($remaining);
		} else if ($remaining == -1) {
			$id_answer	= $("#id_answer").val();
			if (!$id_answer) {
				openFancybox('/kqa/Alerts/TimeIsUp/', 404, 254, false);
				playerDamage(1, true);
			}
		}
	}, 1000);
}

function performAction($action) {
	if ($action) {
		if ($action == 'loadQuestion') {
			loadQuestion($("#id_course").val());
		} else if ($action == 'monster_hit') {
			contentShowData("#box_rightanswer", 'The monster got it right.<br />The answer was "'+$answer+'".');
			setTimeout(function() {
				contentHide("#box_rightanswer");
				loadQuestion($("#id_course").val());
			}, 2000);
			//loadQuestion($("#id_course").val());
		// If player died
		} else if ($action == 'player_lost') {
			// Get necessary info
			$id_areamap			= $("#id_areamap").val();
			// Display message and options
			openFancybox('/kqa/Alerts/PlayerDies/'+$id_areamap, 404, 254, false);
			// Redirect to the parent local map
			//$(location).attr('href', '/kqa/Maps/Sophia/');
		// If User won
		} else if ($action == 'player_won') {
			//openFancyboxTemp('/kqa/Alerts/MonsterDies/', 300, 200, false, 3000);
			$monster_treasure	= $("#monster_treasure").val();
			$.post('/kqa/Combat/saveGold/', {monster_treasure: $monster_treasure}, function($return) {
				if ($return) {
					$return		= $return.trim();
					contentShowData("#gold", $return);
				}
			});
			$("#box_counter").html('');
			addTreasure($monster_treasure);
			restartCombat();
		}
	}
}

function dungeonEnd() {
	// Variables
	$id_aremap	= $("#id_areamap").val();
	$tot_xp		= $("#tot_xp").val();
	$("#tot_xp").val(0);
	$("#target").val('');
	if (($id_aremap) && ($tot_xp)) {
		// Save Xp to DB
		saveXP($tot_xp);
		// Calculate treasure drop
		$.post('/kqa/Combat/calculateTreasureDrop/', {
			id_aremap:	$id_aremap
		}, function($return) {
			if ($return) {
				// Save Gold  -- detele this -> add to this /Combat/calculateTreasureDrop procedure	
				$.post('/kqa/Combat/saveGold/', {monster_treasure: $return.gold}, function($return) {
					if ($return) {
						$return		= $return.trim();
						contentShowData("#gold", $return);
					}
				});
				// Display "Dungeon is over" and treasure report Message
				$.fancybox({
					href			: '/kqa/Alerts/DungeonFinished/'+$return.gold+'/'+$tot_xp+'/'+$return.name_item1+'/'+$return.name_item2,
					width			: 404,
					height			: 254,
					autoScale		: false,
					showCloseButton	: true,
					scrolling		: 'no',
					transitionIn	: 'elastic',
					transitionOut	: 'elastic',
					speedIn			: 600,
					speedOut		: 200,
					type			: 'iframe',
					onClosed		: function() {
					// Go to parent map
						$.post('/kqa/Maps/loadParentMap/', {
							id_areamap:	$id_areamap
						}, function($return) {
							if ($return) {
								$("#id_parentmap",				$return.id_parentmap);
								contentHide("#area_name");
								contentHide("#center");
								$("#center").html('');
								$("#turn").html('');
								$("#boxright").html('');
								$("#room").html('');
								contentShowData("#area_name",	$return.area_name);
								contentShowData("#center",		'<div class="map_area" id="map_area" style="margin-left: 130px;">'+$return.map+'</div>');
								contentShowData("#level",		'Level '+$return.level);
							} else {
								alert("Sorry,\n\nWe weren't able to redirect you to the local map you were before this dungeon.\nWe are working on the problem.\n\nMeanhile, please, try reloading the world of Sophia.\n\nThank you.");
							}
						});
					}
				});
			}
		});
		
	}
}

function saveXP($tot_xp) {
	if ($tot_xp) {
		$.post('/kqa/Combat/saveXP/', {tot_xp: $tot_xp}, function($return) {
			if ($return) {
				$return	= $return.trim();
				contentShowData("#xp", $return);
			}
		});
	}
}