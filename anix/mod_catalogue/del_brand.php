<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idBrand"])){
	$idBrand=$_POST["idBrand"];
} elseif(isset($_GET["idBrand"])){
	$idBrand=$_GET["idBrand"];
}
?>
<?php
$title = _("Anix - Suppression d'une marque");
include("../html_header.php");
setTitleBar(_("Suppression d'une marque"));
?>

<?
$result=request("select $TBL_catalogue_brands.id,$TBL_catalogue_brands.name from $TBL_catalogue_brands where $TBL_catalogue_brands.id='$idBrand'", $link);
$brand=mysql_fetch_object($result);
$cancelLink="./list_brands.php";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>

<form id='main_form' name='del' action='./list_brands.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='delete'>
  <input type='hidden' name='idBrand' value='<?=$idBrand?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Êtes vous sûr de vouloir supprimer la marque")." \"".$brand->name."\""?> ? </font><br><br>
  <I><?php echo _("NB: Cette action est irrécupérable. De plus, les produits de cette marque auront une marque indéfinie après la suppression."); ?></I>
  </b></center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>

