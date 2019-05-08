<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$delete=false;
if(isset($_REQUEST["action"])){
	$action=$_REQUEST["action"];
} else $action="";
if(isset($_REQUEST["idCat"])){
	$idCat=$_REQUEST["idCat"];
} else $idCat=0;
?>
<?php
include("./list_products.actions.php");
?>
<?
$title = _("Anix - Liste des produits");
include("../html_header.php");
setTitleBar(_("Modification des produits"));
$button=array();
$buttons[]=array(
	"type"=>"select",
	"text"=>_("Pour les produits sélectionnés"),
	"id"=>"batch_action",
	"choices" => array("0"=>"----", "1"=>_("Copier"), "2"=>_("Déplacer")),
	"link"=>"javascript:doBatch();"
);
printButtons($buttons);
?>
<form id='main_form' action="./list_products.php" method="POST">
<input type='hidden' name='action' id='action' value='' />
<input type='hidden' name='destination' id='destination' value='' />
<table border="0" align="center" CellPadding="0" CellSpacing="0" width="100%">
<tr bgcolor='#FFFFFF'>
<td colspan='2'>
<?
$categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.deletable, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$nbCategories = mysql_num_rows($categories);
if($nbCategories) {
	$tableCategories = getCatTable($categories);
	echo showProducts($tableCategories,0,$idCat,0,"./list_products.php?idCat=",true,$link);
	echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idCat'</SCRIPT>";
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
<script type="text/javascript">
function doBatch(){
	selected = document.getElementById('batch_action').selectedIndex;
	action = document.getElementById('batch_action').options[selected].value;
	if(action==1){
		anixPopup('./select_catalogue_cat.php?action=batchCopy&idProduct=0');
	}
	if(action==2){
		anixPopup('./select_catalogue_cat.php?action=batchMove&idProduct=0');
	}
}
function chooseCat(id,idCatChosen,action){
	alert(action);
	if(action=="moveProduct"){
		document.location='./list_products.php?action=move&idProduct='+id+'&moveto='+idCatChosen;
	}
	if(action=="batchMove"){
		document.getElementById('action').value='batchMove';
		document.getElementById('destination').value=idCatChosen;
		document.getElementById('main_form').submit();
	}
	if(action=="copyProduct"){
		document.location='./list_products.php?action=copy&idProduct='+id+'&copyto='+idCatChosen;
	}
	if(action=="batchCopy"){
		document.getElementById('action').value='batchCopy';
		document.getElementById('destination').value=idCatChosen;
		document.getElementById('main_form').submit();
	}
	if(action=="copyCategory"){
		document.location='./list_products.php?action=copyCategory&idCopy='+id+'&copyto='+idCatChosen+'&idCat=<?php echo $idCat;?>';
	}
	if(action=="moveCategory"){
		document.location='./list_products.php?action=moveCategory&idMove='+id+'&moveto='+idCatChosen+'&idCat=<?php echo $idCat;?>';
	}
}
</script>
<?
include ("../html_footer.php");
mysql_close($link);
?>
