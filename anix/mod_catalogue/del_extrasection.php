<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat="";
if(isset($_POST["idExtraSection"])){
	$idExtraSection=$_POST["idExtraSection"];
} elseif(isset($_GET["idExtraSection"])){
	$idExtraSection=$_GET["idExtraSection"];
}
?>
<?
$title = _("Anix - Suppression de section additionnelle");
include("../html_header.php");
setTitleBar(_("Suppression d'une section additionnelle"));
?>
<?
$result=request("select $TBL_catalogue_extracategorysection.id,$TBL_catalogue_extracategorysection.id_cat,$TBL_catalogue_info_extracategorysection.name from $TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection,$TBL_gen_languages where $TBL_catalogue_extracategorysection.id=$idExtraSection and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_extracategorysection.id_extrasection=$TBL_catalogue_extracategorysection.id and $TBL_catalogue_info_extracategorysection.id_language=$TBL_gen_languages.id", $link);
$extraSection=mysql_fetch_object($result);
$cancelLink="./mod_category.php?idCat=".$extraSection->id_cat."&action=edit";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>

<form id='main_form' name='del' action='./mod_category.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='deleteExtraSection'>
  <input type='hidden' name='idCat' value='<?=$idCat?>'>
  <input type='hidden' name='idExtraSection' value='<?=$idExtraSection?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Êtes vous sûr de vouloir supprimer la section additionnelle")." \"".$extraSection->name."\""?> ? </font><br><br>
  <I><?php echo _("NB: Les informations contenues dans ce champ seront également supprimées de facon irrécupérable!"); ?></I>
  </b></center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
