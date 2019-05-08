<?php
if($action=="moveup"){
	if(isset($_POST["idProduct"])){
		$idProduct=$_POST["idProduct"];
	} elseif(isset($_GET["idProduct"])){
		$idProduct=$_GET["idProduct"];
	} else {
		$errors++;
		$errMessage.=_("Le produit n'a pas été spéifié")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_products where id='$idProduct'",$link);
		if(mysql_num_rows($request)){
			$product=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le produit spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_products where id_category='".$product->id_category."' and ordering='".($product->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upProduct=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le produit spécifié est dété au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set ordering='".($product->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$product->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre du produit.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set ordering='".$product->ordering."' where id='".$upProduct->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre du produit.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre du produit a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idProduct"])){
		$idProduct=$_POST["idProduct"];
	} elseif(isset($_GET["idProduct"])){
		$idProduct=$_GET["idProduct"];
	} else {
		$errors++;
		$errMessage.=_("Le produit n'a pas été spéifié")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_products where id='$idProduct'",$link);
		if(mysql_num_rows($request)){
			$product=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le produit spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_products where id_category='".$product->id_category."' and ordering='".($product->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$upProduct=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le produit spécifié est dété au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set ordering='".($product->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$product->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre du produit.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set ordering='".$product->ordering."' where id='".$upProduct->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre du produit.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre du produit a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="deleteproduct"){
	if(isset($_POST["idProduct"])){
		$idProduct=$_POST["idProduct"];
	} else {
		$errors++;
		$errMessage.=_("Le produit n'a pas été spéifié")."<br />";
	}
	$productOrdering=0;$productCategory=0;
	if(!$errors){
		$request=request("SELECT ordering,id_category from $TBL_catalogue_products where id=$idProduct",$link);
		if(mysql_num_rows($request)){
			$tmp=mysql_fetch_object($request);
			$productOrdering=$tmp->ordering;
			$productCategory=$tmp->id_category;
		}else{
			$errors++;
			$errMessage.=_("Le produit spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$idCat = $productCategory;
		//Update the orderings inside the category
		request("UPDATE $TBL_catalogue_products set ordering=ordering-1 WHERE id_category='$productCategory' and ordering > $productOrdering",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des produits.")."<br />";
		}
	}
	if(!$errors){
		//Delete the images from file system
		$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_catalogue_products where id=$idProduct",$link);
		$editProduct=mysql_fetch_object($request);
		if($editProduct->image_file_orig!=""){
			if(!unlink("../".$CATALOG_folder_images.$editProduct->image_file_orig)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image du produit."));
			}
		}
		if($editProduct->image_file_small!="imgprd_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editProduct->image_file_small)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image du produit."));
			}
		}
		if($editProduct->image_file_large!="imgprd_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editProduct->image_file_large)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la grande image du produit."));
			}
		}
		if($editProduct->image_file_icon!="imgprd_icon_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editProduct->image_file_icon)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'icone du produit."));
			}
		}
	}
	if(!$errors){
		//Delete the attachments from file system
		$request=request("SELECT file_name from $TBL_catalogue_attachments where id_product='$idProduct'",$link);
		while($attachments=mysql_fetch_object($request)){
			if($attachments->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$attachments->file_name)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression des fichiers attaché au produit."));
				}
			}
		}
		request("DELETE FROM $TBL_catalogue_attachments where id_product='$idProduct'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression des fichiers attaché de la base de données.")."<br />";
		}
	}
	//Delete the links
	if(!$errors){
		try{
			Link::deleteAllLinks(1,$idProduct);
		} catch (Exception $e){
			$ANIX_messages->addError($e->getMessage());
		}
	}
	//Update the featured that links to the product
	if(!$errors){
		request("UPDATE `$TBL_catalogue_featured`
             SET `id_catalogue_prd`=0,`active`='N'
             WHERE `id_catalogue_prd`='$idProduct'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour des vedettes.")."<br />";
		}
	}
	if(!$errors){
		request("DELETE FROM $TBL_catalogue_product_prices
             WHERE $TBL_catalogue_product_prices.id_product='$idProduct'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression des prix du produit.")."<br />";
		}
	}
	//Delete the product options
	if(!$errors){
		request("DELETE $TBL_catalogue_product_option_choices,$TBL_catalogue_info_choices
             FROM $TBL_catalogue_product_option_choices,
                  $TBL_catalogue_info_choices,
                  $TBL_catalogue_product_options
             WHERE $TBL_catalogue_product_options.id_product='$idProduct'
             AND $TBL_catalogue_product_option_choices.id_option=$TBL_catalogue_product_options.id
             AND $TBL_catalogue_info_choices.id_choice=$TBL_catalogue_product_option_choices.id",$link);
		if(mysql_errno($link)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression des choix des options.")."<br />";
		}
		request("DELETE $TBL_catalogue_product_options,$TBL_catalogue_info_options
             FROM $TBL_catalogue_product_options,
                  $TBL_catalogue_info_options
             WHERE $TBL_catalogue_product_options.id_product='$idProduct'
             AND $TBL_catalogue_info_options.id_option=$TBL_catalogue_product_options.id",$link);
		if(mysql_errno($link)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression des options de produits.")."<br />";
		}
	}
	//Delete extrafilds values for the product
	if(!$errors){
		request("DELETE FROM $TBL_catalogue_extrafields_values
             WHERE $TBL_catalogue_extrafields_values.id_product='$idProduct'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression des champs supplémentaires.")."<br />";
		}
	}
	if(!$errors){
		request("DELETE $TBL_catalogue_products,$TBL_catalogue_info_products
             FROM $TBL_catalogue_products,$TBL_catalogue_info_products
             WHERE $TBL_catalogue_products.id='$idProduct'
             AND $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression du produit.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("Le produit a été supprimé correctement.")."<br />";
	}
	$action="edit";
}
?>

<?
if($action=="copy"){
	if(isset($_POST["idProduct"])){
		$idProduct=$_POST["idProduct"];
	} elseif(isset($_GET["idProduct"])){
		$idProduct=$_GET["idProduct"];
	} else {
		$errors++;
		$errMessage.=_("Le produit n'a pas été spéifié")."<br />";
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
		$ordering=getMaxProductsOrder($copyTo,$link);
		if(!$ordering){
			$errors++;
			$errMessage.=_("La catégorie de destination est invalide.")."<br />";
		}
	}
	if(!$errors){
		$categoryTmp = request("SELECT reference_pattern from $TBL_catalogue_categories WHERE id='$copyTo'",$link);
		$reference=mysql_fetch_object($categoryTmp);
		$tmp=copyProduct($idProduct,$copyTo,$ordering,NULL,$reference->reference_pattern,$link);
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	}
	if(!$errors){
		$message.=_("Le produit a été copié correctement.")."<br />";
		$idCat = $copyTo;
	}
	$action="edit";
}
?>

<?
if($action=="batchCopy"){
	if(isset($_REQUEST["destination"])){
		$copyTo=$_REQUEST["destination"];
	} else {
		$ANIX_messages->addError(_("La destination de la copie n'a pas été spécifiée."));
	}
	if(!isset($_POST["checkedPrd"]) || !count($_POST["checkedPrd"])){
		$ANIX_messages->addError(_("Les produits à copier n'ont pas été spécifiés."));
	}
	if(!$ANIX_messages->nbErrors){
		$ordering=getMaxProductsOrder($copyTo,$link);
		if(!$ordering){
			$ANIX_messages->addError(_("La catégorie de destination est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$categoryTmp = request("SELECT reference_pattern from $TBL_catalogue_categories WHERE id='$copyTo'",$link);
		$reference=mysql_fetch_object($categoryTmp);
		foreach ($_POST["checkedPrd"] as $idProduct=>$tmp) if(!$ANIX_messages->nbErrors){
			$tmp=copyProduct($idProduct,$copyTo,$ordering,NULL,$reference->reference_pattern,$link);
			if($tmp["errors"]){
				$ANIX_messages->addError($tmp["errMessage"]);
			}
			$ordering++;
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("Les produits ont été déplacés correctement."));
		$idCat = $copyTo;
	}
	$action="edit";
}
?>

<?
if($action=="move"){
	if(isset($_POST["idProduct"])){
		$idProduct=$_POST["idProduct"];
	} elseif(isset($_GET["idProduct"])){
		$idProduct=$_GET["idProduct"];
	} else {
		$ANIX_messages->addError(_("Le produit n'a pas été spéifié"));
	}
	if(isset($_REQUEST["moveto"])){
		$moveTo=$_REQUEST["moveto"];
	} else {
		$ANIX_messages->addError(_("La destination du déplacement n'a pas été spécifiée."));
	}
	if(!$ANIX_messages->nbErrors){
		$ordering=getMaxProductsOrder($moveTo,$link);
		if(!$ordering){
			$ANIX_messages->addError(_("La catégorie de destination est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$tmp=moveProduct($idProduct,$moveTo,$ordering,$link);
		if($tmp["errors"]){
			$ANIX_messages->addError($tmp["errMessage"]);
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("Le produit a été déplacé correctement."));
		$idCat = $moveTo;
	}
	$action="edit";
}
?>

<?
if($action=="batchMove"){
	if(isset($_REQUEST["destination"])){
		$moveTo=$_REQUEST["destination"];
	} else {
		$ANIX_messages->addError(_("La destination du déplacement n'a pas été spécifiée."));
	}
	if(!isset($_POST["checkedPrd"]) || !count($_POST["checkedPrd"])){
		$ANIX_messages->addError(_("Les produits à déplacer n'ont pas été spécifiés."));
	}
	if(!$ANIX_messages->nbErrors){
		$ordering=getMaxProductsOrder($moveTo,$link);
		if(!$ordering){
			$ANIX_messages->addError(_("La catégorie de destination est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		foreach ($_POST["checkedPrd"] as $idProduct=>$tmp) if(!$ANIX_messages->nbErrors){
			$tmp=moveProduct($idProduct,$moveTo,$ordering,$link);
			if($tmp["errors"]){
				$ANIX_messages->addError($tmp["errMessage"]);
			}
			$ordering++;
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("Les produits ont été déplacés correctement."));
		$idCat = $moveTo;
	}
	$action="edit";
}
?>
<?php
if($action=="moveCatUp"){
	if(isset($_REQUEST["move"])){
		$moveCat=$_REQUEST["move"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie n'a pas été spécifiée")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_categories where id='$moveCat'",$link);
		if(mysql_num_rows($request)){
			$category=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La catégorie spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_categories where id_parent='".$category->id_parent."' and ordering='".($category->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upCategory=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La catégorie spécifiée est dété au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set ordering='".($category->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$category->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set ordering='".$category->ordering."' where id='".$upCategory->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de la catégorie a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="moveCatDown"){
	if(isset($_REQUEST["move"])){
		$moveCat=$_REQUEST["move"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie n'a pas été spécifié")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_categories where id='$moveCat'",$link);
		if(mysql_num_rows($request)){
			$category=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La catégorie spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_categories where id_parent='".$category->id_parent."' and ordering='".($category->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downCategory=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La catégorie spécifiée est dété au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set ordering='".($category->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$category->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_categories set ordering='".$category->ordering."' where id='".$downCategory->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de la catégorie a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="deleteCat"){
	if(isset($_POST["idCat"])){
		$idCat=$_POST["idCat"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie n'a pas été spécifiée")."<br />";
	}
	$catOrdering = 0;$catParent=0;
	if(!$errors){
		$request=request("SELECT deletable,ordering,id_parent from $TBL_catalogue_categories where id=$idCat",$link);
		if(mysql_num_rows($request)){
			$tmp=mysql_fetch_object($request);
			$catOrdering=$tmp->ordering;
			$catParent=$tmp->id_parent;
			if($tmp->deletable=="N"){
				$errors++;
				$errMessage.=_("Cette catégorie ne peut-être supprimée.")."<br />";
			}
		}else{
			$errors++;
			$errMessage.=_("La catégorie spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors && (!isset($_POST["method"]) || ($_POST["method"]!="delete" && $_POST["method"]!="move"))) {
		$errors++;
		$errMessage.=_("Aucune méthode de suppression valide n'a été spécifié. La catégorie n'a pas été supprimée.")."<br />";
	}
	if(!$errors && $_POST["method"]=="delete"){
		$categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
		$tableCategories = getCatTable($categories);
		$tmp=deleteCategory($idCat,$tableCategories,$idCat,$link );
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	} elseif(!$errors && $_POST["method"]=="move"){
		$moveto = $_POST["moveto"];
		if(!$moveto){
			$errors++;
			$errMessage=_("Vous n'avez pas spécifié dans quelle catégorie vous souhaitez déplacer les produits et sous-catégories.")."<br />";
		}
		if(!$errors){
			//get the target category max ordering
			$maxCatOrder = getMaxCategoryOrder($moveto,$link)-1;
			//update the subcategories ordering
			request("UPDATE $TBL_catalogue_categories set ordering=ordering+$maxCatOrder where id_parent='$idCat'",$link);
			// Move the subcategories to the target
			request("UPDATE $TBL_catalogue_categories set id_parent='$moveto' where id_parent='$idCat'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors du déplacement des sous-catégories")."<br />";
			}
		}
		if(!$errors){
			//get the target category max products ordering
			$maxProductsOrder= getMaxProductsOrder($moveto,$link)-1;
			//update the ordering
			request("UPDATE $TBL_catalogue_products set ordering=ordering+$maxProductsOrder where id_category=$idCat",$link);
			//Move the product to the product category
			request("UPDATE $TBL_catalogue_products set id_category=$moveto where id_category=$idCat",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors du déplacement des produits.")."<br />";
			}
		}
		if(!$errors){
			//update orderings from where we are deleting
			request("UPDATE $TBL_catalogue_categories set ordering=ordering-1 where id_parent=$catParent and ordering > $catOrdering",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la modification des ordres des catégories")."<br />";
			}
		}
		if(!$errors){
			//Delete the images from file system
			$request=request("SELECT image_file_large,image_file_small from $TBL_catalogue_categories where id=$idCat",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_small!="$CATALOG_folder_images/imgcat_small_no_image.jpg"){
				if(!unlink("../".$editCategory->image_file_small)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de la catégorie.")."<br />";
				}
			}
			if($editCategory->image_file_large!="$CATALOG_folder_images/imgcat_large_no_image.jpg"){
				if(!unlink("../".$editCategory->image_file_large)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de la catégorie.")."<br />";
				}
			}
		}
		if(!$errors){
			//Delete the category extrasections
			request("DELETE
                  $TBL_catalogue_extracategorysection,
                  $TBL_catalogue_info_extracategorysection
                FROM
                  $TBL_catalogue_extracategorysection,
                  $TBL_catalogue_info_extracategorysection
                WHERE $TBL_catalogue_extracategorysection.id_cat='$idCat'
                AND $TBL_catalogue_info_extracategorysection.id_extrasection=$TBL_catalogue_extracategorysection.id",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression des sections additionnelles de la catégorie.")."<br />";
			}
		}
		if(!$errors){
			//Delete the category extrafields values from products
			request("DELETE
                  $TBL_catalogue_extrafields_values
                FROM
                  $TBL_catalogue_extrafields,
                  $TBL_catalogue_extrafields_values
                WHERE $TBL_catalogue_extrafields.id_cat='$idCat'
                AND $TBL_catalogue_extrafields_values.id_extrafield=$TBL_catalogue_extrafields.id",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression des valeurs des champs additionnels.")."<br />";
			}
			//Delete the category extrafields
			request("DELETE
                  $TBL_catalogue_extrafields,
                  $TBL_catalogue_info_extrafields
                FROM
                  $TBL_catalogue_extrafields,
                  $TBL_catalogue_info_extrafields
                WHERE $TBL_catalogue_extrafields.id_cat='$idCat'
                AND $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields.id",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression des champs additionnels de la catégorie.")."<br />";
			}
		}
		if(!$errors){
			//Delete the category information
			request("DELETE from $TBL_catalogue_info_categories where id_catalogue_cat='$idCat'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression des informations de la catégorie.")."<br />";
			}
		}
		if(!$errors){
			//Delete the category
			request("DELETE from $TBL_catalogue_categories where id=$idCat",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression de la catégorie.")."<br />";
			}
		}
	}
	if(!$errors){
		$message.=_("La catégorie a été supprimée correctement.")."<br />";
	}
	if(isset($_REQUEST["idCat"])) $idCat = $_REQUEST["idCat"];
	$action="edit";
}
?>
<?
if($action=="copyCategory"){
	if(isset($_REQUEST["idCopy"])){
		$idCat=$_REQUEST["idCopy"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie à copier n'a pas été spécifiée")."<br />";
	}
	if(isset($_REQUEST["copyto"])){
		$copyTo=$_REQUEST["copyto"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie de destination n'a pas été spécifié")."<br />";
	}
	if(!$errors){
		$categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering, $TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la récupération de la liste des catégories.")."<br />";
		} else {
			$tableCategories = getCatTable($categories);
		}
	}
	if(!$errors){
		$tmp=copyCategory($idCat,$copyTo,$tableCategories,$idCat,NULL,$link);
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	}
	if(!$errors){
		$message.=_("La catégorie a été copiée correctement.")."<br />";
		$idCat = $copyTo;
	} else {
		if(isset($_REQUEST["idCat"])) $idCat = $_REQUEST["idCat"];
	}
	$action="edit";
}
?>
<?
if($action=="moveCategory"){
	if(isset($_REQUEST["idMove"])){
		$idCat=$_REQUEST["idMove"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie à déplacer n'a pas été spécifiée")."<br />";
	}
	if(isset($_GET["moveto"])){
		$moveTo=$_GET["moveto"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie de destination n'a pas été spécifié")."<br />";
	}
	if(!$errors){
		$categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering, $TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la récupération de la liste des catégories.")."<br />";
		} else {
			$tableCategories = getCatTable($categories);
		}
	}
	if(!$errors){
		$tmp=moveCategory($idCat,$moveTo,$tableCategories,$link);
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
		$message.=$tmp["message"];
	}
	if(!$errors){
	} elseif(isset($_REQUEST["idCat"])) {
		$idCat = $_REQUEST["idCat"];
	}
	$action="edit";
}
?>