<?php
if($action=="moveup"){
	if(isset($_POST["idPage"])){
		$idPage=$_POST["idPage"];
	} elseif(isset($_GET["idPage"])){
		$idPage=$_GET["idPage"];
	} else {
		$errors++;
		$errMessage.=$sr6;
	}
	if(!$errors){
		$request=request("SELECT id,ordering,id_category from $TBL_content_pages where id='$idPage'",$link);
		if(mysql_num_rows($request)){
			$page=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=$str7;
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_content_pages where id_category='".$page->id_category."' and ordering='".($page->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upPage=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La page specifiee est deja au plus haut niveau.")."<br>";//$str8;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_pages set ordering='".($page->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$page->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre de la page.")."<br>";//$str9;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_pages set ordering='".$page->ordering."' where id='".$upPage->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre de la page.")."<br>";//$str9;
		}
	}
	if(!$errors){
		$message.=_("L'ordre de la page a ete modifie avec succes.")."<br>";//$str10;
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idPage"])){
		$idPage=$_POST["idPage"];
	} elseif(isset($_GET["idPage"])){
		$idPage=$_GET["idPage"];
	} else {
		$errors++;
		$errMessage.=_("La page n'a pas ete specifiee.")."<br>";//$str6;
	}
	if(!$errors){
		$request=request("SELECT id,id_category,ordering from $TBL_content_pages where id='$idPage'",$link);
		if(mysql_num_rows($request)){
			$page=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La page specifiee est invalide.")."<br>";//$str7;
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_content_pages where id_category='".$page->id_category."' and ordering='".($page->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downPage=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La page specifiee est deja au plus bas niveau.")."<br>";//$str11;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_pages set ordering='".($page->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$page->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre de la page.")."<br>";//$str9;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_pages set ordering='".$page->ordering."' where id='".$downPage->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre de la page.")."<br>";//$str9;
		}
	}
	if(!$errors){
		$message.=_("L'ordre de la page a ete modifie avec succes.")."<br>";//$str10;
	}
	$action="edit";
}
?>
<?
if($action=="delete"){
	$idPage=$_POST["idPage"];
	$request = request("SELECT `id`,`deletable`,`ordering` FROM `$TBL_content_pages` where `id`='$idPage'",$link);
	if(mysql_num_rows($request)){
		$page = mysql_fetch_object($request);
	} else {
		$errors++;
		$errMessage.=_("La page specifiee est invalide")."<br />";//$str1;
	}
	if(!$errors && $page->deletable=="N"){
		$errors++;
		$errMessage.=_("Cette page ne peut être supprimée.")."<br />";//$str1;
	}
	if(!$errors){
		request("DELETE `$TBL_content_pages`,`$TBL_content_info_pages`
               FROM `$TBL_content_pages`,`$TBL_content_info_pages`
               WHERE `$TBL_content_pages`.`id`='$idPage'
               AND `$TBL_content_info_pages`.`id_page`=`$TBL_content_pages`.`id`",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression de la page.")."<br />";
		}
	}
	if(!$errors){ //Update the orderings
		request("UPDATE `$TBL_content_pages` SET `ordering`=`ordering`-1 WHERE `id_category`='".$page->id_category."' AND `ordering`>".$page->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des pages.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("La page dynamique a ete supprimee correctement");//$str3;
	}
}

if($action=="addArticleLink"){
	if(isset($_POST["idCategory"])){
		$idCategory=$_POST["idCategory"];
	} elseif(isset($_GET["idCategory"])){
		$idCategory=$_GET["idCategory"];
	} else $idCategory=0;
	if(isset($_POST["idArticle"])){
		$idItem=$_POST["idArticle"];
	} elseif(isset($_GET["idArticle"])){
		$idItem=$_GET["idArticle"];
	} else $idItem=0;
	if(!$idCategory){
		$errors++;
		$errMessage.=_("La catégorie n'a pas été spécifiée.")."<br />";
	}
	if(!$idItem){
		$errors++;
		$errMessage.=_("L'article n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		request("
			INSERT INTO `$TBL_content_pages` (`id_category`,`type`,`link_module`,`link_id_item`,`ordering`)
			VALUES ('$idCategory','link','articles','$idItem','1')
		",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de l'ajout de la page liée.")."<br />";
		} else {
			$linkId = mysql_insert_id($link);
		}
	}
	if(!$errors){
		$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
		while(!$errors && $language = mysql_fetch_object($request)){
			request("
				INSERT INTO `$TBL_content_info_pages` (`id_page`,`id_language`)
				VALUES ('$linkId','$language->id')
			",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'ajout des informations de la page liée.")."<br />";
			}
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_content_pages` SET `ordering`=`ordering`+1 WHERE `id_category`='$idCategory' AND `id`<>'$linkId'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des pages.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("La page liée a été ajoutée correctement");//$str3;
	}
}

if($action=="addNewsLink"){
	if(isset($_POST["idCategory"])){
		$idCategory=$_POST["idCategory"];
	} elseif(isset($_GET["idCategory"])){
		$idCategory=$_GET["idCategory"];
	} else $idCategory=0;
	if(isset($_POST["idNews"])){
		$idItem=$_POST["idNews"];
	} elseif(isset($_GET["idNews"])){
		$idItem=$_GET["idNews"];
	} else $idItem=0;
	if(!$idCategory){
		$errors++;
		$errMessage.=_("La catégorie n'a pas été spécifiée.")."<br />";
	}
	if(!$idItem){
		$errors++;
		$errMessage.=_("La nouvelle n'a pas été spécifiée.")."<br />";
	}
	if(!$errors){
		request("
			INSERT INTO `$TBL_content_pages` (`id_category`,`type`,`link_module`,`link_id_item`,`ordering`)
			VALUES ('$idCategory','link','news','$idItem','1')
		",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de l'ajout de la page liée.")."<br />";
		} else {
			$linkId = mysql_insert_id($link);
		}
	}
	if(!$errors){
		$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
		while(!$errors && $language = mysql_fetch_object($request)){
			request("
				INSERT INTO `$TBL_content_info_pages` (`id_page`,`id_language`)
				VALUES ('$linkId','$language->id')
			",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'ajout des informations de la page liée.")."<br />";
			}
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_content_pages` SET `ordering`=`ordering`+1 WHERE `id_category`='$idCategory' AND `id`<>'$linkId'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des pages.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("La page liée a été ajoutée correctement");//$str3;
	}
}
?>