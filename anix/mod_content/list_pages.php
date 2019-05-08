<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory=0;
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="";
?>
<?php
include("./list_pages.actions.php");
?>
<?
$title = _("Anix - Liste des pages dynamiques");
include("../html_header.php");
setTitleBar(_("Liste des pages dynamiques"));
?>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2'>
<?
echo showPages($idCategory,$link);
?>
</table>
<?
include ("../html_footer.php");
mysql_close($link);
?>
