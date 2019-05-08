<?php
  if($action=="delete"){
    $idGroup=$_POST["idGroup"];
    $request = request("SELECT `id` FROM $TBL_admin_groups where `id`='$idGroup'",$link);
    if(mysql_num_rows($request)){
      $files = mysql_fetch_object($request);
    } else {
      $errors++;
      $errMessage.=_("Le groupe spécifié est invalide.")."<br />";
    }
    //Delete the users of the group
    if(!$errors){
      request("DELETE FROM $TBL_admin_admin
               WHERE $TBL_admin_admin.`id_group`='$idGroup'",$link);
      if(mysql_errno($link)){
        $errors++;
        $errMessage.=_("Une erreur s'est produite lors de la suppression des utilisateurs du groupe.")."<br />";
      }
    }
    if(!$errors){
      request("DELETE FROM $TBL_admin_groups
               WHERE `id`='$idGroup'",$link);
      if(mysql_errno($link)){
        $errors++;
        $errMessage.=_("Une erreur s'est produite lors de la suppression du groupe.")."<br />";
      }
    }
    if(!$errors){
      $message.=_("Le groupe a été supprimé correctement.")."<br />";
    }
  }
?>