<?
if($action=="moveup"){
	if(isset($_POST["idMenuitem"])){
		$idMenuitem=$_POST["idMenuitem"];
	} elseif(isset($_GET["idMenuitem"])){
		$idMenuitem=$_GET["idMenuitem"];
	} else {
		$errors++;
		$errMessage.=_("Le composant n'a pas ete specifie.")."<br>";//$str1;
	}
	if(!$errors){
		$request=request("SELECT id,ordering,id_category,id_parent from $TBL_content_menuitems where id='$idMenuitem'",$link);
		if(mysql_num_rows($request)){
			$menuitem=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le composant specifie est invalide.")."<br>";//$str2;
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_content_menuitems where id_category='".$menuitem->id_category."' and id_parent='".$menuitem->id_parent."' and ordering='".($menuitem->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upMenuitem=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le composant specifie est deja au plus haut niveau.")."<br>";//$str3;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_menuitems set ordering='".($menuitem->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$menuitem->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre du composant.")."<br>";//$str4;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_menuitems set ordering='".$menuitem->ordering."' where id='".$upMenuitem->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre du composant.")."<br>";//$str4;
		}
	}
	if(!$errors){
		$message.=_("L'ordre du composant a ete modifie avec succes.")."<br>";//$str5;
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idMenuitem"])){
		$idMenuitem=$_POST["idMenuitem"];
	} elseif(isset($_GET["idMenuitem"])){
		$idMenuitem=$_GET["idMenuitem"];
	} else {
		$errors++;
		$errMessage.=_("Le composant n'a pas ete specifie.")."<br>";//$str1;
	}
	if(!$errors){
		$request=request("SELECT id,id_category,id_parent,ordering from $TBL_content_menuitems where id='$idMenuitem'",$link);
		if(mysql_num_rows($request)){
			$menuitem=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le composant specifie est invalide.")."<br>";//$str2;
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_content_menuitems where id_category='".$menuitem->id_category."' and id_parent='".$menuitem->id_parent."' and ordering='".($menuitem->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downMenuitem=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le composant specifie est deja au plus bas niveau.")."<br>";//$str6;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_menuitems set ordering='".($menuitem->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$menuitem->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre du composant.")."<br>";//$str4;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_content_menuitems set ordering='".$menuitem->ordering."' where id='".$downMenuitem->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre du composant.")."<br>";//$str4;
		}
	}
	if(!$errors){
		$message.=_("L'ordre du composant a ete modifie avec succes.")."<br>";//$str5;
	}
	$action="edit";
}
?>
<?
if($action=="delete"){
	if(isset($_POST["idCategory"])){
		$idCategory=$_POST["idCategory"];
	} elseif(isset($_GET["idCategory"])){
		$idCategory=$_GET["idCategory"];
	} else {
		$errors++;
		$errMessage.=_("Le menu concerné n'a pas été specifié.")."<br>";//$str7;
	}
	if(isset($_POST["idMenuitem"])){
		$idMenuitem=$_POST["idMenuitem"];
	} elseif(isset($_GET["idMenuitem"])){
		$idMenuitem=$_GET["idMenuitem"];
	} else {
		$errors++;
		$errMessage.=_("Le composant n'a pas été spécifié.")."<br>";//$str1;
	}
	if(!$errors){
		$request = request("SELECT $TBL_content_menuitems.id,$TBL_content_menuitems.type,$TBL_content_menuitems.deletable,$TBL_content_menuitems.ordering,$TBL_content_menuitems.id_parent,$TBL_content_info_menuitems.title FROM $TBL_content_menuitems,$TBL_content_info_menuitems,$TBL_gen_languages WHERE $TBL_content_menuitems.id_category='$idCategory' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_content_info_menuitems.id_menuitem=$TBL_content_menuitems.id AND $TBL_content_info_menuitems.id_language=$TBL_gen_languages.id order by id_parent,ordering",$link);
		$table=getMenusTable($request);
		if($table[$idMenuitem]["deletable"]=="N"){
			$errors++;
			$message.=_("Ce composant de menu ne peut être supprimé.")."<br>";//$str8;
		}
		//print_r($table);
	}
	if(!$errors){
		$tmp=deleteMenuitem($table,$idMenuitem,$link);
		$errors+=$tmp["errors"];
		$errMessage.=$tmp["errMessage"];
	}
	if(!$errors){
		$message.=_("Le composant a été supprimé correctement.")."<br>";//$str8;
	}
}
?>