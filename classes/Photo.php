<?php
class Photo extends DataFromDb {
	public function __construct(array $options = null) {
        parent::__construct($options);        
	}
	
	public static function delete($fname) {
		self::findBy("name='$fname'");
		if (get_called_class()=='Flat') {
			$flat = new Flat();
			$flat->getFull($this->object_id);
			$photo_flat_path = $flat->getPhotoPath();
			unlink($photo_flat_path.$fname);
			unlink($photo_flat_path.$fname.'_prev');
		}
		parent::delete("name='$fname'");
	} 
	
	public static function createWatermark($main_img_obj,$text,$font,$r=128,$g=128,$b=128,$alpha_level=5)
	{		
		$width = imagesx($main_img_obj);
		$height = imagesy($main_img_obj);
		//$angle =  -rad2deg(atan2((-$height),($width)));		
		$angle =  0;
		$text = " ".$text." ";		
		$c = imagecolorallocatealpha($main_img_obj, $r, $g, $b, $alpha_level);
		$size = (($width+$height)/2)*2/strlen($text);
		$box  = imagettfbbox ($size, $angle, $font, $text);
		$x = $width/2 - abs($box[4] - $box[0])/2;
		$y = $height/2 + abs($box[5] - $box[1])/2;		
		imagettftext($main_img_obj,$size ,$angle, $x, $y, $c, $font, $text);
		return $main_img_obj;
	}
	
	public static function setSize($path,$target_width=150,$target_height=100) {		
		// Get the image and create a thumbnail
		$img = imagecreatefromjpeg($path);
		if (!$img) {
			echo "ERROR:could not create image handle ".$path;
			exit(0);
		}
		$width = imageSX($img);
		$height = imageSY($img);
		$size = getimagesize($path);
		if (!$width || !$height) {
			echo "ERROR:Invalid width or height";
			exit(0);
		}
		if ($width > $target_width) {
			$width_ratio = $target_width/$width;
		}
		else {
			$width_ratio = 1;	
		}
		if ($height > $target_height) {
			$height_ratio = $target_height/$height;
		}
		else {
			$height_ratio = 1;	
		}
		if ($width_ratio==1 && $height_ratio==1) return $img;
		$ratio = min($width_ratio,$height_ratio);
		$new_height = $ratio * $height;
		$new_width = $ratio * $width;
		//file_put_contents("1.log","$new_height = $ratio m $height; $new_width = $ratio m $width;\n",FILE_APPEND);
		$new_img = ImageCreateTrueColor($new_width, $new_height);
		if (!@imagecopyresampled($new_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height)) {
			echo "ERROR:Could not resize image";
			exit(0);
		}		
		return $new_img;
	}
	
	public static function setSizeOld($path,$target_width=150,$target_height=100) {
		//$_FILES["Filedata"]["tmp_name"]
			// Get the image and create a thumbnail
		$img = imagecreatefromjpeg($path);
		if (!$img) {
			echo "ERROR:could not create image handle ".$path;
			exit(0);
		}
		$width = imageSX($img);
		$height = imageSY($img);
	
		if (!$width || !$height) {
			echo "ERROR:Invalid width or height";
			exit(0);
		}
		// Build the thumbnail
		$target_ratio = $target_width / $target_height;
		$img_ratio = $width / $height;
		if ($target_ratio > $img_ratio) {
			$new_height = $target_height;
			$new_width = $img_ratio * $target_height;
		} else {
			$new_height = $target_width / $img_ratio;
			$new_width = $target_width;
		}
	
		if ($new_height > $target_height) {
			$new_height = $target_height;
		}
		if ($new_width > $target_width) {
			$new_height = $target_width;
		}
	
		$new_img = ImageCreateTrueColor($target_width, $target_height);
		$white = imagecolorallocate($new_img,255,255,255);
		if (!@imagefilledrectangle($new_img, 0, 0, $target_width-1, $target_height-1, $white)) {
			echo "ERROR:Could not fill new image";
			exit(0);
		}	
		if (!@imagecopyresampled($new_img, $img, ($target_width-$new_width)/2, ($target_height-$new_height)/2, 0, 0, $new_width, $new_height, $width, $height)) {
			echo "ERROR:Could not resize image";
			exit(0);
		}		
		return $new_img;
	}
	
function setSizeOther($src, $width=150, $height=100, $rgb=0xFFFFFF, $quality=100)
{
  if (!file_exists($src)) return false;

  $size = getimagesize($src);

  if ($size === false) return false;

  // Определяем исходный формат по MIME-информации, предоставленной
  // функцией getimagesize, и выбираем соответствующую формату
  // imagecreatefrom-функцию.
  $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
  $icfunc = "imagecreatefrom" . $format;
  if (!function_exists($icfunc)) return false;

  $x_ratio = $width / $size[0];
  $y_ratio = $height / $size[1];

  $ratio       = min($x_ratio, $y_ratio);
  $use_x_ratio = ($x_ratio == $ratio);

  $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
  $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
  $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
  $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

  $isrc = $icfunc($src);
  $idest = imagecreatetruecolor($width, $height);

  imagefill($idest, 0, 0, $rgb);
  imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, 
    $new_width, $new_height, $size[0], $size[1]);

  //imagejpeg($idest, $dest, $quality);

  imagedestroy($isrc);
  //imagedestroy($idest);

  return $idest;
}
	
	public function deletePhotos($object,$id){
		global $db;
		$tmp_path = PHOTOS_PATH.$object.'/'.$id; 
	    if(!is_writeable($tmp_path) && is_dir($tmp_path)){chmod($tmp_path,0777);} 
	    $handle = opendir($tmp_path); 
	  	while($tmp=readdir($handle)){ 
	    	if($tmp!='..' && $tmp!='.' && $tmp!=''){ 
		         if(is_writeable($tmp_path.'/'.$tmp) && is_file($tmp_path.'/'.$tmp)){ 
		                 unlink($tmp_path.'/'.$tmp); 
		         }elseif(!is_writeable($tmp_path.'/'.$tmp) && is_file($tmp_path.'/'.$tmp)){ 
		             chmod($tmp_path.'/'.$tmp,0666); 
		             unlink($tmp_path.'/'.$tmp); 
		         } 
		         
		         if(is_writeable($tmp_path.'/'.$tmp) && is_dir($tmp_path.'/'.$tmp)){ 
		                delete_folder($tmp_path.'/'.$tmp); 
		         }elseif(!is_writeable($tmp_path.'/'.$tmp) && is_dir($tmp_path.'/'.$tmp)){ 
		                chmod($tmp_path.'/'.$tmp,0777); 
		                delete_folder($tmp_path.'/'.$tmp); 
		         } 
	    	}
	  } 
	  closedir($handle); 
	  rmdir($tmp_path);
	  				
	  $db->query("DELETE FROM photo WHERE object_id=$id AND kind_id=".$object);
	  if(!is_dir($tmp_path)){return true;} 
	  else{return false;} 
	} 
}