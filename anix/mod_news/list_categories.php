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
	case "addnews":setTitleBar(_("Ajout d'une nouvelle"));break;
	case "modnews":setTitleBar(_("Modification des nouvelles"));break;
}
?>
<table border="0" align="center" width="100%" CellPadding="0" CellSpacing="0" bgcolor="#FFFFFF">
<tr>
<td colspan='2'>
<?
$categories=request("select $TBL_news_categories.id, $TBL_news_categories.id_parent, $TBL_news_categories.deletable, $TBL_news_categories.ordering, $TBL_news_info_categories.name, $TBL_news_info_categories.description from  $TBL_news_categories,$TBL_gen_languages,$TBL_news_info_categories where $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id and $TBL_news_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_news_categories.id_parent, $TBL_news_categories.ordering", $link);
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
if($action=="addnews"){
	echo _("Veuillez cliquer sur le nom de la catégorie sous laquelle vous souhaitez ajouter la nouvelle.")."<br><br>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./mod_news.php?action=add&idCat=",false);
}
if($action=="modnews"){
	echo _("Veuillez cliquer sur le nom de la catégorie o se trouve la nouvelle à modifier.")."<br><br>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./list_news.php?idCat=",false);
}
?>
</td></tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
