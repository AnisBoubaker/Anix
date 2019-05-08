<?php
require_once("../config.php");
require_once("./module_config.php");
require_once("./mod_porder.xcommon.php");

if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="add";
//Get the supplier ID
if(isset($_REQUEST["idSupplier"])){
	$idSupplier=$_REQUEST["idSupplier"];
} else $idSupplier=0;
//Get the porder ID
if(isset($_REQUEST["idPOrder"])){
	$idPOrder=$_REQUEST["idPOrder"];
} else $idPOrder="0";
?>
<?php
require_once("./mod_porder.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Nouvelle commande d'achat");
elseif($action=="edit" || $action=="update") $title = _("Modification d'une commande d'achat");
else $title = _("Modification d'une commande d'achat");
$module_name="ecommerce";include("../html_header.php");

setTitleBar($title);
?>
<?php
if($action=="edit" || $action=="update" ){
	$porder = new EcommercePOrder($idPOrder);
	$supplier = new EcommerceSupplier($porder->getSupplierId());
}
if($action=="add" || $action=="insert"){
	$porder = new EcommercePOrder(0,$idSupplier);
	$supplier = new EcommerceSupplier($idSupplier);
}

?>
<form id='main_form' action='./mod_porder.php' method='POST' enctype='multipart/form-data' name='main_form'>
<?php
echo "<input type='hidden' name='idPOrder' value='".$porder->getId()."'>";
echo "<input type='hidden' name='idSupplier' value='".$porder->getSupplierId()."'>";
echo "<input type='hidden' name='action' value='".($action=="add" || $action=="insert"?"insert":"update")."'>";
$cancelLink="./view_supplier.php?idSupplier=".$supplier->getId();
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>

<table style='width:100%'>
<tr>
	<td style='vertical-align:top; width:180px;'>
	<?php
	echo "<h3 style='margin:0;'>";
		if($action=="add" || $action=="insert") echo _("Nouveau bon d'achat");
		if($action=="edit" || $action=="update") echo _("Bon d'achat")." #".$porder->getId();
		echo "</h3>";
	?>
	</td>
	<td style='vertical-align:top;'>
		<?php
		/**
		 * LOAD TABS
		 */
		include("./mod_porder.tabs.php");
		if(isset($ANIX_TabSelect)) TABS_changeTab($ANIX_TabSelect);
		?>
	</td>
</tr>
</table>

<div class='invoice_items'>
<table id='invoice_items' class='invoice_items' style='width:700px;'>
<thead>
<tr>
	<td colspan="7" style='border:0;'>
	<input type='button' value="<?php echo _("Ajouter une ligne"); ?>" onclick="javascript:addPOrderLineItem('','','','','','');" />&nbsp;
	<input type='button' value="<?php echo _("Ajouter des produits du catalogue"); ?>" onclick='javascript:anixPopup("./select_catalogue_product.php");' />
	</td>
</tr>
<tr>
	<th><?php echo _("QTÉ"); ?></th>
	<th><?php echo _("REÇU"); ?></th>
	<th><?php echo _("RÉF"); ?></th>
	<th><?php echo _("RÉF. FOURNISSEUR"); ?></th>
	<th><?php echo _("DESCRIPTION"); ?></th>
	<th><?php echo _("PRIX"); ?></th>
	<th><?php echo _("TOTAL"); ?></th>
	<th></th>
</tr>
</thead>
<tbody>
<?php
if($action=="update" || $action=="insert"){//Display the new rows
	if($_POST["nb_new_lines"]>0){
		$newLines = $_POST["nb_new_lines"];
		for($i=$newLines-1;$i>=0;$i--) if(isset($_POST["newrow".$i."_qty"])){
			echo "<tr id='newrow$i'>";
			echo "<td>";
			echo "<input type='text' name='newrow".$i."_qty' style='width:50px' Maxlength='10' class='price' value='".htmlentities($_POST["newrow".$i."_qty"],ENT_QUOTES,"UTF-8")."' />";
			echo "</td>";
			echo "<td>";
			echo "&nbsp;";
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='newrow".$i."_refStore' style='width:80px' Maxlength='50' value='".unhtmlentities($_POST["newrow".$i."_refStore"])."' ";
			if($_POST["newrow".$i."_product"]!="") echo " readonly='readonly'";
			echo " />";
			echo "<input type='hidden' name='newrow".$i."_product' value=\"".$_POST["newrow".$i."_product"]."\" />";
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='newrow".$i."_refSupplier' style='width:80px' Maxlength='50' value='".unhtmlentities($_POST["newrow".$i."_refSupplier"])."' />";
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='newrow".$i."_description' style='width:400px;' value='".unhtmlentities($_POST["newrow".$i."_description"])."'>";
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='newrow".$i."_uprice' style='width:80px' Maxlength='20' class='price' value='".htmlentities($_POST["newrow".$i."_uprice"],ENT_QUOTES,"UTF-8")."'>";
			echo "</td>";
			echo "<td>";
			echo "&nbsp;";
			echo "</td>";
			echo "<td style='vertical-align:middle;'>";
			echo "<img src='../images/del.gif' border='0' alt='Supprimer' onClick='javascript:delItem(0,0,$i)' />";
			echo "</td>";
			echo "</tr>";
		}
	}
}
?>
<?
if($action=="edit" || $action=="update"){
?>
	<?
	$items = $porder->loadItems();
	foreach($items as $item){
		$subtotal=0;
		echo "<tr>";
		echo "<td>";
		echo "<input type='text' name='qty_".$item->getId()."' style='width:50px' Maxlength='10' class='price' ";
		if($action=="edit") echo "value='".$item->getQty()."' />";
		if($action=="update") echo "value='".htmlentities($_POST["qty_".$item->getId()],ENT_QUOTES,"UTF-8")."' />";
		echo "</td>";
		echo "<td>";
		if($porder->getStatus()=="ordered" || $porder->getStatus()=="received"){
			echo "<input type='text' name='receivedqty_".$item->getId()."' style='width:50px' Maxlength='10' class='price' ";
			if($action=="edit") echo "value='".$item->getReceivedQty()."'>";
			if($action=="update") echo "value='".htmlentities($_POST["receivedqty_".$item->getId()],ENT_QUOTES,"UTF-8")."' />";
		} else {
			echo "--";
		}
		echo "</td>";
		echo "<td>";
		echo "<input type='text' name='refStore_".$item->getId()."' style='width:80px' Maxlength='50' ";
		if ($action=="edit"){
			echo "value='".unhtmlentities($item->getStoreRef())."'";
			if($item->getIdProduct()!=0) echo " readonly='readonly'";
		}
		if ($action=="update"){
			echo "value='".unhtmlentities($_POST["refStore_".$item->getId()])."'";
			if($_POST["product_".$item->getId()]!="") echo " readonly='readonly'";
		}
		echo " />";
		echo "<input type='hidden' name='product_".$item->getId()."' value=\"";
		if($action=="edit") echo $item->getIdProduct();
		if($action=="update") echo $_POST["product_".$item->getId()];
		echo "\" />";
		echo "</td>";
		echo "<td>";
		echo "<input type='text' name='refSupplier_".$item->getId()."' style='width:80px' Maxlength='50' ";
		if ($action=="edit"){
			echo "value='".unhtmlentities($item->getSupplierRef())."'";
		}
		if ($action=="update"){
			echo "value='".unhtmlentities($_POST["refSupplier_".$item->getId()])."'";
		}
		echo " />";
		echo "<input type='hidden' name='product_".$item->getId()."' value=\"";
		if($action=="edit") echo $item->getIdProduct();
		if($action=="update") echo $_POST["product_".$item->getId()];
		echo "\" />";
		echo "</td>";
		echo "<td>";
		echo "<input type='text' name='description_".$item->getId()."' style='width:400px;' ";
		if($action=="edit") echo "value='".unhtmlentities($item->getDescription())."'>";
		if($action=="update") echo "value='".unhtmlentities($_POST["description_".$item->getId()])."'>";
		echo "</td>";
		echo "<td>";
		echo "<input type='text' name='uprice_".$item->getId()."' style='width:80px' Maxlength='20' class='price' ";
		if($action=="edit") echo "value='".htmlentities($item->getUprice(),ENT_QUOTES,"UTF-8")."'>";
		if($action=="update") echo "value='".htmlentities($_POST["uprice_".$item->getId()],ENT_QUOTES,"UTF-8")."'>";
		echo "</td>";
		echo "<td>";
		if($action=="edit") echo $item->getTotal();
		if($action=="update"){
			echo number_format($_POST["uprice_".$item->getId()]*$_POST["qty_".$item->getId()],2);
            $subtotal+=$_POST["uprice_".$item->getId()]*$_POST["qty_".$item->getId()];
		}
		echo "</td>";
		echo "<td style='vertical-align:middle;'>";
		if($action=="edit") echo "<img src='../images/del.gif' border='0' alt='Supprimer' onClick='javascript:delItem(".$item->getId().",\""._("Etes vous sur de vouloir supprimer cette ligne ?")."\",-1)' />";
		else echo "&nbsp;";
		echo "</td>";
		echo "</tr>";
	}
	?>
	<tr>
		<td colspan="6" style='text-align:right;'><b><?php echo _("Sous-total");?>:</b></td>
		<td colspan="2"><b><?php if($action=="edit") echo $porder->getSubtotal();?></b></td>
	</tr>
<?php
} //if action == $edit || update

if($action=="edit") $stockNeedsUpdate=$porder->isStockNeedsUpdate();
else $stockNeedsUpdate=false;
?>
</tbody>
</table><!-- invoice items table -->
</div> <!-- invoice_items -->

<input type='hidden' name='nb_new_lines' id='nb_new_lines' value='<?php echo (isset($_POST["nb_new_lines"])?$_POST["nb_new_lines"]:"0");?>' />
<input type='hidden' name='delete_line' id='delete_line' value='0' />
</form>
<br />
<script type="text/javascript">
function showHideSentDate(){
	if(document.getElementById('order_sent').checked){
		document.getElementById('sent_date').style.display='';
	} else {
		document.getElementById('sent_date').style.display='none';
	}
}

showHideSentDate();

function addProduct(id){
	setTimeout('xajax_addProduct('+id+',<?php echo $supplier->getId();?>)', 500);
}

<?php
if($action=="edit" && $stockNeedsUpdate){
	echo "confirmStockUpdate(\""._("Votre stock ne tient pas compte de certains produits de cette commande. Voulez-vous mettre à jour le stock maintenant?")."\",".$porder->getId().",0);";
}
?>
</script>
<?
include ("../html_footer.php");
mysql_close($link);
?>