<?php
if($action=="delete"){
	$idBrand=$_POST["idBrand"];
	$request = request("SELECT image_file_small,image_file_large from $TBL_catalogue_brands where id='$idBrand'",$link);
	if(mysql_num_rows($request)){
		$files = mysql_fetch_object($request);
	} else {
		$errors++;
		$errMessage.=_("La marque spécifiée est invalide")."<br />";
	}
	if(!$errors){
		if($files->image_file_small!="imgbrand_small_no_image.jpg")
		if(!unlink("../".$CATALOG_folder_images.$files->image_file_small)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression de la petite image de la marque.")."<br />";
		}
	}
	if(!$errors){
		if($files->image_file_large!="imgbrand_large_no_image.jpg")
		if(!unlink("../".$CATALOG_folder_images.$files->image_file_large)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression de la grande image de la marque.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set `brand`='0' WHERE brand='$idBrand'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de l'effacement de la marque dans les produits.")."<br />";
		}
	}
	if(!$errors){
		request("DELETE FROM $TBL_catalogue_brands WHERE id='$idBrand'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression de la marque.");
		}
	}
	if(!$errors){
		$message.=_("La marque a été supprimée correctement")."<br />";
	}
}
?>