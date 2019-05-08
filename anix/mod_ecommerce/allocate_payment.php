<?php
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idPayment"])){
	$idPayment=$_POST["idPayment"];
} elseif(isset($_GET["idPayment"])){
	$idPayment=$_GET["idPayment"];
}
?>
<?php
$title = _("Allocation du paiement")." #".$idPayment;
$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php");
setTitleBar(_("Allocation du paiement")." #".$idPayment);
$cancelLink="./mod_payment.php?idPayment=$idPayment";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
printLanguageToggles($link);

$request = request("SELECT * from `$TBL_ecommerce_payment` WHERE id='$idPayment'",$link);
$payment = mysql_fetch_object($request);
?>
<form action='mod_payment.php' method='POST' enctype='multipart/form-data' name='allocate_payment'>
<input type='hidden' name='action' value='allocate_payment' />
<input type='hidden' name='idPayment' value='<?php echo $idPayment; ?>'>
<table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'><?php echo _("Allocation du paiement")." ".$idPayment; ?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
    <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
    <a href='<?=$cancelLink?>'>
        <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
    </td>
</tr>
<tr>
    <td colspan='2'>
        <table width='100%'>
        <tr>
            <td><?php echo _("Paiement #").$idPayment; ?></td>
            <td><?php echo _("Montant").": ".$payment->amount; ?></td>
            <td><?php echo _("Montant à allouer").": ".$payment->to_allocate_amount; ?></td>
        </tr>
        </table>
        <br /><br />
        <table border='1' style='width:80%;border:1px solid #000000' align= 'center'>
        <tr>
            <td align='center'><b><?php echo _("Type");?></b></td>
            <td align='center'><b><?php echo _("Numéro");?></b></td>
            <td align='center'><b><?php echo _("Montant");?></b></td>
            <td align='center'><b><?php echo _("Payé");?></b></td>
            <td align='center'><b><?php echo _("Montant restant");?></b></td>
            <td align='center'><b><?php echo _("Allouer");?></b></td>
        </tr>
        <?php
        $invoices = getCurrentInvoices($payment->id_client,$link);
        $orders = getCurrentOrders($payment->id_client,$link);
        $fieldsCount = 0;
        $fieldNames=array();
        $jsCode = "max_allocation = ".$payment->to_allocate_amount.";\n";
        if(!count($invoices) && !count($orders)) echo "<tr><td colspan='6' align='center'><i>"._("Aucune facture ou commande en cours à allouer pour ce client.")."</i></td></tr>";
        foreach($invoices as $invoice){
        	echo "<tr align='right'>";
        	echo "<td><b>"._("Facture")."</b></td>";
        	echo "<td>".$invoice["id"]."</td>";
        	echo "<td>".$invoice["amount"]."</td>";
        	echo "<td>".$invoice["payed_amount"]."</td>";
        	echo "<td>".$invoice["to_pay"]."</td>";
        	echo "<td align='center'><input type='text' name='inv_".$invoice["id"]."' onChange='javascript:validateFields($fieldsCount)' value='0.00'></td>";
        	echo "</tr>";
        	$jsCode.="field".$fieldsCount."_max_allocation=".$invoice["to_pay"].";\n";
        	$jsCode.="field".$fieldsCount."_oldvalue=0;\n";
        	$fieldNames[$fieldsCount]="inv_".$invoice["id"];
        	$fieldsCount++;
        }
        foreach($orders as $order){
        	echo "<tr align='right'>";
        	echo "<td><b>"._("Commande")."</b></td>";
        	echo "<td>".$order["id"]."</td>";
        	echo "<td>".$order["deposit_amount"]."</td>";
        	echo "<td>".$order["payed_amount"]."</td>";
        	echo "<td>".$order["to_pay"]."</td>";
        	echo "<td align='center'><input type='text' name='ord_".$order["id"]."' onChange='javascript:validateFields($fieldsCount)' value='0.00'></td>";
        	echo "</tr>";
        	$jsCode.="field".$fieldsCount."_max_allocation=".$order["to_pay"].";\n";
        	$jsCode.="field".$fieldsCount."_oldvalue=0;\n";
        	$fieldNames[$fieldsCount]="ord_".$order["id"];
        	$fieldsCount++;
        }
        echo "<tr align='right'>";
        echo "<td colspan='5'><b>"._("Total alloué").":</b></td>";
        echo "<td align='left'> <span id='total_allocated'></span></td>";
        echo "</tr>";
        echo "<tr align='right'>";
        echo "<td colspan='5'><b>"._("Restant à allouer").":</b></td>";
        echo "<td align='left'> <span id='to_allocate'></span></td>";
        echo "</tr>";
        ?>
        </table>
        <br />
    </td>
</tr>
<tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
    &nbsp;
    </td>
    <td background='../images/button_back.jpg' align='right'>
        &nbsp;
    </td>
</tr>
</table>
</table>
</form>
<SCRIPT language='javascript'>
<?php echo $jsCode; ?>
function validateFields(field){
	tmp_total = <?php
	$found=false;
	for($i=0;$i<$fieldsCount;$i++){
		if($found) echo "+";
		echo "parseFloat(document.allocate_payment.".$fieldNames[$i].".value)";
		$found=true;
	} //for
	?>;
	<?php
	for($i=0;$i<$fieldsCount;$i++){
		?>
		if(field==<?=$i?>){
			if( tmp_total > max_allocation){
				alert("<?php echo _("Le montant alloué à cette facture/commande dépasse le montant disponible."); ?> ");
				document.allocate_payment.<?=$fieldNames[$i]?>.value= field<?=$i?>_oldvalue;
				return;
			}
			if(document.allocate_payment.<?=$fieldNames[$i]?>.value > field<?=$i?>_max_allocation){
				//alert("<?php echo _("Le montant alloué à cette facture/commande dépasse le montant requis. Le montant entré a été remplacé par le montant requis."); ?>");
				document.allocate_payment.<?=$fieldNames[$i]?>.value= field<?=$i?>_max_allocation;
			}
			field<?=$i?>_oldvalue = document.allocate_payment.<?=$fieldNames[$i]?>.value;
		}
		<?php
	}//for
	?>
	document.getElementById("total_allocated").innerHTML=tmp_total;
	document.getElementById("to_allocate").innerHTML=max_allocation - tmp_total;
}
</SCRIPT>
<?php
include ("../html_footer.php");
mysql_close($link);
?>