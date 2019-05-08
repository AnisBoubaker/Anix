<?php
if($action=="moveup"){
	if(isset($_POST["idArticle"])){
		$idArticle=$_POST["idArticle"];
	} elseif(isset($_GET["idArticle"])){
		$idArticle=$_GET["idArticle"];
	} else {
		$errors++;
		$errMessage.=_("L'article n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_articles_article where id='$idArticle'",$link);
		if(mysql_num_rows($request)){
			$article=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'article spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_articles_article where id_category='".$article->id_category."' and ordering='".($article->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upArticle=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'article est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article set ordering='".($article->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$article->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de l'article.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article set ordering='".$article->ordering."' where id='".$upArticle->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de l'article.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de l'article a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idArticle"])){
		$idArticle=$_POST["idArticle"];
	} elseif(isset($_GET["idArticle"])){
		$idArticle=$_GET["idArticle"];
	} else {
		$errors++;
		$errMessage.=_("L'article n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_articles_article where id='$idArticle'",$link);
		if(mysql_num_rows($request)){
			$article=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'article spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_articles_article where id_category='".$article->id_category."' and ordering='".($article->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$upArticle=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'article spécifié est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article set ordering='".($article->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$article->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de l'article.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_articles_article set ordering='".$article->ordering."' where id='".$upArticle->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de l'article.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de l'article a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="deletearticle"){
	if(isset($_POST["idArticle"])){
		$idArticle=$_POST["idArticle"];
	} else {
		$errors++;
		$errMessage.=_("L'article n'a pas été spécifié.")."<br />";
	}
	$articleOrdering=0;$articleCategory=0;
	if(!$errors){
		$request=request("SELECT ordering,id_category from $TBL_articles_article where id=$idArticle",$link);
		if(mysql_num_rows($request)){
			$tmp=mysql_fetch_object($request);
			$articleOrdering=$tmp->ordering;
			$articleCategory=$tmp->id_category;
			$idCat = $articleCategory;
		}else{
			$errors++;
			$errMessage.=_("L'article spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		//Update the orderings inside the category
		request("UPDATE $TBL_articles_article set ordering=ordering-1 WHERE id_category='$articleCategory' and ordering > $articleOrdering",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des articles.")."<br />";
		}
	}
	if(!$errors){
		//Delete the attachments from file system
		$request=request("SELECT file_name from $TBL_articles_attachments where id_article='$idArticle'",$link);
		while($attachments=mysql_fetch_object($request)){
			if($attachments->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$attachments->file_name)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression des fichiers attaché à l'article.")."<br />";
				}
			}
		}
		request("DELETE FROM $TBL_articles_attachments where id_article='$idArticle'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression des fichiers attachés de la base de données.")."<br />";
		}
	}
	if(!$errors){
		request("DELETE $TBL_articles_article,$TBL_articles_info_article
               FROM $TBL_articles_article,$TBL_articles_info_article
               WHERE $TBL_articles_article.id='$idArticle'
               AND $TBL_articles_info_article.id_article=$TBL_articles_article.id",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression de l'article.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'article a été supprimé correctement.")."<br />";
	}
	$action="edit";
}
?>

<?
if($action=="copy"){
	if(isset($_POST["idArticle"])){
		$idArticle=$_POST["idArticle"];
	} elseif(isset($_GET["idArticle"])){
		$idArticle=$_GET["idArticle"];
	} else {
		$errors++;
		$errMessage.=_("L'article n'a pas été spécifié.")."<br />";
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
		$ordering=getMaxArticleOrder($copyTo,$link);
		if(!$ordering){
			$errors++;
			$errMessage.=_("La catégorie de destination est invalide.")."<br />";
		}
	}
	if(!$errors){
		$tmp=copyArticle($idArticle,$copyTo,$ordering,$link);
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	}
	if(!$errors){
		$message.=_("L'article a été copié correctement.")."<br />";
		$idCat = $copyTo;
	}
	$action="edit";
}
?>
<?php
if($action=="move"){
	if(isset($_POST["idArticle"])){
		$idArticle=$_POST["idArticle"];
	} elseif(isset($_GET["idArticle"])){
		$idArticle=$_GET["idArticle"];
	} else {
		$errors++;
		$errMessage.=_("L'article n'a pas été spécifié.")."<br />";
	}
	if(isset($_POST["moveto"])){
		$moveTo=$_POST["moveto"];
	} elseif(isset($_GET["moveto"])){
		$moveTo=$_GET["moveto"];
	} else {
		$errors++;
		$errMessage.=_("La destination n'a pas été spécifiée.")."<br />";
	}
	if(!$errors){
		//get the old faq information
		$request=request("SELECT `id`,`id_category`,`ordering` FROM `$TBL_articles_article` WHERE `id`='$idArticle'",$link);
		if(!mysql_num_rows($request)){
			$errors++;
			$errMessage.=_("L'article spécifié est invalide.")."<br />";
		}else {
			$oldArticle = mysql_fetch_object($request);
		}
	}
	if(!$errors){
		$ordering=getMaxArticleOrder($moveTo,$link);
		if(!$ordering){
			$errors++;
			$errMessage.=_("La catégorie de destination est invalide.")."<br />";
		}
	}
	if(!$errors){
		//move the faq
		request("UPDATE `$TBL_articles_article` SET `id_category`='$moveTo', `ordering`='$ordering' WHERE `id`='$idArticle'",$link);
		//ro-order the old category
		request("UPDATE `$TBL_articles_article` SET `ordering`=`ordering`-1 WHERE `id_category`='$oldArticle->id_category' AND `ordering`>'$oldArticle->ordering'",$link);
	}
	if(!$errors){
		$message.=_("L'article a été déplacé correctement.")."<br />";
		$idCat = $moveTo;
	}
	$action="edit";
}
?>