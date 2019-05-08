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
include("./list_brands.actions.php");
?>
<?
$title = _("Anix - Liste des marques");
include("../html_header.php");
setTitleBar(_("Liste des marques"));
$button=array();
$buttons[]=array("type"=>"additem","link"=>'./mod_brand.php?action=add');
printButtons($buttons);
?>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2'>
<?
echo showBrands($link);
?>
</table>
<?
include ("../html_footer.php");
mysql_close($link);
?>
