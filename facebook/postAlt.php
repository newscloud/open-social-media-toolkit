<?php
	// for posting through the callback
	// switch on p and o

	function smartRedirect($url) {
		if(headers_sent()) {
			echo '<script type="text/javascript">window.location = "' . $url . '";</script>';
			die();
		}
		else {
			@header("Location: " . $url);
		}
	}



	switch ($o)
	{
	case 'challenge':
		$passback = '';
		require_once(PATH_FACEBOOK .'/pages/pageChallengeSubmit.class.php');
		$b = pageChallengeSubmit::processChallengeSubmit($code, $passback);
		$msg = urlencode($code);
		if ($b)
	    	smartRedirect( URL_CANVAS . "/?p=profile&memberid={$_POST['fb_sig_user']}&message=$msg");
    	else
	    	smartRedirect(URL_CANVAS . "/?p=challengeSubmit&id={$_POST['challengeid']}&message=$msg".$passback);    	

		break;
	default:
		break;
		
		
	}
	
?>