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
  	  $request=request("SELECT * from $TBL_articles_categories where id='$idCat'",$link);
  	  if(mysql_num_rows($request)){
  	    $category=mysql_fetch_object($request);
  	  } else {
  	    $errors++;
  		$errMessage.=_("La catégorie spécifiée est invalide.")."<br />";
  	  }
  	}
  	if(!$errors){
  	  $request=request("SELECT * from $TBL_articles_categories where id_parent='".$category->id_parent."' and ordering='".($category->ordering-1)."'",$link);
  	  if(mysql_num_rows($request)){
  	    $upCategory=mysql_fetch_object($request);
  	  } else {
  	    $errors++;
  		$errMessage.=_("La catégorie est déjà au plus haut niveau.");
  	  }
  	}
  	if(!$errors){
  	  request("UPDATE $TBL_articles_categories set ordering='".($category->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$category->id."'",$link);
  	  if(mysql_errno()){
  	    $errors++;
  		$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie.")."<br />";
  	  }
  	}
  	if(!$errors){
  	  request("UPDATE $TBL_articles_categories set ordering='".$category->ordering."' where id='".$upCategory->id."'",$link);
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
  	  $errMessage.=_("La catégorie n'a pas été spécifiée")."<br />";
  	}
  	if(!$errors){
  	  $request=request("SELECT * from $TBL_articles_categories where id='$idCat'",$link);
  	  if(mysql_num_rows($request)){
  	    $category=mysql_fetch_object($request);
  	  } else {
  	    $errors++;
  		$errMessage.=_("La catégorie spécifiée est invalide.")."<br />";
  	  }
  	}
  	if(!$errors){
  	  $request=request("SELECT * from $TBL_articles_categories where id_parent='".$category->id_parent."' and ordering='".($category->ordering+1)."'",$link);
  	  if(mysql_num_rows($request)){
  	    $downCategory=mysql_fetch_object($request);
  	  } else {
  	    $errors++;
  		$errMessage.=_("La catégorie spécifiée est déjà au plus bas niveau.")."<br />";
  	  }
  	}
  	if(!$errors){
  	  request("UPDATE $TBL_articles_categories set ordering='".($category->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$category->id."'",$link);
  	  if(mysql_errno()){
  	    $errors++;
  		$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la catégorie.")."<br />";
  	  }
  	}
  	if(!$errors){
  	  request("UPDATE $TBL_articles_categories set ordering='".$category->ordering."' where id='".$downCategory->id."'",$link);
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
  	  $errMessage.=_("La catégorie n'a pas été spécifiée")."<br />";
  	}
  	$catOrdering = 0;$catParent=0;
  	if(!$errors){
  	  $request=request("SELECT deletable,ordering,id_parent from $TBL_articles_categories where id=$idCat",$link);
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
      if(!$errors && isset($_POST["method"]) && $_POST["method"]=="delete"){
  	  $categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering, $TBL_articles_info_categories.name, $TBL_articles_info_categories.description from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
  	  $tableCategories = getCatTable($categories);
  	  $tmp=deleteCategory($idCat,$tableCategories,$idCat,$link );
  	  $errors+=$tmp["errors"];
  	  $errMessage.=$tmp["errMessage"];
  	} elseif(!$errors && isset($_POST["method"]) && $_POST["method"]=="move"){
  	  $moveto = $_POST["moveto"];
  	  if(!$moveto){
  	    $errors++;
  		$errMessage=_("Vous n'avez pas spécifié dans quelle catégorie vous souhaitez déplacer les articles et sous-catégories.")."<br />";
  	  }
  	  if(!$errors){
  	    //get the target category max ordering
  		$maxCatOrder = getMaxCategoryOrder($moveto,$link)-1;
  	    //update the subcategories ordering
  		request("UPDATE $TBL_articles_categories set ordering=ordering+$maxCatOrder where id_parent='$idCat'",$link);
  		// Move the subcategories to the target
  	    request("UPDATE $TBL_articles_categories set id_parent='$moveto' where id_parent='$idCat'",$link);
  		if(mysql_errno($link)){
  		  $errors++;
  		  $errMessage.=_("Une erreur s'est produite lors du déplacement des sous-catégories")."<br />";
  		}
  	  }
  	  if(!$errors){
  	    //get the target category max article ordering
  		$maxArticleOrder= getMaxArticleOrder($moveto,$link)-1;
  	    //update the ordering
  	    request("UPDATE $TBL_articles_article set ordering=ordering+$maxArticleOrder where id_category=$idCat",$link);
  		//Move the article to the article category
  		request("UPDATE $TBL_articles_article set id_category=$moveto where id_category=$idCat",$link);
  		if(mysql_errno($link)){
  		  $errors++;
  		  $errMessage.=_("Une erreur s'est produite lors du déplacement des articles.")."<br />";
  		}
  	  }
  	  if(!$errors){
  	    //update orderings from where we are deleting
  		request("UPDATE $TBL_articles_categories set ordering=ordering-1 where id_parent=$catParent and ordering > $catOrdering",$link);
  		if(mysql_errno($link)){
  		  $errors++;
  		  $errMessage.=_("Une erreur s'est produite lors de la modification des ordres des catégories")."<br />";
  		}
  	  }
  	  if(!$errors){
  	    //Delete the category
  	    request("DELETE from $TBL_articles_info_categories where id_article_cat='$idCat'",$link);
  		if(mysql_errno($link)){
  		  $errors++;
  		  $errMessage.=_("Une erreur s'est produite lors de la suppression des informations de la catégorie.")."<br />";
  		}
  	  }
  	  if(!$errors){
  	    //Delete the category
  	    request("DELETE from $TBL_articles_categories where id=$idCat",$link);
  		if(mysql_errno($link)){
  		  $errors++;
  		  $errMessage.=_("Une erreur s'est produite lors de la suppression de la catégorie.")."<br />";
  		}
  	  }
  	} elseif(!$errors) {
  	  $errors++;
  	  $errMessage.=_("Aucune méthode de suppression valide n'a été spécifiée. La catégorie n'a pas été supprimée.")."<br />";
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
  	  $errMessage.=_("La catégorie n'a pas été spécifiée")."<br />";
  	}
  	if(isset($_GET["copyto"])){
  	  $copyTo=$_GET["copyto"];
  	} else {
  	  $errors++;
  	  $errMessage.=_("La catégorie de destination n'a pas été spécifiée")."<br />";
  	}
  	if(!$errors){
      $categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering, $TBL_articles_info_categories.name, $TBL_articles_info_categories.description from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
      if(mysql_errno($link)){
        $errors++;
        $errMessage.=_("Une erreur s'est produite lors de la récupération de la liste des catégories.")."<br />";
      } else {
        $tableCategories = getCatTable($categories);
      }
    }
    if(!$errors){
      $tmp=copyCategory($idCat,$copyTo,$tableCategories,$idCat,$link);
      $errors+=$tmp["errors"];
  	  $errMessage.=$tmp["errMessage"];
    }
    if(!$errors){
      $message.=_("La catégorie a été copiée correctement.")."<br />";
    }
    $action="edit";
  }
?>