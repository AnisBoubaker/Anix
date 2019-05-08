<?
	include ("../config.php");
	include ("./module_config.php");
	$link = dbConnect();
	if(isset($_POST["action"])){
	  $action=$_POST["action"];
	} elseif(isset($_GET["action"])){
	  $action=$_GET["action"];
	} else $action="";
  $errMessage="";
	$message="";
	$errors=0;
?>
<?php
include("./list_users.actions.php");
?>
<? $title = _("Anix - Liste des utilisateurs");
   include("../html_header.php");
   setTitleBar(_("Liste des utilisateurs"));
?>
<table border="0" align="center" width="60%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  &nbsp;
  </td>
</tr>
<tr>
<td colspan='2'>
<?
  echo showUsers($link);
?>
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    &nbsp;
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  &nbsp;
  </td>
</tr>
</table>
<?
	include ("../html_footer.php");
	mysql_close($link);
?>
