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
if(isset($_POST["idCatTo"])){
	$idCatTo=$_POST["idCatTo"];
} elseif(isset($_GET["idCatTo"])){
	$idCatTo=$_GET["idCatTo"];
} else $idCatTo=0;
if(isset($_POST["idProduct"])){
	$idProduct=$_POST["idProduct"];
} elseif(isset($_GET["idProduct"])){
	$idProduct=$_GET["idProduct"];
} else $idProduct=0;
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?
$title = _("Anix - Ajout d'un lien catalogue");
include("../html_header.php");
switch($action){
	case "addProduct":setTitleBar(_("Ajout d'un lien vers un produit"));break;
	case "addCat":setTitleBar(_("Ajout d'un lien vers une catégorie de produits"));break;
}
if($idProduct){
	$cancelLink = "./mod_product.php?action=edit&idProduct=$idProduct";
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
$productCategories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$nbCategories = mysql_num_rows($productCategories);
if($action=="addProduct") echo _("Veuillez sélectionner la catégorie conenant le produit à relier").":";
if($action=="addCat") echo _("Veuillez sélectionner la catégorie à relier").": ";
if($nbCategories) {
	$tableCategories = getCatTable($productCategories);
	if($idProduct){
		if($action=="addProduct"){
			echo showProductsLinks($tableCategories,0,$idCatTo,0,"./mod_product.php?action=addProductLink&idProduct=$idProduct&idProductTo=","./list_productsLink.php?action=addProduct&idProduct=$idProduct&idCatTo=",$idProduct,$link);
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idCatTo'</SCRIPT>";
		}
		if($action=="addCat"){
			echo showCategories($tableCategories,0,0,"./mod_product.php?action=addProductCatLink&idProduct=$idProduct&idCatTo=%%ID_CAT%%",false,true);
		}
	}
	if($idCat){
		if($action=="addProduct"){
			echo showProductsLinks($tableCategories,0,$idCatTo,0,"./mod_category.php?action=addProductLink&idCat=$idCat&idProductTo=","./list_productsLink.php?action=addProduct&idCat=$idCat&idCatTo=",0,$link);
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idCatTo'</SCRIPT>";
		}
		if($action=="addCat"){
			echo showCategoriesExcept($tableCategories,0,0,"./mod_category.php?action=addProductCatLink&idCat=$idCat&idCatTo=",false,$idCat);
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
