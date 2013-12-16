$('document').ready(function() {

	$("#emailfriend").live("keypress", function(e) {
		if (e.keyCode == 13) {
			inviteFriend();
		}
	});

	$("#invitefriend").live("click", function() {
		inviteFriend();
	});

});

function inviteFriend() {
	$email	= $("#emailfriend").val();
	if ($email) {
		$.post('/kqa/GetStarted/inviteFriend/', {email: $email}, function($data) {
			$return = $data.trim();
			if ($return == 'ok') {
				$("#message").html('<br><br> Email sent! <br><br>');
			} else {
				$("#message").html("<br><br> Something went wrong.<br>We weren't able to send your friend an email.<br><br>Error: "+$return+" <br><br>");
			}
		});
	}
}