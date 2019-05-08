<?
if($action=="moveup"){
	if(isset($_POST["idPhoto"])){
		$idPhoto=$_POST["idPhoto"];
	} elseif(isset($_GET["idPhoto"])){
		$idPhoto=$_GET["idPhoto"];
	} else {
		$ANIX_messages->addError(_("La nouvelle n'a pas été spécifiée."));
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_gallery_photo where id='$idPhoto'",$link);
		if(mysql_num_rows($request)){
			$photo=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_gallery_photo where id_category='".$photo->id_category."' and ordering='".($photo->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upPhoto=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La nouvelle est déjà au plus haut niveau."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_gallery_photo set ordering='".($photo->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$photo->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_gallery_photo set ordering='".$photo->ordering."' where id='".$upPhoto->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("L'ordre de la nouvelle a été modifié avec succès."));
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idPhoto"])){
		$idPhoto=$_POST["idPhoto"];
	} elseif(isset($_GET["idPhoto"])){
		$idPhoto=$_GET["idPhoto"];
	} else {
		$ANIX_messages->addError(_("La nouvelle n'a pas été spécifiée."));
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_gallery_photo where id='$idPhoto'",$link);
		if(mysql_num_rows($request)){
			$photo=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_gallery_photo where id_category='".$photo->id_category."' and ordering='".($photo->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$upPhoto=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La nouvelle spécifiée est déjà au plus bas niveau."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_gallery_photo set ordering='".($photo->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$photo->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_gallery_photo set ordering='".$photo->ordering."' where id='".$upPhoto->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("L'ordre de la nouvelle a été modifié avec succès."));
	}
	$action="edit";
}
?>
<?
if($action=="deletephoto"){
	if(isset($_POST["idPhoto"])){
		$idPhoto=$_POST["idPhoto"];
	} else {
		$ANIX_messages->addError(_("La nouvelle n'a pas été spécifiée."));
	}
	$photoOrdering=0;$photoCategory=0;
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT ordering,id_category from $TBL_gallery_photo where id=$idPhoto",$link);
		if(mysql_num_rows($request)){
			$tmp=mysql_fetch_object($request);
			$photoOrdering=$tmp->ordering;
			$photoCategory=$tmp->id_category;
			$idCat = $photoCategory;
		}else{
			$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//Update the orderings inside the category
		request("UPDATE $TBL_gallery_photo set ordering=ordering-1 WHERE id_category='$photoCategory' and ordering > $photoOrdering",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre des nouvelles."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//Delete the images from file system
		$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_gallery_photo where id=$idPhoto",$link);
		$editPhoto=mysql_fetch_object($request);
		if($editPhoto->image_file_orig!=""){
			if(!unlink("../".$CATALOG_folder_images.$editPhoto->image_file_orig)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image de l'élément."));
			}
		}
		if($editPhoto->image_file_small!="imgphoto_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editPhoto->image_file_small)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image de l'élément."));
			}
		}
		if($editPhoto->image_file_large!="imgphoto_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editPhoto->image_file_large)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la grande image de l'élément."));
			}
		}
		if($editPhoto->image_file_icon!="imgphoto_icon_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editPhoto->image_file_icon)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'icone de l'élément."));
			}
		}
	}
	//Delete the links with the products
	if(!$ANIX_messages->nbErrors){
		request("DELETE $TBL_links_catalogue_photo
               FROM $TBL_gallery_photo,
                    $TBL_links_catalogue_photo
               WHERE $TBL_gallery_photo.id='$idPhoto'
               AND $TBL_links_catalogue_photo.photo='item'
               AND $TBL_links_catalogue_photo.id_photo=$TBL_gallery_photo.id",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des liens avec les produits."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("DELETE $TBL_gallery_photo,$TBL_gallery_info_photo
               FROM $TBL_gallery_photo,$TBL_gallery_info_photo
               WHERE $TBL_gallery_photo.id='$idPhoto'
               AND $TBL_gallery_info_photo.id_photo=$TBL_gallery_photo.id",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La nouvelle a été supprimée correctement."));
	}
	$action="edit";
}
?>

<?
if($action=="copy"){
	if(isset($_POST["idPhoto"])){
		$idPhoto=$_POST["idPhoto"];
	} elseif(isset($_GET["idPhoto"])){
		$idPhoto=$_GET["idPhoto"];
	} else {
		$ANIX_messages->addError(_("La nouvelle n'a pas été spécifiée."));
	}
	if(isset($_POST["copyto"])){
		$copyTo=$_POST["copyto"];
	} elseif(isset($_GET["copyto"])){
		$copyTo=$_GET["copyto"];
	} else {
		$ANIX_messages->addError(_("La destination de la copie n'a pas été spécifiée."));
	}
	if(!$ANIX_messages->nbErrors){
		$ordering=getMaxPhotoOrder($copyTo,$link);
		if(!$ordering){
			$ANIX_messages->addError(_("La catégorie de destination est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		copyPhoto($idPhoto,$copyTo,$ordering,$link);
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La nouvelle a été copiée correctement."));
		$idCat = $copyTo;
	}
	$action="edit";
}
?>

<?
if($action=="move"){
	if(isset($_POST["idPhoto"])){
		$idPhoto=$_POST["idPhoto"];
	} elseif(isset($_GET["idPhoto"])){
		$idPhoto=$_GET["idPhoto"];
	} else {
		$ANIX_messages->addError(_("La nouvelle n'a pas été spécifiée."));
	}
	if(isset($_POST["moveto"])){
		$moveTo=$_POST["moveto"];
	} elseif(isset($_GET["moveto"])){
		$moveTo=$_GET["moveto"];
	} else {
		$ANIX_messages->addError(_("La destination n'a pas été spécifiée."));
	}
	if(!$ANIX_messages->nbErrors){
		//get the old new information
		$request=request("SELECT `id`,`id_category`,`ordering` FROM `$TBL_gallery_photo` WHERE `id`='$idPhoto'",$link);
		if(!mysql_num_rows($request)){
			$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
		}else {
			$oldPhoto = mysql_fetch_object($request);
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ordering=getMaxPhotoOrder($moveTo,$link);
		if(!$ordering){
			$ANIX_messages->addError(_("La catégorie de destination est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//move the photo
		request("UPDATE `$TBL_gallery_photo` SET `id_category`='$moveTo', `ordering`='$ordering' WHERE `id`='$idPhoto'",$link);
		//ro-order the old category
		request("UPDATE `$TBL_gallery_photo` SET `ordering`=`ordering`-1 WHERE `id_category`='$oldPhoto->id_category' AND `ordering`>'$oldPhoto->ordering'",$link);
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La nouvelle a été déplacée correctement."));
		$idCat = $moveTo;
	}
	$action="edit";
}
?>