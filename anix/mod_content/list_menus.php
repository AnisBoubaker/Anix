<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="";
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory="";
$errMessage="";
$message="";
$errors=0;
?>
<?php
include("./list_menus.actions.php");
?>
<?php
$title = _("Anix - Liste des menus");
include("../html_header.php");
setTitleBar(_("Liste des menus"));
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<TR>
<TD colspan='2'>
<?php
echo showMenusList($idCategory,$link);
?>
</TABLE>
<?php
if(isset($_GET["idMenuitem"])){
	echo "<script language='javascript'>document.location='#".$_GET["idMenuitem"]."'</script>";
}
?>
<?
include ("../html_footer.php");
mysql_close($link);
?>
