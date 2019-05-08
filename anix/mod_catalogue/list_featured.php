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
include("./list_featured.actions.php");
?>
<?
$title = _("Anix - Liste des vedettes");
include("../html_header.php");
setTitleBar(_("Liste des vedettes"));
?>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2'>
<?
echo showFeaturedList($link);
?>
</table>
<?
include ("../html_footer.php");
mysql_close($link);
?>
