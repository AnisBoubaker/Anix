<?php
if($action=="insert"){
	if($_POST["active"]=="DATE" && ($_POST["from_date"]=="" || $_POST["to_date"]=="" || $_POST["from_date"]>$_POST["to_date"])){
		$errors++;
		$errMessage.=_("Les dates d'affichage spéifiées sont invalides. Merci d'utiliser le format AAAA-MM-JJ.")."<br />";
	}
	if(!$errors){
		//Insert the category in the database
		$request = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_featured` WHERE id_category='$idCategory' GROUP BY id_category",$link);
		$tmp = mysql_fetch_object($request);
		if(isset($tmp->maximum)) $ordering = $tmp->maximum+1; else $ordering=1;
		$requestString="INSERT INTO `$TBL_catalogue_featured` (`id_category`,`active`,`from_date`,`to_date`,`image_file_large`,`image_file_small`, `ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$idCategory','".$_POST["active"]."','".$_POST["from_date"]."','".$_POST["to_date"]."','imgfeatured_large_no_image.jpg','imgfeatured_small_no_image.jpg','$ordering','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
		request($requestString,$link);
		//If insertion was OK, we rtrieve the id of the inserted category, else error...
		if(!mysql_errno($link)) {
			$idFeatured=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion de la vedette.")."<br />";
			$errors++;
		}
	}

	//Insert and resize the image of the category
	if(!$errors){
		$imageUploaded = (strcmp ($_FILES['image_file']['name'],"")!=0);
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$fileName = 'imgfeatured_'.$idFeatured.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgfeatured_large_'.$idFeatured.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgfeatured_small_'.$idFeatured.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifié d'image ou une erreur s'est produite lors de l'envoi de l'image de la vedette au serveur.")."<br />";
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($featuredCategories[$idCategory]["imglarge_maxH"],$featuredCategories[$idCategory]["imglarge_maxW"]);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($featuredCategories[$idCategory]["imgsmall_maxH"],$featuredCategories[$idCategory]["imgsmall_maxW"]);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spécifié d'image ou une erreur s'est produite lors de l'envoi de l'image de la vedette au serveur.")."<br />";
		}
		if($imageUploaded){
			$requestString ="UPDATE `$TBL_catalogue_featured` SET ";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."'";
			$requestString .="where `id`='".$idFeatured."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la spécification de l'image de la vedette.")."<br />";
			}
		}
	}
	if(!$errors){
		$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
		while($language = mysql_fetch_object($request)){
			request("INSERT INTO $TBL_catalogue_info_featured (`id_featured`,`id_language`,`title`,`field1`,`field2`)
                VALUES ('$idFeatured','".$language->id."','".htmlentities($_POST["title_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["field1_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["field2_".$language->id],ENT_QUOTES,"UTF-8")."')",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'insertions des informations de la vedette.")."<br />";
			}
		}
	}
	if(!$errors){
		$message = _("La vedette a été insété correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	if($_POST["active"]=="DATE" && ($_POST["from_date"]=="" || $_POST["to_date"]=="" || $_POST["from_date"]>$_POST["to_date"])){
		$errors++;
		$errMessage.=_("Les dates d'affichage spéifiées sont invalides. Merci d'utiliser le format AAAA-MM-JJ.")."<br />";
	}
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
			$request=request("SELECT image_file_large,image_file_small from $TBL_catalogue_featured where id='$idFeatured'",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_small!="imgfeatured_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de la vedette.")."<br />";
				}
			}
			if($editCategory->image_file_large!="imgfeatured_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de la vedette.")."<br />";
				}
			}
		}
		if($imageUploaded){
			$fileName = 'imgfeatured_'.$idFeatured.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgfeatured_large_'.$idFeatured.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgfeatured_small_'.$idFeatured.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.'/'.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifié d'image ou une erreur s'est produite lors de l'envoi de l'image de la vedette au serveur.")."<br />";
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($featuredCategories[$idCategory]["imglarge_maxH"],$featuredCategories[$idCategory]["imglarge_maxW"]);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($featuredCategories[$idCategory]["imgsmall_maxH"],$featuredCategories[$idCategory]["imgsmall_maxW"]);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spécifié d'image ou une erreur s'est produite lors de l'envoi de l'image de la vedette au serveur.")."<br />";
		}
	}
	//Update the category
	if(!$errors){
		$requestString ="UPDATE `$TBL_catalogue_featured` SET ";
		if($imageUploaded){
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
		}
		$requestString .="`active`='".$_POST["active"]."',";
		$requestString .="`from_date`='".$_POST["from_date"]."',";
		$requestString .="`to_date`='".$_POST["to_date"]."',";
		$requestString .="`modified_on`='".getDBDate()."',`modified_by`='$anix_username'";
		$requestString .="where `id`='".$idFeatured."'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de la vedette.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
		while($language = mysql_fetch_object($request)){
			request("UPDATE $TBL_catalogue_info_featured
                SET `title`='".htmlentities($_POST["title_".$language->id],ENT_QUOTES,"UTF-8")."',
                `field1`='".htmlentities($_POST["field1_".$language->id],ENT_QUOTES,"UTF-8")."',
                `field2`='".htmlentities($_POST["field2_".$language->id],ENT_QUOTES,"UTF-8")."'
                WHERE id_featured='$idFeatured'
                AND id_language='".$language->id."'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour des informations de la vedette.")."<br >";
			}
		}
	}
	if(!$errors){
		$message = _("La vedette a été mise à jour correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="addProductLink"){
	if(isset($_POST["idProduct"])){
		$idProduct=$_POST["idProduct"];
	} elseif(isset($_GET["idProduct"])){
		$idProduct=$_GET["idProduct"];
	} else $idProduct="";
	$request=request("SELECT id from $TBL_catalogue_products WHERE id='$idProduct'",$link);
	if(!mysql_num_rows($request)){
		$errors++;
		$errMessage.=_("Le produit spéifié est invalide.")."<br />";
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_featured` SET
               `id_catalogue_prd`='$idProduct',
               `id_catalogue_cat`='0'
               WHERE id='$idFeatured'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la liaison de la vedette avec le produit.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("Le lien avec le produit a été effectué")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="addProductCatLink"){
	if(isset($_POST["idCat"])){
		$idCat=$_POST["idCat"];
	} elseif(isset($_GET["idCat"])){
		$idCat=$_GET["idCat"];
	} else $idCat="";
	$request=request("SELECT id from $TBL_catalogue_categories WHERE id='$idCat'",$link);
	if(!mysql_num_rows($request)){
		$errors++;
		$errMessage.=_("La catégorie spécifiée est invalide.")."<br />";
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_featured` SET
               `id_catalogue_prd`='0',
               `id_catalogue_cat`='$idCat'
               WHERE id='$idFeatured'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la liaison de la vedette avec la catégorie.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("Le lien avec la catégorie a été effectué.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="unlink"){
	if(!$errors){
		request("UPDATE `$TBL_catalogue_featured` SET
               `id_catalogue_prd`='0',
               `id_catalogue_cat`='0'
               WHERE id='$idFeatured'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression du lien.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("Le lien a été supprimé correctement.")."<br />";
	}
	$action="edit";
}
?>