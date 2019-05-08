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
	case "add":setTitleBar(_("Ajout d'une catégorie d'articles"));break;
	case "edit":setTitleBar(_("Modification des catégories d'articles"));break;
	case "addarticle":setTitleBar(_("Ajout d'un article"));break;
	case "modarticle":setTitleBar(_("Modification des articles"));break;
	default:setTitleBar(_("Modification des catégories d'articles"));break;
}
?>
<table border="0" align="center" width="100%" CellPadding="0" CellSpacing="0" bgcolor="#FFFFFF">
<td colspan='2'>
<?
$categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.deletable, $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering, $TBL_articles_info_categories.name, $TBL_articles_info_categories.description from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
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
		echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie d'articles")."</b></td>";
		echo "</tr>";
		echo "</table>";
	}
}
if($action=="addarticle"){
	echo _("Veuillez cliquer sur le nom de la catégorie sous laquelle vous souhaitez ajouter l'article.")."<br><br>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./mod_article.php?action=add&idCat=",false);
}
if($action=="modarticle"){
	echo _("Veuillez cliquer sur le nom de la catégorie ou se trouve l'article à modifier.")."<br><br>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./list_articles.php?idCat=",false);
}
?>
</td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
