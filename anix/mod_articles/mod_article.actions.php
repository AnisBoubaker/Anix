<?php
if($action=="insert"){
	if($_POST["active"]=="DATE" && ($_POST["from_date"]=="" || $_POST["to_date"]=="" || $_POST["from_date"]>$_POST["to_date"])){
		$errors++;
		$errMessage.=_("Les dates d'affichage spécifiées sont invalides. Merci d'utiliser le format AAAA-MM-JJ.")."<br />";
	}
	if(!$errors){
		if(isset($_POST["idCat"])){
			$idCat=$_POST["idCat"];
		} elseif(isset($_GET["idCat"])){
			$idCat=$_GET["idCat"];
		} else $idCat="";
		/*$ordering=getMaxArticleOrder($idCat,$link);
		if(!$ordering){
		$errors++;
		$errMessage.=$str2;
		}*/
	}
	//Move down all the article in the category
	if(!$errors){
		$requestString="UPDATE `$TBL_articles_article` SET `ordering`=`ordering`+1 WHERE `id_category`='$idCat'";
		request($requestString,$link);
		if(mysql_errno($link)){
			$errMessage.="Une erreur s'est produite lors de la re-organisation de la categorie.";
			$errors++;
		}
	}
	if(!$errors){
		$requestString ="INSERT INTO `$TBL_articles_article` (`id_category`,`active`,`from_date`,`to_date`,`home_page`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values (";
		$requestString.="'$idCat',";
		$requestString.="'".$_POST["active"]."',";
		$requestString.="'".$_POST["from_date"]."',";
		$requestString.="'".$_POST["to_date"]."',";
		$requestString.="'".(isset($_POST["to_date"])?"Y":"N")."',";
		$requestString.="'1',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username')";
		request($requestString,$link);
		if(!mysql_errno($link)) {
			$idArticle=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion de l'article.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_articles_info_article` (`id_article`,`id_language`,`title`,`short_desc`,`details`,`keywords`,`htmltitle`,`htmldescription`) values (";
			$requestString.="'$idArticle',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["title_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["short_desc_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["details_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=")";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de l'insertion des informations de l'article.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		$message = _("L'article a été inséré correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	if($_POST["active"]=="DATE" && ($_POST["from_date"]=="" || $_POST["to_date"]=="" || $_POST["from_date"]>$_POST["to_date"])){
		$errors++;
		$errMessage.=_("Les dates d'affichage spécifiées sont invalides. Merci d'utiliser le format AAAA-MM-JJ.")."<br />";
	}
	if(!$errors){
		$requestString ="UPDATE `$TBL_articles_article` set ";
		$requestString.="`active`='".$_POST["active"]."',";
		$requestString.="`from_date`='".$_POST["from_date"]."',";
		$requestString.="`to_date`='".$_POST["to_date"]."',";
		$requestString.="`home_page`='".(isset($_POST["home_page"])?"Y":"N")."',";
		$requestString.="`modified_on`='".getDBDate()."',";
		$requestString.="`modified_by`='$anix_username' ";
		$requestString.="WHERE id='$idArticle'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'article.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="UPDATE `$TBL_articles_info_article` set ";
			$requestString.="`title`='".htmlentities($_POST["title_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`short_desc`='".htmlentities($_POST["short_desc_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`details`='".htmlentities($_POST["details_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=" WHERE `id_article`='$idArticle' and id_language='".$row_languages->id."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour des informations de l'article.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		$message = _("L'article a été mis à jour correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="addAttachment"){
	//get the ordering
	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_articles_attachments` WHERE id_article='$idArticle' GROUP BY id_article",$link);
	if(mysql_num_rows($tmp)) {
		$maxOrder= mysql_fetch_object($tmp);
		$maxOrderValue = $maxOrder->maximum+1;
	} else $maxOrderValue=1;
	$requestString ="INSERT INTO `$TBL_articles_attachments` (`id_article`,`id_language`,`title`,`description`,`ordering`) values (";
	$requestString.="'$idArticle',";
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
			$fileName = "article".$idAttachment."_".$_FILES['attachment_file']['name'];
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
		$requestString="UPDATE `$TBL_articles_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.")."<br />";
			$errors++;
		}
	} else {
		$requestString = "DELETE from `$TBL_articles_attachments` where id=$idAttachment";
		request($requestString,$link);
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idArticle'",$link);
	}
	if(!$errors) $message.=_("Le fichier a été correctement attaché à l'élément.")."<br />";
	$action = "edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="updateAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$requestString ="UPDATE `$TBL_articles_attachments` set ";
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
			$fileName = "article".$idAttachment."_".$_FILES['attachment_file']['name'];
			$filePath = $CATALOG_folder_attachments.$fileName;
			if(!move_uploaded_file($_FILES['attachment_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifié de fichier ou une erreur s'est produite lors de l'envoi du fichier attaché au serveur.")."<br />";
				$errors++;
				$fileUploaded=false;
			}
		}
	}
	//On d�init le nouveau nom de fichier...
	if($fileUploaded){
		$request = request("SELECT file_name from `$TBL_articles_attachments` where id='$idAttachment'",$link);
		$oldFile = mysql_fetch_object($request);
		if($oldFile->file_name!="" && $oldFile->file_name!=$fileName) if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancien fichier.")."<br />";
		}
		$requestString="UPDATE `$TBL_articles_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idArticle'",$link);
	}
	if(!$errors) $message = _("Le fichier a été mis à jour courrectement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="delAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$request = request("SELECT file_name,ordering from `$TBL_articles_attachments` where id='$idAttachment'",$link);
	$oldFile = mysql_fetch_object($request);
	if($oldFile->file_name!="") if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
		$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancien fichier.")."<br />";
		$errors++;
	}
	if(!$errors){
		$requestString ="DELETE from `$TBL_articles_attachments` WHERE `id`='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression des informations du fichier attaché.")."<br />";
			$errors++;
		}
	}
	//update the orderings
	if(!$errors){
		request("UPDATE `$TBL_articles_attachments` set ordering=ordering-1 where id_article='$idArticle' and ordering > ".$oldFile->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des fichiers attachés.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idArticle'",$link);
	}
	if(!$errors) $message = _("Le fichier attaché a été supprimé correctement.")."<br />";
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
		$errors++;
		$errMessage.=_("Le fichier attaché n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_articles_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_articles_attachments` where id_article='$idArticle' and ordering='".($attachment->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upAttachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_articles_attachments` set ordering='".($attachment->ordering-1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_articles_attachments` set ordering='".$attachment->ordering."' where id='".$upAttachment->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idArticle'",$link);
		$message.=_("L'ordre du fichier attaché a été modifié correctement.")."<br />";
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
		$errors++;
		$errMessage.=_("Le fichier attaché n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_articles_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_articles_attachments` where id_article='$idArticle' and ordering='".($attachment->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downAttachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_articles_attachments` set ordering='".($attachment->ordering+1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_articles_attachments` set ordering='".$attachment->ordering."' where id='".$downAttachment->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idArticle'",$link);
		$message.=_("L'ordre du fichier attaché a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>