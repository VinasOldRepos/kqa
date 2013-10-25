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
						/*if ($place == 'bothhands') {
							$("#mainhand").html($item_name);
							$("#offhand").html('(busy)');
						} else if ($place == 'offhand') {
							$("#offhand").html($item_name);
						} else {
							$("#"+$place).html($item_name);
						}*/
						$("#"+$place).html($item_name);
						$("#inventory").html($return);
					} else {
						alert("Sorry,\n\nwe weren't able to equip you with this item.\n\nReason: "+$return);
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
		if ($place) {
			// Remove Item from that spot
			$.post('/kqa/Characters/removeWearable/', {
				place:		$place
			}, function($return) {
				$return	= $return.trim();
				if ($return) {
					$("#"+$place).html('-');
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
		$id_item	= $(this).attr('key');
		if ($id_item) {
			$(this).hide();
			// Remove Item from bag
			$.post('/kqa/Characters/removeBag/', {
				id_item:		$id_item
			}, function($return) {
				$("#inventory").html($return);
			});
		}
	});

});