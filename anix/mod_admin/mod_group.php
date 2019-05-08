<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idGroup"])){
	$idGroup=$_POST["idGroup"];
} elseif(isset($_GET["idGroup"])){
	$idGroup=$_GET["idGroup"];
} else $idGroup="";
?>
<?php
include("./mod_group.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'un groupe d'utilisateurs");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'un groupe d'utilisateurs");
else $title = _("Anix - Modification d'un groupe d'utilisateurs");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'un groupe d'utilisateurs"));break;
	case "insert":setTitleBar(_("Ajout d'un groupe d'utilisateurs"));break;
	case "edit":setTitleBar(_("Modification d'un groupe d'utilisateurs"));break;
	case "update":setTitleBar(_("Modification d'un groupe d'utilisateurs"));break;
	default:setTitleBar(_("Modification d'un groupe d'utilisateurs"));break;
}
?>
<form action='./mod_group.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idGroup' value='$idGroup'>";
	echo "<input type='hidden' name='action' value='insert'>";
	$cancelLink="./list_groups.php";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idGroup' value='$idGroup'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./list_groups.php";
}
  ?>
  <table border="0" align="center" width="60%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
    <tr>
      <td colspan='2'>
<?
if($action=="edit"){
	$result=request("SELECT id,name,description from $TBL_admin_groups where id='$idGroup'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Ce groupe n'existe pas."));
	$edit = mysql_fetch_object($result);
}
        ?>
        <table width='100%'>
          <tr>
            <td><?php echo _("Nom du groupe"); ?>:</td>
            <td>
              <input type='text' name='name' size='20' Maxlength='50' <?
              if($action=="edit") echo " value='".unhtmlentities($edit->name)."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["name"]."'";
              ?>>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Déscription"); ?>:</td>
            <td>
              <input type='text' name='description' size='60' Maxlength='250' <?
              if($action=="edit") echo " value='".unhtmlentities($edit->description)."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["description"]."'";
              ?>>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
  </table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
