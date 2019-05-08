<?php
if($action=="moveup"){
	if(isset($_POST["idFeatured"])){
		$idFeatured=$_POST["idFeatured"];
	} elseif(isset($_GET["idFeatured"])){
		$idFeatured=$_GET["idFeatured"];
	} else {
		$errors++;
		$errMessage.=_("La vedette n'a pas été spéifiée.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_featured where id='$idFeatured'",$link);
		if(mysql_num_rows($request)){
			$featured=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La vedette spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_featured where id_category='".$featured->id_category."' and ordering='".($featured->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upFeatured=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La vedette spécifiée est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_featured set ordering='".($featured->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$featured->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la vedette.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_featured set ordering='".$featured->ordering."' where id='".$upFeatured->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la vedette.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de la vedette a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idFeatured"])){
		$idFeatured=$_POST["idFeatured"];
	} elseif(isset($_GET["idFeatured"])){
		$idFeatured=$_GET["idFeatured"];
	} else {
		$errors++;
		$errMessage.=_("La vedette n'a pas été spéifiée.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_featured where id='$idFeatured'",$link);
		if(mysql_num_rows($request)){
			$featured=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La vedette spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_featured where id_category='".$featured->id_category."' and ordering='".($featured->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$upFeatured=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La vedette spécifiée est dété au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_featured set ordering='".($featured->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$featured->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la vedette.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_featured set ordering='".$featured->ordering."' where id='".$upFeatured->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la vedette.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de la vedette a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="delete"){
	$idFeatured=$_POST["idFeatured"];
	$request = request("SELECT id_category,ordering,image_file_small,image_file_large from $TBL_catalogue_featured where id='$idFeatured'",$link);
	if(mysql_num_rows($request)){
		$files = mysql_fetch_object($request);
	} else {
		$errors++;
		$errMessage.=_("La vedette spécifiée est invalide.")."<br />";
	}
	if(!$errors){
		//Update the orderings inside the category
		request("UPDATE $TBL_catalogue_featured set ordering=ordering-1 WHERE id_category='".$files->id_category."' and ordering > ".$files->ordering,$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des vedettes.")."<br />";
		}
	}
	if(!$errors){
		if($files->image_file_small!="imgfeatured_small_no_image.jpg")
		if(!unlink("../".$CATALOG_folder_images.$files->image_file_small)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression de petite image de la vedette.")."<br />";
		}
	}
	if(!$errors){
		if($files->image_file_large!="imgfeatured_large_no_image.jpg")
		if(!unlink("../".$CATALOG_folder_images.$files->image_file_large)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression de grande image de la vedette.")."<br />";
		}
	}
	if(!$errors){
		request("DELETE $TBL_catalogue_featured,$TBL_catalogue_info_featured
               FROM $TBL_catalogue_featured,$TBL_catalogue_info_featured
               WHERE $TBL_catalogue_featured.id='$idFeatured'
               AND $TBL_catalogue_info_featured.id_featured=$TBL_catalogue_featured.id",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression de la vedette.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("La vedette a été supprimée correctement.")."<br />";
	}
}
?>