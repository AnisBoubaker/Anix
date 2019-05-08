<?php
if($action=="moveup"){
	if(isset($_POST["idItem"])){
		$idItem=$_POST["idItem"];
	} elseif(isset($_GET["idItem"])){
		$idItem=$_GET["idItem"];
	} else {
		$errors++;
		$errMessage.=_("Le élément n'a pas été spéifié")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_lists_items where id='$idItem'",$link);
		if(mysql_num_rows($request)){
			$item=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le élément spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_lists_items where id_category='".$item->id_category."' and ordering='".($item->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upItem=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le élément spécifié est dété au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items set ordering='".($item->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$item->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de l'élément.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items set ordering='".$item->ordering."' where id='".$upItem->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de l'élément.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de l'élément a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idItem"])){
		$idItem=$_POST["idItem"];
	} elseif(isset($_GET["idItem"])){
		$idItem=$_GET["idItem"];
	} else {
		$errors++;
		$errMessage.=_("Le élément n'a pas été spéifié")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_lists_items where id='$idItem'",$link);
		if(mysql_num_rows($request)){
			$item=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le élément spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_lists_items where id_category='".$item->id_category."' and ordering='".($item->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$upItem=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le élément spécifié est dété au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items set ordering='".($item->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$item->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de l'élément.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_items set ordering='".$item->ordering."' where id='".$upItem->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de l'élément.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de l'élément a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="deleteitem"){
	if(isset($_POST["idItem"])){
		$idItem=$_POST["idItem"];
	} else {
		$errors++;
		$errMessage.=_("Le élément n'a pas été spéifié")."<br />";
	}
	$itemOrdering=0;$itemCategory=0;
	if(!$errors){
		$request=request("SELECT ordering,id_category from $TBL_lists_items where id=$idItem",$link);
		if(mysql_num_rows($request)){
			$tmp=mysql_fetch_object($request);
			$itemOrdering=$tmp->ordering;
			$itemCategory=$tmp->id_category;
		}else{
			$errors++;
			$errMessage.=_("Le élément spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$idCat = $itemCategory;
		//Update the orderings inside the category
		request("UPDATE $TBL_lists_items set ordering=ordering-1 WHERE id_category='$itemCategory' and ordering > $itemOrdering",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des éléments.")."<br />";
		}
	}
	if(!$errors){
		//Delete the images from file system
		$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_lists_items where id=$idItem",$link);
		$editItem=mysql_fetch_object($request);
		if($editItem->image_file_orig!=""){
			if(!unlink("../".$CATALOG_folder_images.$editItem->image_file_orig)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image de l'élément."));
			}
		}
		if($editItem->image_file_small!="imgflex_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editItem->image_file_small)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image de l'élément."));
			}
		}
		if($editItem->image_file_large!="imgflex_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editItem->image_file_large)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la grande image de l'élément."));
			}
		}
		if($editItem->image_file_icon!="imgflex_icon_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editItem->image_file_icon)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'icone de l'élément."));
			}
		}
	}
	if(!$errors){
		//Delete the attachments from file system
		$request=request("SELECT file_name from $TBL_lists_attachments where id_item='$idItem'",$link);
		while($attachments=mysql_fetch_object($request)){
			if($attachments->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$attachments->file_name)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression des fichiers attaché à l'élément."));
				}
			}
		}
		request("DELETE FROM $TBL_lists_attachments where id_item='$idItem'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression des fichiers attaché de la base de données.")."<br />";
		}
	}
	//Delete extrafilds values for the item
	if(!$errors){
		request("DELETE FROM $TBL_lists_extrafields_values
             WHERE $TBL_lists_extrafields_values.id_item='$idItem'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression des champs supplémentaires.")."<br />";
		}
	}
	if(!$errors){
		request("DELETE $TBL_lists_items,$TBL_lists_info_items
             FROM $TBL_lists_items,$TBL_lists_info_items
             WHERE $TBL_lists_items.id='$idItem'
             AND $TBL_lists_info_items.id_item=$TBL_lists_items.id",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression de l'élément.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("Le élément a été supprimé correctement.")."<br />";
	}
	$action="edit";
}
?>

<?
if($action=="copy"){
	if(isset($_POST["idItem"])){
		$idItem=$_POST["idItem"];
	} elseif(isset($_GET["idItem"])){
		$idItem=$_GET["idItem"];
	} else {
		$errors++;
		$errMessage.=_("Le élément n'a pas été spéifié")."<br />";
	}
	if(isset($_POST["copyto"])){
		$copyTo=$_POST["copyto"];
	} elseif(isset($_GET["copyto"])){
		$copyTo=$_GET["copyto"];
	} else {
		$errors++;
		$errMessage.=_("La destination de la copie n'a pas été spécifiée.")."<br />";
	}
	if(!$errors){
		$ordering=getMaxItemsOrder($copyTo,$link);
		if(!$ordering){
			$errors++;
			$errMessage.=_("La catégorie de destination est invalide.")."<br />";
		}
	}
	if(!$errors){
		$tmp=copyItem($idItem,$copyTo,$ordering,NULL,true,$link);
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	}
	if(!$errors){
		$message.=_("Le élément a été copié correctement.")."<br />";
		$idCat = $copyTo;
	}
	$action="edit";
}
?>