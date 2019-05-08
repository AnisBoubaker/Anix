<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idMenuitem"])){
	$idMenuitem=$_POST["idMenuitem"];
} elseif(isset($_GET["idMenuitem"])){
	$idMenuitem=$_GET["idMenuitem"];
}
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
}
?>
<? $title = _("Anix - Suppression d'un composant de menu");
include("../html_header.php");
setTitleBar(_("Suppression d'un composant de menu"));
?>

<?
$result=request("select $TBL_content_menuitems.id,$TBL_content_menuitems.type,$TBL_content_info_menuitems.title from $TBL_content_menuitems,$TBL_content_info_menuitems,$TBL_gen_languages where $TBL_content_menuitems.id='$idMenuitem' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_content_info_menuitems.id_menuitem=$TBL_content_menuitems.id and $TBL_content_info_menuitems.id_language=$TBL_gen_languages.id", $link);
$menuitem=mysql_fetch_object($result);
$cancelLink="./list_menus.php?idCategory=$idCategory";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>

<form id='main_form' name='del' action='./list_menus.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='delete'>
  <input type='hidden' name='idMenuitem' value='<?=$idMenuitem?>'>
  <input type='hidden' name='idCategory' value='<?=$idCategory?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Etes vous sur de vouloir supprimer");?> <?php
  if($menuitem->type=='submenu') echo _("le sous-menu");
  if($menuitem->type=='link') echo _("le lien");
  echo " \"".$menuitem->title."\" ?</font><br><br>";
  ?>
  </b>
  <?php if($menuitem->type=='submenu') echo _("Les composants dépendant de ce sous menu seront également effacés."); ?>
  </center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>

