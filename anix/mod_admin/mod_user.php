<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idUser"])){
	$idUser=$_POST["idUser"];
} elseif(isset($_GET["idUser"])){
	$idUser=$_GET["idUser"];
} else $idUser="";
if(isset($_POST["idGroup"])){
	$idGroup=$_POST["idGroup"];
} elseif(isset($_GET["idGroup"])){
	$idGroup=$_GET["idGroup"];
} else $idGroup=0;
?>
<?php
include("./mod_user.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'un utilisateur");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'un utilisateur");
else $title = _("Anix - Modification d'un utilisateur");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'un utilisateur"));break;
	case "insert":setTitleBar(_("Ajout d'un utilisateur"));break;
	case "edit":setTitleBar(_("Modification d'un utilisateur"));break;
	case "update":setTitleBar(_("Modification d'un utilisateur"));break;
	default:setTitleBar(_("Modification d'un utilisateur"));break;
}
?>
<form action='./mod_user.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idUser' value='$idUser'>";
	echo "<input type='hidden' name='action' value='insert'>";
	$cancelLink="./list_users.php";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idUser' value='$idUser'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./list_users.php";
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
	$result=request("SELECT * from $TBL_admin_admin where `id`='$idUser'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cet utilisateur n'existe pas."));
	$edit = mysql_fetch_object($result);
}
?>
        <table width='100%'>
          <tr>
            <td><?php echo _("Nom et prénom"); ?>:</td>
            <td>
              <input type='text' name='name' size='40' Maxlength='100' <?
              if($action=="edit") echo " value='".unhtmlentities($edit->name)."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["name"]."'";
              ?>>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Courriel"); ?>:</td>
            <td>
              <input type='text' name='email' size='40' Maxlength='200' <?
              if($action=="edit") echo " value='".unhtmlentities($edit->email)."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["email"]."'";
              ?>>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Tél. 1"); ?>:</td>
            <td>
              <input type='text' name='phone1' size='40' Maxlength='20' <?
              if($action=="edit") echo " value='".unhtmlentities($edit->phone1)."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["phone1"]."'";
              ?>>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Tél. 2"); ?>:</td>
            <td>
              <input type='text' name='phone2' size='40' Maxlength='20' <?
              if($action=="edit") echo " value='".unhtmlentities($edit->phone2)."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["phone2"]."'";
              ?>>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Cellulaire"); ?>:</td>
            <td>
              <input type='text' name='cell' size='40' Maxlength='20' <?
              if($action=="edit") echo " value='".unhtmlentities($edit->cell)."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["cell"]."'";
              ?>>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Pager"); ?>:</td>
            <td>
              <input type='text' name='pager' size='40' Maxlength='20' <?
              if($action=="edit") echo " value='".unhtmlentities($edit->pager)."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["pager"]."'";
              ?>>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Groupe"); ?>:</td>
            <td>
              <SELECT name='idGroup'>
                <?
                $request=request("SELECT `id`,`name` from $TBL_admin_groups ORDER BY `name`",$link);
                while($group=mysql_fetch_object($request)){
                	echo "<option value='".$group->id."'";
                	if($action=="edit" && $edit->id_group==$group->id) echo " SELECTED";
                	if(($action=="update" || $action=="insert") && $_POST["idGroup"]==$group->id) echo " SELECTED";
                	echo ">".$group->name."</option>";
                }
                ?>
              </SELECT>
            </td>
          </tr>
           <tr>
            <td><?php echo _("Langue par défaut"); ?>:</td>
            <td>
              <SELECT name='idLanguage'>
                <?
                $request=request("SELECT `id`,`name` from $TBL_gen_languages ORDER BY `name`",$link);
                while($language=mysql_fetch_object($request)){
                	echo "<option value='".$language->id."'";
                	if($action=="add" && $idLanguage==$language->id) echo " SELECTED";
                	if($action=="edit" && $edit->id_language==$language->id) echo " SELECTED";
                	if(($action=="update" || $action=="insert") && $_POST["idLanguage"]==$language->id) echo " SELECTED";
                	echo ">".$language->name."</option>";
                }
                ?>
              </SELECT>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Login"); ?>:</td>
            <td>
              <input type='text' name='login' size='40' Maxlength='20' <?
              if($action=="edit") echo " value='".$edit->login."'";
              if($action=="insert" || $action=="update")  echo " value='".$_POST["login"]."'";
              ?>>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Nouveau Mot de passe"); ?>:</td>
            <td>
              <input type='password' name='password1' size='40' Maxlength='20'>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Nouveau Mot de passe")."<i>"._("(Vérification)")."</i>";?>:</td>
            <td>
              <input type='password' name='password2' size='40' Maxlength='20'>
            </td>
          </tr>
          <tr>
            <td><?php echo _("Compte désactivé"); ?></td>
            <td>
              <input type='checkbox' name='locked' value='Y'<?
              if($action=="edit" && $edit->locked=="Y") echo " CHECKED";
              if(($action=="insert" || $action=="update") && isset($_POST["locked"]))  echo " CHECKED";
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
