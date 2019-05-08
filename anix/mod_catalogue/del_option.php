<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idProduct"])){
	$idProduct=$_POST["idProduct"];
} elseif(isset($_GET["idProduct"])){
	$idProduct=$_GET["idProduct"];
} else $idProduct=0;
if(isset($_POST["idOption"])){
	$idOption=$_POST["idOption"];
} elseif(isset($_GET["idOption"])){
	$idOption=$_GET["idOption"];
}
?>
<?
$title = _("Anix - Suppression d'une option de produit");
include("../html_header.php");
setTitleBar(_("Suppression d'une option pour le produit"));
?>

<?
$result=request("select $TBL_catalogue_info_options.name optionName,$TBL_catalogue_info_products.name productName from `$TBL_catalogue_info_options`,`$TBL_catalogue_info_products`,`$TBL_gen_languages` where $TBL_catalogue_info_options.id_option='$idOption' AND $TBL_catalogue_info_products.id_product='$idProduct' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_options.id_language=$TBL_gen_languages.id AND $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id",$link);
$option=mysql_fetch_object($result);
$cancelLink = "./mod_product.php?action=edit&idProduct=$idProduct";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>

  <form id='main_form' name='del' action='./mod_product.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='idProduct' value='<?=$idProduct?>'>
  <input type='hidden' name='idOption' value='<?=$idOption?>'>
  <input type='hidden' name='action' value='delOption'>
<table border="0" align="center" width="70%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Êtes vous sûr de vouloir supprimer l'option"); ?>: </font></b><br><br>
  <?
  echo "<b>"._("Produit").": </b> ".$option->productName."<br><br>"."<b>"._("Option").":</b> ".$option->optionName;
  ?>
  </center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>

