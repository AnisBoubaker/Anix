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
//The category to which we are adding the link
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory=0;
//The selected category in the list (used on page reloading)
if(isset($_POST["idSelectedCat"])){
	$idSelectedCat=$_POST["idSelectedCat"];
} elseif(isset($_GET["idSelectedCat"])){
	$idSelectedCat=$_GET["idSelectedCat"];
} else $idSelectedCat=0;
?>
<?
$title = _("Anix - Ajout d'une page liée");
include("../html_header.php");
switch($action){
	case "addNews":setTitleBar(_("Ajout d'une page liée à une nouvelle"));break;
	case "addArticle":setTitleBar(_("Ajout d'une page liée à un article"));break;
	default:setTitleBar(_("Ajout d'une page liée"));
}
$cancelLink = "./list_pages.php?idCategory=$idCategory";
?>
<table border="0" align="center" CellPadding="0" CellSpacing="0" width="100%">
<tr bgcolor='#FFFFFF'>
<td colspan='2'>
<?
if($action==""){//Choose to what kind of element we want to link
	$button=array();
	$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
	$buttons[]=array("type"=>"back","link"=>$cancelLink);
	printButtons($buttons);
	echo "<form id='main_form' action='./add_link.php' method='post'>";
	echo "<p style='text-align:center;'>";
	echo "<b>"._("Veuillez sélectionner le type d'élément à lier").":</b> ";
	echo "<select name='action'>";
	echo "<option value=''>"._("----------")."</option>";
	echo "<option value='addArticle'>"._("Un article")."</option>";
	echo "<option value='addNews'>"._("Une nouvelle")."</option>";
	echo "</select>";
	echo "</p>";
	echo "<input type='hidden' name='idCategory' value='$idCategory' />";
	echo "</form>";
} else {
	$button=array();
	$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
	printButtons($buttons);
}
if($action=="addNews"){
	echo _("Veuillez sélectionner la nouvelle").":";
	$categories=request("select $TBL_news_categories.id, $TBL_news_categories.id_parent, $TBL_news_categories.ordering, $TBL_news_info_categories.name from  $TBL_news_categories,$TBL_gen_languages,$TBL_news_info_categories where $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id and $TBL_news_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_news_categories.id_parent, $TBL_news_categories.ordering", $link);
	$nbCategories = mysql_num_rows($categories);
	if($nbCategories) {
		$tableCategories = getNewsCatTable($categories);
		echo showNews($tableCategories,0,$idSelectedCat,0,"./list_pages.php?action=addNewsLink&idCategory=$idCategory&idNews=","./add_link.php?action=addNews&idCategory=$idCategory&idSelectedCat=",$link);
		if($idSelectedCat) echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idSelectedCat'</SCRIPT>";
	} else {
		echo "<table class='message' width='100%'>";
		echo "<tr>";
		echo "<td align='center'><b>";
		echo _("La base de données ne contient aucune catégorie de nouvelles");
		echo "</b></td>";
		echo "</tr>";
		echo "</table>";
	}
}
elseif($action=="addArticle"){
	echo _("Veuillez sélectionner l'article").":";
	$categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering, $TBL_articles_info_categories.name from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
	$nbCategories = mysql_num_rows($categories);
	if($nbCategories) {
		$tableCategories = getArticlesCatTable($categories);
		echo showArticles($tableCategories,0,$idSelectedCat,0,"./list_pages.php?action=addArticleLink&idCategory=$idCategory&idArticle=","./add_link.php?action=addArticle&idCategory=$idCategory&idSelectedCat=",$link);
		if($idSelectedCat) echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idSelectedCat'</SCRIPT>";
	} else {
		echo "<table class='message' width='100%'>";
		echo "<tr>";
		echo "<td align='center'><b>";
		echo _("La base de données ne contient aucune catégorie d'articles");
		echo "</b></td>";
		echo "</tr>";
		echo "</table>";
	}
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
