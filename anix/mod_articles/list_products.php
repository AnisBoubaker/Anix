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
if(isset($_POST["idPrdCat"])){
	$idPrdCat=$_POST["idPrdCat"];
} elseif(isset($_GET["idPrdCat"])){
	$idPrdCat=$_GET["idPrdCat"];
} else $idPrdCat=0;
if(isset($_POST["idArticle"])){
	$idArticle=$_POST["idArticle"];
} elseif(isset($_GET["idArticle"])){
	$idArticle=$_GET["idArticle"];
} else $idArticle=0;
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?
$title = _("Anix - Ajout d'un lien Produit");
include("../html_header.php");
switch ($action) {
	case "addProduct":setTitleBar(_("Ajout d'un produit"));break;
	case "addCat":setTitleBar(_("Ajout d'une catégorie de produits"));break;
}
if($idArticle){
	$cancelLink = "./mod_article.php?action=edit&idArticle=$idArticle";
}
if($idCat){
	$cancelLink = "./mod_category.php?action=edit&idCat=$idCat";
}
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<table border="0" align="center" CellPadding="0" CellSpacing="0" width="100%">
<tr bgcolor='#FFFFFF'>
<td colspan='2'>
<?
$prodCategories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$nbCategories = mysql_num_rows($prodCategories);
if($action=="addProduct") echo _("Veuillez sélectionner la catégorie contenant le produit à relier").":";
if($action=="addCat") echo _("Veuillez sélectionner la catégorie de produits à relier").":";
if($nbCategories) {
	$tableCategories = getPrdCatTable($prodCategories);
	if($idArticle){
		if($action=="addProduct"){
			echo showProducts($tableCategories,0,$idPrdCat,0,"./mod_article.php?action=addProduct&idArticle=$idArticle&idProduct=","./list_products.php?action=addProduct&idArticle=$idArticle&idPrdCat=",$link);
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idPrdCat'</SCRIPT>";
		}
		if($action=="addCat"){
			echo showPrdCategories($tableCategories,0,0,"./mod_article.php?action=addPrdCat&idArticle=$idArticle&idPrdCat=",false);
		}
	}
	if($idCat){
		if($action=="addProduct"){
			echo showProducts($tableCategories,0,$idPrdCat,0,"./mod_category.php?action=addProduct&idCat=$idCat&idProduct=","./list_products.php?action=addProduct&idCat=$idCat&idPrdCat=",$link);
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idPrdCat'</SCRIPT>";
		}
		if($action=="addCat"){
			echo showPrdCategories($tableCategories,0,0,"./mod_category.php?action=addPrdCat&idCat=$idCat&idPrdCat=",false);
		}
	}


} else {
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie de produits")."</b></td>";
	echo "</tr>";
	echo "</table>";
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

