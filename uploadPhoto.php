<?php
	include_once("./include/common.php");
	/* Note: This thumbnail creation script requires the GD PHP Extension.  
		If GD is not installed correctly PHP does not render this page correctly
		and SWFUpload will get "stuck" never calling uploadSuccess or uploadError
	 */

	// Get the session Id passed from SWFUpload. We have to do this to work-around the Flash Player Cookie Bug
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}	
	ini_set("html_errors", "0");

	if (isset($_POST['kind'])) {
		$kind = clearTextData($_POST['kind']);
		if (!in_array($kind,array(TENEMENT,FLAT,HOUSE,LAND,NEW_TENEMENT,GARAGE,COMPANY,LOGO,COMMERCIAL))) {
			exit(0);
		}		
	}
	else {
		exit(0);
	}
	
	// Check the upload
	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		echo "ERROR:invalid upload";
		file_put_contents("error.log","ERROR:invalid upload",FILE_APPEND);
		exit(0);
	}
	
	try {
		if ($kind != LOGO && $kind != COMPANY) {
			$img_prev = Photo::setSize($_FILES["Filedata"]["tmp_name"],150,100);
			$img_prev = Photo::createWatermark($img_prev,WATERMARK,"arial.ttf",255,255,255,90);
			
			$img_org = Photo::setSize($_FILES["Filedata"]["tmp_name"],800,600);
			$img_org = Photo::createWatermark($img_org,WATERMARK,"arial.ttf",255,255,255,80);
			
			$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);
			
			imagejpeg($img_prev,PHOTOS_TMP_PATH.$file_id.'_prev');
			imagejpeg($img_org,PHOTOS_TMP_PATH.$file_id);
		}
		elseif ($kind == COMPANY) {			
			$img_prev = Photo::setSize($_FILES["Filedata"]["tmp_name"],150,100);									
			$img_org = Photo::setSize($_FILES["Filedata"]["tmp_name"],800,600);						
			$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);									
			imagejpeg($img_prev,PHOTOS_TMP_PATH.$file_id.'_prev');			
			imagejpeg($img_org,PHOTOS_TMP_PATH.$file_id);			
		}
		elseif ($kind == LOGO) {
			//Логотип
			$img_org = Photo::setSize($_FILES["Filedata"]["tmp_name"],300,300);					
			$file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000);			
			imagejpeg($img_org,PHOTOS_TMP_PATH.$file_id);
		}
		echo $kind.":".$file_id;
	}
	catch (Exception $e) {
		echo $kind.":0";
	}
?>