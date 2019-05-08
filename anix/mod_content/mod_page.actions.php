<?php
if($action=="insert"){
	if($idCategory==-1 || !isset($pageCategories[$idCategory])){
		$errMessage.=_("La categorie specifiee n'existe pas.")."<br>";//$str15;
		$errors++;
	}
	if(!$errors){
		//Insert the category in the database
		$request = request("SELECT MAX(ordering) as maximum from `$TBL_content_pages` WHERE id_category='$idCategory' GROUP BY id_category",$link);
		$tmp = mysql_fetch_object($request);
		$ordering = 1;
		if(mysql_num_rows($request)) $ordering = ($tmp->maximum+1);
		$requestString="INSERT INTO $TBL_content_pages (`id_category`,`ordering`,`id_menu`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$idCategory','$ordering','".htmlentities($_POST["id_menu"],ENT_QUOTES,"UTF-8")."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
		//echo $requestString;
		request($requestString,$link);
		//echo mysql_error();
		//If insertion was OK, we rtrieve the id of the inserted category, else error...
		if(!mysql_errno($link)) {
			$idPage=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion de la categorie de produits.")."<br>";//$str1;
			$errors++;
		}
	}
	if(!$errors){
		$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
		while($language = mysql_fetch_object($request)){
			request("INSERT INTO $TBL_content_info_pages (`id_page`,`id_language`,`title`,`short_desc`,`content`,`keywords`,`htmltitle`,`htmldescription`)
                VALUES ('$idPage','".$language->id."','".htmlentities($_POST["title_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["shortdesc_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["content_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["keywords_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["htmltitle_".$language->id],ENT_QUOTES,"UTF-8")."','".htmlentities($_POST["htmldescription_".$language->id],ENT_QUOTES,"UTF-8")."')",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'insertions des informations de la page.")."<br>";//$str2;
			}
		}
	}
	if(!$errors){
		$message = _("La page a ete inseree correctement.")."<br>";//$str3;
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	//Update the category
	if(!$errors){
		//get the last values
		$request=request("SELECT * FROM `$TBL_content_pages` WHERE `id`='$idPage'",$link);
		$oldPage = mysql_fetch_object($request);
		request("UPDATE `$TBL_content_pages` SET `id_menu`='".htmlentities($_POST["id_menu"],ENT_QUOTES,"UTF-8")."' WHERE `id`='$idPage'",$link);
		$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
		while($language = mysql_fetch_object($request)){
			request("UPDATE $TBL_content_info_pages
                SET `title`='".htmlentities($_POST["title_".$language->id],ENT_QUOTES,"UTF-8")."',
                `content`='".htmlentities($_POST["content_".$language->id],ENT_QUOTES,"UTF-8")."',
                `short_desc`='".htmlentities($_POST["shortdesc_".$language->id],ENT_QUOTES,"UTF-8")."',
                `keywords`='".htmlentities($_POST["keywords_".$language->id],ENT_QUOTES,"UTF-8")."',
                `htmltitle`='".htmlentities($_POST["htmltitle_".$language->id],ENT_QUOTES,"UTF-8")."',
                `htmldescription`='".htmlentities($_POST["htmldescription_".$language->id],ENT_QUOTES,"UTF-8")."'
                WHERE id_page='$idPage'
                AND id_language='".$language->id."'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la mise a jour des informations de la page.")."<br>";//$str4;
			}
			if($_POST["id_menu"]==$oldPage->id_menu && isset($_POST["url_".$language->id]))
			request("UPDATE `$TBL_content_info_menuitems`
	        		SET `link`='".$_POST["url_".$language->id]."'
	        		WHERE `id_menuitem`='".$_POST["id_menu"]."'
	        		AND `id_language`='".$language->id."'",$link);
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_pages
               SET `modified_on`='".getDBDate()."',modified_by='$anix_username'
               WHERE `id`='$idPage'",$link);
	}
	if(!$errors){
		$message = _("La page a ete mise a jour correctement.")."<br>";//$str5;
		$action="edit";
	}
}
?>