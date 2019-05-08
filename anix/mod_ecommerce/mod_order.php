<?
require_once("../config.php");
require_once("./module_config.php");
require_once("./mod_order.xcommon.php");

$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="add";
//Get the client ID
if(isset($_POST["idClient"])){
	$idClient=$_POST["idClient"];
} elseif(isset($_GET["idClient"])){
	$idClient=$_GET["idClient"];
} else $idClient=0;
//If the client ID was not set, send to client selection page
/*if($action=="add" && !$idClient){
	Header("Location: ./choose_client.php?target=order");
	exit();
}*/
if(isset($_POST["idOrder"])){
	$idOrder=$_POST["idOrder"];
} elseif(isset($_GET["idOrder"])){
	$idOrder=$_GET["idOrder"];
} else $idOrder="";
$message = "";
?>
<?php
require_once("./mod_order.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Nouvelle commande");
elseif($action=="edit" || $action=="update") $title = _("Modification d'une commande");
else $title = _("Modification d'une commande");
$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php");

setTitleBar($title);
?>
<form action='./fraud_check.php' method='POST' enctype='multipart/form-data' name='fraudCheck'>
	<input type='hidden' name='method' value='' />
	<input type='hidden' name='idOrder' value='<?=$idOrder?>'>
</form>
<form action='./mod_order.php' method='POST' enctype='multipart/form-data' name='delForm'>
  <input type='hidden' name='idOrder' value='<?=$idOrder?>'>
  <input type='hidden' name='idItem' value=''>
  <input type='hidden' name='action' value='delete'>
</form>
<form id='main_form' action='./mod_order.php' method='POST' enctype='multipart/form-data' name='main_form'>
<?
if($action=="edit" || $action=="update"){
	$result=request("SELECT *
                       FROM $TBL_ecommerce_order where `id`='$idOrder'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette commande n'existe pas."));
	$edit = mysql_fetch_object($result);
	$idClient = $edit->id_client;
}
if($idClient){
	echo $errMessage;
	//Get client information
	$result=request("SELECT * FROM $TBL_ecommerce_customer where `id`='$idClient'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Ce client n'existe pas."));
	$client = mysql_fetch_object($result);
	$result=request("SELECT * FROM $TBL_ecommerce_address where `id`='".$client->id_address_mailing."'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: L'adresse de livraison n'existe pas."));
	$client_mailing = mysql_fetch_object($result);
	if($client->id_address_billing!=$client->id_address_mailing){
		$result=request("SELECT * FROM $TBL_ecommerce_address where `id`='".$client->id_address_billing."'",$link);
		if(!mysql_num_rows($result)) die(_("Erreur de protection: L'adresse de facturation n'existe pas."));
		$client_billing = mysql_fetch_object($result);
	} else $client_billing=$client_mailing;
}

if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idOrder' value='$idOrder'>";
	echo "<input type='hidden' name='idClient' value='$idClient'>";
	echo "<input type='hidden' name='action' value='insert'>";
	if($idClient) $cancelLink="./view_client.php?idClient=$idClient";
	else $cancelLink="./choose_client.php?target=order";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idOrder' value='$idOrder'>";
	echo "<input type='hidden' name='idClient' value='$idClient'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./view_client.php?idClient=$idClient";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>
<script type='text/javascript'>
<?php
if($action=="edit"){
	?>
	var oldDeliveryDate="<?php echo $edit->delivery_date?>";
	var oldShippingDate="<?php echo ($edit->shipping_date!="0000-00-00"?$edit->shipping_date:"")?>";
	function showHideDeliveryNotification(){
		if(document.getElementById('delivery_date').value!=oldDeliveryDate){
			document.getElementById('delivery_notification_control').style.display='';
		} else{
			document.getElementById('delivery_notification_control').style.display='none';
		}
	}
	function showHideShippingNotification(){
		if(document.getElementById('shipping_date').value!=oldShippingDate){
			document.getElementById('shipping_notification_control').style.display='';
		} else{
			document.getElementById('shipping_notification_control').style.display='none';
		}
	}
	<?php
} else {//if action = edit
	?>
	function showHideDeliveryNotification(){ return true; }
	function showHideShippingNotification(){ return true; }
	<?php
}//else
if(isset($_POST["nb_new_lines"]) && $_POST["nb_new_lines"]>0) echo "\$JS_COUNT_NEW_LINES=".$_POST["nb_new_lines"].";";
?>
</script>
<table style='width:100%'>
<tr>
	<td style='vertical-align:top; width:180px;'>
	<table>
	<tr>
	<td style="vertical-align:top;">
		<?php
		if($action=="edit"){ //Fraud detection status
			if($edit->fraud_check_mode=="none") echo "<img src='../images/fraud_level_unknown.jpg' alt=\""._("L\'évaluation de fraude n'a pas été effectuée.")."\" title=\""._("L'évaluation de fraude n'a pas été effectuée.")."\" style='vertical-align:middle;' /> ";
			elseif($edit->fraud_check_result==$ECOMMERCE_faud_level_awaiting) echo "<img src='../images/fraud_level_awaiting.jpg' alt='"._("Niveau de fraude: ÉVALUATION EN COURS")."' title='"._("Niveau de fraude: ÉVALUATION EN COURS")."' style='vertical-align:middle;' /> ";
			elseif($edit->fraud_check_result<=$ECOMMERCE_faud_level_low) echo "<img src='../images/fraud_level_low.jpg' alt='"._("Niveau de fraude: BAS")."' title='"._("Niveau de fraude: BAS")."' style='vertical-align:middle;' /> ";
			elseif($edit->fraud_check_result<=$ECOMMERCE_faud_level_medium) echo "<img src='../images/fraud_level_medium.jpg' alt='"._("Niveau de fraude: MOYEN")."' title='"._("Niveau de fraude: MOYEN")."' style='vertical-align:middle;' /> ";
			elseif($edit->fraud_check_result<=$ECOMMERCE_faud_level_high) echo "<img src='../images/fraud_level_high.jpg' alt='"._("Niveau de fraude: ÉLEVÉ")."' title='"._("Niveau de fraude: ÉLEVÉ")."' style='vertical-align:middle;' /> ";
			elseif($edit->fraud_check_result<=$ECOMMERCE_faud_level_alert) echo "<img src='../images/fraud_level_alert.jpg' alt='"._("Niveau de fraude: CRITIQUE")."' title='"._("Niveau de fraude: CRITIQUE")."' style='vertical-align:middle;' /> ";
		}
		?>
	</td>
	<td style="vertical-align:top;">
		<?php
		echo "<h3 style='margin:0;'>";
		if($action=="add" || $action=="insert") echo _("Nouvelle commande");
		if($action=="edit" || $action=="update") echo _("Commande")." #$idOrder ";
		echo "</h3>";
		if($action=="edit"){
			echo "(";
			if($edit->status=="stand by") echo "<font color='red'>"._("Accompte")."</font>";
			if($edit->status=="ordered") echo _("Validee");
			if($edit->status=="invoiced") {
				echo "<font color='blue'>"._("Facturee").": #$edit->id_invoice"."</font>";
			}
			if($edit->status=="payed") echo "<font color='green'>"._("Payee")."</font>";
			if($edit->status=="voided") echo "<font color='red'>"._("Annulee")."</font>";
			echo ")";
		} //if action == edit
		?>
	</td>
	</tr>
	<tr>
		<td></td>
		<td>
		<?php
		if($action=="edit" && $edit->status=="stand by"){
			$topay_amount = number_format($edit->deposit_amount - $edit->payed_amount,2,".","");
			echo "<input type='button' onclick=\"javascript:window.location='./mod_payment.php?action=add&idClient=$client->id&allocate=order&allocateid=$edit->id&amount=$topay_amount'\" value=\""._("Payer l'acompte")."\">";
		}
		if($action=="edit" && $edit->status=="ordered"){
			$topay_amount = number_format($edit->deposit_amount - $edit->payed_amount,2,".","");
			echo "<input type='button' onclick=\"javascript:window.location='./mod_invoice.php?action=add&idOrder=$idOrder'\" value=\""._("Facturer la commande")."\">";
		}
		?>
		</td>
	</tr>
	</table>
	</td>
	<td style='vertical-align:top;'>
		<?php
		/**
		 * LOAD TABS
		 */
		include("./mod_order.tabs.php");
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

$stockNeedsUpdate=false;

if($action=="edit" || $action=="update"){
?>
	<?
	$subtotal = 0;
	//retrieves line items if edit mode
	$items = request("SELECT * from `$TBL_ecommerce_invoice_item`
	                  WHERE id_order='$edit->id' ORDER BY `id`",$link);
	if(mysql_num_rows($items)) while($item=mysql_fetch_object($items)){
		echo "<tr>";
		echo "<td>";
		echo "<input type='text' name='qty_".$item->id."' style='width:50px' Maxlength='10' class='price' ";
		if($action=="edit") echo "value='".htmlentities($item->qty,ENT_QUOTES,"UTF-8")."'>";
		if($action=="update") echo "value='".htmlentities($_POST["qty_".$item->id],ENT_QUOTES,"UTF-8")."' />";
		echo "</td>";
		echo "<td>";
		echo "<input type='text' name='reference_".$item->id."' style='width:80px' Maxlength='50' ";
		if ($action=="edit"){
			echo "value='".unhtmlentities($item->reference)."'";
			if($item->id_product!=0) echo " readonly='readonly'";
		}
		if ($action=="update"){
			echo "value='".unhtmlentities($_POST["reference_".$item->id])."'";
			if($_POST["product_".$item->id]!="") echo " readonly='readonly'";
		}
		echo " />";
		echo "<input type='hidden' name='product_".$item->id."' value=\"";
		if($action=="edit") echo $item->id_product;
		if($action=="update") echo $_POST["product_".$item->id];
		echo "\" />";
		echo "</td>";
		echo "<td>";
		echo "<input type='text' name='description_".$item->id."' style='width:400px;' ";
		if($action=="edit") echo "value='".unhtmlentities($item->description)."'>";
		if($action=="update") echo "value='".unhtmlentities($_POST["description_".$item->id])."'>";
		echo "<br><TEXTAREA class='mceNoEditor' name='details_".$item->id."' style='width:400px;' rows='5'>";
		if($action=="edit") echo unhtmlentities($item->details);
		if($action=="update") echo unhtmlentities($_POST["details_".$item->id]);
		echo "</TEXTAREA>";
		echo "</td>";
		echo "<td>";
		echo "<input type='text' name='uprice_".$item->id."' style='width:80px' Maxlength='20' class='price' ";
		if($action=="edit") echo "value='".htmlentities($item->uprice,ENT_QUOTES,"UTF-8")."'>";
		if($action=="update") echo "value='".htmlentities($_POST["uprice_".$item->id],ENT_QUOTES,"UTF-8")."'>";
		echo "</td>";
		echo "<td>";
		if($action=="edit") echo number_format($item->uprice*$item->qty,2);
		if($action=="update"){
			echo number_format($_POST["uprice_".$item->id]*$_POST["qty_".$item->id],2);
            $subtotal+=$_POST["uprice_".$item->id]*$_POST["qty_".$item->id];
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
		<td colspan="2"><b><?php echo ($action=="edit"?$edit->subtotal:number_format($subtotal,2))?></b></td>
	</tr>
<?php
} //if action == $edit || update
?>
</tbody>
</table><!-- invoice items table -->
</div> <!-- invoice_items -->


<input type='hidden' name='nb_new_lines' id='nb_new_lines' value='<?php echo (isset($_POST["nb_new_lines"])?$_POST["nb_new_lines"]:"0");?>' />
<input type='hidden' name='delete_line' id='delete_line' value='0' />
</form>
<br />
<script type="text/javascript">
function addProduct(id){
	setTimeout('xajax_addProduct('+id+')', 500);
}

<?php
if($action=="edit" && ($edit->status=="ordered" || $edit->status=="invoiced") && $stockNeedsUpdate){
	echo "confirmStockUpdate(\""._("Votre stock ne tient pas compte de certains produits de cette commande. Voulez-vous mettre à jour le stock maintenant?")."\",$edit->id,0);";
}
?>
</script>
<?
include ("../html_footer.php");
mysql_close($link);
?>
