<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$delete=false;
$errMessage="";
$message="";
$errors=0;
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="";
?>
<?php
include("./list_categories.actions.php");
?>
<?
$title = _("Anix - Liste des catégories");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'une catégorie d'éléments"));break;
	case "edit":setTitleBar(_("Modification des catégories d'éléments"));break;
	case "additem":setTitleBar(_("Ajout d'un élément"));break;
	case "updateitems":setTitleBar(_("Mise à jour d'éléments"));break;
	case "moditem":setTitleBar(_("Modification des éléments"));break;
}
?>
<table border="0" align="center" width="100%" CellPadding="0" CellSpacing="0" bgcolor="#FFFFFF">
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
$tableCategories = getCatTable();
$nbCategories = count($tableCategories);
if($action=="add"){
	echo _("Veuillez cliquer sur le nom de la catégorie parente sous laquelle vous souhaitez ajouter la nouvelle catégorie.")."<br><br>";
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td><a href='./mod_category.php?action=add&idCat=0'>"._("Premier niveau")."</a></td>";
	echo "</tr>";
	echo "</table>";
	$tableCategories = getCatTable();
	echo showCategories($tableCategories,0,0,"./mod_category.php?action=add&idCat=",false,true);
}
if($action=="edit"){
	if($nbCategories) {
		//var_dump($tableCategories);
		echo showCategories($tableCategories,0,0,"",true,true);
	} else {
		echo "<table class='message' width='100%'>";
		echo "<tr>";
		echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie d'éléments")."</b></td>";
		echo "</tr>";
		echo "</table>";
	}
}
if($action=="additem"){
	echo _("Veuillez cliquer sur le nom de la catégorie sous laquelle vous souhaitez ajouter l'élément.")."<br><br>";
	$tableCategories = getCatTable();
	echo showCategories($tableCategories,0,0,"./mod_item.php?action=add&idCat=",false,false);
}
if($action=="moditem"){
	echo _("Veuillez cliquer sur le nom de la catégorie où se trouve l'élément à modifier.")."<br><br>";
	$tableCategories = getCatTable();
	echo showCategories($tableCategories,0,0,"./list_items.php?idCat=",false,false);
}
if($action=="updateitems"){
	echo _("Veuillez cliquer sur le nom de la catégorie où se trouve les éléments à mettre à jour.")."<br><br>";
	$tableCategories = getCatTable();
	echo showCategories($tableCategories,0,0,"./update_items.php?idCat=",false,false);
}
?>
</td></tr>
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'>&nbsp;</font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
    &nbsp;
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
