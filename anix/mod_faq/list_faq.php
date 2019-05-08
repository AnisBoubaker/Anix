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
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?php
include("./list_faq.actions.php");
?>
<?
$title = _("Anix - Liste des FAQ");
include("../html_header.php");
setTitleBar(_("Modification des FAQ"));
?>
<table border="0" align="center" CellPadding="0" CellSpacing="0" width="100%">
<tr bgcolor='#FFFFFF'>
<td colspan='2'>
<?
$categories=request("select $TBL_faq_categories.id, $TBL_faq_categories.id_parent, $TBL_faq_categories.contain_items, $TBL_faq_categories.ordering, $TBL_faq_info_categories.name, $TBL_faq_info_categories.description from  $TBL_faq_categories,$TBL_gen_languages,$TBL_faq_info_categories where $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_faq_categories.id_parent, $TBL_faq_categories.ordering", $link);
$nbCategories = mysql_num_rows($categories);
echo _("Veuillez sélectionner la catégorie contenant la question à modifier:");
if($nbCategories) {
	$tableCategories = getCatTable($categories);
	echo showFaq($tableCategories,0,$idCat,0,"./list_faq.php?idCat=",true,$link);
	echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idCat'</SCRIPT>";
} else {
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie de FAQ")."</b></td>";
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
