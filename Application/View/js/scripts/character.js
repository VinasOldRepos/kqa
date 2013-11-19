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

});