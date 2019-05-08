<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idClient"])){
	$idClient=$_POST["idClient"];
} elseif(isset($_GET["idClient"])){
	$idClient=$_GET["idClient"];
} else $idClient="";
?>
<?
$title = _("Fiche Client");
$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php");
$cancelLink="./search_client.php";
?>
<table border="0" align="center" width="70%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'> Fiche Client</font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <a href='./mod_client.php?action=edit&idClient=<?=$idClient?>'>
          <img src='../locales/<?=$used_language?>/images/button_edit.jpg' border='0'></a>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <?
        $result=request("SELECT *
                           FROM `$TBL_ecommerce_customer`
                           WHERE `$TBL_ecommerce_customer`.`id`='$idClient'",$link);
        if(!mysql_num_rows($result)) die(_("Erreur de protection: Ce client n'existe pas."));
        $edit = mysql_fetch_object($result);
        $result=request("SELECT *
                           FROM $TBL_ecommerce_address where `id`='".$edit->id_address_mailing."'",$link);
        if(!mysql_num_rows($result)) die(_("Erreur de protection: L'adresse de livraison n 'existe pas."));
        $edit_mailing = mysql_fetch_object($result);
        if($edit->id_address_billing!=$edit->id_address_mailing){
        	$result=request("SELECT *
                             FROM $TBL_ecommerce_address where `id`='".$edit->id_address_billing."'",$link);
        	if(!mysql_num_rows($result)) die(_("Erreur de protection: L'adresse de facturation n'existe pas."));
        	$edit_billing = mysql_fetch_object($result);
        } else $edit_billing=$edit_mailing;
        $result = request("SELECT name,image_file,locales_folder from `$TBL_gen_languages` WHERE `id`='".$edit->language."'",$link);
        $language = mysql_fetch_object($result);
        ?>
        <!--Division en 2 colonnes -->
        <table width='90%' align='center'>
        <tr>
          <!--Colonne 1-->
          <td width='33%' valign='top'>
            <?
            echo "<h3><B>".$edit->firstname." ".$edit->lastname."<br>";
            echo $edit->company."</b></h3><br>";
            echo _("Tel").": ".$edit->phone."<br>";
            echo _("Cellulaire").": ".$edit->cell."<br>";
            echo _("Telecopie").": ".$edit->fax."<br>";
            echo _("Courriel").": <a href='mailto:".$edit->email."'>".$edit->email."</a><br>";
            echo _("Login").": ".$edit->login."<br><br>";
            echo "<b>"._("Langue de correspondance").":</b><br><br><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'>  ".$language->name;
            ?>
          </td>
          <!--Colonne 2-->
          <td width='33%' valign='top'>
            <b><?php echo _("Adresse de livraison");?>:</b><br><br>
            <?
            echo $edit_mailing->num." ".$edit_mailing->street1."<br>";
            if($edit_mailing->street2!="") echo $edit_mailing->street2."<br>";
            echo $edit_mailing->city." ".$edit_mailing->province."<br>";
            echo $edit_mailing->zip." ".$edit_mailing->country;
            ?>
            <br><br>
            <b><?php echo _("Adresse de facturation");?>:</b><br><br>
            <?
            echo $edit_billing->num." ".$edit_billing->street1."<br>";
            if($edit_billing->street2!="") echo $edit_billing->street2."<br>";
            echo $edit_billing->city." ".$edit_billing->province."<br>";
            echo $edit_billing->zip." ".$edit_billing->country;
            ?>
          </td>
          <td width='33%' valign='top'>
            <h1><?php echo _("Client"); ?> #<?php echo id_format($edit->id); ?></h1>
            <b><?php echo _("Groupe de prix").":";?></b> <?php
            if($edit->id_user_group){
				$request=request("SELECT `name` FROM `$TBL_catalogue_info_price_groups` WHERE `id_price_group`='$edit->id_user_group'",$link);
	            $group = mysql_fetch_object($request);
	            echo $group->name;
            } else echo _("Public");
            ?><br />
            <b><?php echo _("Termes").":";?></b> <?php
            $request=request("SELECT `name` FROM `$TBL_ecommerce_terms` WHERE `id`='$edit->id_terms'",$link);
            $term = mysql_fetch_object($request);
            echo $term->name;
            ?><br />
            <b><?php echo _("Marge de crédit").":"; ?></b> <?php echo $edit->credit_margin; ?><br /><br />
            <b><?php echo _("Solde du compte").":"; ?></b> <?php echo $edit->balance; ?><br /><br />
            <input type='button' onclick="javascript:window.location='./mod_order.php?action=add&idClient=<?php echo $idClient;?>'" value="<?php echo _("Nouvelle commande")?>" /><br /><br />
            <?php
            if(!isset($_GET["history"])){
            	echo "<input type='button' onclick=\"javascript:window.location='./view_client.php?idClient=$edit->id&history=1'\" value=\""._("Voir l'historique")."\" />";
            }
            else{
            	echo "<input type='button' onclick=\"javascript:window.location='./view_client.php?idClient=$edit->id'\" value=\""._("Cacher l'historique")."\" />";
            }
            ?>
          </td>
        </tr>
        </table>
      </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <a href='./mod_client.php?action=edit&idClient=<?=$idClient?>'>
          <img src='../locales/<?=$used_language?>/images/button_edit.jpg' border='0'></a>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
  </table>
</form>
<!--Commandes -->
    <br />
    <table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
      <tr height='20'>
          <td  background='../images/button_back.jpg' align='left' valign='middle'>
            <font class='edittable_header'> <?php
            if(isset($_GET["history"])) echo _("Commandes");
            else echo _("Commandes en cours");
            ?></font>
          </td>
          <td background='../images/button_back.jpg' align='right'>
            &nbsp;
          </td>
        </tr>
        <tr>
          <td colspan='2'>
            <table width='100%'>
              <tr>
                <td width='120'>&nbsp;</td>
                <td align='center'><b>#<?php echo _("Commande");?></b></td>
                <td align='center'><b><?php echo _("Status");?></b></td>
                <td align='center'><b><?php echo _("Date");?></b></td>
                <td align='center'><b><?php echo _("Livraison");?></b></td>
                <td align='center'><b><?php echo _("Sous-total");?></b></td>
                <td align='center'><b><?php echo _("Acompte requis");?></b></td>
                <td align='center'><b><?php echo _("Montant payé");?></b></td>
              </tr>
              <?
              $requestStr="SELECT * FROM $TBL_ecommerce_order where `id_client`='$idClient'";
              if(!isset($_GET["history"])) $requestStr.=" AND (`status`='stand by' or `status`='ordered' or (`status`='invoiced' and `shipping_date`='0000-00-00'))";
              $requestStr.=" ORDER BY `order_date`,`delivery_date`,`id` DESC";
              $orders = request($requestStr,$link);
              if(!mysql_num_rows($orders)) {
              	echo "<tr>";
              	echo "<td align='center' colspan='4'>";
              	echo "<br><b><i>"._("Aucune commande en cours pour ce client.")."</i></b><br>";
              	echo "</td>";
              	echo "</tr>";
              } else {
              	while($order = mysql_fetch_object($orders)){
              		echo "<tr>";
              		echo "<td>";
              		if($order->status=="ordered" || $order->status=="stand by") echo "<a href='./del_order.php?id=$order->id'><img src='../images/del.gif' border='0' alt="._("Supprimer")."></a>";
              		if($order->status=="ordered" || $order->status=="stand by") echo "<a href='./mod_order.php?idOrder=$order->id&action=edit'><img src='../images/edit.gif' border='0' alt="._("Modifier")."></a>";
              		if($order->status!="voided" && $order->shipping_date=="0000-00-00") echo "<a href='./mod_order.php?idOrder=$order->id&action=edit'><img src='../images/shipping.gif' border='0' alt="._("Livraison")."></a>";
              		echo "<a href='./view_order.php?id=$order->id'><img src='../images/view.gif' border='0' alt="._("Voir")."></a>";
              		echo "<a href='./pdf/view_order.php?id=$order->id' target='_blank'><img src='../images/pdf.gif' border='0' alt="._("Version PDF")."></a>";
              		if($order->status=="ordered"){
              			echo "<a href='./mod_invoice.php?action=add&idOrder=$order->id'><img src='../images/invoice.gif' border='0' alt="._("Facturer")."></a>";
              		}
              		if($order->status=="stand by"){
              			$to_pay = number_format($order->deposit_amount - $order->payed_amount,2,".","");
              			echo "<a href='./mod_payment.php?action=add&idClient=$idClient&allocate=order&allocateid=$order->id&amount=$to_pay'><img src='../images/pay.gif' border='0' alt="._("Payer l'acompte")."></a>";
              		}
              		echo "</td>";
              		echo "<td align='center'>";
              		if($order->fraud_check_mode=="none") echo "<img src='../images/fraud_level_unknown.jpg' alt=\""._("L\'évaluation de fraude n'a pas été effectuée.")."\" title=\""._("L'évaluation de fraude n'a pas été effectuée.")."\" style='vertical-align:middle;' />";
              		else {
              			$link_fraud_details="";
              			$link_fraud_details_closing="";
              			if(isset($ECOMMERCE_fraudcheck_methods[$order->fraud_check_mode]) && isset($ECOMMERCE_fraudcheck_methods[$order->fraud_check_mode]["details_url"])){
              				$link_fraud_details="<a href='".$ECOMMERCE_fraudcheck_methods[$order->fraud_check_mode]["details_url"]."' target='_blank'>";
              				$link_fraud_details = str_replace("%%ID_ORDER%%",$order->id,$link_fraud_details);
              				$link_fraud_details_closing="</a>";
              			}
              			if($order->fraud_check_result==$ECOMMERCE_faud_level_awaiting) echo $link_fraud_details."<img src='../images/fraud_level_awaiting.jpg' alt='"._("Niveau de fraude: ÉVALUATION EN COURS")."' title='"._("Niveau de fraude: ÉVALUATION EN COURS")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
              			elseif($order->fraud_check_result<=$ECOMMERCE_faud_level_low) echo $link_fraud_details."<img src='../images/fraud_level_low.jpg' alt='"._("Niveau de fraude: BAS")."' title='"._("Niveau de fraude: BAS")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
              			elseif($order->fraud_check_result<=$ECOMMERCE_faud_level_medium) echo $link_fraud_details."<img src='../images/fraud_level_medium.jpg' alt='"._("Niveau de fraude: MOYEN")."' title='"._("Niveau de fraude: MOYEN")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
              			elseif($order->fraud_check_result<=$ECOMMERCE_faud_level_high) echo $link_fraud_details."<img src='../images/fraud_level_high.jpg' alt='"._("Niveau de fraude: ÉLEVÉ")."' title='"._("Niveau de fraude: ÉLEVÉ")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
              			elseif($order->fraud_check_result<=$ECOMMERCE_faud_level_alert) echo $link_fraud_details."<img src='../images/fraud_level_alert.jpg' alt='"._("Niveau de fraude: CRITIQUE")."' title='"._("Niveau de fraude: CRITIQUE")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
              		}
              		echo "&nbsp;<b>".id_format($order->id)."</b>";
              		echo "</td>";
              		//echo "<td align='center'>";
              		//echo id_format($order->id);
              		//echo "</td>";
              		echo "<td align='center'>";
              		switch($order->status){
              			case "stand by": echo "<font color='red'>"._("En attente d'accompte")."</font>";break;
              			case "ordered": echo _("Validée");break;
              			case "invoiced": echo _("Facturée");break;
              			case "voided": echo _("Annulée");break;
              		}
              		echo "</td>";
              		echo "<td align='center'>";
              		echo $order->order_date;
              		echo "</td>";
              		echo "<td align='center'>";
              		if($order->delivery_date < date('Y-m-d',time()) && ($order->status=="ordered" || $order->status=="stand by")) $late_order=true; else $late_order=false;
              		if($late_order) echo "<font color='red'>";
              		echo $order->delivery_date;
              		if($late_order) echo "</font>";
              		echo "</td>";
              		echo "<td align='right'>";
              		echo $order->subtotal." $currency_symbol";
              		echo "</td>";
              		echo "<td align='right'>";
              		echo $order->deposit_amount ." $currency_symbol";
              		echo "</td>";
              		echo "<td align='right'>";
              		echo $order->payed_amount ." $currency_symbol";
              		echo "</td>";
              		echo "</tr>";
              	}
              }
              ?>
            </table>
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
    <!-- Factures -->
    <br />
    <table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
      <tr height='20'>
          <td  background='../images/button_back.jpg' align='left' valign='middle'>
            <font class='edittable_header'> <?php
            if(isset($_GET["history"])) echo _("Factures");
            else echo _("Factures en cours");
            ?></font>
          </td>
          <td background='../images/button_back.jpg' align='right'>
            &nbsp;
          </td>
        </tr>
        <tr>
          <td colspan='2'>
            <table width='100%'>
              <tr>
                <td width='120'>&nbsp;</td>
                <td align='center'><b>#<?php echo _("Facture");?></b></td>
                <td align='center'><b><?php echo _("Status");?></b></td>
                <td align='center'><b><?php echo _("Date");?></b></td>
                <td align='center'><b><?php echo _("Échéance");?></b></td>
                <td align='center'><b><?php echo _("Sous-total");?></b></td>
                <td align='center'><b><?php echo _("Total");?></b></td>
                <td align='center'><b><?php echo _("Payé");?></b></td>
                <td align='center'><b><?php echo _("Reste");?></b></td>
              </tr>
              <?
              $requestStr="SELECT * FROM $TBL_ecommerce_invoice where `id_client`='$idClient'";
              if(!isset($_GET["history"])) $requestStr.="AND (`status` IN('created','issued'))";
              $requestStr.="ORDER BY `invoice_date` DESC,`id` DESC";
              $invoices=request($requestStr,$link);
              if(!mysql_num_rows($invoices)) {
              	echo "<tr>";
              	echo "<td align='center' colspan='4'>";
              	echo "<br><b><i>"._("Aucune facture pour ce client.")."</i></b><br>";
              	echo "</td>";
              	echo "</tr>";
              } else {
              	while($invoice = mysql_fetch_object($invoices)){
              		echo "<tr>";
              		echo "<td>";
              		//echo "<a href='./del_invoice.php?id=$invoice->id'><img src='../images/del.gif' border='0' alt="._("Supprimer")."></a>";
              		if($invoice->status=="payed" && $invoice->refund=="N"){
              			echo "<a href='javascript:confirmRefund($invoice->id)'><img src='../images/refund.jpg' border='0' alt="._("Rembourser la facture")."></a>";
              		}
              		if($invoice->status=="issued"){
              			//unissue link
              			echo "<a href='./issue_invoice.php?action=un_issue_confirm&idInvoice=$invoice->id'><img src='../images/unissue_invoice.gif' border='0' alt="._("Ré-éditer la facture")."></a>";
              		}
              		if($invoice->status=='created')echo "<a href='./mod_invoice.php?idInvoice=$invoice->id&action=edit'><img src='../images/edit.gif' border='0' alt="._("Modifier")."></a>";
              		echo "<a href='./view_invoice.php?id=$invoice->id'><img src='../images/view.gif' border='0' alt="._("Voir")."></a>";
              		echo "<a href='./pdf/view_invoice.php?id=$invoice->id' target='_blank'><img src='../images/pdf.gif' border='0' alt="._("Version PDF")."></a>";
              		if($invoice->status=="created"){
              			echo "<a href='./issue_invoice.php?action=issue_confirm&idInvoice=$invoice->id'><img src='../images/issue_invoice.gif' border='0' alt="._("Émettre la facture")."></a>";
              		}
              		if($invoice->status=="issued"){
              			$to_pay = number_format($invoice->grandtotal - $invoice->payed_amount,2,".","");
              			echo "<a href='./mod_payment.php?action=add&idClient=$idClient&allocate=invoice&allocateid=$invoice->id&amount=$to_pay'><img src='../images/pay.gif' border='0' alt="._("Payer l'acompte")."></a>";
              		}
              		echo "</td>";
              		echo "<td align='center'>";
              		if($invoice->refund=="Y") echo "R";
              		echo id_format($invoice->id);
              		echo "</td>";
              		echo "<td align='center'>";
              		switch($invoice->status){
              			case "created": echo _("Créée");break;
              			case "issued": echo _("Publiée");break;
              			case "payed": echo _("Payée");break;
              			case "voided": echo _("Annulée");break;
              			case "refunded": echo _("Remboursée")." ($invoice->id_refund)";break;
              		}
              		echo "</td>";
              		echo "<td align='center'>";
              		echo $invoice->invoice_date;
              		echo "</td>";
              		echo "<td align='center'>";
              		echo $invoice->due_date;
              		echo "</td>";
              		echo "<td align='right'>";
              		echo $invoice->subtotal." $currency_symbol";
              		echo "</td>";
              		echo "<td align='right'>";
              		echo $invoice->grandtotal." $currency_symbol";
              		echo "</td>";
              		echo "<td align='right'>";
              		echo $invoice->payed_amount." $currency_symbol";
              		echo "</td>";
              		echo "<td align='right'>";
              		echo number_format($invoice->grandtotal-$invoice->payed_amount,2,".","")." $currency_symbol";
              		echo "</td>";
              		echo "</tr>";
              	}
              }
              ?>
            </table>
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
    <!-- Paiements -->
    <br />
    <table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
      <tr height='20'>
          <td  background='../images/button_back.jpg' align='left' valign='middle'>
            <font class='edittable_header'> <?php
            if(isset($_GET["history"])) echo _("Paiements");
            else echo _("Paiements à allouer");
            ?></font>
          </td>
          <td background='../images/button_back.jpg' align='right'>
            &nbsp;
          </td>
        </tr>
        <tr>
          <td colspan='2'>
            <table width='100%'>
              <tr>
                <td width='120'>&nbsp;</td>
                <td align='center'><b>#<?php echo _("Paiement");?></b></td>
                <td align='center'><b><?php echo _("Recu le");?></b></td>
                <td align='center'><b><?php echo _("Type");?></b></td>
                <td align='center'><b><?php echo _("Montant");?></b></td>
                <td align='center'><b><?php echo _("Alloué");?></b></td>
                <td align='center'><b><?php echo _("Reste à allouer");?></b></td>
              </tr>
              <?
              $requestStr="
              	SELECT `$TBL_ecommerce_payment`.id,
              		   reception_date,
              		   id_payment_type as payment_type,
              		   amount,
              		   allocated_amount,
              		   to_allocate_amount
                FROM $TBL_ecommerce_payment
                WHERE `id_client`='$idClient'";
              if(!isset($_GET["history"])) $requestStr.=" AND `to_allocate_amount`<>0";
              $requestStr.=" ORDER BY `reception_date` DESC,`id` DESC";
              $payments = request($requestStr,$link);
              $paymentTypes = getPaymentTypes($link);
              if(!mysql_num_rows($payments)) {
              	echo "<tr>";
              	echo "<td align='center' colspan='4'>";
              	echo "<br><b><i>"._("Aucun paiement à allouer pour ce client.")."</i></b><br>";
              	echo "</td>";
              	echo "</tr>";
              } else {
              	while($payment = mysql_fetch_object($payments)){
              		echo "<tr>";
              		echo "<td width='100'>";
              		echo "<a href='./mod_payment.php?idPayment=$payment->id&action=edit'><img src='../images/edit.gif' border='0' alt="._("Modifier")."></a>";
              		echo "<a href='./pdf/view_payment.php?id=$payment->id' target='_blank'><img src='../images/pdf.gif' border='0' alt="._("Version PDF")."></a>";
              		echo "</td>";
              		echo "<td align='center'>";
              		echo id_format($payment->id);
              		echo "</td>";
              		echo "<td align='center'>";
              		echo $payment->reception_date;
              		echo "</td>";
              		echo "<td align='center'>";
              		//echo $payment->name;
              		if(isset($paymentTypes[$payment->payment_type])) echo $paymentTypes[$payment->payment_type]["name"];
              		elseif($payment->payment_type==-1) echo _("Coupon");
              		echo "</td>";
              		echo "<td align='right'>";
              		echo $payment->amount." $currency_symbol";
              		echo "</td>";
              		echo "<td align='right'>";
              		echo $payment->allocated_amount." $currency_symbol";
              		echo "</td>";
              		echo "<td align='right'>";
              		if($payment->to_allocate_amount<>0) echo "<font color='red'>";
              		echo $payment->to_allocate_amount." $currency_symbol";
              		if($payment->to_allocate_amount<>0) echo "</font>";
              		echo "</td>";
              		echo "</tr>";
              	}
              }
              ?>
            </table>
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
    <!-- Coupons -->
    <br />
    <table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
      <tr height='20'>
          <td  background='../images/button_back.jpg' align='left' valign='middle'>
            <font class='edittable_header'> <?php
            if(isset($_GET["history"])) echo _("Coupons");
            else echo _("Coupons non utilisés");
            ?></font>
          </td>
          <td background='../images/button_back.jpg' align='right'>
            &nbsp;
          </td>
        </tr>
        <tr>
          <td colspan='2'>
            <table width='100%'>
              <tr>
                <td width='120'>&nbsp;</td>
                <td align='center'><b>#<?php echo _("Coupon");?></b></td>
                <td align='center'><b><?php echo _("Type");?></b></td>
                <td align='center'><b><?php echo _("Usage");?></b></td>
                <td align='center'><b><?php echo _("Validité");?></b></td>
                <td align='center'><b><?php echo _("Valeur");?></b></td>
              </tr>
              <?
              $coupons = EcommerceCoupon::getCouponsByClient($idClient,isset($_GET["history"]));

              if(!count($coupons)) {
              	echo "<tr>";
              	echo "<td align='center' colspan='4'>";
              	echo "<br><b><i>"._("Aucun coupon pour ce client.")."</i></b><br>";
              	echo "</td>";
              	echo "</tr>";
              } else {
              	foreach($coupons as $coupon){
              		echo "<tr>";
              		echo "<td width='100'>";
              		echo "<a href='./mod_coupon.php?idCoupon=$coupon->id&action=edit'><img src='../images/edit.gif' border='0' alt="._("Modifier")."></a>";
              		echo "<a href='./del_coupon.php?idCoupon=$coupon->id'><img src='../images/del.gif' border='0' alt="._("Supprimer")."></a>";
              		echo "</td>";
              		echo "<td align='center'>";
              		echo $coupon->code;
              		echo "</td>";
              		echo "<td align='center'>";
              		echo $coupon->getTypeString();
              		echo "</td>";
              		echo "<td align='center'>";
              		echo $coupon->getUsageString();
              		echo "</td>";
              		echo "<td align='center'>";
              		echo _("Du").": ".($coupon->validFrom!="0000-00-00"?$coupon->validFrom:_("N/D"));
              		echo " ";
              		echo _("Au").": ".($coupon->validUntil!="0000-00-00"?$coupon->validUntil:_("N/D"));
              		echo "</td>";
              		echo "<td align='right'>";
              		if($coupon->getValue()) echo $coupon->getValue()." ".$currency_symbol;
              		else echo _("N/D");
              		echo "</td>";
              		echo "</tr>";
              	}
              }
              ?>
            </table>
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
<script type='text/javascript'>
function confirmRefund(id){
	if(confirm("<?php echo _("Êtes vous sûr de vouloir rembourser la facture Num.");?>"+id+"?")){
		window.location="./mod_invoice.php?idInvoice="+id+"&action=refund";
	}
}
</script>
<?
include ("../html_footer.php");
mysql_close($link);
?>
