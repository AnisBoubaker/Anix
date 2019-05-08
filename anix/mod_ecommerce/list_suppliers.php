<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="";
?>
<?php
include("./list_suppliers.actions.php");
?>
<?
$title = "Liste des fournisseurs";
include("../html_header.php");
setTitleBar(_("Liste des forunisseurs"));
$button=array();
$buttons[]=array("type"=>"additem","link"=>"./mod_supplier.php?action=add");
printButtons($buttons);
?>
<table style='margin:0 auto;width:700px;background:#ffffff;'>
<?php
$suppliers = new EcommerceSuppliersList();
foreach($suppliers as $supplier){
	echo "<tr style=''>";
	echo "<td style='width:60px;border-bottom:1px solid #000000;background:#e7eff2;'>";
		echo "<a href='javascript:void(0);' onclick='confirmDeleteSupplier(".$supplier->getId().")'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la marque")."\"></a>";
		echo "&nbsp;<a href='./mod_supplier.php?action=edit&idSupplier=".$supplier->getId()."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier les informations du fournisseur")."\"></a>";
		echo "&nbsp;<a href='./view_supplier.php?idSupplier=".$supplier->getId()."'><img src='../images/view.gif' border='0' alt=\""._("Fiche Fournisseur")."\"></a>";
	echo "</td>";
	echo "<td style='border-bottom:1px solid #000000;'><a href='./view_supplier.php?idSupplier=".$supplier->getId()."'>".$supplier->getName()."</a></td>";
	echo "</tr>";
}
?>
</table>
<script type="text/javascript">
function confirmDeleteSupplier(id){
	if(confirm("<?php echo _("Êtes vous sûr de vouloir supprimer ce fournisseur?\\n\\nNB:Les commandes non reçues du fournisseur seront également effacées et le stock sera mis à jour. De plus, les commandes reçues du fournisseur ne seront plus accessibles.");?>")) document.location='./list_suppliers.php?action=deleteSupplier&idSupplier='+id;
}
</script>
<?
include ("../html_footer.php");
mysql_close($link);
?>
