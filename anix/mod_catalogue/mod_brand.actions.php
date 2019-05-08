<?php
if($action=="insert"){
	//Insert the category in the database
	$requestString="INSERT INTO `$TBL_catalogue_brands` (`name`,`image_file_large`,`image_file_small`, `URL`,`customer_service_phone`,`customer_service_email`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('".htmlentities($_POST["name"],ENT_QUOTES,"UTF-8")."','imgbrand_large_no_image.jpg','imgbrand_small_no_image.jpg','".htmlentities($_POST["URL"],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["customer_service_phone"],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["customer_service_email"],ENT_QUOTES,"UTF-8")."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
	request($requestString,$link);
	//If insertion was OK, we rtrieve the id of the inserted category, else error...
	if(!mysql_errno($link)) {
		$idBrand=mysql_insert_id($link);
	} else {
		$errMessage.=_("Une erreur s'est produite lors de l'insertion de la marque.")."<br />";
		$errors++;
	}
	//Insert and resize the image of the category
	if(!$errors){
		$imageUploaded = (strcmp ($_FILES['image_file']['name'],"")!=0);
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$fileName = 'imgbrand_'.$idBrand.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgbrand_large_'.$idBrand.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgbrand_small_'.$idBrand.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de la marque au serveur.")."<br />";
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_brand_large_max_width,$CATALOG_image_brand_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_brand_small_max_width,$CATALOG_image_brand_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de la marque au serveur.")."<br />";
		}
		if($imageUploaded){
			$requestString ="UPDATE `$TBL_catalogue_brands` SET ";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."'";
			$requestString .="where `id`='".$idBrand."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la spécification de l'image de la marque.")."<br />";
			}
		}
	}
	if(!$errors){
		$message = _("La marque a été insérée correctement");
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	//Insert and resize the image of the category
	$fileName_large="";
	$fileName_small="";
	$imageUploaded=false;
	if(!$errors && (strcmp ($_FILES['image_file']['name'],"")!=0)){
		$imageUploaded = true;
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$request=request("SELECT image_file_large,image_file_small from $TBL_catalogue_brands where id='$idBrand'",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_small!="imgbrand_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de la marque.")."<br />";
				}
			}
			if($editCategory->image_file_large!="imgbrand_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de la marque.")."<br />";
				}
			}
		}
		if($imageUploaded){
			$fileName = 'imgbrand_'.$idBrand.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgbrand_large_'.$idBrand.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgbrand_small_'.$idBrand.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.'/'.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de la marque au serveur.")."<br />";
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_brand_large_max_width,$CATALOG_image_brand_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_brand_small_max_width,$CATALOG_image_brand_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de la marque au serveur.")."<br />";
		}
	}
	//Update the category
	if(!$errors){
		$requestString ="UPDATE `$TBL_catalogue_brands` SET ";
		if($imageUploaded){
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
		}
		$requestString .="`name`='".htmlentities($_POST["name"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`URL`='".htmlentities($_POST["URL"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`customer_service_phone`='".htmlentities($_POST["customer_service_phone"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`customer_service_email`='".htmlentities($_POST["customer_service_email"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="modified_on='".getDBDate()."',modified_by='$anix_username'";
		$requestString .="where `id`='".$idBrand."'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de la marque.")."<br />";
		}
	}
	if(!$errors){
		$message = _("La marque a été mise à jour correctement")."<br />";
		$action="edit";
	}
}
?>