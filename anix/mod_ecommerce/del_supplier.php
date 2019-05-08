<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idSupplier"])){
	$idSupplier=$_POST["idSupplier"];
} elseif(isset($_GET["idSupplier"])){
	$idSupplier=$_GET["idSupplier"];
}
?>
<?
$title = _("Suppression d'un fournisseur");
include("../html_header.php");
setTitleBar(_("Suppression d'un fournisseur"));
?>

<?
$result=request("select $TBL_ecommerce_supplier.id,$TBL_ecommerce_supplier.name from $TBL_ecommerce_supplier where $TBL_ecommerce_supplier.id='$idSupplier'", $link);
$supplier=mysql_fetch_object($result);
$cancelLink="./list_suppliers.php";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>

<form id='main_form' name='del' action='./list_suppliers.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='delete'>
  <input type='hidden' name='idSupplier' value='<?=$idSupplier?>'>
<table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Êtes vous sûr de vouloir supprimer la fournisseur");?> "<?php echo $supplier->name?>" ? </font><br><br>
  <I><?php echo _("NB: Cette action est irrécupérable. De plus, toute référence à ce fournisseur sera effacée des produits du catalogue."); ?></I>
  </b></center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>

