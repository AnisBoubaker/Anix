<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idProduct"])){
	$idProduct=$_POST["idProduct"];
} elseif(isset($_GET["idProduct"])){
	$idProduct=$_GET["idProduct"] ;
} else $idProduct=0;
if(isset($_REQUEST["action"])){
	$action= $_REQUEST["action"];
} else $action="copy";
?>
<?php
switch($action){
	case "copy":$title = _("Copie d'un produit"); break;
	case "move":$title = _("Déplacement d'un produit"); break;
}
include("../html_header.php"); ?>
<TABLE border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?php
$request = request("SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.id_category,$TBL_catalogue_products.active,$TBL_catalogue_info_products.name FROM $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages where $TBL_catalogue_products.id=$idProduct and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id",$link);
$product = mysql_fetch_object($request);
setTitleBar($title);
$cancelLink="./list_products.php?action=edit";
if($product) $cancelLink.="&idCat=".$product->id_category."#".$product->id_category;
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<TR> <TD colspan='2'>
<?
if(!$product){
	echo "<i><CENTER>"._("Erreur : Ce produit n'existe pas.")."</CENTER></I>";
} else {
?>
<?php
switch ($action){
	case "copy": echo _("Veuillez sélectionner la catégorie où vous souhaitez copier le produit");break;
	case "move": echo _("Veuillez sélectionner la catégorie où vous souhaitez déplacer le produit");break;
}
?>
<?php
$categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$tableCategories = getCatTable($categories);
echo showCategories($tableCategories,0,0,"./list_products.php?action=copy&idProduct=$idProduct&copyto=%%ID_CAT%%",false,false);
?>
<?
} //else !$product
?>
</TD></TR></TABLE>
<?
include ("../html_footer.php");
mysql_close($link);
?>
