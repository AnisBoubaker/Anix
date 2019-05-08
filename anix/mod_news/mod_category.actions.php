<?
if($action=="insert"){
	$ordering=getMaxCategoryOrder($idCat,$link);
$requestString="INSERT INTO `$TBL_news_categories` (`ordering`, `id_parent`,`contain_items`,`id_menu`,`newsimg_icon_width`,`newsimg_icon_height`,`newsimg_small_width`,`newsimg_small_height`,`newsimg_large_width`,`newsimg_large_height`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$ordering','$idCat','".(isset($_POST["contain_items"])?"Y":"N")."','".$_POST["id_menu"]."','".$_POST["newsimg_icon_width"]."','".$_POST["newsimg_icon_height"]."','".$_POST["newsimg_small_width"]."','".$_POST["newsimg_small_height"]."','".$_POST["newsimg_large_width"]."','".$_POST["newsimg_large_height"]."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
	request($requestString,$link);
	if(!mysql_errno($link)) {
		$idCat=mysql_insert_id($link);
	} else {
		$ANIX_messages->addError(_("Une erreur s'est produite lors de l'insertion de la catégorie de nouvelles."));
	}
	if(!$ANIX_messages->nbErrors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$ANIX_messages->nbErrors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_news_info_categories` (`id_news_cat`,`id_language`,`name`,`description`,`htmltitle`,`htmldescription`,`keywords`) values (";
			$requestString.="'$idCat',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=")";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$ANIX_messages->addError(_("Une erreur s'est produite lors de l'insertion des informations de la catégorie."));
			}
		}
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La catégorie a été insérée correctement"));
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
	while (!$ANIX_messages->nbErrors && $row_languages=mysql_fetch_object($languages)){
		$requestString="UPDATE `$TBL_news_info_categories` set ";
		$requestString.="`name`='".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`description`='".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
		$requestString.=" WHERE `id_news_cat`='$idCat' and id_language='".$row_languages->id."'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$ANIX_messages->addError(_("Une erreur s'est produite lors de la mise à jour de la catégorie."));
		}
	}
	if(!$ANIX_messages->nbErrors){
		$requestString="";
		$requestString.="UPDATE $TBL_news_categories SET ";
		$requestString.="`contain_items`='".(isset($_POST["contain_items"])?"Y":"N")."',";
		$requestString.="`id_menu`='".$_POST["id_menu"]."',";
		$requestString .="`newsimg_icon_width`='".$_POST["newsimg_icon_width"]."',";
		$requestString .="`newsimg_icon_height`='".$_POST["newsimg_icon_height"]."',";
		$requestString .="`newsimg_small_width`='".$_POST["newsimg_small_width"]."',";
		$requestString .="`newsimg_small_height`='".$_POST["newsimg_small_height"]."',";
		$requestString .="`newsimg_large_width`='".$_POST["newsimg_large_width"]."',";
		$requestString .="`newsimg_large_height`='".$_POST["newsimg_large_height"]."',";
		$requestString.="modified_on='".getDBDate()."',";
		$requestString.="modified_by='$anix_username' ";
		$requestString.="WHERE id='$idCat'";
		request($requestString,$link);
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La catégorie a été mise à jour correctement"));
		$action="edit";
	}
}
?>