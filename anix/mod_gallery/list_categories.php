<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$delete=false;

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
	case "add":setTitleBar(_("Ajout d'une catégorie de nouvelles"));break;
	case "edit":setTitleBar(_("Modification des catégories de nouvelles"));break;
	case "addphoto":setTitleBar(_("Ajout d'une nouvelle"));break;
	case "modphoto":setTitleBar(_("Modification des nouvelles"));break;
}
?>
<table border="0" align="center" width="100%" CellPadding="0" CellSpacing="0" bgcolor="#FFFFFF">
<tr>
<td colspan='2'>
<?
$categories=request("select $TBL_gallery_categories.id, $TBL_gallery_categories.id_parent, $TBL_gallery_categories.deletable, $TBL_gallery_categories.ordering, $TBL_gallery_info_categories.name, $TBL_gallery_info_categories.description from  $TBL_gallery_categories,$TBL_gen_languages,$TBL_gallery_info_categories where $TBL_gallery_info_categories.id_gallery_cat=$TBL_gallery_categories.id and $TBL_gallery_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_gallery_categories.id_parent, $TBL_gallery_categories.ordering", $link);
$nbCategories = mysql_num_rows($categories);
if($action=="add"){
	echo _("Veuillez cliquer sur le nom de la catégorie parente sous laquelle vous souhaitez ajouter la nouvelle catégorie.")."<br><br>";
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td><a href='./mod_category.php?action=add&idCat=0'>"._("Premier niveau")."</a></td>";
	echo "</tr>";
	echo "</table>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./mod_category.php?action=add&idCat=",false);
}
if($action=="edit"){
	if($nbCategories) {
		$tableCategories = getCatTable($categories);
		echo showCategories($tableCategories,0,0,"",true);
	} else {
		echo "<table class='message' width='100%'>";
		echo "<tr>";
		echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie de nouvelles")."</b></td>";
		echo "</tr>";
		echo "</table>";
	}
}
if($action=="addphoto"){
	echo _("Veuillez cliquer sur le nom de la catégorie sous laquelle vous souhaitez ajouter la nouvelle.")."<br><br>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./mod_photo.php?action=add&idCat=",false);
}
if($action=="modphoto"){
	echo _("Veuillez cliquer sur le nom de la catégorie o se trouve la nouvelle à modifier.")."<br><br>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./list_photos.php?idCat=",false);
}
?>
</td></tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
