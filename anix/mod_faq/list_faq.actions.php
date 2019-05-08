<?
if($action=="moveup"){
	if(isset($_POST["idFaq"])){
		$idFaq=$_POST["idFaq"];
	} elseif(isset($_GET["idFaq"])){
		$idFaq=$_GET["idFaq"];
	} else {
		$errors++;
		$errMessage.=_("La question n'a pas été spécifiée")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_faq_faq where id='$idFaq'",$link);
		if(mysql_num_rows($request)){
			$faq=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La question spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_faq_faq where id_category='".$faq->id_category."' and ordering='".($faq->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upFaq=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La question est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_faq_faq set ordering='".($faq->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$faq->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la question.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_faq_faq set ordering='".$faq->ordering."' where id='".$upFaq->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la question.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de la question a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idFaq"])){
		$idFaq=$_POST["idFaq"];
	} elseif(isset($_GET["idFaq"])){
		$idFaq=$_GET["idFaq"];
	} else {
		$errors++;
		$errMessage.=_("La question n'a pas été spécifiée")."<br />";
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_faq_faq where id='$idFaq'",$link);
		if(mysql_num_rows($request)){
			$faq=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La question spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT * from $TBL_faq_faq where id_category='".$faq->id_category."' and ordering='".($faq->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$upFaq=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("La question spécifiée est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_faq_faq set ordering='".($faq->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$faq->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la question.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_faq_faq set ordering='".$faq->ordering."' where id='".$upFaq->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre de la question.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'ordre de la question a été modifié avec succès.")."<br />";
	}
	$action="edit";
}
?>

<?
if($action=="deletefaq"){
	if(isset($_POST["idFaq"])){
		$idFaq=$_POST["idFaq"];
	} else {
		$errors++;
		$errMessage.=_("La question n'a pas été spécifiée")."<br />";
	}
	$faqOrdering=0;$faqCategory=0;
	if(!$errors){
		$request=request("SELECT ordering,id_category from $TBL_faq_faq where id=$idFaq",$link);
		if(mysql_num_rows($request)){
			$tmp=mysql_fetch_object($request);
			$faqOrdering=$tmp->ordering;
			$faqCategory=$tmp->id_category;
		}else{
			$errors++;
			$errMessage.=_("La question spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$idCat = $faqCategory;
		//Update the orderings inside the category
		request("UPDATE $TBL_faq_faq set ordering=ordering-1 WHERE id_category='$faqCategory' and ordering > $faqOrdering",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des question.")."<br />";
		}
	}
	if(!$errors){
		request("DELETE $TBL_faq_faq,$TBL_faq_info_faq
             FROM $TBL_faq_faq,$TBL_faq_info_faq
             WHERE $TBL_faq_faq.id='$idFaq'
             AND $TBL_faq_info_faq.id_faq=$TBL_faq_faq.id",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression de la question.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("La question a été supprimée correctement.")."<br />";
	}
	$action="edit";
}
?>

<?
if($action=="copy"){
	if(isset($_POST["idFaq"])){
		$idFaq=$_POST["idFaq"];
	} elseif(isset($_GET["idFaq"])){
		$idFaq=$_GET["idFaq"];
	} else {
		$errors++;
		$errMessage.=_("La question n'a pas été spécifiée")."<br />";
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
		$ordering=getMaxFaqOrder($copyTo,$link);
		if(!$ordering){
			$errors++;
			$errMessage.=_("La catégorie de destination est invalide.")."<br />";
		}
	}
	if(!$errors){
		$tmp=copyFaq($idFaq,$copyTo,$ordering,$link);
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	}
	if(!$errors){
		$message.=_("La question a été copiée correctement.")."<br />";
		$idCat = $copyTo;
	}
	$action="edit";
}
?>

<?php
if($action=="move"){
	if(isset($_POST["idFaq"])){
		$idFaq=$_POST["idFaq"];
	} elseif(isset($_GET["idFaq"])){
		$idFaq=$_GET["idFaq"];
	} else {
		$errors++;
		$errMessage.=_("La question n'a pas été spécifiée.")."<br />";
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
		$request=request("SELECT `id`,`id_category`,`ordering` FROM `$TBL_faq_faq` WHERE `id`='$idFaq'",$link);
		if(!mysql_num_rows($request)){
			$errors++;
			$errMessage.=_("La question spécifiée est invalide.")."<br />";
		}else {
			$oldFaq = mysql_fetch_object($request);
		}
	}
	if(!$errors){
		$ordering=getMaxFaqOrder($moveTo,$link);
		if(!$ordering){
			$errors++;
			$errMessage.=_("La catégorie de destination est invalide.")."<br />";
		}
	}
	if(!$errors){
		//move the faq
		request("UPDATE `$TBL_faq_faq` SET `id_category`='$moveTo', `ordering`='$ordering' WHERE `id`='$idFaq'",$link);
		//ro-order the old category
		request("UPDATE `$TBL_faq_faq` SET `ordering`=`ordering`-1 WHERE `id_category`='$oldFaq->id_category' AND `ordering`>'$oldFaq->ordering'",$link);
	}
	if(!$errors){
		$message.=_("La question a été déplacée correctement.")."<br />";
		$idCat = $moveTo;
	}
	$action="edit";
}
?>