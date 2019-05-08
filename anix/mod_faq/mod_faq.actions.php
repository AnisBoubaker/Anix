<?
if($action=="insert"){
	if(isset($_POST["idCat"])){
		$idCat=$_POST["idCat"];
	} elseif(isset($_GET["idCat"])){
		$idCat=$_GET["idCat"];
	} else $idCat="";
	$ordering=getMaxFaqOrder($idCat,$link);
	if(!$ordering){
		$errors++;
		$errMessage.=_("La catégorie spécifiée n'est pas valide")."<br />";
	}
	if(!$errors){
		$requestString ="INSERT INTO `$TBL_faq_faq` (`id_category`,`active`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values (";
		$requestString.="'$idCat',";
		$requestString.="'".$_POST["active"]."',";
		$requestString.="'$ordering',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username')";
		request($requestString,$link);
		if(!mysql_errno($link)) {
			$idFaq=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion de la question.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_faq_info_faq` (`id_faq`,`id_language`,`question`,`response`,`keywords`,`htmltitle`,`htmldescription`) values (";
			$requestString.="'$idFaq',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["question_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["response_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=")";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de l'insertion des informations de la question.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		$message = _("La question a été insérée correctement.")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="update"){

	$requestString ="UPDATE `$TBL_faq_faq` set ";
	$requestString.="`active`='".$_POST["active"]."',";
	$requestString.="modified_on='".getDBDate()."',modified_by='$anix_username' ";
	$requestString.="WHERE id='$idFaq'";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$errMessage.=_("Une erreur s'est produite lors de la mise à jour de la question.")."<br />";
		$errors++;
	}

	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="UPDATE `$TBL_faq_info_faq` set ";
			$requestString.="`question`='".htmlentities($_POST["question_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`response`='".htmlentities($_POST["response_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=" WHERE `id_faq`='$idFaq' and id_language='".$row_languages->id."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour des informations de la question.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		$message = _("La question a été mise à jour correctement.")."<br />";
		$action="edit";
	}
}
?>