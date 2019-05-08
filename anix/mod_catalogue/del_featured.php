<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idFeatured"])){
	$idFeatured=$_POST["idFeatured"];
} elseif(isset($_GET["idFeatured"])){
	$idFeatured=$_GET["idFeatured"];
}
?>
<?
$title = _("Anix - Suppression d'une vedette");
include("../html_header.php");
setTitleBar(_("Suppression d'une vedette"));
?>

<?
$result=request("select $TBL_catalogue_info_featured.title from $TBL_catalogue_info_featured,$TBL_gen_languages where $TBL_catalogue_info_featured.id_featured='$idFeatured' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_featured.id_language=$TBL_gen_languages.id", $link);
$featured=mysql_fetch_object($result);
$cancelLink="./list_featured.php";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>

<form id='main_form' name='del' action='./list_featured.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='delete'>
  <input type='hidden' name='idFeatured' value='<?=$idFeatured?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Êtes vous sûr de vouloir supprimer la vedette"); ?>: </font><br><br>"<?=$featured->title?>" ?
  </b></center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>

