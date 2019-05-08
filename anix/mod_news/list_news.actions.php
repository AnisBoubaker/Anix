<?
if($action=="moveup"){
	if(isset($_POST["idNews"])){
		$idNews=$_POST["idNews"];
	} elseif(isset($_GET["idNews"])){
		$idNews=$_GET["idNews"];
	} else {
		$ANIX_messages->addError(_("La nouvelle n'a pas été spécifiée."));
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_news_news where id='$idNews'",$link);
		if(mysql_num_rows($request)){
			$news=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_news_news where id_category='".$news->id_category."' and ordering='".($news->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upNews=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La nouvelle est déjà au plus haut niveau."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news set ordering='".($news->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$news->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news set ordering='".$news->ordering."' where id='".$upNews->id."'",$link);
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
	if(isset($_POST["idNews"])){
		$idNews=$_POST["idNews"];
	} elseif(isset($_GET["idNews"])){
		$idNews=$_GET["idNews"];
	} else {
		$ANIX_messages->addError(_("La nouvelle n'a pas été spécifiée."));
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_news_news where id='$idNews'",$link);
		if(mysql_num_rows($request)){
			$news=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT * from $TBL_news_news where id_category='".$news->id_category."' and ordering='".($news->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$upNews=mysql_fetch_object($request);
		} else {
			$ANIX_messages->addError(_("La nouvelle spécifiée est déjà au plus bas niveau."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news set ordering='".($news->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$news->id."'",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre de la nouvelle."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("UPDATE $TBL_news_news set ordering='".$news->ordering."' where id='".$upNews->id."'",$link);
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
if($action=="deletenews"){
	if(isset($_POST["idNews"])){
		$idNews=$_POST["idNews"];
	} else {
		$ANIX_messages->addError(_("La nouvelle n'a pas été spécifiée."));
	}
	$newsOrdering=0;$newsCategory=0;
	if(!$ANIX_messages->nbErrors){
		$request=request("SELECT ordering,id_category from $TBL_news_news where id=$idNews",$link);
		if(mysql_num_rows($request)){
			$tmp=mysql_fetch_object($request);
			$newsOrdering=$tmp->ordering;
			$newsCategory=$tmp->id_category;
			$idCat = $newsCategory;
		}else{
			$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//Update the orderings inside the category
		request("UPDATE $TBL_news_news set ordering=ordering-1 WHERE id_category='$newsCategory' and ordering > $newsOrdering",$link);
		if(mysql_errno()){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de l'ordre des nouvelles."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//Delete the images from file system
		$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_news_news where id=$idNews",$link);
		$editNews=mysql_fetch_object($request);
		if($editNews->image_file_orig!=""){
			if(!unlink("../".$CATALOG_folder_images.$editNews->image_file_orig)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image de l'élément."));
			}
		}
		if($editNews->image_file_small!="imgnews_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editNews->image_file_small)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la petite image de l'élément."));
			}
		}
		if($editNews->image_file_large!="imgnews_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editNews->image_file_large)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de la grande image de l'élément."));
			}
		}
		if($editNews->image_file_icon!="imgnews_icon_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editNews->image_file_icon)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'icone de l'élément."));
			}
		}
	}
	if(!$ANIX_messages->nbErrors){
		//Delete the attachments from file system
		$request=request("SELECT file_name from $TBL_news_attachments where id_news='$idNews'",$link);
		while($attachments=mysql_fetch_object($request)){
			if($attachments->file_name!=""){
				if(!unlink("../".$CATALOG_folder_attachments.$attachments->file_name)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression des fichiers attaché à la nouvelle."));
				}
			}
		}
		request("DELETE FROM $TBL_news_attachments where id_news='$idNews'",$link);
		if(mysql_errno($link)){
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la suppression des fichiers attachés de la base de données."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		request("DELETE $TBL_news_news,$TBL_news_info_news
               FROM $TBL_news_news,$TBL_news_info_news
               WHERE $TBL_news_news.id='$idNews'
               AND $TBL_news_info_news.id_news=$TBL_news_news.id",$link);
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
	if(isset($_POST["idNews"])){
		$idNews=$_POST["idNews"];
	} elseif(isset($_GET["idNews"])){
		$idNews=$_GET["idNews"];
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
		$ordering=getMaxNewsOrder($copyTo,$link);
		if(!$ordering){
			$ANIX_messages->addError(_("La catégorie de destination est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		copyNews($idNews,$copyTo,$ordering,$link);
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
	if(isset($_POST["idNews"])){
		$idNews=$_POST["idNews"];
	} elseif(isset($_GET["idNews"])){
		$idNews=$_GET["idNews"];
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
		$request=request("SELECT `id`,`id_category`,`ordering` FROM `$TBL_news_news` WHERE `id`='$idNews'",$link);
		if(!mysql_num_rows($request)){
			$ANIX_messages->addError(_("La nouvelle spécifiée est invalide."));
		}else {
			$oldNews = mysql_fetch_object($request);
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ordering=getMaxNewsOrder($moveTo,$link);
		if(!$ordering){
			$ANIX_messages->addError(_("La catégorie de destination est invalide."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		//move the news
		request("UPDATE `$TBL_news_news` SET `id_category`='$moveTo', `ordering`='$ordering' WHERE `id`='$idNews'",$link);
		//ro-order the old category
		request("UPDATE `$TBL_news_news` SET `ordering`=`ordering`-1 WHERE `id_category`='$oldNews->id_category' AND `ordering`>'$oldNews->ordering'",$link);
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La nouvelle a été déplacée correctement."));
		$idCat = $moveTo;
	}
	$action="edit";
}
?>