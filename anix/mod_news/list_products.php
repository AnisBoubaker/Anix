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
if(isset($_POST["idPrdCat"])){
	$idPrdCat=$_POST["idPrdCat"];
} elseif(isset($_GET["idPrdCat"])){
	$idPrdCat=$_GET["idPrdCat"];
} else $idPrdCat=0;
if(isset($_POST["idNews"])){
	$idNews=$_POST["idNews"];
} elseif(isset($_GET["idNews"])){
	$idNews=$_GET["idNews"];
} else $idNews=0;
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?
$title = _("Anix - Ajout d'un lien Produit");
include("../html_header.php");
switch($action){
	case "addProduct":setTitleBar(_("Ajout d'un produit"));break;
	case "addCat":setTitleBar(_("Ajout d'une catégorie de produits"));break;
}
if($idNews){
	$cancelLink = "./mod_news.php?action=edit&idNews=$idNews";
}
if($idCat){
	$cancelLink = "./mod_category.php?action=edit&idCat=$idCat";
}
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<table border="0" align="center" CellPadding="0" CellSpacing="0" width="95%">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
  </td>
  <td background='../images/button_back.jpg' align='right'>
    <a href='<?=$cancelLink?>'><img src='../locales/<?=$used_language?>/images/button_cancel.jpg' border='0'></a>
  </td>
</tr>
<tr bgcolor='#FFFFFF'>
<td colspan='2'>
<?
$prodCategories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$nbCategories = mysql_num_rows($prodCategories);
if($action=="addProduct") echo _("Veuillez sélectionner la catégorie contenant le produit à relier").":";
if($action=="addCat") echo _("Veuillez sélectionner la catégorie de produits à relier").":";
if($nbCategories) {
	$tableCategories = getPrdCatTable($prodCategories);
	if($idNews){
		if($action=="addProduct"){
			echo showProducts($tableCategories,0,$idPrdCat,0,"./mod_news.php?action=addProduct&idNews=$idNews&idProduct=","./list_products.php?action=addProduct&idNews=$idNews&idPrdCat=",$link);
			echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idPrdCat'</SCRIPT>";
		}
		if($action=="addCat"){
			echo showPrdCategories($tableCategories,0,0,"./mod_news.php?action=addPrdCat&idNews=$idNews&idPrdCat=",false);
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
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'>&nbsp;</font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
    <a href='<?=$cancelLink?>'><img src='../locales/<?=$used_language?>/images/button_cancel.jpg' border='0'></a>
  </td>
</tr>
</table>
<?
include ("../html_footer.php");
mysql_close($link);
?>

