<?
if($action=="insert"){
	if(!$errors){
		if(isset($_POST["idCat"])){
			$idCat=$_POST["idCat"];
		} elseif(isset($_GET["idCat"])){
			$idCat=$_GET["idCat"];
		} else $idCat="";
		$ordering=getMaxItemsOrder($idCat,$link);
		if(!$ordering){
			$errors++;
			$errMessage.=_("La catéorie spéifiée n'est pas valide")."<br />";
		} else {
			$parentCategories = getParentsPathIds($idCat,$link);
		}
	}
	if(!$errors){
		$requestString ="INSERT INTO `$TBL_lists_items` (`id_category`,`active`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values (";
		$requestString.="'$idCat',";
		$requestString.="'".$_POST["active"]."',";
		$requestString.="'$ordering',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username')";
		request($requestString,$link);
		if(!mysql_errno($link)) {
			$idItem=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion de l'élément. Le élément n'a pas été inséré.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		//get the image sizes from the category
		$requestString="SELECT * FROM `$TBL_lists_categories` WHERE `id`='$idCat'";
		$tmp = request($requestString,$link);
		if(mysql_num_rows($tmp))
		$catInfos = mysql_fetch_object($tmp);
		else {
			$errMessage.="La categorie specifiee n'existe pas.";
			$errors++;
		}
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_lists_info_items` (`id_item`,`id_language`,`name`,`description`,`keywords`,`htmltitle`,`htmldescription`) values (";
			$requestString.="'$idItem',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=")";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de l'insertion des informations de l'élément.")."<br />";
				$errors++;
			}
		}
	}
	//Insert and resize the image of the item
	$imageUploaded=false;
	if(!$errors && $_POST["image_action"]=="change"){
		$imageUploaded = (strcmp ($_FILES['image_file']['name'],"")!=0);
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$fileName = 'imgflex_tmp_'.$idItem.'_'.$_FILES['image_file']['name'];
			$fileName_orig = 'imgflex_orig_'.$idItem.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgflex_large_'.$idItem.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgflex_small_'.$idItem.'_'.$_FILES['image_file']['name'];
			$fileName_icon = 'imgflex_icon_'.$idItem.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur.")."<br />";
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_prd_orig_max_width,$CATALOG_image_prd_orig_max_height);
				$imageEditor->outputFile($fileName_orig,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->itemimg_large_width!=0?$catInfos->itemimg_large_width:$CATALOG_image_prd_large_max_width,$catInfos->itemimg_large_height!=0?$catInfos->itemimg_large_height:$CATALOG_image_prd_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->itemimg_small_width!=0?$catInfos->itemimg_small_width:$CATALOG_image_prd_small_max_width,$catInfos->itemimg_small_height!=0?$catInfos->itemimg_small_height:$CATALOG_image_prd_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->itemimg_icon_width!=0?$catInfos->itemimg_icon_width:$CATALOG_image_prd_icon_max_width,$catInfos->itemimg_icon_height!=0?$catInfos->itemimg_icon_height:$CATALOG_image_prd_icon_max_height);
				$imageEditor->outputFile($fileName_icon,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur.")."<br />";
		}
	}
	if(!$errors){
		//Update different fields using the item id
		$nb=0;
		$requestString ="UPDATE `$TBL_lists_items` SET ";
		if($imageUploaded){
			$requestString .="`image_file_orig`='".$fileName_orig."', ";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
			$requestString .="`image_file_icon`='".$fileName_icon."'";
			$nb++;
		}
		$requestString .="where `id`='".$idItem."'";
		if($nb){
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la spécification de l'image de l'élément ou de la création de la référence.")."<br />";
			}
		}
	}
	//Update the extrafields
	if(!$errors){
		$requestString="select $TBL_lists_extrafields.id,$TBL_gen_languages.id idLanguage from $TBL_lists_extrafields,$TBL_gen_languages where (";
		$first=true;
		foreach($parentCategories as $cat){
			if(!$first) $requestString.=" OR ";
			$requestString.="$TBL_lists_extrafields.id_cat='$cat'";
			$first = false;
		}
		$requestString.=") and $TBL_gen_languages.used = 'Y' order by $TBL_lists_extrafields.id_cat,$TBL_gen_languages.default";
		$request=request($requestString,$link);
		while($field=mysql_fetch_object($request)){
			//we insert
			$requestString ="INSERT INTO `$TBL_lists_extrafields_values` (`id_extrafield`,`id_item`,`id_language`,`value`) VALUES (";
			$requestString.="'".$field->id."',";
			$requestString.="'$idItem',";
			$requestString.="'".$field->idLanguage."',";
			$requestString.="'".htmlentities($_POST["field".$field->id."_".$field->idLanguage],ENT_QUOTES,"UTF-8")."')";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'insertion d'une valeur de champs additionnel")." (".$field->id.",".$field->idLanguage.").<br>";
			}
		}
	}
	if(!$errors){
		$message = _("Le élément a été inséré correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$request=request("SELECT id_category,ordering from $TBL_lists_items where id='$idItem'",$link);
	if($item=mysql_fetch_object($request)){
		$idCat = $item->id_category;
	} else {
		$errors++;
		$errMessage.=_("Le élément spécifié est invalide.")."<br />";
	}
	if(!$errors){
		$parentCategories = getParentsPathIds($idCat,$link);
		$requestString ="UPDATE `$TBL_lists_items` set ";
		$requestString.="`active`='".$_POST["active"]."',";
		$requestString .="modified_on='".getDBDate()."',modified_by='$anix_username'";
		$requestString.="WHERE id='$idItem'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'élément.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		//get the image sizes from the category
		$requestString="SELECT * FROM `$TBL_lists_categories` WHERE `id`='$idCat'";
		$tmp = request($requestString,$link);
		if(mysql_num_rows($tmp))
		$catInfos = mysql_fetch_object($tmp);
		else {
			$errMessage.="La categorie specifiee n'existe pas.";
			$errors++;
		}
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="UPDATE `$TBL_lists_info_items` set ";
			$requestString.="`name`='".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`description`='".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=" WHERE `id_item`='$idItem' and id_language='".$row_languages->id."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour des informations de l'élément.")."<br />";
				$errors++;
			}
		}
	}
	//Insert and resize the image of the item
	$imageUploaded=false;
	if(!$errors && $_POST["image_action"]=="change" && (strcmp ($_FILES['image_file']['name'],"")!=0)){
		$imageUploaded = true;
		if(!isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_lists_items where id=$idItem",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_orig!=""){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_orig)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));;
				}
			}
			if($editCategory->image_file_small!="imgflex_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
				}
			}
			if($editCategory->image_file_large!="imgflex_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de l'élément."));
				}
			}
			if($editCategory->image_file_icon!="imgflex_icon_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_icon)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne icone de l'élément."));
				}
			}
		}
		if($imageUploaded){
			$fileName = 'imgflex_tmp_'.$idItem.'_'.$_FILES['image_file']['name'];
			$fileName_orig = 'imgflex_orig_'.$idItem.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgflex_large_'.$idItem.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgflex_small_'.$idItem.'_'.$_FILES['image_file']['name'];
			$fileName_icon = 'imgflex_icon_'.$idItem.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addWarning(_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur."));
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_prd_orig_max_width,$CATALOG_image_prd_orig_max_height);
				$imageEditor->outputFile($fileName_orig,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->itemimg_large_width!=0?$catInfos->itemimg_large_width:$CATALOG_image_prd_large_max_width,$catInfos->itemimg_large_height!=0?$catInfos->itemimg_large_height:$CATALOG_image_prd_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->itemimg_small_width!=0?$catInfos->itemimg_small_width:$CATALOG_image_prd_small_max_width,$catInfos->itemimg_small_height!=0?$catInfos->itemimg_small_height:$CATALOG_image_prd_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->itemimg_icon_width!=0?$catInfos->itemimg_icon_width:$CATALOG_image_prd_icon_max_width,$catInfos->itemimg_icon_height!=0?$catInfos->itemimg_icon_height:$CATALOG_image_prd_icon_max_height);
				$imageEditor->outputFile($fileName_icon,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		}else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur.")."<br />";
		}

	}
	//DELETE THE IMAGE = REPLACE BY THE NO_IMAGE
	if(!$errors && $_POST["image_action"]=="delete"){
		$imageUploaded=true;
		$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_lists_items where id=$idItem",$link);
		$editCategory=mysql_fetch_object($request);
		if($editCategory->image_file_orig!=""){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_orig)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
			}
		}
		if($editCategory->image_file_small!="imgflex_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
			}
		}
		if($editCategory->image_file_large!="imgflex_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de l'élément."));
			}
		}
		if($editCategory->image_file_icon!="imgflex_icon_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_icon)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne icone de l'élément."));
			}
		}
		$fileName_orig="";
		$fileName_large="imgflex_large_no_image.jpg";
		$fileName_small="imgflex_small_no_image.jpg";
		$fileName_icon="imgflex_icon_no_image.jpg";
	}
	if(!$errors){
		//Update different fields using the item id
		$nb=0;
		$requestString ="UPDATE `$TBL_lists_items` SET ";
		if($imageUploaded){
			$requestString .="`image_file_orig`='".$fileName_orig."',";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
			$requestString .="`image_file_icon`='".$fileName_icon."'";
			$nb++;
		}
		$requestString .="where `id`='".$idItem."'";
		if($nb){
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la spécification de l'image de l'élément ou de la création de la référence.")."<br />";
			}
		}
	}
	//Update the extrafields
	if(!$errors){
		$requestString="select $TBL_lists_extrafields.id,$TBL_gen_languages.id idLanguage from $TBL_lists_extrafields,$TBL_gen_languages where (";
		$first=true;
		foreach($parentCategories as $cat){
			if(!$first) $requestString.=" OR ";
			$requestString.="$TBL_lists_extrafields.id_cat='$cat'";
			$first = false;
		}
		$requestString.=") and $TBL_gen_languages.used = 'Y' order by $TBL_lists_extrafields.id_cat,$TBL_gen_languages.default";
		$request=request($requestString,$link);
		while($field=mysql_fetch_object($request)){
			$tmp=request("SELECT id_extrafield from $TBL_lists_extrafields_values where id_extrafield='".$field->id."' and id_item='$idItem' and id_language='".$field->idLanguage."'",$link);
			if(mysql_num_rows($tmp)){
				//we update first
				$requestString ="UPDATE `$TBL_lists_extrafields_values` SET ";
				$requestString.="`value`='".htmlentities($_POST["field".$field->id."_".$field->idLanguage],ENT_QUOTES,"UTF-8")."' ";
				$requestString.="WHERE id_item='$idItem' AND id_extrafield='".$field->id."' and id_language='".$field->idLanguage."'";
				request($requestString,$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.=_("Une erreur s'est produite lors de la mise à jour d'un champs additionnel")." (".$field->id.",".$field->idLanguage.").<br>";
				}
			} else {
				//we insert
				$requestString ="INSERT INTO `$TBL_lists_extrafields_values` (`id_extrafield`,`id_item`,`id_language`,`value`) VALUES (";
				$requestString.="'".$field->id."',";
				$requestString.="'$idItem',";
				$requestString.="'".$field->idLanguage."',";
				$requestString.="'".htmlentities($_POST["field".$field->id."_".$field->idLanguage],ENT_QUOTES,"UTF-8")."')";
				request($requestString,$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.=_("Une erreur s'est produite lors de l'insertion d'une valeur de champs additionnel")." (".$field->id.",".$field->idLanguage.").<br>";
				}
			}
		}
	}
	if(!$errors){
		$message = _("Le élément a été mis à jour correctement.")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="addAttachment"){
	//get the ordering
	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_lists_attachments` WHERE id_item='$idItem' GROUP BY id_item",$link);
	if(mysql_num_rows($tmp)) {
		$maxOrder= mysql_fetch_object($tmp);
		$maxOrderValue = $maxOrder->maximum+1;
	} else $maxOrderValue=1;
	$requestString ="INSERT INTO `$TBL_lists_attachments` (`id_item`,`id_language`,`title`,`description`,`ordering`) values (";
	$requestString.="'$idItem',";
	$requestString.="'".$_POST['id_language']."',";
	$requestString.="'".htmlentities($_POST['title'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="'".htmlentities($_POST['description'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="'$maxOrderValue')";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.");
		$errors++;
	} else {
		$idAttachment=mysql_insert_id($link);
		$fileUploaded=false;
	}
	if(!$errors){
		$fileUploaded = (strcmp($_FILES['attachment_file']['name'],"")!=0);
		if($fileUploaded && isFileProhibited($_FILES['attachment_file']['name'])){
			$errors++;
			$errMessage.=_("Ce type de fichiers est interdit.")."<br />";
		}
		if($fileUploaded && !$errors){
			$fileName = "flex".$idAttachment."_".$_FILES['attachment_file']['name'];
			$filePath = $CATALOG_folder_attachments.$fileName;
			if(!move_uploaded_file($_FILES['attachment_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifié de fichier ou une erreur s'est produite lors de l'envoi du fichier attaché au serveur.")."<br />";
				$errors++;
				$fileUploaded=false;
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spécifié de fichier.")."<br />";
			$errors++;
		}
	}
	//On définit le nouveau nom de fichier...
	if(!$errors && $fileUploaded){
		$requestString="UPDATE `$TBL_lists_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.")."<br />";
			$errors++;
		}
	} else {
		$requestString = "DELETE from `$TBL_lists_attachments` where id=$idAttachment";
		request($requestString,$link);
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idItem'",$link);
	}
	if(!$errors) $message.=_("Le fichier a été correctement attaché à l'élément.")."<br />";
	$action = "edit";
	$ANIX_TabSelect=2;
}
?>
<?
if($action=="updateAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$requestString ="UPDATE `$TBL_lists_attachments` set ";
	$requestString.="`id_language`='".$_POST['id_language']."',";
	$requestString.="`title`='".htmlentities($_POST['title'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="`description`='".htmlentities($_POST['description'],ENT_QUOTES,"UTF-8")."' ";
	$requestString.="WHERE `id`='$idAttachment'";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$errMessage.=_("Une erreur s'est produite lors de la mise à jour du fichier attaché.")."<br />";
		$errors++;
	}
	$fileUploaded=false;
	if(!$errors){
		$fileUploaded = (strcmp($_FILES['attachment_file']['name'],"")!=0);
		if($fileUploaded && isFileProhibited($_FILES['attachment_file']['name'])){
			$errors++;
			$errMessage.=_("Ce type de fichiers est interdit.")."<br />";
			$fileUploaded=false;
		}
		if($fileUploaded && !$errors){
			$fileName = "flex".$idAttachment."_".$_FILES['attachment_file']['name'];
			$filePath = $CATALOG_folder_attachments.$fileName;
			if(!move_uploaded_file($_FILES['attachment_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifié de fichier ou une erreur s'est produite lors de l'envoi du fichier attaché au serveur.")."<br />";
				$errors++;
				$fileUploaded=false;
			}
		}
	}
	//On définit le nouveau nom de fichier...
	if($fileUploaded){
		$request = request("SELECT file_name from `$TBL_lists_attachments` where id='$idAttachment'",$link);
		$oldFile = mysql_fetch_object($request);
		if($oldFile->file_name!="" && $oldFile->file_name!=$fileName) if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancien fichier.")."<br />";
		}
		$requestString="UPDATE `$TBL_lists_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour du fichier attaché.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idItem'",$link);
	}
	if(!$errors) $message = _("Le fichier a été mis à jour courrectement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=2;
}
?>
<?
if($action=="delAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$request = request("SELECT file_name,ordering from `$TBL_lists_attachments` where id='$idAttachment'",$link);
	$oldFile = mysql_fetch_object($request);
	if($oldFile->file_name!="") if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
		$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancien fichier.")."<br />";
		$errors++;
	}
	if(!$errors){
		$requestString ="DELETE from `$TBL_lists_attachments` WHERE `id`='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression des informations du fichier attaché.")."<br />";
			$errors++;
		}
	}
	//update the orderings
	if(!$errors){
		request("UPDATE `$TBL_lists_attachments` set ordering=ordering-1 where id_item='$idItem' and ordering > ".$oldFile->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des fichiers attachés.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idItem'",$link);
	}
	if(!$errors) $message = _("Le fichier attaché a été supprimé correctement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=2;
}
?>
<?
if($action=="moveAttachmentUp"){
	if(isset($_POST["idAttachment"])){
		$idAttachment=$_POST["idAttachment"];
	} elseif(isset($_GET["idAttachment"])){
		$idAttachment=$_GET["idAttachment"];
	} else {
		$errors++;
		$errMessage.=_("Le fichier attaché n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_lists_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_lists_attachments` where id_item='$idItem' and ordering='".($attachment->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upAttachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_lists_attachments` set ordering='".($attachment->ordering-1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_lists_attachments` set ordering='".$attachment->ordering."' where id='".$upAttachment->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idItem'",$link);
		$message.=_("L'ordre du fichier attaché a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=2;
}
?>
<?
if($action=="moveAttachmentDown"){
	if(isset($_POST["idAttachment"])){
		$idAttachment=$_POST["idAttachment"];
	} elseif(isset($_GET["idAttachment"])){
		$idAttachment=$_GET["idAttachment"];
	} else {
		$errors++;
		$errMessage.=_("Le fichier attaché n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_lists_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_lists_attachments` where id_item='$idItem' and ordering='".($attachment->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downAttachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_lists_attachments` set ordering='".($attachment->ordering+1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_lists_attachments` set ordering='".$attachment->ordering."' where id='".$downAttachment->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idItem'",$link);
		$message.=_("L'ordre du fichier attaché a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=2;
}
?>