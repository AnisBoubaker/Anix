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
	//Move down all the news in the category
	if(!$ANIX_messages->nbErrors){
		$requestString="UPDATE `$TBL_news_news` SET `ordering`=`ordering`+1 WHERE `id_category`='$idCat'";
		request($requestString,$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la re-organisation de la categorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$requestString ="INSERT INTO `$TBL_news_news` (`id_category`,`active`,`from_date`,`to_date`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values (";
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
			$idNews=mysql_insert_id($link);
		} else {
			$ANIX_messages->addError(_("Une erreur s'est produite lors de l'insertion de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//get the image sizes from the category
		$requestString="SELECT * FROM `$TBL_news_categories` WHERE `id`='$idCat'";
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
			$requestString="INSERT INTO `$TBL_news_info_news` (`id_news`,`id_language`,`title`,`date`,`short_desc`,`details`,`keywords`,`htmltitle`,`htmldescription`) values (";
			$requestString.="'$idNews',";
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
	//Insert and resize the image of the news
	$imageUploaded=false;
	if(!$ANIX_messages->nbErrors && $_POST["image_action"]=="change"){
		$imageUploaded = (strcmp ($_FILES['image_file']['name'],"")!=0);
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$fileName = 'imgnews_tmp_'.$idNews.'_'.$_FILES['image_file']['name'];
			$fileName_orig = 'imgnews_orig_'.$idNews.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgnews_large_'.$idNews.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgnews_small_'.$idNews.'_'.$_FILES['image_file']['name'];
			$fileName_icon = 'imgnews_icon_'.$idNews.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addError(_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur."));
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_prd_orig_max_width,$CATALOG_image_prd_orig_max_height);
				$imageEditor->outputFile($fileName_orig,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->newsimg_large_width!=0?$catInfos->newsimg_large_width:$CATALOG_image_prd_large_max_width,$catInfos->newsimg_large_height!=0?$catInfos->newsimg_large_height:$CATALOG_image_prd_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->newsimg_small_width!=0?$catInfos->newsimg_small_width:$CATALOG_image_prd_small_max_width,$catInfos->newsimg_small_height!=0?$catInfos->newsimg_small_height:$CATALOG_image_prd_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->newsimg_icon_width!=0?$catInfos->newsimg_icon_width:$CATALOG_image_prd_icon_max_width,$catInfos->newsimg_icon_height!=0?$catInfos->newsimg_icon_height:$CATALOG_image_prd_icon_max_height);
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
	$request=request("SELECT id_category,ordering from $TBL_news_news where id='$idNews'",$link);
	if($news=mysql_fetch_object($request)){
		$idCat = $news->id_category;
	} else {
		$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
	}
	if($_POST["active"]=="DATE" && ($_POST["from_date"]=="" || $_POST["to_date"]=="" || $_POST["from_date"]>$_POST["to_date"])){
		$ANIX_messages->addError(_("Les dates d'affichage spécifiées sont invalides. Merci d'utiliser le format AAAA-MM-JJ."));
	}
	if(!$ANIX_messages->nbErrors){
		$requestString ="UPDATE `$TBL_news_news` set ";
		$requestString.="`active`='".$_POST["active"]."',";
		$requestString.="`from_date`='".$_POST["from_date"]."',";
		$requestString.="`to_date`='".$_POST["to_date"]."',";
		$requestString.="`modified_on`='".getDBDate()."',";
		$requestString.="`modified_by`='$anix_username' ";
		$requestString.="WHERE id='$idNews'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//get the image sizes from the category
		$requestString="SELECT * FROM `$TBL_news_categories` WHERE `id`='$idCat'";
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
			$requestString="UPDATE `$TBL_news_info_news` set ";
			$requestString.="`title`='".htmlentities($_POST["title_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`date`='".htmlentities($_POST["date_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`short_desc`='".htmlentities($_POST["short_desc_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`details`='".htmlentities($_POST["details_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=" WHERE `id_news`='$idNews' and id_language='".$row_languages->id."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour des informations de la nouvelle."));
			}
		}
	}
	//Insert and resize the image of the news
	$imageUploaded=false;
	if(!$ANIX_messages->nbErrors && $_POST["image_action"]=="change" && (strcmp ($_FILES['image_file']['name'],"")!=0)){
		$imageUploaded = true;
		if(!isImageAllowed($_FILES['image_file']['name'])){
			$ANIX_messages->addError(_("Ce type de fichier image n'est pas pris en charge."));
		}
		if($imageUploaded){
			$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_news_news where id=$idNews",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_orig!=""){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_orig)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
				}
			}
			if($editCategory->image_file_small!="imgnews_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
				}
			}
			if($editCategory->image_file_large!="imgnews_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de l'élément."));
				}
			}
			if($editCategory->image_file_icon!="imgnews_icon_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_icon)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne icone de l'élément."));
				}
			}
		}
		if($imageUploaded){
			$fileName = 'imgnews_tmp_'.$idNews.'_'.$_FILES['image_file']['name'];
			$fileName_orig = 'imgnews_orig_'.$idNews.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgnews_large_'.$idNews.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgnews_small_'.$idNews.'_'.$_FILES['image_file']['name'];
			$fileName_icon = 'imgnews_icon_'.$idNews.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addWarning(_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image de l'élément au serveur."));
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_prd_orig_max_width,$CATALOG_image_prd_orig_max_height);
				$imageEditor->outputFile($fileName_orig,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->newsimg_large_width!=0?$catInfos->newsimg_large_width:$CATALOG_image_prd_large_max_width,$catInfos->newsimg_large_height!=0?$catInfos->newsimg_large_height:$CATALOG_image_prd_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->newsimg_small_width!=0?$catInfos->newsimg_small_width:$CATALOG_image_prd_small_max_width,$catInfos->newsimg_small_height!=0?$catInfos->newsimg_small_height:$CATALOG_image_prd_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->newsimg_icon_width!=0?$catInfos->newsimg_icon_width:$CATALOG_image_prd_icon_max_width,$catInfos->newsimg_icon_height!=0?$catInfos->newsimg_icon_height:$CATALOG_image_prd_icon_max_height);
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
		$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_news_news where id=$idNews",$link);
		$editCategory=mysql_fetch_object($request);
		if($editCategory->image_file_orig!=""){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_orig)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
			}
		}
		if($editCategory->image_file_small!="imgnews_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de l'élément."));
			}
		}
		if($editCategory->image_file_large!="imgnews_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de l'élément."));
			}
		}
		if($editCategory->image_file_icon!="imgnews_icon_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_icon)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne icone de l'élément."));
			}
		}
		$fileName_orig="";
		$fileName_large="imgnews_large_no_image.jpg";
		$fileName_small="imgnews_small_no_image.jpg";
		$fileName_icon="imgnews_icon_no_image.jpg";
	}
	if(!$ANIX_messages->nbErrors){
		//Update different fields using the news id
		$nb=0;
		$requestString ="UPDATE `$TBL_news_news` SET ";
		if($imageUploaded){
			$requestString .="`image_file_orig`='".$fileName_orig."',";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
			$requestString .="`image_file_icon`='".$fileName_icon."'";
			$nb++;
		}
		$requestString .="where `id`='".$idNews."'";
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
<?
if($action=="addAttachment"){
	//get the ordering
	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_news_attachments` WHERE id_news='$idNews' GROUP BY id_news",$link);
	if(mysql_num_rows($tmp)) {
		$maxOrder= mysql_fetch_object($tmp);
		$maxOrderValue = $maxOrder->maximum+1;
	} else $maxOrderValue=1;
	$requestString ="INSERT INTO `$TBL_news_attachments` (`id_news`,`id_language`,`title`,`description`,`ordering`) values (";
	$requestString.="'$idNews',";
	$requestString.="'".$_POST['id_language']."',";
	$requestString.="'".htmlentities($_POST['title'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="'".htmlentities($_POST['description'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="'$maxOrderValue')";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$ANIX_messages->addError(_("Une erreur s'est produite lors de l'ajout du fichier attaché."));
	} else {
		$idAttachment=mysql_insert_id($link);
		$fileUploaded=false;
	}
	if(!$ANIX_messages->nbErrors){
		$fileUploaded = (strcmp($_FILES['attachment_file']['name'],"")!=0);
		if($fileUploaded && isFileProhibited($_FILES['attachment_file']['name'])){
			$ANIX_messages->addError(_("Ce type de fichiers est interdit."));
		}
		if($fileUploaded && !$ANIX_messages->nbErrors){
			$fileName = "news".$idAttachment."_".$_FILES['attachment_file']['name'];
			$filePath = $CATALOG_folder_attachments.$fileName;
			if(!move_uploaded_file($_FILES['attachment_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addError(_("Vous n'avez pas spécifié de fichier ou une erreur s'est produite lors de l'envoi du fichier attaché au serveur."));
				$fileUploaded=false;
			}
		} else { //Not uploaded
			$ANIX_messages->addError(_("Vous n'avez pas spécifié de fichier."));
		}
	}
	//On définit le nouveau nom de fichier...
	if(!$ANIX_messages->nbErrors && $fileUploaded){
		$requestString="UPDATE `$TBL_news_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$ANIX_messages->addError(_("Une erreur s'est produite lors de l'ajout du fichier attaché."));
		}
	} else {
		$requestString = "DELETE from `$TBL_news_attachments` where id=$idAttachment";
		request($requestString,$link);
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idNews'",$link);
	}
	if(!$ANIX_messages->nbErrors) $ANIX_messages->addMessage(_("Le fichier a été correctement attaché à l'élément."));
	$action = "edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="updateAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$requestString ="UPDATE `$TBL_news_attachments` set ";
	$requestString.="`id_language`='".$_POST['id_language']."',";
	$requestString.="`title`='".htmlentities($_POST['title'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="`description`='".htmlentities($_POST['description'],ENT_QUOTES,"UTF-8")."' ";
	$requestString.="WHERE `id`='$idAttachment'";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour du fichier attaché."));
	}
	$fileUploaded=false;
	if(!$ANIX_messages->nbErrors){
		$fileUploaded = (strcmp($_FILES['attachment_file']['name'],"")!=0);
		if($fileUploaded && isFileProhibited($_FILES['attachment_file']['name'])){
			$ANIX_messages->addError(_("Ce type de fichiers est interdit."));
			$fileUploaded=false;
		}
		if($fileUploaded && !$ANIX_messages->nbErrors){
			$fileName = "news".$idAttachment."_".$_FILES['attachment_file']['name'];
			$filePath = $CATALOG_folder_attachments.$fileName;
			if(!move_uploaded_file($_FILES['attachment_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addError(_("Vous n'avez pas spécifié de fichier ou une erreur s'est produite lors de l'envoi du fichier attaché au serveur."));
				$fileUploaded=false;
			}
		}
	}
	//On d�init le nouveau nom de fichier...
	if($fileUploaded){
		$request = request("SELECT file_name from `$TBL_news_attachments` where id='$idAttachment'",$link);
		$oldFile = mysql_fetch_object($request);
		if($oldFile->file_name!="" && $oldFile->file_name!=$fileName) if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
			$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancien fichier."));
		}
		$requestString="UPDATE `$TBL_news_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$ANIX_messages->addError(_("Une erreur s'est produite lors de l'ajout du fichier attaché."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idNews'",$link);
	}
	if(!$ANIX_messages->nbErrors) $ANIX_messages->addMessage(_("Le fichier a été mis à jour courrectement."));
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="delAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$request = request("SELECT file_name,ordering from `$TBL_news_attachments` where id='$idAttachment'",$link);
	$oldFile = mysql_fetch_object($request);
	if($oldFile->file_name!="") if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
		$ANIX_messages->addError(("Une erreur s'est produite lors de la suppression de l'ancien fichier."));
	}
	if(!$ANIX_messages->nbErrors){
		$requestString ="DELETE from `$TBL_news_attachments` WHERE `id`='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des informations du fichier attaché."));
		}
	}
	//update the orderings
	if(!$ANIX_messages->nbErrors){
		request("UPDATE `$TBL_news_attachments` set ordering=ordering-1 where id_news='$idNews' and ordering > ".$oldFile->ordering,$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre des fichiers attachés."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idNews'",$link);
	}
	if(!$ANIX_messages->nbErrors) $ANIX_messages->addMessage(_("Le fichier attaché a été supprimé correctement."));
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="moveAttachmentUp"){
	if(isset($_POST["idAttachment"])){
		$idAttachment=$_POST["idAttachment"];
	} elseif(isset($_GET["idAttachment"])){
		$idAttachment=$_GET["idAttachment"];
	} else {
		$ANIX_messages->addError(_("Le fichier attaché n'a pas été spécifié."));
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT id,ordering from `$TBL_news_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("Le fichier attaché spécifié est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT id,ordering from `$TBL_news_attachments` where id_news='$idNews' and ordering='".($attachment->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upAttachment=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("Le fichier attaché est déjà au plus haut niveau."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE `$TBL_news_attachments` set ordering='".($attachment->ordering-1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE `$TBL_news_attachments` set ordering='".$attachment->ordering."' where id='".$upAttachment->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idNews'",$link);
		$ANIX_messages->addMessage(_("L'ordre du fichier attaché a été modifié correctement."));
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="moveAttachmentDown"){
	if(isset($_POST["idAttachment"])){
		$idAttachment=$_POST["idAttachment"];
	} elseif(isset($_GET["idAttachment"])){
		$idAttachment=$_GET["idAttachment"];
	} else {
		$ANIX_messages->addError(_("Le fichier attaché n'a pas été spécifié."));
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT id,ordering from `$TBL_news_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("Le fichier attaché spécifié est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT id,ordering from `$TBL_news_attachments` where id_news='$idNews' and ordering='".($attachment->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downAttachment=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("Le fichier attaché est déjà au plus bas niveau."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE `$TBL_news_attachments` set ordering='".($attachment->ordering+1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE `$TBL_news_attachments` set ordering='".$attachment->ordering."' where id='".$downAttachment->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idNews'",$link);
		$ANIX_messages->addMessage(_("L'ordre du fichier attaché a été modifié correctement."));
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>