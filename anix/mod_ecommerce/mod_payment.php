<?php
include ("../config.php");
include ("./module_config.php");

$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="edit";
//Get the client ID
if(isset($_POST["idClient"])){
	$idClient=$_POST["idClient"];
} elseif(isset($_GET["idClient"])){
	$idClient=$_GET["idClient"];
} else $idClient=0;
//If the client ID was not set, send to client selection page
if($action=="add" && !$idClient){
	Header("Location: ./choose_client.php?target=payment");
	exit();
}
if(isset($_POST["idPayment"])){
	$idPayment=$_POST["idPayment"];
} elseif(isset($_GET["idPayment"])){
	$idPayment=$_GET["idPayment"];
} else $idPayment="";
?>
<?php
require_once("./mod_payment.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Nouveau paiement");
elseif($action=="edit" || $action=="update") $title = _("Modification d'un paiement");
else $title = _("Modification d'un paiement");
$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php");
//to let us use the javascript calendar to pickup dates.
$loadCalendar=true;
?>
<?
if($action=="edit"){
	$result=request("SELECT *
                    FROM $TBL_ecommerce_payment where `id`='$idPayment'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Ce paiement n'existe pas."));
	$edit = mysql_fetch_object($result);
	$idClient = $edit->id_client;
}
//Get client information
$result=request("SELECT *
                FROM $TBL_ecommerce_customer where `id`='$idClient'",$link);
if(!mysql_num_rows($result)) die(_("Erreur de protection: Ce client n'existe pas."));
$client = mysql_fetch_object($result);
$result=request("SELECT *
                FROM $TBL_ecommerce_address where `id`='".$client->id_address_mailing."'",$link);
if(!mysql_num_rows($result)) die(_("Erreur de protection: L'adresse de livraison n'existe pas."));
$client_mailing = mysql_fetch_object($result);
if($client->id_address_billing!=$client->id_address_mailing){
	$result=request("SELECT *
                    FROM $TBL_ecommerce_address where `id`='".$client->id_address_billing."'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: L'adresse de facturation n'existe pas."));
	$client_billing = mysql_fetch_object($result);
} else $client_billing=$client_mailing;
?>
<?php
if($action=="edit"){
?>
<form action='allocate_payment.php' method='POST' enctype='multipart/form-data' name='allocate_payment'>
    <input type='hidden' name='idPayment' value='<?php echo $edit->id; ?>'>
</form>
<?php
}//if(action==edit)
?>
<form action='./mod_payment.php' method='POST' enctype='multipart/form-data' name='mainForm'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idPayment' value='$idPayment'>";
	echo "<input type='hidden' name='idClient' value='$idClient'>";
	echo "<input type='hidden' name='action' value='insert'>";
	if(isset($_GET["allocate"]) && isset($_GET["allocateid"]) && isset($_GET["amount"])){
		echo "<input type='hidden' name='allocate' value='1'>";
		if($_GET["allocate"]=="order") echo "<input type='hidden' name='ord_".$_GET["allocateid"]."' value='max'>";
		elseif ($_GET["allocate"]=="invoice") echo "<input type='hidden' name='inv_".$_GET["allocateid"]."' value='max'>";
	}
	$cancelLink="./view_client.php?idClient=$idClient";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idPayment' value='$idPayment'>";
	echo "<input type='hidden' name='idClient' value='$idClient'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./view_client.php?idClient=$idClient";
}
?>
<table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'>
        <?
        if($action=="add" || $action=="insert"){
        	echo "Nouveau paiement";
        } elseif ($action=="edit" || $action=="update") {
        	echo _("Modification d'un paiement");
        } else {
        	echo _("Modification d'un paiement");
        }
          ?>
        </font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
    <tr>
        <td colspan='2'>
        <!-- Division en 3 colonnes -->
        <table width='100%'>
        <tr valign='top'>
        <td width='33%'>
            <b><?php echo _("Paiement effectué par").":";?></b><br />
            <?
            echo "<b>".$client->company."<br>";
            echo $client->firstname." ".$client->lastname."</b><br>";
            echo _("Telephone").": ".$client->phone."<br>";
            echo _("Cellulaire").": ".$client->cell."<br>";
            echo _("Telecopie").": ".$client->fax."<br>";
            echo _("Courriel").": <a href='mailto:".$client->email."'>".$client->email."</a><br>";
            ?>
        </td>
        <td width='33%'>
            <?php
            if($action=="insert" || $action=="add"){
            	echo "<b>"._("Type de paiement").":</b>";
            	echo "<br /><table>";
            	echo "<tr valign='baseline' align='center'>";
            	//coupon
            	echo "<td><img src='../images/coupon.jpg' border='0' onClick='javascript:document.mainForm.payment_type[0].checked=true;showFields();'><br />";
            	echo "<input type='radio' name='payment_type' value='-1' onclick='javascript:showFields();' />";
            	echo "</td>";
            	//other payment types
            	$payment_types = getPaymentTypes($link);
            	$i=1;
            	foreach($payment_types as $payment_type){
            		echo "<td><img src='../images/".$payment_type["image_file"]."' border='0' onClick='javascript:document.mainForm.payment_type[$i].checked=true;showFields();'><br />";
            		echo "<input type='radio' name='payment_type' value='".$payment_type["id"]."' onclick='javascript:showFields();' />";
            		echo "</td>";
            		$i++;
            	}
            	echo "</tr>";
            	echo "</table>";
            ?>
                <br />
                <table id='fields' style='visibility:hidden;'>
                <tr>
                    <td><b><?php echo _("Montant")." :";?></b></td>
                    <td><input type='text' name='amount' <?php if(isset($_GET["amount"])) echo "value='".$_GET["amount"]."'";?>  onblur='javascript:showFields();'/></td>
                </tr>
                <tr id='row_coupn_choose' style='display:none;'>
                    <td><span style='font-weight:bold;'><?php echo _("Coupon");?></span></td>
                    <td><select name='coupon_code'>
                    	<option value='0'>-------------</option>
	                    <?php
	                    $coupons = EcommerceCoupon::getCouponsByClient($client->id,false,true);
	                    $result2=request("SELECT * FROM $TBL_ecommerce_coupon where `id_client`='".$client->id."' AND `type`='fixed' AND ( (`usage`='once' AND `usage_count`='0') OR (`usage`='unlimited') OR (`usage`='count' AND `usage_count`<`max_usage`))",$link);
	                    foreach($coupons as $coupon){
	                    	echo "<option value='".$coupon->code."'>".$coupon->code.": ".$coupon->getValue().$currency_symbol."</option>";
	                    }
	                    ?>
                    </select>
                    </td>
                </tr>
                <tr>
                    <td><span id='field1' style='font-weight:bold;' ></span></td>
                    <td><input type='text' id='field1_input' name='field1'  /></td>
                </tr>
                <tr>
                    <td><span id='field2' style='font-weight:bold;' ></span></td>
                    <td><input type='text' id='field2_input' name='field2'  /></td>
                </tr>
                <tr>
                    <td><span id='field3' style='font-weight:bold;' ></span></td>
                    <td><input type='text' id='field3_input' name='field3'  /></td>
                </tr>
                <tr>
                    <td><span id='field4' style='font-weight:bold;' ></span></td>
                    <td><input type='text' id='field4_input' name='field4'  /></td>
                </tr>
                </table>
            <?php
            } //if action==insert || add
            if($action=="edit" || $action=="update"){
            	echo "<h1>"._("Paiement #").id_format($edit->id)."</h1>";
            	echo "<b>"._("Type de paiement").":</b>&nbsp;";
            	$payment_types = getPaymentTypes($link);
            	if($edit->id_payment_type==-1){
            		echo "<img src='../images/coupon.jpg' border='0' /><br><br>";
            		echo "<b>"._("Code")."</b>: ".$edit->field1;
            	} else foreach($payment_types as $payment_type){
            		if($payment_type["id"]==$edit->id_payment_type){
            			$current_payment = $payment_type;
            			echo "<img src='../images/".$payment_type["image_file"]."' border='0' /><br><br>";
            			echo "<table>";
            			echo "<tr><td><b>"._("Montant")." :</b>"."</td>";
            			echo "<td>".$edit->amount."</td></tr>";
            			$i=1;
            			foreach($payment_type["fields"] as $field){
            				echo "<tr><td>";
            				echo "<b>".$field."</b> :";
            				echo "</td><td>";
            				switch($i){
            					case 1: echo $edit->field1;break;
            					case 2: echo $edit->field2;break;
            					case 3: echo $edit->field3;break;
            					case 4: echo $edit->field4;break;
            				}
            				echo "</td></tr>";
            				$i++;
            			}
            			echo "</table>";
            		}
            	}
            }
            ?>
        </td>
        <td width='33%'>
            <b><?php echo _("Date de réception").":";?></b>
              <input type='text' name='reception_date' id='reception_date' size='10' Maxlength='10'<?
              if($action=="edit") echo " value='".$edit->reception_date."'";
              if($action=="insert" || $action=="update") echo " value='".$_POST["reception_date"]."'";
              if($action=="add") echo " value='".date('Y-m-d',time())."'";
            ?> READONLY /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('reception_date'),this);" style='vertical-align:bottom;' />
            <br /><br /><br />
            <?php
            if($action=="edit" || $action=="update"){
            ?>
                <table>
                <tr>
                    <td align='right'><b><?php echo _("Total alloué").":";?></b></td>
                    <td align='right'><?php echo $edit->allocated_amount; ?></td>
                </tr>
                <tr>
                    <td align='right'><b><?php echo _("Total restant à allouer").":";?></b></td>
                    <td align='right'><?php echo $edit->to_allocate_amount; ?></td>
                </tr>
                </table>
                <?php
                if($action=="edit" && $edit->to_allocate_amount>0){
                	echo "<br />";
                	echo "<input type='button' value='"._("Allouer ce paiement")."' onClick='document.allocate_payment.submit()'>";
                }
                ?>
            <?php
            }//if($action=="edit" || $action=="update")
            ?>
        </td>
        </tr>
        </table>
        <!-- Fin: Division en 3 colonnes -->
        </td>
    </tr>
    <tr height='20'>
        <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
        </td>
        <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
        <a href='<?=$cancelLink?>'>
            <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
        </td>
    </tr>
</table>
</form>
<br /><br />
<!-- Allocations list -->
<?php
if($action=="edit"){
?>
    <table border="0" align="center" width='50%' bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
        <td  background='../images/button_back.jpg' align='left' valign='middle'>
            <font class='edittable_header'><?php echo _("Allocations du paiement"); ?></font>
        </td>
        <td background='../images/button_back.jpg' align='right'>
            <?php if($edit->to_allocate_amount>0) {?><img src='../locales/<?=$used_language?>/images/button_add.jpg' border='0' onClick='document.allocate_payment.submit()' style='cursor:pointer;'><?}?>
        </td>
    </tr>
    <tr>
        <td colspan='2'>
            <?php
            $request =request("SELECT * FROM `$TBL_ecommerce_payment_allocation` WHERE `id_payment`='$idPayment'",$link);
            if(!mysql_num_rows($request)){
            	echo "<center><i>"._("Aucune allocation pour ce paiement...")."</i></center>";
            }
            else{
            	echo "<table width='50%' align='center'>";
            	echo "<tr>";
            	echo "<td><b>"._("Type")."</b></td>";
            	echo "<td><b>"._("Num#")."</b></td>";
            	echo "<td><b>"._("Montant")."</b></td>";
            	echo "<td>&nbsp;</td>";
            	echo "</tr>";
            	while($allocation = mysql_fetch_object($request)){
            		echo "<tr>";
            		echo "<td>";
            		if($allocation->id_invoice==0) echo "<b>"._("Commande")."</b>";
            		else echo "<b>"._("Facture")."</b>";
            		echo "</td>";
            		echo "<td>".($allocation->id_invoice==0?$allocation->id_order:$allocation->id_invoice)."</td>";
            		echo "<td>".$allocation->amount."</td>";
            		echo "<td><img src='../images/del.gif' border='0' onClick='document.delAllocation.submit()'></td>";
            		echo "</tr>";
            	}
            	echo "</table>";
            }
            ?>
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
<?php
} //if action==edit
?>
<SCRIPT language='Javascript'>
function showFields(){
	<?php
	//coupn checked
	echo "if(document.mainForm.payment_type[0].checked){";
	echo "document.getElementById(\"fields\").style.visibility=\"visible\";";
	for($k=1;$k<=4;$k++){
		echo "document.getElementById(\"field$k\").innerHTML=\"\";\n";
		echo "document.getElementById(\"field".$k."_input\").style.visibility=\"hidden\";\n";
	}
	echo "if(parseFloat(document.mainForm.amount.value)<=0){\n";
	echo "document.getElementById(\"row_coupn_choose\").style.display='none';\n";
	echo "} else {\n";
	echo "document.getElementById(\"row_coupn_choose\").style.display='';\n";
	echo "}\n"; //else
	echo "return;\n";
	echo "}"; //if
	$i =1;
	foreach($payment_types as $payment_type){
		echo "if(document.mainForm.payment_type[$i].checked){";
		echo "document.getElementById(\"fields\").style.visibility=\"visible\";";
		echo "document.getElementById(\"row_coupn_choose\").style.display='none';\n";
		$nbFields = count($payment_type["fields"]);
		$j=1;
		//show payement type fields
		foreach($payment_type["fields"] as $field){
			echo "document.getElementById(\"field$j\").innerHTML=\"$field :\";";
			echo "document.getElementById(\"field".$j."_input\").style.visibility=\"visible\";";
			$j++;
		}
		//hide the other fields
		for($k=$j;$k<=4;$k++){
			echo "document.getElementById(\"field$k\").innerHTML=\"\";";
			echo "document.getElementById(\"field".$k."_input\").style.visibility=\"hidden\";";
		}
		echo "}"; //if
		$i++;
	}

	?>
}
<?php
if($action=="add" || $action=="insert") echo "showFields();";
?>
</SCRIPT>
<?
include ("../html_footer.php");
mysql_close($link);
?>