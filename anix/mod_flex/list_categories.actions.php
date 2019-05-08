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
		$request=request("SELECT * from $TBL_lists_categories where id='$idCat'",$link);
		if(mysql_num_rows($request)){
			$category=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La catégorie spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_lists_categories where id_parent='".$category->id_parent."' and ordering='".($category->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upCategory=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La catégorie spécifiée est dété au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_categories set ordering='".($category->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$category->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_categories set ordering='".$category->ordering."' where id='".$upCategory->id."'",$link);
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
		$request=request("SELECT * from $TBL_lists_categories where id='$idCat'",$link);
		if(mysql_num_rows($request)){
			$category=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La catégorie spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_lists_categories where id_parent='".$category->id_parent."' and ordering='".($category->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downCategory=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La catégorie spécifiée est dété au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_categories set ordering='".($category->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$category->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_lists_categories set ordering='".$category->ordering."' where id='".$downCategory->id."'",$link);
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
		$request=request("SELECT deletable,ordering,id_parent from $TBL_lists_categories where id=$idCat",$link);
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
		$tableCategories = getCatTable();
		$tmp=deleteCategory($idCat,$tableCategories,$idCat,$link );
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	} elseif(!$errors && $_POST["method"]=="move"){
		$moveto = $_POST["moveto"];
		if(!$moveto){
			$errors++;
			$errMessage=_("Vous n'avez pas spécifié dans quelle catégorie vous souhaitez déplacer les éléments et sous-catégories.")."<br />";
		}
		if(!$errors){
			//get the target category max ordering
			$maxCatOrder = getMaxCategoryOrder($moveto,$link)-1;
			//update the subcategories ordering
			request("UPDATE $TBL_lists_categories set ordering=ordering+$maxCatOrder where id_parent='$idCat'",$link);
			// Move the subcategories to the target
			request("UPDATE $TBL_lists_categories set id_parent='$moveto' where id_parent='$idCat'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors du déplacement des sous-catégories")."<br />";
			}
		}
		if(!$errors){
			//get the target category max items ordering
			$maxItemsOrder= getMaxItemsOrder($moveto,$link)-1;
			//update the ordering
			request("UPDATE $TBL_lists_items set ordering=ordering+$maxItemsOrder where id_category=$idCat",$link);
			//Move the item to the item category
			request("UPDATE $TBL_lists_items set id_category=$moveto where id_category=$idCat",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors du déplacement des éléments.")."<br />";
			}
		}
		if(!$errors){
			//update orderings from where we are deleting
			request("UPDATE $TBL_lists_categories set ordering=ordering-1 where id_parent=$catParent and ordering > $catOrdering",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la modification des ordres des catégories")."<br />";
			}
		}
		if(!$errors){
			//Delete the images from file system
			$request=request("SELECT image_file_large,image_file_small from $TBL_lists_categories where id=$idCat",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_small!="$CATALOG_folder_images/imgcatflex_small_no_image.jpg"){
				if(!unlink("../".$editCategory->image_file_small)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne petite image de la catégorie.")."<br />";
				}
			}
			if($editCategory->image_file_large!="$CATALOG_folder_images/imgcatflex_large_no_image.jpg"){
				if(!unlink("../".$editCategory->image_file_large)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancienne grande image de la catégorie.")."<br />";
				}
			}
		}
		if(!$errors){
			//Delete the category extrasections
			request("DELETE
                  $TBL_lists_extracategorysection,
                  $TBL_lists_info_extracategorysection
                FROM
                  $TBL_lists_extracategorysection,
                  $TBL_lists_info_extracategorysection
                WHERE $TBL_lists_extracategorysection.id_cat='$idCat'
                AND $TBL_lists_info_extracategorysection.id_extrasection=$TBL_lists_extracategorysection.id",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression des sections additionnelles de la catégorie.")."<br />";
			}
		}
		if(!$errors){
			//Delete the category extrafields values from items
			request("DELETE
                  $TBL_lists_extrafields_values
                FROM
                  $TBL_lists_extrafields,
                  $TBL_lists_extrafields_values
                WHERE $TBL_lists_extrafields.id_cat='$idCat'
                AND $TBL_lists_extrafields_values.id_extrafield=$TBL_lists_extrafields.id",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression des valeurs des champs additionnels.")."<br />";
			}
			//Delete the category extrafields
			request("DELETE
                  $TBL_lists_extrafields,
                  $TBL_lists_info_extrafields
                FROM
                  $TBL_lists_extrafields,
                  $TBL_lists_info_extrafields
                WHERE $TBL_lists_extrafields.id_cat='$idCat'
                AND $TBL_lists_info_extrafields.id_extrafield=$TBL_lists_extrafields.id",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression des champs additionnels de la catégorie.")."<br />";
			}
		}
		if(!$errors){
			//Delete the category information
			request("DELETE from $TBL_lists_info_categories where id_lists_cat='$idCat'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la suppression des informations de la catégorie.")."<br />";
			}
		}
		if(!$errors){
			//Delete the category
			request("DELETE from $TBL_lists_categories where id=$idCat",$link);
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
		$tableCategories = getCatTable();
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
		$tableCategories = getCatTable();
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