<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_REQUEST["idCatTo"])){
	$idCatTo=$_GET["idCatTo"];
} else $idCatTo=0;
if(isset($_REQUEST["warnStock"])){
	$warnStock = true;
} else $warnStock = false;
?>
<?
//$title = _("Anix - Ajout d'un lien");
//title is handled in module_config
$title=_("Ajout d'un lien: Produit du catalogue");
include("../html_header_popup.php");
setTitleBar($title);
$button=array();
$buttons[]=array("type"=>"back","link"=>"javascript:cancel();");
printButtons($buttons);
?>
<?php
$productCategories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$nbCategories = mysql_num_rows($productCategories);
if($nbCategories) {
	$tableCategories = getCatalogueCatTable($productCategories);
	echo showCatalogueProducts($tableCategories,0,$idCatTo,0,"./select_catalogue_product.php?".($warnStock?"warnStock=1&":"")."idCatTo=",0,$link,$warnStock);
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
	window.close();
}
function addProductConfirm(){
	alert("<?php echo _("Le produit a bien été ajouté.");?>");
}
function stockAlert(id){
	if(confirm("<?php echo _("Ce produit n'est pas en stock, êtes vous sûr de vouloir l'ajouter?"); ?>")){
		addProduct(id);
	}
}
function addProduct(id){
	opener.addProduct(id);
	alert("<?php echo _("Le produit a bien été ajouté.");?>");
}
</script>
<?
include ("../html_footer_popup.php");
mysql_close($link);
?>