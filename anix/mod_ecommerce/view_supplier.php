<?
include ("../config.php");
include ("./module_config.php");

if(isset($_REQUEST["action"])){
	$action=$_REQUEST["action"];
} else $idSupplier=0;
if(isset($_REQUEST["idSupplier"])){
	$idSupplier=$_REQUEST["idSupplier"];
} else $idSupplier=0;
if($idSupplier) try{
	$supplierObj = new EcommerceSupplier($idSupplier);
} catch (Exception $e){
	$ANIX_messages->addError($e->getMessage());
	$supplierObj=new EcommerceSupplier();
}
?>
<?php
include("./view_supplier.actions.php");
?>
<?
$title = _("Fiche Fournisseur");
$module_name="ecommerce";
include("../html_header.php");
$cancelLink="./list_suppliers.php";
setTitleBar($title);
$button=array();
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>
<table style='width:100%'>
<tr>
<td style='vertical-align:top; width:180px;'>
	<h3><?php echo _("Fournisseur")." #".$supplierObj->getId();?></h3>
	<input type="button" value="<?php echo _("Nouvelle commande"); ?>" onclick="document.location='./mod_porder.php?action=add&idSupplier=<?php echo $supplierObj->getId();?>'" />
</td>
<td style='vertical-align:top;'>
	<?php
	/**
	 * LOAD TABS
	 */
	include("./view_supplier.tabs.php");
	if(isset($ANIX_TabSelect)) TABS_changeTab($ANIX_TabSelect);
	?>
</td>
</tr>
</table>
<!-- ORDERS LIST -->
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
<td background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'> <?php
    if(isset($_GET["history"])) echo _("Commandes");
    else echo _("Commandes en cours");
    ?></font>
</td>
<td background='../images/button_back.jpg' align='right'>
    &nbsp;
</td>
</tr>
</table>
<table style='width:100%;background:#ffffff;text-align:center;'>
<?php
try{
	$porders = new EcommercePOrdersList($supplierObj->getId());
} catch (Exception $e){
	$ANIX_messages->addError($e->getMessage());
}
if(!$ANIX_messages->nbErrors && $porders->getNbOrders()){
?>
<tr>
    <td style='width:40px;'>&nbsp;</td>
    <td align='center'><b>#<?php echo _("Commande");?></b></td>
    <td align='center'><b><?php echo _("État");?></b></td>
    <td align='center'><b><?php echo _("Créée le");?></b></td>
    <td align='center'><b><?php echo _("Réception prévue");?></b></td>
    <td align='center'><b><?php echo _("Montant total");?></b></td>
</tr>
<?php
foreach ($porders as $porder){
	$today = date("Y-m-d");
	if($porder->getStatus()=="ordered" && $today>$porder->getExpectedReceptionDate()) $warning = "color:#ff0000;font-weight:bold;";
	else $warning="";
	echo "<tr>";
	echo "<td style='background:#e7eff2;border-bottom:1px solid #000000;text-align:left;'>";
	echo "<a href='javascript:void(0);' onclick='javascript:confirmDeleteOrder(".$porder->getId().")'><img src='../images/del.gif' /></a>";
	echo "<a href='./mod_porder.php?action=edit&idPOrder=".$porder->getId()."'><img src='../images/edit.gif' /></a>";
	echo "</td>";
	echo "<td style='border-bottom:1px solid #000000;$warning'><a href='./mod_porder.php?action=edit&idPOrder=".$porder->getId()."'>".id_format($porder->getId())."</a></td>";
	echo "<td style='border-bottom:1px solid #000000;$warning'>";
	switch($porder->getStatus()){
		case "created": echo _("Nouvelle commande");break;
		case "ordered": echo _("Commandée");break;
		case "received": echo _("Reçue");break;
	}
	echo "</td>";
	echo "<td style='border-bottom:1px solid #000000;$warning'>".$porder->getOrderDate()."</td>";
	echo "<td style='border-bottom:1px solid #000000;$warning'>".$porder->getExpectedReceptionDate()."</td>";
	echo "<td style='border-bottom:1px solid #000000;$warning'>".$porder->getSubtotal()."</td>";
	echo "</tr>";
}
?>

<?php
} else { //NO ORDERS FOUND FOR THIS SUPPLIER
?>
<tr>
<td style="text-align:center;padding:30px 0;"><i><?php echo _("Aucune commande à afficher"); ?></i></td>
</tr>
<?php
}
?>
</table>
<script type="text/javascript">
function confirmDeleteOrder(id){
	if(confirm("<?php echo _("Êtes vous sûr de vouloir supprimer la commande d'achat?\\n\\nNB:Les inscriptions au stock relatives à cette commande seront effacées.");?>")) document.location='./view_supplier.php?idSupplier=<?php echo $idSupplier;?>&action=deletePOrder&idPOrder='+id;
}
</script>
<?
include ("../html_footer.php");
?>
