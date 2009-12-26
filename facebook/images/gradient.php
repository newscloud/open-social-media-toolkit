<?php
/*
 * Generates a single-pixel-wide vertical gradient PNG with supplied arguments
 * requests will be cached in the same directory if possible 
 * (web server needs write access)
 * URL looks something like this:
 * grad.php?color1=8A31DD&color2=202010&size=100
 * CSS looks something like this:
 * background: #338877 url('grad.php?color1=FF2244&color2=338877&size=200') top left repeat-x;
 */

$args = array(
	"size"     => "16",     // image size (length) in pixels
	"color1"   => "FFFFFF", // top color, in HTML hex
	"color2"   => "000000", // bottom color, in HTML hex
	"alpha"    => "0",		// alpha trasparency 0-127
	"position" => "1"			// for future use
	);

// used to mark unique sessions
$request_id = "";

foreach($args as $key => $val) {
	if(isset($_REQUEST[$key])) {
		$args[$key] = $_REQUEST[$key];
	}
	$request_id .= $key . $args[$key];
}

$args['color1']=str_ireplace('#','',$args['color1']);
$args['color2']=str_ireplace('#','',$args['color2']);
$imX = 1;
$imY = $args['size'];

// unique hash
$request_id = md5($request_id);
$file_cache_path = PATH_CACHE."/gradient_" . $request_id . ".png";

if (file_exists($file_cache_path)) {
	// try reading cached file
	//echo "Found cached file, reading it now.";
	header("Content-type: image/png");
	readfile($file_cache_path);
} else {
	// else create a new one
	$im = imagecreate($imX, $imY); // size of image
	$grad_img = imageColorGradient($im, 0, 0, $imX, $imY, $args['color1'], $args['color2'], $args['alpha']);
	
	if (prepWritableFile($file_cache_path)) {
		//echo "Creating a new cache file";
		imagepng($grad_img, $file_cache_path);
		header("Content-type: image/png");
		readfile($file_cache_path);
	} else {
		// if all caching fails, output directly
		header("Content-type: image/png");
		imagepng($grad_img);
	}
	imagedestroy($grad_img);			
}


/**
 * FUNCTIONS
 */

// Create a rectangle from 0,0 to $imX,$imY in $im, with a (vertical) gradient from #000000 to #FFFFFF.
function imageColorGradient($img,$x1,$y1,$x2,$y2,$f_c,$s_c,$al)
{
	sscanf($f_c, "%2x%2x%2x", $red, $green, $blue);
	$f_c = array($red,$green,$blue);

	sscanf($s_c, "%2x%2x%2x", $red, $green, $blue);
	$s_c = array($red,$green,$blue);

	if($y2>$y1) $y=$y2-$y1;
	else $y=$y1-$y2;

	if($f_c[0]>$s_c[0]) $r_range=$f_c[0]-$s_c[0];
	else $r_range=$s_c[0]-$f_c[0];
	if($f_c[1]>$s_c[1]) $g_range=$f_c[1]-$s_c[1];
	else $g_range=$s_c[1]-$f_c[1];
	if($f_c[2]>$s_c[2]) $b_range=$f_c[2]-$s_c[2];
	else $b_range=$s_c[2]-$f_c[2];
	$r_px=$r_range/$y;
	$g_px=$g_range/$y;
	$b_px=$b_range/$y;
	$r=$f_c[0];
	$g=$f_c[1];
	$b=$f_c[2];

	for($i=0;$i<=$y;$i++){
		$col=imagecolorallocatealpha($img,round($r),round($g),round($b),$al);
		imageline($img,$x1,$y1+$i,$x2,$y1+$i,$col);
		if($f_c[0]<$s_c[0]) $r+=$r_px;
		else $r-=$r_px;
		if($f_c[1]<$s_c[1]) $g+=$g_px;
		else $g-=$g_px;
		if($f_c[2]<$s_c[2]) $b+=$b_px;
		else $b-=$b_px;
	}
	return $img;
}

function prepWritableFile($file_path) {
	if (file_exists($file_path) && is_writable($file_path)) {
		return true;
	} else if (touch($file_path)) {
		return true;
	} else {
		return false;
	}			
}

function validHexColor($input = '000000', $default = '000000') {
	// A valid Hexadecimal color is exactly 6 characters long
	// and eigher a digit or letter from a to f
	return (eregi('^[0-9a-f]{6}$', $input)) ? $input : $default ;
}
?>