<?
	include ("../config.php");
	include ("./module_config.php");
	$link = dbConnect();
	if(isset($_POST["action"])){
	  $action=$_POST["action"];
	} elseif(isset($_GET["action"])){
	  $action=$_GET["action"];
	} else $action="";
?>
<?php
include("./list_groups.actions.php");
?>
<? $title = _("Anix - Liste des groupes d'utilisateurs");
   $menu_ouvert = 1;
   $module_name="admin";
   include("../html_header.php");
?>
<table border="0" align="center" width="60%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'><?php echo _("Liste des goupes d'utilisateurs"); ?></font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  <a href='./mod_group.php?action=add'><img src='../locales/<?=$used_language?>/images/button_add.jpg' border='0'></a>
  </td>
</tr>
<tr>
<td colspan='2'>
<?
  echo showUserGroups($link);
?>
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    &nbsp;
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  <a href='./mod_group.php?action=add'><img src='../locales/<?=$used_language?>/images/button_add.jpg' border='0'></a>
  </td>
</tr>
</table>
<?
	include ("../html_footer.php");
	mysql_close($link);
?>
