<?php
 /* Image server for an image for the story */
	if (isset($_GET['id'])) {
		$imageid=$_GET['id'];
		$x=$_GET['x']; // maximum width
		$y=$_GET['y']; // maximum height
		if (!is_numeric($imageid) OR !is_numeric($x) OR !is_numeric($y)) die();
		$dx=$x;
		$dy=$y;
		if (isset($_GET['fixed'])) $fixed=$_GET['fixed']; else $fixed=''; // fix x or y dimension
		if (isset($_GET['crop'])) $crop='c'; else $crop=''; // crop the height
		$file_cache_path=PATH_CACHE.'/scaled_'.$imageid.'_'.$dx.'_'.$dy.'_'.$fixed.$crop.'.jpg';
		if (file_exists($file_cache_path)) {
			// try reading cached file
			header("Content-type: image/jpg");
			readfile($file_cache_path);
		} else {
			require_once(PATH_CORE.'classes/db.class.php');
			$db=new cloudDatabase();
			$q=$db->queryC("SELECT url FROM ContentImages WHERE id=$imageid");
			if (!$q) die();
			$data=$db->readQ($q);
			// else create a new scaled image
			$file_orig=$data->url; // old PATH_CACHE.'/story_'.$imageid.'.jpg';
			$srcImage = imagecreatefromjpeg($file_orig);
			list($srcWidth, $srcHeight) = getimagesize($file_orig);
			$srcWidth>$srcHeight?$layout='landscape':$layout='portrait';
			if (($layout=='landscape' AND $srcWidth<$dx) OR ($layout=='portrait' AND $srcHeight<$dy)) {
				// too small, use original image
				$dx=$srcWidth;
				$dy=$srcHeight;
			}
			switch ($fixed) {
				case 'y':
				   // scale the width to the requested height
			       $dx  = round($srcWidth * ($dy/$srcHeight));
				break;
				case 'x':
					// scale the height to the requested width
					$dy=round($srcHeight * ($dx/$srcWidth));		    				
				break;
				default:
					// scale the shortest axis
					if ($layout=='portrait') {
				       $dx  = round($srcWidth * ($dy/$srcHeight));
					} else {
						$dy=round($srcHeight * ($dx/$srcWidth));		    				
					}
				break;
			}
			if ($crop=='c' AND $dy>$y) {
		   		// scale it to tempImage
			   $tempImage = imagecreatetruecolor( $dx,$dy);
			   ImageCopyResampled( $tempImage, $srcImage, 0, 0, 0, 0, $dx, $dy, $srcWidth, $srcHeight );
			   // crop it to destImage
			   $destImage = imagecreatetruecolor( $dx,$y);
			   imagecopy($destImage,$tempImage,0,0,0,0,$dx,$y);
			   $bordercolor = ImageColorAllocate($destImage,0,0,0);			
			   ImageRectangle($destImage,0,0,($dx-1),($y-1),$bordercolor);			  
			   imagedestroy($tempImage);	
			} else if ($crop=='c' AND $dx>$x) {
		   		// scale it to tempImage
			   $tempImage = imagecreatetruecolor( $dx,$dy);
			   ImageCopyResampled( $tempImage, $srcImage, 0, 0, 0, 0, $dx, $dy, $srcWidth, $srcHeight );
			   // crop it to destImage
			   $destImage = imagecreatetruecolor( $x,$dy);
			   imagecopy($destImage,$tempImage,0,0,0,0,$x,$dy);
			   $bordercolor = ImageColorAllocate($destImage,0,0,0);			
			   ImageRectangle($destImage,0,0,($x-1),($dy-1),$bordercolor);			  
			   imagedestroy($tempImage);	
			} else {
			   $destImage = imagecreatetruecolor( $dx,$dy);
			   ImageCopyResampled( $destImage, $srcImage, 0, 0, 0, 0, $dx, $dy, $srcWidth, $srcHeight );
			}
	       Imagejpeg( $destImage,$file_cache_path,100);		
			if (prepWritableFile($file_cache_path)) {
				header("Content-type: image/jpg");
				readfile($file_cache_path);
			} else {
				// if all caching fails, output directly
				header("Content-type: image/jpg");
				imagejpeg($destImage);
			}
			imagedestroy($srcImage);	
			imagedestroy($destImage);			
		}
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

?>


