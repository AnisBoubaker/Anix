<?
/**
    * JE SUIS ICI !!!!! ou ca ici? chez second cup? jadore ca etre chez second cup avec toi! et en plus maintenant que je vais a hec jai plein de trucs
    * a etudier et genre...ouais ouais je taime plus que tout et genre jai super hate de ta marier et quon habite ensemble. bon tu es en train de faire
    * ton drole en mesurant mon nez.  ce que tu as pas compris cest que ton doigt est tellement gros que genre tu peux meme pas mesure quelque
    * chose avec. donc la marge derreur est genre de 30% la...
    * ok on y va. sti que tu sens bon en passant. je suis en train de tembrasser ok bye
    **/
?>
<?
require_once("../config.php");
require_once("./module_config.php");
require_once("./mod_invoice.xcommon.php");

$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
//Get the client ID
if(isset($_POST["idOrder"])){
	$idOrder=$_POST["idOrder"];
} elseif(isset($_GET["idOrder"])){
	$idOrder=$_GET["idOrder"];
} else $idOrder=0;
if(isset($_POST["idInvoice"])){
	$idInvoice=$_POST["idInvoice"];
} elseif(isset($_GET["idInvoice"])){
	$idInvoice=$_GET["idInvoice"];
} else $idInvoice=0;
//If the client ID was not set, send to client selection page
if($action=="add" && !$idOrder){
	Header("Location: ./choose_order.php");
	exit();
}
?>
<?php
require_once("./mod_invoice.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Nouvelle facture");
elseif($action=="edit" || $action=="update") $title = _("Modification d'une facture");
else $title = _("Modification d'une facture");
$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php");
setTitleBar($title);
//to let us use the javascript calendar to pickup dates.
?>
<form action='./mod_invoice.php' method='POST' enctype='multipart/form-data' name='delForm'>
  <input type='hidden' name='idInvoice' value='<?=$idInvoice?>'>
  <input type='hidden' name='idItem' value=''>
  <input type='hidden' name='action' value='deleteLine'>
</form>
<form id='main_form' action='./mod_invoice.php' method='POST' enctype='multipart/form-data' name='main_form'>
<?
if($action=="edit" || $action=="update"){
	$result=request("SELECT *
                       FROM $TBL_ecommerce_invoice where `id`='$idInvoice'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette facture n'existe pas."));
	$edit = mysql_fetch_object($result);
	$idClient = $edit->id_client;
	$idOrder = $edit->id_order;
	$result = request("SELECT * from `$TBL_ecommerce_order` WHERE `id`='$idOrder'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette commande n'existe pas."));
	$order = mysql_fetch_object($result);
}
if($action=="add" || $action=="insert"){
	$result = request("SELECT * from `$TBL_ecommerce_order` WHERE `id`='$idOrder'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette commande n'existe pas."));
	$edit = mysql_fetch_object($result);
	$idClient = $edit->id_client;
}

if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idInvoice' value='$idInvoice'>";
	echo "<input type='hidden' name='idOrder' value='$idOrder'>";
	echo "<input type='hidden' name='action' value='insert'>";
	$cancelLink="./choose_order.php?target=invoice";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idInvoice' value='$idInvoice'>";
	echo "<input type='hidden' name='idOrder' value='$idOrder'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./view_client.php?idClient=$idClient";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);

//Get client information
$result=request("SELECT * FROM $TBL_ecommerce_customer where `id`='$idClient'",$link);
if(!mysql_num_rows($result)) die(_("Erreur de protection: Ce client n'existe pas."));
$client = mysql_fetch_object($result);
if($action=="add"){
	$order_mailing = $edit->mailing_address;
	$invoice_billing = $edit->billing_address; //From order if we add or from invoice if we edit
} else {
	$order_mailing = $order->mailing_address;
	$invoice_billing = $edit->billing_address; //From order if we add or from invoice if we edit
}
?>
<table style='width:100%'>
<tr>
	<td style='vertical-align:top; width:180px;'><!--Col1-->
		<?php
		echo "<h3 style='margin:0;'>";
		if($action=="add" || $action=="insert") echo _("Nouvelle facture");
		if($action=="edit" || $action=="update") echo _("Facture")." #".($edit->refund=="Y"?"R":"")."$idInvoice ";
		echo "</h3>";
		if($action=="edit"){
			echo "<br /><input type='button' onclick=\"javascript:window.location='./issue_invoice.php?action=issue_confirm&idInvoice=$idInvoice'\" value=\""._("Émettre")."\">";
		} //if action == edit
		?>
	</td><!--Col1-->
	<td><!--Col2-->
		<?php
		/**
		 * LOAD TABS
		 */
		include("./mod_invoice.tabs.php");
		if(isset($ANIX_TabSelect)) TABS_changeTab($ANIX_TabSelect);
		?>
	</td><!--Col2-->
</tr>
</table>
<div class='invoice_items'>
<table id='invoice_items' class='invoice_items' style='width:700px;'>
<thead>
<tr>
	<td colspan="7" style='border:0;'>
	<input type='button' value="<?php echo _("Ajouter une ligne"); ?>" onclick="javascript:addLineItem('','','','','');" />&nbsp;
	<input type='button' value="<?php echo _("Ajouter des produits du catalogue"); ?>" onclick='javascript:anixPopup("./select_catalogue_product.php?warnStock=1");' />
	</td>
</tr>
<tr>
	<th><?php echo _("QTÉ"); ?></th>
	<th><?php echo _("CODE"); ?></th>
	<th><?php echo _("DESCRIPTION"); ?></th>
	<th><?php echo _("PRIX"); ?></th>
	<th><?php echo _("TOTAL"); ?></th>
	<th></th>
</tr>
</thead>
<tbody>
<?php
if ($action=="update" || $action=="insert"){//Display the new rows
	if($_POST["nb_new_lines"]>0){
		$newLines = $_POST["nb_new_lines"];
		for($i=$newLines-1;$i>=0;$i--) if(isset($_POST["newrow".$i."_qty"])){
			echo "<tr id='newrow$i'>";
			echo "<td>";
			echo "<input type='text' name='newrow".$i."_qty' style='width:50px' Maxlength='10' class='price' value='".htmlentities($_POST["newrow".$i."_qty"],ENT_QUOTES,"UTF-8")."' />";
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='newrow".$i."_reference' style='width:80px' Maxlength='50' value='".unhtmlentities($_POST["newrow".$i."_reference"])."' ";
			if($_POST["newrow".$i."_product"]!="") echo " readonly='readonly'";
			echo " />";
			echo "<input type='hidden' name='newrow".$i."_product' value=\"".$_POST["newrow".$i."_product"]."\" />";
			echo "</td>";
			echo "<td>";
			echo "<input type='text' name='newrow".$i."_description' style='width:400px;' value='".unhtmlentities($_POST["newrow".$i."_description"])."'>";
			echo "<br><TEXTAREA class='mceNoEditor' name='newrow".$i."_details' style='width:400px;' rows='5'>";
			echo unhtmlentities($_POST["newrow".$i."_details"]);
			echo "</TEXTAREA>";
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
$subtotal = 0;
//retrieves line items if edit mode
if($action=="edit" || $action=="update"){
	$items = request("SELECT * from `$TBL_ecommerce_invoice_item`
                      WHERE id_invoice='$edit->id' ORDER BY `id`",$link);
}
if($action=="add" || $action=="insert"){ //Load the items from the order
	$items = request("SELECT * from `$TBL_ecommerce_invoice_item`
                              WHERE id_order='$idOrder' ORDER BY `id`",$link);
}

$stockNeedsUpdate=false;

if(mysql_num_rows($items)) while($item=mysql_fetch_object($items)){
	echo "<tr>";
	echo "<td>";
	echo "<input type='text' name='qty_".$item->id."' style='width:50px' Maxlength='10' class='price' ";
	if($action=="edit" || $action=="add") echo "value='".htmlentities($item->qty,ENT_QUOTES,"UTF-8")."'>";
	if($action=="update" || $action=="insert") echo "value='".htmlentities($_POST["qty_".$item->id],ENT_QUOTES,"UTF-8")."' />";
	echo "</td>";
	echo "<td>";
	echo "<input type='text' name='reference_".$item->id."' style='width:80px' Maxlength='50' ";
	if ($action=="edit" || $action=="add"){
		echo "value='".unhtmlentities($item->reference)."'";
		if($item->id_product!=0) echo " readonly='readonly'";
	}
	if ($action=="update" || $action=="insert"){
		echo "value='".unhtmlentities($_POST["reference_".$item->id])."'";
		if($_POST["product_".$item->id]!="") echo " readonly='readonly'";
	}
	echo " />";
	echo "<input type='hidden' name='product_".$item->id."' value=\"";
	if($action=="edit" || $action=="add") echo $item->id_product;
	if($action=="update" || $action=="insert") echo $_POST["product_".$item->id];
	echo "\" />";
	echo "</td>";
	echo "<td>";
	echo "<input type='text' name='description_".$item->id."' style='width:400px;' ";
	if($action=="edit" || $action=="add") echo "value='".unhtmlentities($item->description)."'>";
	if($action=="update" || $action=="insert") echo "value='".unhtmlentities($_POST["description_".$item->id])."'>";
	echo "<br><textarea class='mceNoEditor' name='details_".$item->id."' style='width:400px;' rows='5'>";
	if($action=="edit" || $action=="add") echo unhtmlentities($item->details);
	if($action=="update" || $action=="insert") echo unhtmlentities($_POST["details_".$item->id]);
	echo "</textarea>";
	echo "</td>";
	echo "<td>";
	if($action=="edit" || $action=="add") $uprice = $item->uprice;
	if($action=="update" || $action=="insert") $uprice = $_POST["uprice_".$item->id];
	echo "<input type='text' name='uprice_".$item->id."' style='width:80px' Maxlength='20' class='price' value='".htmlentities($uprice,ENT_QUOTES,"UTF-8")."'>";
	echo "</td>";
	echo "<td>";
	if($action=="edit" || $action=="add") echo number_format($uprice*$item->qty,2);
	if($action=="update" || $action=="insert"){
		echo number_format($uprice*$_POST["qty_".$item->id],2);
		$subtotal+=$uprice*$_POST["qty_".$item->id];
	}
	if($action=="add"){
		$subtotal=$uprice*$item->qty;
	}
	echo "</td>";
	echo "<td style='vertical-align:middle;'>";
	if($action=="edit") echo "<img src='../images/del.gif' border='0' alt='Supprimer' onClick='javascript:delItem(".$item->id.",\""._("Etes vous sur de vouloir supprimer cette ligne ?")."\",-1)' />";
	else echo "&nbsp;";
	echo "</td>";
	echo "</tr>";
	//Check if the item has been unstocked
	if($action=="edit" && $item->id_product && $item->qty!=$item->unstocked_qty){
		$stockNeedsUpdate = true;
	}
} // if mysql num rows --> while mysql fetch
?>
<tr>
	<td colspan="4" style='text-align:right;'><b><?php echo _("Sous-total");?>:</b></td>
	<td colspan="2" style='text-align:right'><b><?php echo ($action=="edit"?$edit->subtotal:number_format($subtotal,2))?></b></td>
</tr>
<?php
/**
 * GET THE TAXES
 */
if($action=="edit"){
	$request = request("SELECT `$TBL_ecommerce_tax_authority`.name,`$TBL_ecommerce_tax_item`.amount
	                    FROM `$TBL_ecommerce_tax_authority`,`$TBL_ecommerce_tax_item`
	                    WHERE `$TBL_ecommerce_tax_item`.`id_invoice`='$idInvoice'
	                    AND `$TBL_ecommerce_tax_authority`.`id`=`$TBL_ecommerce_tax_item`.id_tax_authority",$link);
}
if($action=="add" || $action=="update" || $action=="insert"){
	$request = request("SELECT `$TBL_ecommerce_tax_group`.`method` as groupmethod,`$TBL_ecommerce_tax_authority`.*
                        FROM `$TBL_ecommerce_tax_group`,`$TBL_ecommerce_tax_authority`,`$TBL_ecommerce_tax_group_authority`
                        WHERE `$TBL_ecommerce_tax_group`.id = '$client->id_tax_group'
                        AND `$TBL_ecommerce_tax_group_authority`.`id_tax_group`=`$TBL_ecommerce_tax_group`.`id`
                        AND `$TBL_ecommerce_tax_group_authority`.`id_tax_authority`=`$TBL_ecommerce_tax_authority`.`id`
                        ORDER BY $TBL_ecommerce_tax_authority.`ordering`",$link);
}
$amount = 0;
$grandtotal = $edit->subtotal;
while($taxes = mysql_fetch_object($request)){
	if($action=="edit") $grandtotal+=$taxes->amount;
	echo "<tr>";
	echo "<td colspan='4' style='text-align:right'>";
	echo "<b>$taxes->name:</b>";
	echo "</td>";
	echo "<td colspan='2' style='text-align:right'>";
	if($action=="edit") echo "<b>$taxes->amount</b>";
	else echo "<b>"._("N/D")."</b>";
	echo "</td>";
	echo "</tr>";
}
/**
 * PRINT INVOICE TOTAL
 */
echo "<tr>";
echo "<td colspan='4' style='text-align:right'>";
echo "<b>"._("Total").":</b>";
echo "</td>";
echo "<td colspan='2' style='text-align:right'>";
echo "<b>".($action=="edit"?number_format($grandtotal,2,".",""):_("N/D"))."</b>";
echo "</td>";
echo "</tr>";
?>
</tbody>
</table><!-- invoice items table -->
</div> <!-- invoice_items -->


<input type='hidden' name='nb_new_lines' id='nb_new_lines' value='<?php echo (isset($_POST["nb_new_lines"])?$_POST["nb_new_lines"]:"0");?>' />
<input type='hidden' name='delete_line' id='delete_line' value='0' />
</form>
<script type="text/javascript">
function addProduct(id){
	setTimeout('xajax_addProduct('+id+',<?php echo $client->id_tax_group;?>)', 500);
}
<?php
if($action=="edit" && ($order->status=="ordered" || $order->status=="invoiced") && $stockNeedsUpdate){
	echo "confirmStockUpdate(\""._("Votre stock ne tient pas compte de certains produits de cette facture. Voulez-vous mettre à jour le stock maintenant?")."\",$order->id,$edit->id);";
}
?>
</script>
<?
include ("../html_footer.php");
mysql_close($link);
?>
