<?
if($action=="insert"){
	if($_POST["active"]=="DATE" && ($_POST["from_date"]=="" || $_POST["to_date"]=="" || $_POST["from_date"]>$_POST["to_date"])){
		$ANIX_messages->addError(_("Les dates d'affichage spécifiées sont invalides. Merci d'utiliser le format AAAA-MM-JJ."));
	}
	if(!$ANIX_messages->nbErrors){
		if(isset($_POST["idCat"])){
			$idCat=$_POST["idCat"];
		} elseif(isset($_GET["idCat"])){
			$idCat=$_GET["idCat"];
		} else $idCat="";
	}
	//Move down all the photo in the category
	if(!$ANIX_messages->nbErrors){
		$requestString="UPDATE `$TBL_gallery_photo` SET `ordering`=`ordering`+1 WHERE `id_category`='$idCat'";
		request($requestString,$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la re-organisation de la categorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$requestString ="INSERT INTO `$TBL_gallery_photo` (`id_category`,`active`,`from_date`,`to_date`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values (";
		$requestString.="'$idCat',";
		$requestString.="'".$_POST["active"]."',";
		$requestString.="'".$_POST["from_date"]."',";
		$requestString.="'".$_POST["to_date"]."',";
		$requestString.="'1',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username')";
		request($requestString,$link);
		if(!mysql_errno($link)) {
			$idPhoto=mysql_insert_id($link);
		} else {
			$ANIX_messages->addError(_("Une erreur s'est produite lors de l'insertion de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//get the image sizes from the category
		$requestString="SELECT * FROM `$TBL_gallery_categories` WHERE `id`='$idCat'";
		$tmp = request($requestString,$link);
		if(mysql_num_rows($tmp))
		$catInfos = mysql_fetch_object($tmp);
		else {
			$ANIX_messages->addError(_("La categorie specifiee n'existe pas."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$ANIX_messages->nbErrors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_gallery_info_photo` (`id_photo`,`id_language`,`title`,`date`,`short_desc`,`details`,`keywords`,`htmltitle`,`htmldescription`) values (";
			$requestString.="'$idPhoto',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["title_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["date_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["short_desc_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["details_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=")";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$ANIX_messages->addError(_("Une erreur s'est produite lors de l'insertion des informations de la nouvelle."));
			}
		}
	}
	//Insert and resize the image of the photo
	$imageUploaded=false;
	if(!$ANIX_messages->nbErrors && $_POST["image_action"]=="change"){
		$imageUploaded = (strcmp ($_FILES['image_file']['name'],"")!=0);
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$fileName = 'imgphoto_tmp_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$fileName_orig = 'imgphoto_orig_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgphoto_large_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgphoto_small_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$fileName_icon = 'imgphoto_icon_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addError(_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur."));
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_prd_orig_max_width,$CATALOG_image_prd_orig_max_height);
				$imageEditor->outputFile($fileName_orig,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->photo_large_width!=0?$catInfos->photo_large_width:$CATALOG_image_prd_large_max_width,$catInfos->photo_large_height!=0?$catInfos->photo_large_height:$CATALOG_image_prd_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->photo_small_width!=0?$catInfos->photo_small_width:$CATALOG_image_prd_small_max_width,$catInfos->photo_small_height!=0?$catInfos->photo_small_height:$CATALOG_image_prd_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->photo_icon_width!=0?$catInfos->photo_icon_width:$CATALOG_image_prd_icon_max_width,$catInfos->photo_icon_height!=0?$catInfos->photo_icon_height:$CATALOG_image_prd_icon_max_height);
				$imageEditor->outputFile($fileName_icon,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'image temporaire."));
				}
			}
		} else { //Not uploaded
			$ANIX_messages->addWarning(_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La nouvelle a été insérée correctement"));
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$request=request("SELECT id_category,ordering from $TBL_gallery_photo where id='$idPhoto'",$link);
	if($photo=mysql_fetch_object($request)){
		$idCat = $photo->id_category;
	} else {
		$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
	}
	if($_POST["active"]=="DATE" && ($_POST["from_date"]=="" || $_POST["to_date"]=="" || $_POST["from_date"]>$_POST["to_date"])){
		$ANIX_messages->addError(_("Les dates d'affichage spécifiées sont invalides. Merci d'utiliser le format AAAA-MM-JJ."));
	}
	if(!$ANIX_messages->nbErrors){
		$requestString ="UPDATE `$TBL_gallery_photo` set ";
		$requestString.="`active`='".$_POST["active"]."',";
		$requestString.="`from_date`='".$_POST["from_date"]."',";
		$requestString.="`to_date`='".$_POST["to_date"]."',";
		$requestString.="`modified_on`='".getDBDate()."',";
		$requestString.="`modified_by`='$anix_username' ";
		$requestString.="WHERE id='$idPhoto'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//get the image sizes from the category
		$requestString="SELECT * FROM `$TBL_gallery_categories` WHERE `id`='$idCat'";
		$tmp = request($requestString,$link);
		if(mysql_num_rows($tmp))
		$catInfos = mysql_fetch_object($tmp);
		else {
			$ANIX_messages->addError(_("La categorie specifiee n'existe pas."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$ANIX_messages->nbErrors && $row_languages=mysql_fetch_object($languages)){
			$requestString="UPDATE `$TBL_gallery_info_photo` set ";
			$requestString.="`title`='".htmlentities($_POST["title_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`date`='".htmlentities($_POST["date_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`short_desc`='".htmlentities($_POST["short_desc_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`details`='".htmlentities($_POST["details_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=" WHERE `id_photo`='$idPhoto' and id_language='".$row_languages->id."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour des informations de la nouvelle."));
			}
		}
	}
	//Insert and resize the image of the photo
	$imageUploaded=false;
	if(!$ANIX_messages->nbErrors && $_POST["image_action"]=="change" && (strcmp ($_FILES['image_file']['name'],"")!=0)){
		$imageUploaded = true;
		if(!isImageAllowed($_FILES['image_file']['name'])){
			$ANIX_messages->addError(_("Ce type de fichier image n'est pas pris en charge."));
		}
		if($imageUploaded){
			$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_gallery_photo where id=$idPhoto",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_orig!=""){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_orig)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
				}
			}
			if($editCategory->image_file_small!="imgphoto_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
				}
			}
			if($editCategory->image_file_large!="imgphoto_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de l'élément."));
				}
			}
			if($editCategory->image_file_icon!="imgphoto_icon_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_icon)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne icone de l'élément."));
				}
			}
		}
		if($imageUploaded){
			$fileName = 'imgphoto_tmp_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$fileName_orig = 'imgphoto_orig_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgphoto_large_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgphoto_small_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$fileName_icon = 'imgphoto_icon_'.$idPhoto.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addWarning(_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur."));
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_prd_orig_max_width,$CATALOG_image_prd_orig_max_height);
				$imageEditor->outputFile($fileName_orig,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->photo_large_width!=0?$catInfos->photo_large_width:$CATALOG_image_prd_large_max_width,$catInfos->photo_large_height!=0?$catInfos->photo_large_height:$CATALOG_image_prd_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->photo_small_width!=0?$catInfos->photo_small_width:$CATALOG_image_prd_small_max_width,$catInfos->photo_small_height!=0?$catInfos->photo_small_height:$CATALOG_image_prd_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->photo_icon_width!=0?$catInfos->photo_icon_width:$CATALOG_image_prd_icon_max_width,$catInfos->photo_icon_height!=0?$catInfos->photo_icon_height:$CATALOG_image_prd_icon_max_height);
				$imageEditor->outputFile($fileName_icon,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'image temporaire."));
				}
			}
		}else { //Not uploaded
			$ANIX_messages->addWarning(_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur."));
		}

	}
	//DELETE THE IMAGE = REPLACE BY THE NO_IMAGE
	if(!$ANIX_messages->nbErrors && $_POST["image_action"]=="delete"){
		$imageUploaded=true;
		$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_gallery_photo where id=$idPhoto",$link);
		$editCategory=mysql_fetch_object($request);
		if($editCategory->image_file_orig!=""){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_orig)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
			}
		}
		if($editCategory->image_file_small!="imgphoto_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
			}
		}
		if($editCategory->image_file_large!="imgphoto_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de l'élément."));
			}
		}
		if($editCategory->image_file_icon!="imgphoto_icon_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_icon)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne icone de l'élément."));
			}
		}
		$fileName_orig="";
		$fileName_large="imgphoto_large_no_image.jpg";
		$fileName_small="imgphoto_small_no_image.jpg";
		$fileName_icon="imgphoto_icon_no_image.jpg";
	}
	if(!$ANIX_messages->nbErrors){
		//Update different fields using the photo id
		$nb=0;
		$requestString ="UPDATE `$TBL_gallery_photo` SET ";
		if($imageUploaded){
			$requestString .="`image_file_orig`='".$fileName_orig."',";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
			$requestString .="`image_file_icon`='".$fileName_icon."'";
			$nb++;
		}
		$requestString .="where `id`='".$idPhoto."'";
		if($nb){
			request($requestString,$link);
			if(mysql_errno($link)) {
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la spécification de l'image de l'élément ou de la création de la référence."));
			}
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La nouvelle a été mise à jour correctement"));
		$action="edit";
	}
}
?>