<?php
if($action=="insert"){
	//Get the ordering number of the new category
	$ordering=getMaxCategoryOrder($idCat,$link);
	//Insert the category in the database
	$requestString="INSERT INTO `$TBL_catalogue_categories`
					(`ordering`, `id_parent`,`image_file_large`,`image_file_small`,`contain_products`,`hide_products`,`reference_pattern`,`id_menu`,`productimg_icon_width`,`productimg_icon_height`,`productimg_small_width`,`productimg_small_height`,`productimg_large_width`,`productimg_large_height`,`created_on`,`created_by`,`modified_on`,`modified_by`)
					VALUES ('$ordering','$idCat','imgcat_large_no_image.jpg','imgcat_small_no_image.jpg','".(isset($_POST["contain_products"])?"Y":"N")."','".(isset($_POST["hide_products"])?"Y":"N")."','".$_POST["reference_pattern"]."','".$_POST["id_menu"]."','".$_POST["productimg_icon_width"]."','".$_POST["productimg_icon_height"]."','".$_POST["productimg_small_width"]."','".$_POST["productimg_small_height"]."','".$_POST["productimg_large_width"]."','".$_POST["productimg_large_height"]."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
	request($requestString,$link);
	//If insertion was OK, we rtrieve the id of the inserted category, else error...
	if(!mysql_errno($link)) {
		$idCat=mysql_insert_id($link);
	} else {
		$errMessage.=_("Une erreur s'est produite lors de l'insertion de la catégorie de produits.")."<br />";
		$errors++;
	}
	//Insert the category information
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_catalogue_info_categories` (`id_catalogue_cat`,`id_language`,`name`,`description`,`keywords`,`htmltitle`,`htmldescription`) values (";
			$requestString.="'$idCat',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
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
			$fileName = 'imgcat_'.$idCat.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgcat_large_'.$idCat.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgcat_small_'.$idCat.'_'.$_FILES['image_file']['name'];
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
			$requestString ="UPDATE `$TBL_catalogue_categories` SET ";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."'";
			$requestString .="where `id`='".$idCat."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la spécification de l'image de la catégorie.")."<br />";
			}
		}
	}
	//Insert the partnership information
	if(!$errors){
		//erase previous partnerships
		//request("DELETE FROM `$TBL_catalogue_anix_partner` WHERE `id_catalogue_category`='$idCat'",$link);
		//get the partners information
		$requestString = "SELECT `id` FROM `$TBL_catalogue_partner`";
		$request=request($requestString,$link);
		while($partner = mysql_fetch_object($request)){
			if(isset($_POST["partner_".$partner->id])){
				request("INSERT INTO `$TBL_catalogue_anix_partner` (`id_catalogue_category`,`id_partner`,`id_partner_category`) VALUES ('$idCat','$partner->id','".$_POST["partner_".$partner->id]."')",$link);
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
		$requestString="UPDATE `$TBL_catalogue_info_categories` set ";
		$requestString.="`name`='".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`description`='".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";

		$requestString.=" WHERE `id_catalogue_cat`='$idCat' and id_language='".$row_languages->id."'";
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
			$request=request("SELECT image_file_large,image_file_small from $TBL_catalogue_categories where id=$idCat",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_small!="imgcat_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de la catégorie.")."<br />";
				}
			}
			if($editCategory->image_file_large!="imgcat_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de la catégorie.")."<br />";
				}
			}
		}
		if($imageUploaded){
			$fileName = 'imgcat_'.$idCat.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgcat_large_'.$idCat.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgcat_small_'.$idCat.'_'.$_FILES['image_file']['name'];
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
	}
	//DELETE THE IMAGE = REPLACE BY THE NO_IMAGE
	if(!$errors && $_POST["image_action"]=="delete"){
		$imageUploaded=true;
		$request=request("SELECT image_file_large,image_file_small from $TBL_catalogue_categories where id=$idCat",$link);
		$editCategory=mysql_fetch_object($request);
		if($editCategory->image_file_small!="imgcat_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
				$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de la catégorie.")."<br />";
			}
		}
		if($editCategory->image_file_large!="imgcat_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
				$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de la catégorie.")."<br />";
			}
		}
		$fileName_orig="";
		$fileName_large="imgprd_large_no_image.jpg";
		$fileName_small="imgprd_small_no_image.jpg";
		$fileName_icon="imgprd_icon_no_image.jpg";
	}
	//Update the category
	if(!$errors){
		$requestString ="UPDATE `$TBL_catalogue_categories` SET ";
		if($imageUploaded){
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
		}
		$requestString .="`contain_products`='".(isset($_POST["contain_products"])?"Y":"N")."',";
		$requestString .="`hide_products`='".(isset($_POST["hide_products"])?"Y":"N")."',";
		$requestString .="`reference_pattern`='".$_POST["reference_pattern"]."',";
		$requestString .="`id_menu`='".$_POST["id_menu"]."',";
		$requestString .="`productimg_icon_width`='".$_POST["productimg_icon_width"]."',";
		$requestString .="`productimg_icon_height`='".$_POST["productimg_icon_height"]."',";
		$requestString .="`productimg_small_width`='".$_POST["productimg_small_width"]."',";
		$requestString .="`productimg_small_height`='".$_POST["productimg_small_height"]."',";
		$requestString .="`productimg_large_width`='".$_POST["productimg_large_width"]."',";
		$requestString .="`productimg_large_height`='".$_POST["productimg_large_height"]."',";
		$requestString .="modified_on='".getDBDate()."',modified_by='$anix_username'";
		$requestString .="where `id`='".$idCat."'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de la catégorie.")."<br />";
		}
	}
	//Update the extra sections values
	if(!$errors){
		$extraSections=request("SELECT $TBL_catalogue_extracategorysection.id,$TBL_catalogue_info_extracategorysection.id_language from $TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection where id_cat='$idCat' and id=id_extrasection",$link);
		while($extraSection=mysql_fetch_object($extraSections)){
			$requestString ="UPDATE `$TBL_catalogue_info_extracategorysection` SET";
			$requestString.="`value`='".htmlentities($_POST["extrasection_".$extraSection->id."_".$extraSection->id_language],ENT_QUOTES,"UTF-8")."'";
			$requestString.="where id_extrasection='".$extraSection->id."' and id_language='".$extraSection->id_language."'";
			request($requestString,$link);
			//echo $requestString;
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour des informations de la section additionnelle.")."<br />";
			}
		}
	}
	//Insert the partnership information
	if(!$errors){
		//erase previous partnerships
		request("DELETE FROM `$TBL_catalogue_anix_partner` WHERE `id_catalogue_category`='$idCat'",$link);
		//get the partners information
		$requestString = "SELECT `id` FROM `$TBL_catalogue_partner`";
		$request=request($requestString,$link);
		while($partner = mysql_fetch_object($request)){
			if(isset($_POST["partner_".$partner->id])){
				request("INSERT INTO `$TBL_catalogue_anix_partner` (`id_catalogue_category`,`id_partner`,`id_partner_category`) VALUES ('$idCat','$partner->id','".$_POST["partner_".$partner->id]."')",$link);
			}
		}
	}
	if(!$errors){
		$message = _("La catégorie a été mise à jour correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="addExtraField"){
	if(!isset($_POST["type"])){
		$errors++;
		$errMessage.=_("Vous n'avez pas spécifié le type de champs additionnel.")."<br />";
	}
	if(!$errors && $_POST["type"]=="selection"){
		//Check if we have values on each language for selection field
		$languages=request("SELECT id from $TBL_gen_languages WHERE `used`='Y'",$link);
		while($language=mysql_fetch_object($languages)){
			if($_POST["param3_".$language->id]==""){
				$errors++;
			}
		}
		if($errors){
			$errMessage.=_("Vous devez spécifiez des valeurs de sélection pour CHAQUE langue.")."<br />";
		}
	}
	if(!$errors){
		//Inserting the extrafield
		$params="";
		if($_POST["type"]=="text"){
			$params=$_POST["param1"];
			if($params=="") $params=50;
		}
		//get the ordering
		$tmp = request("SELECT MAX(ordering) as maximum from $TBL_catalogue_extrafields WHERE id_cat='$idCat' GROUP BY id_cat",$link);
		if(mysql_num_rows($tmp)) {
			$maxOrder= mysql_fetch_object($tmp);
			$maxOrderValue = $maxOrder->maximum+1;
		} else $maxOrderValue=1;
		$requestString="INSERT INTO `$TBL_catalogue_extrafields` (`datatype`,`id_cat`,`params`,`ordering`) values ('".$_POST["type"]."','$idCat','$params','$maxOrderValue')";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout du champs supplémentaire.")."<br />";
			$errors++;
		}
	}
	//if it's a selection field, we insert depending on the language.
	if(!$errors){
		//we retrieve the id of the inserted extra_field
		$idExtraField=mysql_insert_id($link);
		$languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used ='Y'",$link);
		while($language=mysql_fetch_object($languages)){
			$selectionValue="";
			$name = htmlentities($_POST["name_".$language->id],ENT_QUOTES,"UTF-8");
			$description = htmlentities($_POST["description_".$language->id],ENT_QUOTES,"UTF-8");
			if($_POST["type"]=="selection"){
				$selectionValue=addslashes($_POST["param3_".$language->id]);
			}
			$requestString="INSERT INTO `$TBL_catalogue_info_extrafields` (`id_extrafield`,`id_language`,`name`,`description`, `selection_values`) values ('$idExtraField','".$language->id."','$name','$description','$selectionValue')";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errMessage.=_("Une erreur s'est produite lors de l'ajout des informations du champs additionnel.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) {
		$message = _("Le champs supplémentaire a été créé correctement pour cette catégorie.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="updateExtraField"){
	$idExtraField=$_POST["idExtraField"];
	//Inserting the category
	$params="";
	if($_POST["datatype"]=="text"){
		$params=$_POST["param1"];
		if($params=="") $params=50;
		$requestString ="UPDATE `$TBL_catalogue_extrafields` SET ";
		$requestString.="`params`='".$params."' ";
		$requestString.="where `id`='$idExtraField'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour du champs supplémentaire.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used ='Y'",$link);
		while($language=mysql_fetch_object($languages)){
			$requestString ="UPDATE `$TBL_catalogue_info_extrafields` SET ";
			$requestString.="`name`='".htmlentities($_POST["name_".$language->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`description`='".htmlentities($_POST["description_".$language->id],ENT_QUOTES,"UTF-8")."' ";
			if($_POST["datatype"]=="selection"){
				$value=addslashes($_POST["param3_".$language->id]);
				$requestString.=",`selection_values`='$value' ";
			}
			$requestString.="where id_language=".$language->id." and id_extrafield=$idExtraField";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errMessage.=_("Une erreur s'est produite lors de la déinition des valeurs de sélection.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) $message = _("Le champs supplémentaire a été modifié correctement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="deleteExtraField"){
	$idExtraField=$_POST["idExtraField"];
	$request = request("SELECT id,ordering,deletable from `$TBL_catalogue_extrafields` where id='$idExtraField'",$link);
	if(mysql_num_rows($request)){
		$extrafield = mysql_fetch_object($request);
	} else{
		$errors++;
		$errMessage.=_("Le champs additionnel spécifié est invalide.")."<br />";
	}
	if(!$errors && $extrafield->deletable=="N"){
		$errors++;
		$errMessage.=_("Ce champs additionnel ne peut être supprimé.")."<br />";
	}
	if(!$errors){
		$requestString = "DELETE from `$TBL_catalogue_extrafields_values` where id_extrafield='$idExtraField'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression des informations contenues par le champs à supprimer.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$requestString = "DELETE from `$TBL_catalogue_info_extrafields` where id_extrafield='$idExtraField'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression des informations du champs.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$requestString = "DELETE from `$TBL_catalogue_extrafields` where id='$idExtraField'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression du champs.")."<br />";
			$errors++;
		}
	}
	//update the orderings
	if(!$errors){
		request("UPDATE `$TBL_catalogue_extrafields` set ordering=ordering-1 where id_cat='$idCat' and ordering > ".$extrafield->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des champs additionnels.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) $message = _("Le champs additionnel a été supprimé correctement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="addExtraSection"){
	if(!$errors){
		//get the ordering
		$tmp = request("SELECT MAX(ordering) as maximum from $TBL_catalogue_extracategorysection WHERE id_cat='$idCat' GROUP BY id_cat",$link);
		if(mysql_num_rows($tmp)) {
			$maxOrder= mysql_fetch_object($tmp);
			$maxOrderValue = $maxOrder->maximum+1;
		} else $maxOrderValue=1;
		$requestString="INSERT INTO `$TBL_catalogue_extracategorysection` (`id_cat`,`ordering`) values ('$idCat','$maxOrderValue')";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout de la section supplémentaire.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		//we retrieve the id of the inserted extra_field
		$idExtraSection=mysql_insert_id($link);
		$languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used ='Y'",$link);
		while($language=mysql_fetch_object($languages)){
			$name = htmlentities($_POST["name_".$language->id],ENT_QUOTES,"UTF-8");
			$value = $CATALOG_editor_default_value;
			$requestString="INSERT INTO `$TBL_catalogue_info_extracategorysection` (`id_extrasection`,`id_language`,`name`, `value`) values ('$idExtraSection','".$language->id."','$name','$value')";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errMessage.=_("Une erreur s'est produite lors de l'ajout des informations de la section additionnelle.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) {
		$message = _("La section additionnelle a été créée correctement pour cette catégorie.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=2;
}
?>
<?
if($action=="deleteExtraSection"){
	$idExtraSection=$_POST["idExtraSection"];
	$request = request("SELECT id,ordering from `$TBL_catalogue_extracategorysection` where id='$idExtraSection'",$link);
	if(mysql_num_rows($request)){
		$extrasection = mysql_fetch_object($request);
	} else{
		$errors++;
		$errMessage.=_("La section additionnelle spécifiée est invalide.")."<br />";
	}
	if(!$errors && $extrasection->deletable=="N"){
		$errors++;
		$errMessage.=_("Cette section additionnelle ne peut être supprimée.")."<br />";
	}
	if(!$errors){
		$requestString = "DELETE from `$TBL_catalogue_info_extracategorysection` where id_extrasection=$idExtraSection";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression des informations de la section.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$requestString = "DELETE from `$TBL_catalogue_extracategorysection` where id=$idExtraSection";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression de la section.")."<br />";
			$errors++;
		}
	}
	//update the orderings
	if(!$errors){
		request("UPDATE `$TBL_catalogue_extracategorysection` set ordering=ordering-1 where id_cat='$idCat' and ordering > ".$extrasection->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des sections additionnelles.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) $message = _("La section additionnelle a été supprimée correctement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=2;
}
?>
<?
if($action=="updateExtraSection"){
	$idExtraSection=$_POST["idExtraSection"];
	//Inserting the category
	$languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used ='Y'",$link);
	while($language=mysql_fetch_object($languages)){
		$name = htmlentities($_POST["name_".$language->id],ENT_QUOTES,"UTF-8");
		$requestString="UPDATE `$TBL_catalogue_info_extracategorysection` SET `name`='$name' WHERE `id_extrasection`='$idExtraSection' and `id_language`='".$language->id."'";
		request($requestString,$link);
		if(mysql_errno($link)){
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour des informations de la section additionnelle.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) $message = _("La section additionnelle a été modifiée correctement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=2;
}
?>
<?
if($action=="addAttachment"){
	//get the ordering
	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_attachments` WHERE id_category='$idCat' GROUP BY id_category",$link);
	if(mysql_num_rows($tmp)) {
		$maxOrder= mysql_fetch_object($tmp);
		$maxOrderValue = $maxOrder->maximum+1;
	} else $maxOrderValue=1;
	$requestString ="INSERT INTO `$TBL_catalogue_attachments` (`id_category`,`id_language`,`title`,`description`,`ordering`) values (";
	$requestString.="'$idCat',";
	$requestString.="'".$_POST['id_language']."',";
	$requestString.="'".htmlentities($_POST['title'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="'".htmlentities($_POST['description'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="'$maxOrderValue')";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.")."<br />";
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
			$fileName = "catalogue".$idAttachment."_".$_FILES['attachment_file']['name'];
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
	//On d�init le nouveau nom de fichier...
	if(!$errors && $fileUploaded){
		$requestString="UPDATE `$TBL_catalogue_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché")."<br />";
			$errors++;
		}
	} else {
		$requestString = "DELETE from `$TBL_catalogue_attachments` where id=$idAttachment";
		request($requestString,$link);
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) $message.=_("Le fichier a été correctement attaché.")."<br />";
	$action = "edit";
	$ANIX_TabSelect=4;
}
?>
<?
if($action=="updateAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$requestString ="UPDATE `$TBL_catalogue_attachments` set ";
	$requestString.="`id_language`='".$_POST['id_language']."',";
	$requestString.="`title`='".htmlentities($_POST['title'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="`description`='".htmlentities($_POST['description'],ENT_QUOTES,"UTF-8")."' ";
	$requestString.="WHERE `id`='$idAttachment'";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$errMessage.=_("Une erreur s'est produite lors de la mise à jour du fichier attaché")."<br />";
		$errors++;
	}
	$fileUploaded=false;
	if(!$errors){
		$fileUploaded = (strcmp($_FILES['attachment_file']['name'],"")!=0);
		if($fileUploaded && isFileProhibited($_FILES['attachment_file']['name'])){
			$errors++;
			$errMessage.=_("Ce type de fichiers est interdit.")."<br />";
		}
		if($fileUploaded && !$errors){
			$fileName = "catalogue".$idAttachment."_".$_FILES['attachment_file']['name'];
			$filePath = $CATALOG_folder_attachments.$fileName;
			if(!move_uploaded_file($_FILES['attachment_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifié de fichier ou une erreur s'est produite lors de l'envoi du fichier attaché au serveur.")."<br />";
				$errors++;
				$fileUploaded=false;
			}
		}
	}
	//On definit le nouveau nom de fichier...
	if($fileUploaded){
		$request = request("SELECT file_name from `$TBL_catalogue_attachments` where id='$idAttachment'",$link);
		$oldFile = mysql_fetch_object($request);
		if($oldFile->file_name!="" && $oldFile->file_name!=$fileName) if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancien fichier.")."<br />";
		}
		$requestString="UPDATE `$TBL_catalogue_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) $message = _("Le fichier a été mis à jour courrectement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=4;
}
?>
<?
if($action=="delAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$request = request("SELECT file_name,ordering from `$TBL_catalogue_attachments` where id='$idAttachment'",$link);
	$oldFile = mysql_fetch_object($request);
	if($oldFile->file_name!="") if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
		$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancien fichier.")."<br />";
		$errors++;
	}
	if(!$errors){
		$requestString ="DELETE from `$TBL_catalogue_attachments` WHERE `id`='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression des informations du fichier attaché.")."<br />";
			$errors++;
		}
	}
	//update the orderings
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering=ordering-1 where id_category='$idCat' and ordering > ".$oldFile->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des fichiers attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idCat'",$link);
	}
	if(!$errors) $message = _("Le fichier attaché a été supprimé correctement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=4;
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
		$request=request("SELECT id,ordering from `$TBL_catalogue_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_attachments` where id_category='$idCat' and ordering='".($attachment->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upAttachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering='".($attachment->ordering-1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering='".$attachment->ordering."' where id='".$upAttachment->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idCat'",$link);
		$message.=_("L'ordre du fichier attaché a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=4;
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
		$request=request("SELECT id,ordering from `$TBL_catalogue_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_attachments` where id_category='$idCat' and ordering='".($attachment->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downAttachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering='".($attachment->ordering+1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering='".$attachment->ordering."' where id='".$downAttachment->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idCat'",$link);
		$message.=_("L'ordre du fichier attaché a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=4;
}
?>
<?
if($action=="moveEFup"){
	if(isset($_POST["idExtrafield"])){
		$idExtrafield=$_POST["idExtrafield"];
	} elseif(isset($_GET["idExtrafield"])){
		$idExtrafield=$_GET["idExtrafield"];
	} else {
		$errors++;
		$errMessage.=_("Le champs additionnel n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_catalogue_extrafields where id='$idExtrafield'",$link);
		if(mysql_num_rows($request)){
			$extrafield=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le champs additionnel spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_catalogue_extrafields where id_cat='$idCat' and ordering='".($extrafield->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upField=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le champs additionnel est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_extrafields set ordering='".($extrafield->ordering-1)."' where id='$idExtrafield'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=("Une erreur s'est produitlors de la mise à jour de l'ordre du champs additionnel.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_extrafields set ordering='".$extrafield->ordering."' where id='".$upField->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produitlors de la mise à jour de l'ordre du champs additionnel.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idCat'",$link);
		$message.=_("L'ordre du champs additionnel a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="moveEFdown"){
	if(isset($_POST["idExtrafield"])){
		$idExtrafield=$_POST["idExtrafield"];
	} elseif(isset($_GET["idExtrafield"])){
		$idExtrafield=$_GET["idExtrafield"];
	} else {
		$errors++;
		$errMessage.=_("Le champs additionnel n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_catalogue_extrafields where id='$idExtrafield'",$link);
		if(mysql_num_rows($request)){
			$extrafield=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le champs additionnel spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_catalogue_extrafields where id_cat='$idCat' and ordering='".($extrafield->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downField=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le champs additionnel est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_extrafields set ordering='".($extrafield->ordering+1)."' where id='$idExtrafield'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=("Une erreur s'est produitlors de la mise à jour de l'ordre du champs additionnel.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_extrafields set ordering='".$extrafield->ordering."' where id='".$downField->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=("Une erreur s'est produitlors de la mise à jour de l'ordre du champs additionnel.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idCat'",$link);
		$message.=_("L'ordre du champs additionnel a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="moveESup"){
	if(isset($_POST["idExtraSection"])){
		$idExtrasection=$_POST["idExtraSection"];
	} elseif(isset($_GET["idExtraSection"])){
		$idExtrasection=$_GET["idExtraSection"];
	} else {
		$errors++;
		$errMessage.=_("La section additionnelle n'a pas été spécifiée.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_extracategorysection` where id='$idExtrasection'",$link);
		if(mysql_num_rows($request)){
			$extrasection=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La section additionnelle spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_extracategorysection` where id_cat='$idCat' and ordering='".($extrasection->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upSection=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La section additionnelle est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_extracategorysection` set ordering='".($extrasection->ordering-1)."' where id='$idExtrasection'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produitlors de la mise à jour de l'ordre de la section additionnelle.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_extracategorysection` set ordering='".$extrasection->ordering."' where id='".$upSection->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produitlors de la mise à jour de l'ordre de la section additionnelle.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idCat'",$link);
		$message.=_("L'ordre de la section additionnelle a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=2;
}
?>
<?
if($action=="moveESdown"){
	if(isset($_POST["idExtraSection"])){
		$idExtrasection=$_POST["idExtraSection"];
	} elseif(isset($_GET["idExtraSection"])){
		$idExtrasection=$_GET["idExtraSection"];
	} else {
		$errors++;
		$errMessage.=_("La section additionnelle n'a pas été spécifiée.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_extracategorysection` where id='$idExtrasection'",$link);
		if(mysql_num_rows($request)){
			$extrasection=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La section additionnelle spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_extracategorysection` where id_cat='$idCat' and ordering='".($extrasection->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downSection=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La section additionnelle est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_extracategorysection` set ordering='".($extrasection->ordering+1)."' where id='$idExtrasection'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produitlors de la mise à jour de l'ordre de la section additionnelle.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_extracategorysection` set ordering='".$extrasection->ordering."' where id='".$downSection->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produitlors de la mise à jour de l'ordre de la section additionnelle.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idCat'",$link);
		$message.=_("L'ordre de la section additionnelle a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=2;
}
?>