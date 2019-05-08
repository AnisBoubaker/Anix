<?
if($action=="insert"){
	$ordering=getMaxCategoryOrder($idCat,$link);
	$requestString="INSERT INTO `$TBL_articles_categories` (`ordering`, `id_parent`,`contain_items`,`id_menu`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$ordering','$idCat','".(isset($_POST["contain_items"])?"Y":"N")."','".$_POST["id_menu"]."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
	request($requestString,$link);
	if(!mysql_errno($link)) {
		$idCat=mysql_insert_id($link);
	} else {
		$errMessage.=_("Une erreur s'est produite lors de l'insertion de la catégorie d'articles.")."<br />";
		$errors++;
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_articles_info_categories` (`id_article_cat`,`id_language`,`name`,`description`,`htmltitle`,`htmldescription`,`keywords`) values (";
			$requestString.="'$idCat',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=")";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de l'insertion des informations de la catégorie.")."<br />";
				$errors++;
			}
		}
	}
	//Insert and resize the image of the category
	if(!$errors && $_POST["image_action"]=="change"){
		$imageUploaded = (strcmp ($_FILES['image_file']['name'],"")!=0);
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$fileName = 'imgcatarticle_'.$idCat.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgcatarticle_large_'.$idCat.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgcatarticle_small_'.$idCat.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifiéd'image ou une erreur s'est produite lors de l'envoi de l'image de la catégorie au serveur.")."<br />";
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_cat_large_max_width,$CATALOG_image_cat_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_cat_small_max_width,$CATALOG_image_cat_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spécifiéd'image ou une erreur s'est produite lors de l'envoi de l'image de la catégorie au serveur.")."<br />";
		}
		if($imageUploaded){
			$requestString ="UPDATE `$TBL_articles_categories` SET ";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."'";
			$requestString .="where `id`='".$idCat."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la spécification de l'image de la catégorie.")."<br />";
			}
		}
	}
	if(!$errors){
		$message = _("La catégorie a été insérée correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
	while (!$errors && $row_languages=mysql_fetch_object($languages)){
		$requestString="UPDATE `$TBL_articles_info_categories` set ";
		$requestString.="`name`='".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`description`='".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
		$requestString.=" WHERE `id_article_cat`='$idCat' and id_language='".$row_languages->id."'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de la catégorie.")."<br />";
			$errors++;
		}
	}
	//Insert and resize the image of the category
	$fileName_large="";
	$fileName_small="";
	$imageUploaded=false;
	if(!$errors && $_POST["image_action"]=="change" && (strcmp ($_FILES['image_file']['name'],"")!=0)){
		$imageUploaded = true;
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$request=request("SELECT image_file_large,image_file_small from $TBL_articles_categories where id=$idCat",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_small!="imgcatarticle_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de la catégorie.")."<br />";
				}
			}
			if($editCategory->image_file_large!="imgcatarticle_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de la catégorie.")."<br />";
				}
			}
		}
		if($imageUploaded){
			$fileName = 'imgcatarticle_'.$idCat.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgcatarticle_large_'.$idCat.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgcatarticle_small_'.$idCat.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifiéd'image ou une erreur s'est produite lors de l'envoi de l'image de la catégorie au serveur.")."<br />";
				$imageUploaded=false;
			} else{
				//echo "Je passe0...";
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				//echo "Je passe1...";
				$imageEditor->resize($CATALOG_image_cat_large_max_width,$CATALOG_image_cat_large_max_height);
				//echo "Je passe2...";
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				//echo "Je passe3...";
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				//echo "Je passe4...";
				$imageEditor->resize($CATALOG_image_cat_small_max_width,$CATALOG_image_cat_small_max_height);
				//echo "Je passe5...";
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				//echo "Je passe6...";
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spécifiéd'image ou une erreur s'est produite lors de l'envoi de l'image de la catégorie au serveur.")."<br />";
		}
	}
	//DELETE THE IMAGE = REPLACE BY THE NO_IMAGE
	if(!$errors && $_POST["image_action"]=="delete"){
		$imageUploaded=true;
		$request=request("SELECT image_file_large,image_file_small from $TBL_articles_categories where id=$idCat",$link);
		$editCategory=mysql_fetch_object($request);
		if($editCategory->image_file_small!="imgcatarticle_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
				$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de la catégorie.")."<br />";
			}
		}
		if($editCategory->image_file_large!="imgcatarticle_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
				$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de la catégorie.")."<br />";
			}
		}
		$fileName_large="imgcatarticle_large_no_image.jpg";
		$fileName_small="imgcatarticle_small_no_image.jpg";
	}
	if(!$errors){
		$requestString ="UPDATE $TBL_articles_categories SET ";
		if($imageUploaded){
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
		}
		$requestString.="`id_menu`='".$_POST["id_menu"]."', ";
		$requestString.="`contain_items`='".(isset($_POST["contain_items"])?"Y":"N")."', ";
		$requestString.="modified_on='".getDBDate()."', ";
		$requestString.="modified_by='$anix_username' ";
		$requestString.="WHERE id='$idCat'";
		request($requestString,$link);
	}
	if(!$errors){
		$message = _("La catégorie a été mise à jour correctement.")."<br />";
		$action="edit";
	}
}
?>