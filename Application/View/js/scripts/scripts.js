$('document').ready(function() {
	
	// What happens when user clicks on link to open partial content
	$(".partial_link").live("click", function() {
		$url	= $(this).attr('target');
		if ($url) {
			$.post('/kqa'+$url, {}, function($return) {
				if ($return) {
					$("#map_area").html($return);
				} else {
					alert("Sorry,\n\nWe weren't able to load the requsted page.");
				}
			});
		}
	});

	// What happens when user inserts a new character
	$("#new_char_submit").live("click", function() {
		$vc_name	= $("#char_name").val();
		if ($vc_name) {
			$.post('/kqa/Characters/insertCharacter/', {
				vc_name:	$vc_name
			}, function($return) {
				$return		= $return.trim();
				if ($return == 'ok') {
					/*/
					$.post('/kqa/Maps/loadWorldMap/', {}, function($res) {
						if ($res) {
							contentHide("#area_name");
							contentHide("#map_area");
							contentShowData("#area_name",	$res.area_name);
							contentShowData("#map_area",	$res.map);
						} else {
							alert("Sorry,\n\nWe weren't able to load the World Map. Pease try again later.\n\nError: "+$return);
						}
					});
					/*/
					$(location).attr('href', '/kqa/Maps/Sophia/');
				} else {
					alert("Sorry,\n\nWe weren't able to save your character. Please try again later.\n\nError: "+$return);
				}
			});
		}
	});

	// What happens when user selects character
	$(".char_name").live("click", function() {
		$(location).attr('href', '/kqa/Maps/Sophia/');
	});

	$("#close_fancy").live("click", function() {
		parent.$.fancybox.close();
		return false;
	});
});

// ***************************** \\
// ***** GENERAL FUNCTIONS ***** \\
// ***************************** \\

// Change cursor to Wait
function cursorWait($obj) {
	if ($obj) {
		document.body.style.cursor	= 'wait';
		$($obj).css('cursor',		'wait');
		return true;
	}
	return false;
}

// Change cursor to default
function cursorDefault($obj) {
	if ($obj) {
		document.body.style.cursor	= 'default';
		$($obj).css('cursor',		'default');
		return true;
	}
	return false;
}

// Handles ajax content being showed
function contentShowData($object, $data) {
	$($object).hide();
	$($object).html($data);
	$($object).show(400);
}

// Handles static content being showed
function contentShow($object) {
	$($object).hide();
	$($object).show(400);
}


// Handles static content being hidden
function contentHide($object) {
	$($object).hide(200);
}

// Handles static content being displayed in a flying box
function showFlyingBox($obj, $interval, $content, $pos_left, $pos_top) {
	if (($obj) && ($interval) && ($content) && ($pos_left) && ($pos_top)) {
		return setTimeout(function() {
			$($obj).hide();
			$($obj).html($content);
			$($obj).css('left', $pos_left);
			$($obj).css('top', $pos_top);
			$($obj).show(400);
		}, $interval);
	}
}

// Opens fancybox
function openFancybox($url, $width, $height, $close_btn) {
	$.fancybox({
		href			: $url,
		width			: $width,
		height			: $height,
		autoScale		: false,
		showCloseButton	: $close_btn,
		scrolling		: 'no',
		transitionIn	: 'elastic',
		transitionOut	: 'elastic',
		speedIn			: 600,
		speedOut		: 200,
		type			: 'iframe'/*,
		onClosed		: function() {
			$key		= parent.$('#pager_pg_num').val();
			$ordering	= parent.$('#ordering').val();
			$offset		= parent.$('#offset').val();
			$limit		= parent.$('#limit').val();
			$direction	= parent.$('#direction').val();
			$direction	= parent.$('#direction').val();
			$str_search	= parent.$('#str_search').val();
			$parent_id	= parent.$('#parent_id').val();
			$actionurl	= actionURL();
			if (($key) && ($ordering) && ($offset) && ($limit) && ($direction) && ($actionurl)) {
				document.body.style.cursor	= 'wait';
				//fetchResults($actionurl, $key, $ordering, $offset, $limit, $direction, $str_search, $parent_id);
			}
		}*/
	});
}

// Opens fancybox for 2 secs
function openFancyboxTemp($url, $width, $height, $close_btn, $milisecs) {
	$milisecs			= (!$milisecs) ? 3000 : $milisecs;
	$.fancybox({
		href			: $url,
		width			: $width,
		height			: $height,
		autoScale		: false,
		showCloseButton	: $close_btn,
		scrolling		: 'no',
		transitionIn	: 'elastic',
		transitionOut	: 'elastic',
		speedIn			: 600,
		speedOut		: 200,
		type			: 'iframe'/*,
		onClosed		: function() {
		}*/
	});
	setTimeout(function() {
		$.fancybox.close();
	}, $milisecs);
}

// Similiar to the PHP one
function sprintf($length, $char, $string) {
	if (($length) && ($string)&& ($char)) {
		$str_len	= $string.length;
		if ($length > $str_len) {
			for ($i = 0; $i < $length - $str_len; $i++) {
				$string	= $char+$string;
			}
		}
		return $string;
	}
}