<?php
  if($action=="insert"){
    if($_POST["name"]==""){
      $errors++;
      $errMessage.=_("Le nom du groupe ne peut Être vide.")."<br />";
    }
    if(!$errors && $anix_demo_mode){
      $errors++;
      $errMessage.=_("Désolé, cette opération n'est pas permise en mode démo.")."<br />";
    }
    if(!$errors){
      //Insert the group in the database
  	  $requestString="INSERT INTO $TBL_admin_groups (`name`,`description`) values ('".htmlentities($_POST["name"])."','".htmlentities($_POST["description"])."')";
      request($requestString,$link);
      //If insertion was OK, we rtrieve the id of the inserted category, else error...
    	if(!mysql_errno($link)) {
    	  $idGroup=mysql_insert_id($link);
    	} else {
    	  $errMessage.=_("Une erreur s'est produite lors de l'insertion du groupe car le nom existe déjà")."<br />";
    	  $errors++;
    	}
    }
  	if(!$errors){
  	  $message = _("Le groupe a été inséré correctement")."<br />";
  	  $action="edit";
  	}
  }
?>
<?
  if($action=="update"){
    $request=request("SELECT `id` from $TBL_admin_groups WHERE `id`='$idGroup'",$link);
    if(!mysql_num_rows($request)){
      $errors++;
      $errMessage.=_("Le groupe spécifié est invalide.")."<br />";
    }
    if(!$errors && $anix_demo_mode){
      $errors++;
      $errMessage.=$str14;
    }
    //Update the category
    if(!$errors){
      $requestString ="UPDATE `$TBL_admin_groups` SET ";
      $requestString .="`name`='".htmlentities($_POST["name"],ENT_QUOTES,"UTF-8")."',";
      $requestString .="`description`='".htmlentities($_POST["description"],ENT_QUOTES,"UTF-8")."' ";
      $requestString .="WHERE `id`='".$idGroup."'";
		  request($requestString,$link);
		  if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour du groupe.")."<br />";
		  }
    }
  	if(!$errors){
  	  $message = _("Le groupe a été mis à jour correctement.")."<br />";
  	  $action="edit";
  	}
  }
?>