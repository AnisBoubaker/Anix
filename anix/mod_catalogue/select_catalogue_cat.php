<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_REQUEST["action"])){
	$action = $_REQUEST["action"];
} else $action="";
if(isset($_REQUEST["idProduct"])){
	$idProduct = $_REQUEST["idProduct"];
} else $idProduct=0;
if(isset($_REQUEST["idCat"])){
	$idCat = $_REQUEST["idCat"];
} else $idCat=0;

if($idProduct){
	$id = $idProduct;
} else {
	$id=$idCat;
}
?>
<?
$title=_("Sélection d'une catégorie de produits");
include("../html_header_popup.php");
setTitleBar($title);
$button=array();
$buttons[]=array("type"=>"back","link"=>"javascript:cancel();");
printButtons($buttons);
?>
<?php
$categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$tableCategories = getCatTable($categories);
echo showCategories($tableCategories,0,0,"javascript:chooseCat($id,%%ID_CAT%%,'$action')",false,false);
?>
<script type="text/javascript">
function cancel(){
	window.close();
}
function chooseCat(idProduct,idCat,action){
	opener.chooseCat(idProduct,idCat,action);
	window.close();
}
</script>
<?
include ("../html_footer_popup.php");
mysql_close($link);
?>