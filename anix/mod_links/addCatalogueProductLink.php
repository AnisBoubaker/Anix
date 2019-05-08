<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(!isset($_SESSION["linkCart"]) || !isset($_SESSION["linkCart"]["from"]) || !isset($_SESSION["linkCart"]["fromId"])){
	header("Location: ./index.php?action=error");
	exit();
}
if(isset($_REQUEST["idCatTo"])){
	$idCatTo=$_GET["idCatTo"];
} else $idCatTo=0;
$linkModuleID = 1;
?>
<?
//$title = _("Anix - Ajout d'un lien");
//title is handled in module_config
$title=_("Ajout d'un lien: Produit du catalogue");
include("../html_header_popup.php");
setTitleBar($title);
$button=array();
$buttons[]=array("type"=>"cancel","link"=>"javascript:cancel();");
printButtons($buttons);
?>
<?php
$productCategories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$nbCategories = mysql_num_rows($productCategories);
if($nbCategories) {
	$tableCategories = getCatalogueCatTable($productCategories);
	echo showCatalogueProductsLinks($tableCategories,0,$idCatTo,0,"./addLink.php?action=add&linkType=$linkModuleID&linkTo=","./addCatalogueProductLink.php?idCatTo=",$_SESSION["linkCart"]["fromId"],$link);
	echo "<script type='text/javascript'>location.href='#$idCatTo'</SCRIPT>";
} else {
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie de produits")."</b></td>";
	echo "</tr>";
	echo "</table>";
}
?>
<script type="text/javascript">
function cancel(){
	window.location='./addLink.php';
}
</script>
<?
include ("../html_footer_popup.php");
mysql_close($link);
?>