<?php

class images {
	var $db;
	
	function __construct() {
		// do nothing
		require_once (PATH_CORE.'/classes/db.class.php');
		$this->db=new cloudDatabase();
	}

	function fetchImage() {
	 /* Image server for an image for the story */
		if (isset($_GET['f'])) {
			$image=$_GET['f']; // filename			
			$x=$_GET['x']; // maximum width
			$y=$_GET['y']; // maximum height
			if (isset($_GET['path'])) {
				$path=$_GET['path'];
			} else {
				$path='default';
			}
			$dx=$x;
			$dy=$y;
			$imgArr=explode('.',$image);
			if (isset($_GET['fixed'])) $fixed=$_GET['fixed']; else $fixed=''; // fix x or y dimension
			if (isset($_GET['crop'])) $crop='c'; else $crop=''; // crop the height
			$file_cache_path=PATH_CACHE.'/scaleImg_'.$imgArr[0].'_'.$dx.'_'.$dy.'_'.$fixed.$crop.'.jpg';
			//$this->db->log($file_cache_path);
			if (file_exists($file_cache_path)) {
				// try reading cached file
				header("Content-type: image/jpg");
				readfile($file_cache_path);
			} else {
				$this->scaleImage($imgArr[0],$imgArr[1],$path,$x,$y,$dx,$dy,$fixed,$crop,$file_cache_path);
			}
		}		
	}
	
	function scaleImage($image='',$imageType='jpg',$path='default',$x=0,$y=0,$dx=0,$dy=0,$fixed='',$crop='',$file_cache_path='') {		
			// else create a new scaled image
			switch ($path) {
				case 'uploads':
					$path=PATH_UPLOAD_IMAGES;
				break;
				case 'submissions':
					$path=PATH_UPLOAD_SUBMISSIONS;
				break;
				default:
					$path=PATH_CACHE;
				break;
			}
			$file_orig=$path.'/'.$image.'.'.$imageType;
			//$this->db->log($file_orig);
			//$imageType=$this->getExtension($imaqe);
			$imageType=strtolower($imageType);
			//$this->db->log('scaleimage name'.$file_orig.' type'.$imageType.' '.$file_cache_path);
			switch ($imageType) {
				case 'jpg':
					$srcImage = imagecreatefromjpeg($file_orig);
				break;
				case 'png':
					$srcImage = imagecreatefrompng($file_orig);
				break;
				case 'gif':
					$srcImage = imagecreatefromgif($file_orig);
				break;
				default:
					// 	error
					die('No valid image type');
				break;
			}
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
			   imageColorAllocate($tempImage,255,255,255);
			   imagealphablending($tempImage, false );
			 	imagesavealpha( $tempImage, true );
			   ImageCopyResampled( $tempImage, $srcImage, 0, 0, 0, 0, $dx, $dy, $srcWidth, $srcHeight );
			   // crop it to destImage
			   $destImage = imagecreatetruecolor( $dx,$y);
			   imagecopy($destImage,$tempImage,0,0,0,0,$dx,$y);
			   //$bordercolor = ImageColorAllocate($destImage,0,0,0);			
			   //ImageRectangle($destImage,0,0,($dx-1),($y-1),$bordercolor);			  
			   imagedestroy($tempImage);	
			} else if ($crop=='c' AND $dx>$x) {
		   		// scale it to tempImage
			   $tempImage = imagecreatetruecolor( $dx,$dy);
			   ImageColorAllocate($tempImage,255,255,255);
			   imagealphablending($tempImage, false );
			 	imagesavealpha( $tempImage, true );
			   ImageCopyResampled( $tempImage, $srcImage, 0, 0, 0, 0, $dx, $dy, $srcWidth, $srcHeight );
			   // crop it to destImage
			   $destImage = imagecreatetruecolor( $x,$dy);
			   imagecopy($destImage,$tempImage,0,0,0,0,$x,$dy);
			   //$bordercolor = ImageColorAllocate($destImage,0,0,0);			
			   //ImageRectangle($destImage,0,0,($x-1),($dy-1),$bordercolor);			  
			   imagedestroy($tempImage);	
			} else {
			   $destImage = imagecreatetruecolor( $dx,$dy);
			   ImageColorAllocate($destImage,255,255,255);
			   imagealphablending($destImage, false );
			 	imagesavealpha( $destImage, true );
			   ImageCopyResampled( $destImage, $srcImage, 0, 0, 0, 0, $dx, $dy, $srcWidth, $srcHeight );
			}
	       Imagejpeg( $destImage,$file_cache_path,100);		
			if ($this->prepWritableFile($file_cache_path)) {
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

	function prepWritableFile($file_path) {
		if (file_exists($file_path) && is_writable($file_path)) {
			return true;
		} else if (touch($file_path)) {
			return true;
		} else {
			return false;
		}			
	}
 	function getExtension($str) {
         $i = strrpos($str,".");
         if (!$i) { return ""; }
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 	}
}
?>