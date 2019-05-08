<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?
$title = _("Anix - Suppression d'une catégorie");
include("../html_header.php");
setTitleBar(_("Suppression d'une catégorie"));
?>
<form id='main_form' action='./list_categories.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='action' value='deletecat'>
<input type='hidden' name='idCat' value='<?=$idCat?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_faq_categories.id,$TBL_faq_info_categories.name FROM $TBL_faq_categories,$TBL_faq_info_categories,$TBL_gen_languages where $TBL_faq_categories.id=$idCat and $TBL_gen_languages.id='$used_language_id' and $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id",$link);
$category=mysql_fetch_object($request);
$request = request("SELECT COUNT(*) as nb FROM $TBL_faq_categories where id_parent='$idCat' group by id_parent",$link);
$nbSubcats = mysql_fetch_object($request);
$request = request("SELECT COUNT(*) as nb FROM $TBL_faq_faq where id_category='$idCat' group by id_category",$link);
$nbNews = mysql_fetch_object($request);
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>'./list_categories.php?action=edit');
printButtons($buttons);
?>
<td colspan='2'>
<?
if(!$category){
	echo "<i><center>"._("Erreur : Cette catégorie n'existe pas")."</center></i>";
} else {
?>
<center><b><font color='red'><?php echo _("Êtes vous sr de vouloir supprimer cette catégorie ?"); ?></font></b></center><br><br>
<?php echo _("Si oui, veuillez sélectionner la méthode de suppression puis cliquez sur \"OK\". Sinon, cliquez simplement sur le bouton \"Annuler\".");?><br><br>
<table width='60%' align='center'>
<tr>
  <td><b><?php echo _("Nom de la catégorie:"); ?></b></td>
  <td><?=$category->name?></td>
</tr>
<tr>
  <td><b><?php echo _("Nombre de sous catégories directes:"); ?></b></td>
  <td><?=$nbSubcats->nb?></td>
</tr>
<tr>
  <td><b><?php echo _("Nombre de questions directes:");?></b></td>
  <td><?=$nbNews->nb?></td>
</tr>
</table>
<table width='100%'>
<tr>
  <td valign='top'>
    <input type='radio' name='method' value='delete'>
  </td>
  <td>
    <?php echo _("Supprimer la catégorie ainsi que toutes les sous-catégories et questions qu'elle contient."); ?>
  </td>
</tr>
<tr>
  <td valign='top'>
    <input type='radio' name='method' value='move'>
  </td>
  <td>
    <?php echo _("Supprimer la catégorie après avoir déplacé ses sous-catégories et questions vers la catégorie :"); ?><br>
	<SELECT name='moveto'>
	  <option value='0'>-- <?php echo _("Choisissez"); ?> --</option>
	  <?
	  $categories=request("select $TBL_faq_categories.id, $TBL_faq_categories.id_parent, $TBL_faq_categories.ordering, $TBL_faq_info_categories.name, $TBL_faq_info_categories.description from  $TBL_faq_categories,$TBL_gen_languages,$TBL_faq_info_categories where $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_faq_categories.id_parent, $TBL_faq_categories.ordering", $link);
	  $tableCategories = getOtherCatTable($categories,$idCat);
	  $listCategories = getCategoriesList($tableCategories,0 , 0);
	  foreach($listCategories as $row){
	  	echo "<option value='".$row["id"]."'>".$row["name"]."</option>";
	  }
	  ?>
	</SELECT>
  </td>
</tr>
</table>
<?
} //else !$category
?>
</td>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
