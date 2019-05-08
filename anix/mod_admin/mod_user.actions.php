<?php
if($action=="insert"){
	if($_POST["name"]==""){
		$errors++;
		$errMessage.=_("Le nom de l'utilisateur ne peut être vide.")."<br />";
	}
	if($_POST["login"]==""){
		$errors++;
		$errMessage.=_("Le login de l'utilisateur ne peut être vide.")."<br />";
	}
	if(!loginPassValid($_POST["login"]) || !loginPassValid($_POST["password1"])) {
		$errors++;
		$errMessage.=_("Le login et mot de passe ne peuvent être composés que par des lettres et/ou des chiffres.")."<br />";
	}
	if(strlen($_POST["login"])<6 || strlen($_POST["login"])>20){
		$errors++;
		$errMessage.=_("Le login doit comporter entre 8 et 15 caractères.")."<br />";
	}
	if(strlen($_POST["password1"])<8 || strlen($_POST["password1"])>15){
		$errors++;
		$errMessage.=_("Le mot de passe doit comporter entre 8 et 15 caractères.")."<br />";
	}
	if(strcmp($_POST["password1"],$_POST["password2"])){
		$errors++;
		$errMessage.=_("Vous devez entrer deux fois le même mot de passe.")."<br />";
	}
	if(!strcmp($_POST["password1"],$_POST["login"])){
		$errors++;
		$errMessage.=_("Le login et le mot de passe ne peuvent être les mêmes.")."<br />";
	}
	if(!$errors && $anix_demo_mode){
		$errors++;
		$errMessage.=_("Désolé, cette opération n'est pas permise en mode démo.")."<br />";
	}
	if(!$errors){
		$cryptedPass = crypt($_POST["password1"],substr($_POST["login"],0,2));
		//Insert the user in the database
		$requestString="
				INSERT INTO $TBL_admin_admin (`id_group`,`name`,`email`,`phone1`,`phone2`,`cell`,`pager`,`id_language`,`login`,`password`,`locked`)
				VALUES ('".$_POST["idGroup"]."',
						'".htmlentities($_POST["name"],ENT_QUOTES,"UTF-8")."',
						'".htmlentities($_POST["email"],ENT_QUOTES,"UTF-8")."',
						'".htmlentities($_POST["phone1"],ENT_QUOTES,"UTF-8")."',
						'".htmlentities($_POST["phone2"],ENT_QUOTES,"UTF-8")."',
						'".htmlentities($_POST["cell"],ENT_QUOTES,"UTF-8")."',
						'".htmlentities($_POST["pager"],ENT_QUOTES,"UTF-8")."',
						'".$_POST["idLanguage"]."',
						'".$_POST["login"]."',
						'$cryptedPass',
						'".(isset($_POST["locked"])?"Y":"N")."'
					  )";
		request($requestString,$link);
		//If insertion was OK, we rtrieve the id of the inserted category, else error...
		if(!mysql_errno($link)) {
			$idUser=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion de l'utilisateur car le login existe déjà")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$message = _("L'utilisateur a été inséré correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$request=request("SELECT `id`,`login` from $TBL_admin_admin WHERE `id`='$idUser'",$link);
	if(!mysql_num_rows($request)){
		$errors++;
		$errMessage.=_("L'utilisateur spécifié est invalide.")."<br />";
	} else {
		$user=mysql_fetch_object($request);
	}
	if($_POST["name"]==""){
		$errors++;
		$errMessage.=_("Le nom de l'utilisateur ne peut être vide.")."<br />";
	}
	if($_POST["login"]==""){
		$errors++;
		$errMessage.=_("Le login de l'utilisateur ne peut être vide.")."<br />";
	}
	if(!loginPassValid($_POST["login"])) {
		$errors++;
		$errMessage.=_("Le login et mot de passe ne peuvent être composés que par des lettres et/ou des chiffres.")."<br />";
	}
	if(strlen($_POST["login"])<6 || strlen($_POST["login"])>20){
		$errors++;
		$errMessage.=_("Le login doit comporter entre 8 et 15 caractères.")."<br />";
	}
	if($_POST["password1"]!="" || $_POST["password1"]!=""){
		if(strlen($_POST["password1"])<8 || strlen($_POST["password1"])>15){
			$errors++;
			$errMessage.=_("Le mot de passe doit comporter entre 8 et 15 caractères.")."<br />";
		}
		if(strcmp($_POST["password1"],$_POST["password2"])){
			$errors++;
			$errMessage.=_("Vous devez entrer deux fois le même mot de passe.")."<br />";
		}
		if(!strcmp($_POST["password1"],$_POST["login"])){
			$errors++;
			$errMessage.=_("Le login et le mot de passe ne peuvent être les mêmes.")."<br />";
		}
		if(!$errors){
			$cryptedPass = crypt($_POST["password1"],substr($_POST["login"],0,2));
		}
	}
	if(!$errors && $anix_demo_mode){
		$errors++;
		$errMessage.=$str36;
	}
	//Update the category
	if(!$errors){
		$requestString ="UPDATE `$TBL_admin_admin` SET ";
		$requestString .="`id_group`='$idGroup',";
		$requestString .="`name`='".htmlentities($_POST["name"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`email`='".htmlentities($_POST["email"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`phone1`='".htmlentities($_POST["phone1"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`phone2`='".htmlentities($_POST["phone2"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`cell`='".htmlentities($_POST["cell"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`pager`='".htmlentities($_POST["pager"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`id_language`='".$_POST["idLanguage"]."',";
		if(strcmp($_POST["login"],$user->login)) $requestString .="`login`='".$_POST["login"]."',";
		if(isset($cryptedPass)) $requestString .="`password`='$cryptedPass',";
		$requestString .="`locked`='".(isset($_POST["locked"])?"Y":"N")."' ";
		$requestString .="WHERE `id`='".$idUser."'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'utilisateur car le nouveau login est déjà existant.")."<br />";
		}
	}
	if(!$errors){
		$message = _("L'utilisateur a été mis à jour correctement.")."<br />";
		$action="edit";
	}
}
?>