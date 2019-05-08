<?
if($action=="moveup"){
	if(isset($_POST["idCat"])){
		$idCat=$_POST["idCat"];
	} elseif(isset($_GET["idCat"])){
		$idCat=$_GET["idCat"];
	} else {
		$ANIX_messages->addError(_("La catégorie n'a pas été spécifiée"));
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_news_categories where id='$idCat'",$link);
		if(mysql_num_rows($request)){
			$category=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La catégorie spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_news_categories where id_parent='".$category->id_parent."' and ordering='".($category->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upCategory=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La catégorie est déjà au plus haut niveau."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_categories set ordering='".($category->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$category->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_categories set ordering='".$category->ordering."' where id='".$upCategory->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("L'ordre de la catégorie a été modifié avec succès."));
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
		$ANIX_messages->addError(_("La catégorie n'a pas été spécifiée"));
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_news_categories where id='$idCat'",$link);
		if(mysql_num_rows($request)){
			$category=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La catégorie spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_news_categories where id_parent='".$category->id_parent."' and ordering='".($category->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downCategory=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La catégorie spécifiée est déjà au plus bas niveau."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_categories set ordering='".($category->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$category->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_categories set ordering='".$category->ordering."' where id='".$downCategory->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("L'ordre de la catégorie a été modifié avec succès."));
	}
	$action="edit";
}
?>
<?
if($action=="deletecat"){
	if(isset($_POST["idCat"])){
		$idCat=$_POST["idCat"];
	} else {
		$ANIX_messages->addError(_("La catégorie n'a pas été spécifiée"));
	}
	$catOrdering = 0;$catParent=0;
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT deletable,ordering,id_parent from $TBL_news_categories where id=$idCat",$link);
		if(mysql_num_rows($request)){
			$tmp=mysql_fetch_object($request);
			$catOrdering=$tmp->ordering;
			$catParent=$tmp->id_parent;
			if($tmp->deletable=="N") $ANIX_messages->addError(_("Cette catégorie ne peut être supprimée."));
		}else{
			$ANIX_messages->addError(_("La catégorie spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors && isset($_POST["method"]) && $_POST["method"]=="delete"){
		$categories=request("select $TBL_news_categories.id, $TBL_news_categories.id_parent, $TBL_news_categories.ordering, $TBL_news_info_categories.name, $TBL_news_info_categories.description from  $TBL_news_categories,$TBL_gen_languages,$TBL_news_info_categories where $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id and $TBL_news_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_news_categories.id_parent, $TBL_news_categories.ordering", $link);
		$tableCategories = getCatTable($categories);
		$tmp=deleteCategory($idCat,$tableCategories,$idCat,$link );
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	} elseif(!$ANIX_messages->nbErrors && isset($_POST["method"]) && $_POST["method"]=="move"){
		$moveto = $_POST["moveto"];
		if(!$moveto){
			$ANIX_messages->addError(_("Vous n'avez pas spécifié dans quelle catégorie vous souhaitez déplacer les nouvelles et sous-catégories."));
		}
		if(!$ANIX_messages->nbErrors){
			//get the target category max ordering
			$maxCatOrder = getMaxCategoryOrder($moveto,$link)-1;
			//update the subcategories ordering
			request("UPDATE $TBL_news_categories set ordering=ordering+$maxCatOrder where id_parent='$idCat'",$link);
			// Move the subcategories to the target
			request("UPDATE $TBL_news_categories set id_parent='$moveto' where id_parent='$idCat'",$link);
			if(mysql_errno($link)){
				$ANIX_messages->addError(_("Une erreur s'est produite lors du déplacement des sous-catégories"));
			}
		}
		if(!$ANIX_messages->nbErrors){
			//get the target category max news ordering
			$maxNewsOrder= getMaxNewsOrder($moveto,$link)-1;
			//update the ordering
			request("UPDATE $TBL_news_news set ordering=ordering+$maxNewsOrder where id_category=$idCat",$link);
			//Move the news to the news category
			request("UPDATE $TBL_news_news set id_category=$moveto where id_category=$idCat",$link);
			if(mysql_errno($link)){
				$ANIX_messages->addError(_("Une erreur s'est produite lors du déplacement des nouvelles."));
			}
		}
		if(!$ANIX_messages->nbErrors){
			//update orderings from where we are deleting
			request("UPDATE $TBL_news_categories set ordering=ordering-1 where id_parent=$catParent and ordering > $catOrdering",$link);
			if(mysql_errno($link)){
				$ANIX_messages->addError(_("Une erreur s'est produite lors de la modification des ordres des catégories."));
			}
		}
		if(!$ANIX_messages->nbErrors){
			//Delete the category
			request("DELETE from $TBL_news_info_categories where id_news_cat='$idCat'",$link);
			if(mysql_errno($link)){
				$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des informations de la catégorie."));
			}
		}
		if(!$ANIX_messages->nbErrors){
			//Delete the category
			request("DELETE from $TBL_news_categories where id=$idCat",$link);
			if(mysql_errno($link)){
				$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression de la catégorie."));
			}
		}
	} elseif(!$ANIX_messages->nbErrors) {
		$ANIX_messages->addError(_("Aucune méthode de suppression valide n'a été spécifiée. La catégorie n'a pas été supprimée."));
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La catégorie a été supprimée correctement."));
	}
	$action="edit";
}
?>
<?
if($action=="copy"){
	if(isset($_GET["idCat"])){
		$idCat=$_GET["idCat"];
	} else {
		$ANIX_messages->addError(_("La catégorie n'a pas été spécifiée"));
	}
	if(isset($_GET["copyto"])){
		$copyTo=$_GET["copyto"];
	} else {
		$ANIX_messages->addError(_("La catégorie de destination n'a pas été spécifiée"));
	}
	if(!$ANIX_messages->nbErrors){
		$categories=request("select $TBL_news_categories.id, $TBL_news_categories.id_parent, $TBL_news_categories.ordering, $TBL_news_info_categories.name, $TBL_news_info_categories.description from  $TBL_news_categories,$TBL_gen_languages,$TBL_news_info_categories where $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id and $TBL_news_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_news_categories.id_parent, $TBL_news_categories.ordering", $link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la récupération de la liste des catégories."));
		} else {
			$tableCategories = getCatTable($categories);
		}
	}
	if(!$ANIX_messages->nbErrors){
		copyCategory($idCat,$copyTo,$tableCategories,$idCat,$link);
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La catégorie a été copiée correctement."));
	}
	$action="edit";
}
?>