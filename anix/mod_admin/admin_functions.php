<?php
  function showUserGroups($link){
    global $TBL_admin_groups;
	  $returnStr="";
	  $request = request("SELECT * FROM $TBL_admin_groups ORDER BY name",$link);
	  if(!mysql_num_rows($request)){
      $returnStr.="<center><i>"._("Aucun groupe trouvé en base de données.")."</i></center>";
    }
	  while($group=mysql_fetch_object($request)){
	    $returnStr.="<table class='edittable_text' width='100%'>";
	    $returnStr.="<tr>";
	    $returnStr.="<td align='left' valign='middle' width='42' bgcolor='#e7eff2'>";
	    $returnStr.="<a href='./mod_group.php?action=edit&idGroup=".$group->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier ce groupe")."\"></a>";
  		$returnStr.="&nbsp;<a href='./del_group.php?idGroup=".$group->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer ce groupe")."\"></a>";
  		$returnStr.="</td>";
      $returnStr.="<td>";
  		$returnStr.="<B>".$group->name."</B>";
  		$returnStr.="<br><i>".$group->description."</i>";
  		$returnStr.="</td>";
 	    $returnStr.="</tr>";
  		$returnStr.="</table>";
  	}
	  return $returnStr;
  }
  function showUsers($link){
    global $TBL_admin_groups;
    global $TBL_admin_admin;
	  $returnStr="";
	  $request = request("SELECT * FROM $TBL_admin_groups ORDER BY name",$link);
	  if(!mysql_num_rows($request)){
      $returnStr.="<center><i>"._("Aucun groupe trouvé en base de données.")."</i></center>";
    }
	  while($group=mysql_fetch_object($request)){
	    $returnStr.="<table class='edittable_text' width='100%'>";
	    $returnStr.="<tr>";
	    $returnStr.="<td align='right' valign='middle' width='42' bgcolor='#e7eff2'>";
	    $returnStr.="<a href='./mod_user.php?action=add&idGroup=".$group->id."'><img src='../images/add_user.gif' border='0' alt=\""._("Ajouter un utilisateur")."\"></a>";
      $returnStr.="</td>";
  		$returnStr.="</td>";
      $returnStr.="<td>";
  		$returnStr.="<img src='../images/users_group.gif' border='0' align='middle' alt=\""._("Groupe")."\"><B><U>".$group->name."</U></B>";
  		$returnStr.="<br><i>".$group->description."</i>";
  		$returnStr.="</td>";
 	    $returnStr.="</tr>";
  		$returnStr.="</table>";
  		$request2=request("SELECT `id`,`name`,`login` from $TBL_admin_admin WHERE `id_group`='".$group->id."' ORDER BY `name`",$link);
  		if(!mysql_num_rows($request2)){
        $returnStr.="<table class='edittable_text' width='100%'>";
  	    $returnStr.="<tr>";
  	    $returnStr.="<td align='left' valign='middle' width='42' bgcolor='#e7eff2'>&nbsp;</td>";
    		$returnStr.="</td>";
        $returnStr.="<td align='center'><i>"._("Aucun utilisateur dans ce groupe")."</i></td>";
   	    $returnStr.="</tr>";
    		$returnStr.="</table>";
      }
  		while($user=mysql_fetch_object($request2)){
        $returnStr.="<table class='edittable_text' width='100%'>";
  	    $returnStr.="<tr>";
  	    $returnStr.="<td align='left' valign='middle' width='42' bgcolor='#e7eff2'>";
  	    $returnStr.="<a href='./mod_user.php?action=edit&idUser=".$user->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier cet utilisateur")."\"></a>";
    		$returnStr.="&nbsp;<a href='./del_user.php?idUser=".$user->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer cet utilisateur")."\"></a>";
    		$returnStr.="</td>";
        $returnStr.="<td>";
    		$returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='../images/user.gif' border='0' align='middle' alt=\""._("Utilisateur")."\">".$user->name." (".$user->login.")";
    		$returnStr.="</td>";
   	    $returnStr.="</tr>";
    		$returnStr.="</table>";
      }
  	}
	  return $returnStr;
  }
?>
