<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat="";
if(isset($_POST["idExtraField"])){
	$idExtraField=$_POST["idExtraField"];
} elseif(isset($_GET["idExtraField"])){
	$idExtraField=$_GET["idExtraField"];
}
?>
<?
$title = _("Anix - Suppression de champs additionnel");
include("../html_header.php");
setTitleBar(_("Suppression d'un champs additionnel de produits"));
?>

<?
$result=request("select $TBL_catalogue_extrafields.id,$TBL_catalogue_extrafields.datatype,$TBL_catalogue_extrafields.id_cat,$TBL_catalogue_extrafields.params,$TBL_catalogue_info_extrafields.name from $TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields,$TBL_gen_languages where $TBL_catalogue_extrafields.id=$idExtraField and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields.id and $TBL_catalogue_info_extrafields.id_language=$TBL_gen_languages.id", $link);
$extraField=mysql_fetch_object($result);
$cancelLink="./mod_category.php?idCat=".$extraField->id_cat."&action=edit";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>

<form id='main_form' name='del' action='./mod_category.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='deleteExtraField'>
  <input type='hidden' name='idCat' value='<?=$idCat?>'>
  <input type='hidden' name='idExtraField' value='<?=$idExtraField?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Êtes vous sûr de vouloir supprimer le champs"); ?> "<?=$extraField->name?>" ? </font><br><br>
  <?
  if($extraField->id_cat){
  	echo "<I>"._("NB: Les informations contenues dans ce champs seront également supprimées pour tous les produits de la catégorie!")."</I>";
  }
  ?>
  </b></center><br />
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
