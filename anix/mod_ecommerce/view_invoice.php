<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["id"])){
	$idInvoice=$_POST["id"];
} elseif(isset($_GET["id"])){
	$idInvoice=$_GET["id"];
} else $idInvoice="";
?>
<?
$result=request("SELECT *
                FROM `$TBL_ecommerce_invoice`
                WHERE `$TBL_ecommerce_invoice`.`id`='$idInvoice'",$link);
if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette facture n'existe pas."));
$edit = mysql_fetch_object($result);
//Loads the customer
$result=request("SELECT *
                FROM `$TBL_ecommerce_customer`
                WHERE `$TBL_ecommerce_customer`.`id`='$edit->id_client'",$link);
if(!mysql_num_rows($result)) die(_("Erreur de protection: Le client n'existe pas."));
$client = mysql_fetch_object($result);
//Loads the order
$result=request("SELECT *
                FROM `$TBL_ecommerce_order`
                WHERE `$TBL_ecommerce_order`.`id`='$edit->id_order'",$link);
if(!mysql_num_rows($result)) die(_("Erreur de protection: La commande n'existe pas."));
$order = mysql_fetch_object($result);
?>
<?
$title = _("Facture");
$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php");
$cancelLink="./view_client.php?idClient=$client->id";
?>
<table border="0" align="center" width="70%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'> Facture</font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <!--Division en 2 colonnes -->
        <table width='90%' align='center'>
        <tr valign='top'>
            <td valign='top'>
                <b><u><?php echo _("Client").":"; ?></b></u><br />
                <?
                echo "<B>".$client->firstname." ".$client->lastname."<br>";
                echo $client->company."</b><br>";
                echo _("Tel").": ".$client->phone."<br>";
                echo _("Cellulaire").": ".$client->cell."<br>";
                echo _("Telecopie").": ".$client->fax."<br>";
                echo _("Courriel").": <a href='mailto:".$client->email."'>".$client->email."</a><br>";
                ?>
            </td>
            <td align='right' valign='top'>
                <h1><?php echo _("Facture #").($edit->refund=="Y"?"R":"").id_format($edit->id); ?> (<?php
                switch($edit->status){
                	case "created":echo _("Créée");break;
                	case "issued":echo _("Émise");break;
                	case "payed":echo _("Payée");break;
                	case "voided":echo _("Annulée");break;
                }
                ?>)</h1>
                <?php
                echo "<b>"._("Date").":</b> ".$edit->invoice_date."<br />";
                echo "<b>"._("Date limite").":</b> ".$edit->due_date."<br /><br />";
                echo "<b>"._("Montant percu").":</b> ".$edit->payed_amount."<br />";
                echo "<br />";
                echo "<h3>"._("Commande").": <a href='./view_order.php?id=$edit->id_order'>".id_format($edit->id_order)."</a></h3>";
                ?>
            </td>
        </tr>
        <tr><td><br /></td></tr>
        <tr>
          <!--Colonne 1-->
          <td width='33%' valign='top'>
            <b><u><?php echo _("Adresse de livraison").":"; ?></b></u><br />
            <?
            echo "<B>".$client->firstname." ".$client->lastname."<br>";
            echo $client->company."</b><br>";
            echo nl2br($order->mailing_address);
            ?>
          </td>
          <!--Colonne 2-->
          <td width='33%' valign='top'>
            <b><u><?php echo _("Adresse de facturation").":"; ?></b></u><br />
            <?
            echo "<B>".$client->firstname." ".$client->lastname."<br>";
            echo $client->company."</b><br>";
            echo nl2br($edit->billing_address);
            ?>
          </td>
        </tr>
        </table><br />
      </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Détails"); ?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
    <tr>
        <td colspan='2'><br />
            <?php
            $items = request("SELECT * from `$TBL_ecommerce_invoice_item`
                                  WHERE id_invoice='$edit->id'",$link);
            if(mysql_num_rows($items)) {
            	echo "<table border='1' align='center' width='80%'>";
            	echo "<tr>";
            	echo "  <td align='center'><B>"._("QTE")."</B></td>";
            	echo "  <td align='center'><B>"._("CODE")."</B></td>";
            	echo "  <td align='center'><B>"._("DESCRIPTION")."</B></td>";
            	echo "  <td align='right'><B>"._("PRIX")."</B></TD>";
            	echo "  <TD align='right'><B>"._("TOTAL")."</B></TD>";
            	echo "</tr>";
            	while($item=mysql_fetch_object($items)){
            		echo "<tr valign='top'>";
            		echo "<td>";
            		echo htmlentities($item->qty,ENT_QUOTES,"UTF-8");
            		echo "</td>";
            		echo "<td>";
            		echo htmlentities($item->reference,ENT_QUOTES,"UTF-8");
            		echo "</td>";
            		echo "<td>";
            		echo "<b>".unhtmlentities($item->description)."</b><br />";
            		echo unhtmlentities($item->details);
            		echo "</td>";
            		echo "<td align='right'>";
            		echo number_format($item->uprice,2,"."," ");
            		echo "</td>";
            		echo "<td align='right'>";
            		echo number_format($item->uprice*$item->qty,2,"."," ");
            		echo "</td>";
            		echo "</tr>";
            	}
            	echo "<tr>";
            	echo "<td colspan='4' align='right'>";
            	echo "<b>Sous-total:</b>";
            	echo "</td>";
            	echo "<td align='right'>";
            	echo "<b>".number_format($edit->subtotal,2,"."," ")."</b>";
            	echo "</td>";
            	echo "</tr>";
            	//Get the customer taxes
            	$request = request("SELECT `$TBL_ecommerce_tax_authority`.name,`$TBL_ecommerce_tax_item`.amount
                                                    FROM `$TBL_ecommerce_tax_authority`,`$TBL_ecommerce_tax_item`
                                                    WHERE `$TBL_ecommerce_tax_item`.`id_invoice`='$idInvoice'
                                                    AND `$TBL_ecommerce_tax_authority`.`id`=`$TBL_ecommerce_tax_item`.id_tax_authority",$link);
            	$amount = 0;
            	$grandtotal = $edit->subtotal;
            	while($taxes = mysql_fetch_object($request)){
            		$grandtotal+=$taxes->amount;
            		echo "<tr>";
            		echo "<td colspan='4' align='right'>";
            		echo "<b>$taxes->name:</b>";
            		echo "</td>";
            		echo "<td colspan='2' align='right'>";
            		echo "<b>".number_format($taxes->amount,2,"."," ")."</b>";
            		echo "</td>";
            		echo "</tr>";
            	}
            	echo "<tr>";
            	echo "<td colspan='4' align='right'>";
            	echo "<b>"._("Total").":</b>";
            	echo "</td>";
            	echo "<td colspan='2' align='right'>";
            	echo "<b>".number_format($grandtotal,2,"."," ")."</b>";
            	echo "</td>";
            	echo "</tr>";
            	echo "<tr>";
            	echo "<td colspan='4' align='right'>";
            	echo "<b>"._("Balance").":</b>";
            	echo "</td>";
            	echo "<td colspan='2' align='right'>";
            	echo "<b>".number_format($grandtotal-$edit->payed_amount,2,"."," ")."</b>";
            	echo "</td>";
            	echo "</tr>";
            	echo "</table>";
            }
            ?>
        </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Paiements"); ?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
    <tr>
        <td colspan='2'><br />
            <?php
            $request = request("
                SELECT `$TBL_ecommerce_payment_allocation`.`id_payment`,`$TBL_ecommerce_payment_allocation`.`amount`,`$TBL_ecommerce_payment`.`reception_date`
                FROM `$TBL_ecommerce_payment_allocation`, `$TBL_ecommerce_payment`
                WHERE `$TBL_ecommerce_payment_allocation`.`id_invoice`='$idInvoice'
                AND `$TBL_ecommerce_payment`.id=`$TBL_ecommerce_payment_allocation`.`id_payment`",$link);
            if(mysql_num_rows($request)){
            	echo "<table width='80%' align='center' border='1'>";
            	echo "<tr>";
            	echo "<td align='center'><b>"._("Paiement #")."</b></td>";
            	echo "<td align='center'><b>"._("Date")."</b></td>";
            	echo "<td align='center'><b>"._("Montant")."</b></td>";
            	echo "</tr>";
            	while($payment = mysql_fetch_object($request)){
            		echo "<tr>";
            		echo "<td><b><a href='mod_payment.php?idPayment=$payment->id_payment'>".id_format($payment->id_payment)."</a></b></td>";
            		echo "<td>".$payment->reception_date."</td>";
            		echo "<td align='right'>".$payment->amount."</td>";
            		echo "</tr>";
            	}
            	echo "</table>";
            } else echo "<center>"._("Aucun payement n'a été effetué pour cette facture.")."</center>";
            ?>
            <br />
        </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <a href='<?=$cancelLink?>'>
          <img src='../locales/<?=$used_language?>/images/button_backward.jpg' border='0'></a>
      </td>
    </tr>
  </table>
<!--Commandes -->
<?
include ("../html_footer.php");
mysql_close($link);
?>
