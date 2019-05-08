<?php
  if($action=="delete"){
    $idUser=$_POST["idUser"];
    $request = request("SELECT `id` FROM $TBL_admin_admin where `id`='$idUser'",$link);
    if(mysql_num_rows($request)){
      $files = mysql_fetch_object($request);
    } else {
      $errors++;
      $errMessage.=_("L'utilisateur spécifié est invalide.")."<br />";
    }
    //Delete the users of the group
    if(!$errors){
      request("DELETE FROM $TBL_admin_admin
               WHERE $TBL_admin_admin.`id`='$idUser'",$link);
      if(mysql_errno($link)){
        $errors++;
        $errMessage.=_("Une erreur s'est produite lors de la suppression de l'utilisateur.")."<br />";
      }
    }
    if(!$errors){
      $message.=_("L'utilisateur a été supprimé correctement.")."<br />";
    }
  }
?>