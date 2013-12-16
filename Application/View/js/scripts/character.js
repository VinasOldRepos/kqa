$('document').ready(function() {

	// What happens when user clicks on an encounter map tile
	$(".item_name").live("click", function() {
		$id_item	= $(this).attr('key');
		$place		= $(this).attr('place');
		$item_name	= $(this).html();
		if ($id_item) {
			// Hide clicked list item
			$(this).hide();
			// If it's a wearable item
			if ($place) {
				// Place the item in the right spot
				$.post('/kqa/Characters/placeWearable/', {
					id_item:	$id_item,
					place:		$place
				}, function($return) {
					$return	= $return.trim();
					if ($return) {
						if ($place == 'bothhands') {
							$("#mainhand").html($item_name);
							$("#offhand").html($item_name);
						} else if ($place == 'finger') {
							if ($("#rightfinger").html() == '-') {
								$("#rightfinger").html($item_name);
							} else {
								if ($("#leftfinger").html() == '-') {
									$("#leftfinger").html($item_name);
								} else {
									$("#rightfinger").html($item_name);
								}
							}
						} else if (($place == 'mainhand') || ($place == 'offhand')) {
							if ($("#mainhand").html() == $("#offhand").html()) {
								$("#mainhand").html('-');
								$("#offhand").html('-');
							}
							$("#"+$place).html($item_name);
						} else {
							$("#"+$place).html($item_name);
						}
						$("#"+$place).html($item_name);
						$("#inventory").html($return);
					} else {
						alert("Sorry,\n\nwe weren't able to equip you with this item.");
					}
				});
			// If it's a bag item
			} else {
				// Place the item in bag
				$.post('/kqa/Characters/placeBag/', {
					id_item:	$id_item
				}, function($return) {
					$return	= $return.trim();
					if ($return) {
						$("#bag").html($return);
					} else {
						alert("Sorry,\n\nwe weren't able to equip you with this item.");
					}
				});
			}
		}
	});

	$(".place").live("click", function() {
		$id_item	= $(this).attr('key');
		$place		= $(this).attr('id');
		$main_hand	= $("#mainhand").attr('key');
		$off_hand	= $("#offhand").attr('key');
		if ($place) {
			// Remove Item from that spot
			if (($id_item) && ($id_item == $main_hand) && ($id_item == $off_hand)) {
				$("#mainhand").html('-');
				$("#offhand").html('-');
			} else {
				$("#"+$place).html('-');
			}
			$.post('/kqa/Characters/removeWearable/', {
				place:		$place,
				id_item:	$id_item,
				main_hand:	$main_hand,
				off_hand:	$off_hand
			}, function($return) {
				$return	= $return.trim();
				if ($return) {
					$("#inventory").html($return);
				} else {
					alert("Sorry,\n\nwe weren't able to unequip this item from you.");
				}
			});
		}
	}).live("mouseover", function() {
		if ($(this).html() != '-') {
			document.body.style.cursor	= 'pointer';
		}
	}).live("mouseout", function() {
		document.body.style.cursor	= 'default';
	});

	$(".bagplace").live("click", function() {
		$id_inventory	= $(this).attr('key');
		if ($id_inventory) {
			$(this).hide();
			// Remove Item from bag
			$.post('/kqa/Characters/removeBag/', {
				id_item:	$id_item
			}, function($return) {
				$("#inventory").html($return);
			});
		}
	});

	$("#proceed_dungeon").live("click", function() {
		document.body.style.cursor	= 'wait';
		$target_map	= $("#target_map").val();
		$id_areamap	= $("#id_areamap").val();
		if (($target_map) && ($id_areamap)) {
			// Load char info
			$.post('/kqa/Characters/loadCharInfo/', {}, function($return) {
				if ($return) {
					parent.$("#boxleft").html($return.character);
					parent.$("#player_hp").html($return.player_hp);
					parent.$("#player_min_dmg").html($return.player_min_dmg);
					parent.$("#player_max_dmg").html($return.player_max_dmg);
					parent.$("#player_me").html($return.player_me);
					parent.$("#player_ds").html($return.player_ds);
					parent.$("#timebonus").html($return.timebonus);
				} else {
					alert("Sorry,\n\nwe were not possible to retrieve your Character info.");
				}
			});
			// Load encounter area
			$.post('/kqa/Maps/loadEncounterArea/', {
				id_areamap:		$target_map,
				id_parentmap:	$id_areamap
			}, function($return) {
				if ($return) {
					parent.$("#id_parentmap").val($id_areamap);
					parent.$("#id_areamap").val($return.id_areamap);
					parent.$("#tot_monsters").val($return.tot_monsters);
					parent.$("#current_monster").val($return.current_monster);
					parent.$("#step").val($return.step);
					parent.$("#tot_steps").val($return.tot_steps);
					parent.$("#xp").html($return.xp);
					parent.contentHide("#area_name");
					parent.contentHide("#map_area");
					parent.contentShowData("#area_name",	$return.area_name);
					parent.contentShowData("#map_area",	$return.map);
					parent.contentShowData("#level",		'Level '+$return.level);
					parent.contentShowData("#room",		'Room '+$return.step+' of '+$return.tot_steps);
					// Show action options
					parent.$("#boxright").load("/kqa/Combat/actionOptions/");
					document.body.style.cursor	= 'default';
					parent.document.body.style.cursor	= 'default';
					parent.$.fancybox.close();
				}
			});
		}
	});

	$("#giveup_dungeon").live("click", function() {
		$id_areamap	= parent.$("#id_areamap").val();
		if ($id_areamap) {
			document.body.style.cursor	= 'default';
			parent.document.body.style.cursor	= 'default';
			parent.$.fancybox.close();
		}
	});

});