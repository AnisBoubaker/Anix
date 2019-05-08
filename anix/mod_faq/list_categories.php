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
$title = _("Anix - Liste des catégories de FAQ");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'une catégorie de FAQ"));break;
	case "edit":setTitleBar(_("Modification des catégories de FAQ"));break;
	case "addfaq":setTitleBar(_("Ajout d'une FAQ"));break;
	case "modfaq":setTitleBar(_("Modification des FAQ"));break;
}
?>
<table border="0" align="center" width="100%" CellPadding="0" CellSpacing="0" bgcolor="#FFFFFF">
<td colspan='2'>
<?
$categories=request("select $TBL_faq_categories.id, $TBL_faq_categories.deletable, $TBL_faq_categories.id_parent, $TBL_faq_categories.ordering, $TBL_faq_info_categories.name, $TBL_faq_info_categories.description from  $TBL_faq_categories,$TBL_gen_languages,$TBL_faq_info_categories where $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_faq_categories.id_parent, $TBL_faq_categories.ordering", $link);
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
		echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie de FAQ")."</b></td>";
		echo "</tr>";
		echo "</table>";
	}
}
if($action=="addfaq"){
	echo _("Veuillez cliquer sur le nom de la catégorie sous laquelle vous souhaitez ajouter la question.")."<br><br>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./mod_faq.php?action=add&idCat=",false);
}
if($action=="modfaq"){
	echo _("Veuillez cliquer sur le nom de la catégorie où se trouve la question à modifier.")."<br><br>";
	$tableCategories = getCatTable($categories);
	echo showCategories($tableCategories,0,0,"./list_faq.php?idCat=",false);
}
?>
</td></tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
