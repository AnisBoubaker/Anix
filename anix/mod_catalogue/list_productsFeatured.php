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
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory=0;
if(isset($_POST["idFeatured"])){
	$idFeatured=$_POST["idFeatured"];
} elseif(isset($_GET["idFeatured"])){
	$idFeatured=$_GET["idFeatured"];
} else $idFeatured=0;
?>
<?
$title = _("Anix - Ajout d'un lien pour une vedette");
include("../html_header.php");
switch($action){
	case "addProduct":setTitleBar(_("Liaison de la vedette vers un produit"));break;
	case "addCat":setTitleBar(_("Liaison de la vedette vers une catégorie de produits"));break;
}
$cancelLink = "./mod_featured.php?action=edit&idFeatured=$idFeatured";
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<table border="0" align="center" CellPadding="0" CellSpacing="0" width="95%">
<tr bgcolor='#FFFFFF'>
<td colspan='2'>
<?
$productCategories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$nbCategories = mysql_num_rows($productCategories);
if($action=="addProduct") echo _("Veuillez sélectionner la catégorie  conenant le produit à relier").": ";
if($action=="addCat") echo _("Veuillez sélectionner la catégorie  de produits à relier").": ";
if($nbCategories) {
	$tableCategories = getCatTable($productCategories);
	if($action=="addProduct"){
		echo showProductsLinks($tableCategories,0,$idCat,0,"./mod_featured.php?action=addProductLink&idFeatured=$idFeatured&idProduct=","./list_productsFeatured.php?action=addProduct&idFeatured=$idFeatured&idCat=",0,$link);
		echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idCat'</SCRIPT>";
	}
	if($action=="addCat"){
		echo showCategories($tableCategories,0,0,"./mod_featured.php?action=addProductCatLink&idFeatured=$idFeatured&idCat=%%ID_CAT%%",false,true);
	}
} else {
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie de produits.")."</b></td>";
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
