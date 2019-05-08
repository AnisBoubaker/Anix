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
	$idOrder=$_POST["id"];
} elseif(isset($_GET["id"])){
	$idOrder=$_GET["id"];
} else $idOrder="";
?>
<?
$title = _("Commande");
$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php");
$result=request("SELECT *
	            FROM `$TBL_ecommerce_order`
	            WHERE `$TBL_ecommerce_order`.`id`='$idOrder'",$link);
if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette commande n'existe pas."));
$edit = mysql_fetch_object($result);
?>
<?
//Loads the customer
$result=request("SELECT *
                FROM `$TBL_ecommerce_customer`
                WHERE `$TBL_ecommerce_customer`.`id`='$edit->id_client'",$link);
if(mysql_num_rows($result)) { $client = mysql_fetch_object($result); $cancelLink="./view_client.php?idClient=$edit->id_client"; }
else { $client=null; $cancelLink="./index.php"; }
?>
<table border="0" align="center" width="70%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'> Commande</font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <?php
        if($edit->status=="stand by" || $edit->status=="ordered"){
        	echo "<a href='mod_order.php?idOrder=$edit->id&idClient=$edit->id_client&action=edit'><img src='../locales/$used_language/images/button_edit.jpg' border='0'></a>";
        }
        ?>
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
                if($client){
                	echo "<a href='./view_client.php?idClient=$edit->id_client'><B>".$client->firstname." ".$client->lastname."</a><br>";
                	echo $client->company."</b><br>";
                	echo _("Tel").": ".$client->phone."<br>";
                	echo _("Cellulaire").": ".$client->cell."<br>";
                	echo _("Telecopie").": ".$client->fax."<br>";
                	echo _("Courriel").": <a href='mailto:".$client->email."'>".$client->email."</a><br>";
                } else echo "<B>"._("Client supprimé")."</B>";
                ?>
            </td>
            <td align='right' valign='top' colspan="2">
                <h1><?php echo _("Commande #").$edit->id; ?><br />(<?php
                switch($edit->status){
                	case "stand by":echo _("En attente d'accompte");break;
                	case "ordered":echo _("Validée");break;
                	case "invoiced":echo _("Facturée");break;
                	case "payed":echo _("Payée");break;
                	case "voided":echo _("Annulée");break;
                }
                ?> )</h1>
                <?php
                echo "<b>"._("Date").":</b> ".$edit->order_date."<br />";
                echo "<b>"._("Date de livraison").":</b> ".$edit->delivery_date."<br /><br />";
                echo "<b>"._("Dépôt requis").":</b> ".$edit->deposit_amount."<br />";
                echo "<b>"._("Montant percu").":</b> ".$edit->payed_amount."<br />";
                if($edit->status=="invoiced") echo "<br /><br /><b>"._("Facture").": <a href='view_invoice.php?id=$edit->id_invoice'>#$edit->id_invoice</a>";
                ?>
            </td>
        </tr>
        <tr><td><br /></td></tr>
        <tr>
          <!--Colonne 1-->
          <td width='33%' valign='top'>
            <b><u><?php echo _("Adresse de livraison").":"; ?></b></u><br />
            <?
            echo nl2br($edit->mailing_address);
            ?>
          </td>
          <!--Colonne 2-->
          <td width='33%' valign='top'>
            <b><u><?php echo _("Adresse de facturation").":"; ?></b></u><br />
            <?
            echo nl2br($edit->billing_address);
            ?>
          </td>
          <td width='33%' valign='top'>
            <b><u><?php echo _("Livraison").":"; ?></b></u><br />
            <?
            if($edit->shipping_date=='0000-00-00') echo "<i>"._("NON EXPÉDIÉE")."</i>";
            else {
            	echo _("Expédiée le: ").$edit->shipping_date."<br />";
            	//get the transporter
            	$result = request("SELECT * FROM `$TBL_ecommerce_shipping_transporters`,`$TBL_ecommerce_info_transporter` WHERE `$TBL_ecommerce_shipping_transporters`.`id`='".$edit->id_transporter."' AND `$TBL_ecommerce_info_transporter`.`id_transporter`=`$TBL_ecommerce_shipping_transporters`.`id` AND `$TBL_ecommerce_info_transporter`.`id_language`='$used_language_id'",$link);
            	if(mysql_num_rows($result)){
            		$transporter = mysql_fetch_object($result);
            		echo _("Transporteur: ").$transporter->name."<br />";
            		if($transporter->tracking_url!="" && $edit->tracking!="") {
            			echo _("Suivi: ");
            			echo "<a href='".str_replace("%%TRACKINGID%%",$edit->tracking,$transporter->tracking_url)."' target='_blank'>".$edit->tracking."</a>";
            		}
            	}
            }
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
                                  WHERE id_order='$edit->id' ORDER BY `id`",$link);
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
            		echo unhtmlentities($item->reference);
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
