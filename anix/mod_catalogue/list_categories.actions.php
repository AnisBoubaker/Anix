<?php
if($action=="moveup"){
	if(isset($_POST["idCat"])){
		$idCat=$_POST["idCat"];
	} elseif(isset($_GET["idCat"])){
		$idCat=$_GET["idCat"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie n'a pas été spécifiée")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_categories where id='$idCat'",$link);
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
if($action=="movedown"){
	if(isset($_POST["idCat"])){
		$idCat=$_POST["idCat"];
	} elseif(isset($_GET["idCat"])){
		$idCat=$_GET["idCat"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie n'a pas été spécifié")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_catalogue_categories where id='$idCat'",$link);
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
if($action=="deletecat"){
	if(isset($_POST["idCat"])){
		$idCat=$_POST["idCat"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie n'a pas été spécifié")."<br />";
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
	$action="edit";
}
?>
<?
if($action=="copy"){
	if(isset($_GET["idCat"])){
		$idCat=$_GET["idCat"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie à copier n'a pas été spécifiée")."<br />";
	}
	if(isset($_GET["copyto"])){
		$copyTo=$_GET["copyto"];
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
	}
	$action="edit";
}
?>
<?
if($action=="move"){
	if(isset($_GET["idCat"])){
		$idCat=$_GET["idCat"];
	} else {
		$errors++;
		$errMessage.=_("La catégorie à copier n'a pas été spécifiée")."<br />";
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
	$action="edit";
}
?>