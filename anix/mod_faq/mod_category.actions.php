<?
if($action=="insert"){
	$ordering=getMaxCategoryOrder($idCat,$link);
	$requestString="INSERT INTO `$TBL_faq_categories` (`ordering`, `id_parent`,`contain_items`,`id_menu`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$ordering','$idCat','".(isset($_POST["contain_items"])?"Y":"N")."','".$_POST["id_menu"]."','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
	request($requestString,$link);
	if(!mysql_errno($link)) {
		$idCat=mysql_insert_id($link);
	} else {
		$errMessage.=_("Une erreur s'est produite lors de l'insertion de la catégorie de FAQ.")."<br />";
		$errors++;
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_faq_info_categories` (`id_faq_cat`,`id_language`,`name`,`description`,`keywords`,`htmltitle`,`htmldescription`) values (";
			$requestString.="'$idCat',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=")";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de l'insertion des informations de la catégorie.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		$message = _("La catégorie a été insérée correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
	while (!$errors && $row_languages=mysql_fetch_object($languages)){
		$requestString="UPDATE `$TBL_faq_info_categories` set ";
		$requestString.="`name`='".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`description`='".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
		$requestString.=" WHERE `id_faq_cat`='$idCat' and id_language='".$row_languages->id."'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de la catégorie de FAQ.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_faq_categories SET
					`contain_items`='".(isset($_POST["contain_items"])?"Y":"N")."',
					`id_menu`='".$_POST["id_menu"]."',
					`modified_on`='".getDBDate()."',
					`modified_by`='$anix_username'
				WHERE id='$idCat'",$link);
	}
	if(!$errors){
		$message = _("La catégorie a été mise à jour correctement")."<br />";
		$action="edit";
	}
}
?>