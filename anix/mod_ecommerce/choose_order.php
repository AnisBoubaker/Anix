<?php
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$title = _("Choix d'une commande a facturer");
$menu_ouvert = 2;$module_name="ecommerce";include("../html_header.php");
?>
<table border="0" align="center" width="90%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'>
      <?php echo _("Choisissez la commande a facturer"); ?>
    </font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
  </td>
</tr>
<tr>
  <td colspan='2'>
      <?php
        echo _("Veuillez choisir la commande à transformer en facture parmis les commande en cours suivantes");  ?>:<br><br>
      <table width='100%'>
      <tr>
        <td>&nbsp;</td>
        <td><b><?php echo _("#Commande");?></b></td>
        <td><b><?php echo _("Client");?></b></td>
        <td><b><?php echo _("Date");?></b></td>
        <td><b><?php echo _("Livraison");?></b></td>
        <td><b><?php echo _("Sous-total");?></b></td>
        <td><b><?php echo _("Montant percu");?></b></td>
      </tr>
      <?php
      $requestString = "
            SELECT `$TBL_ecommerce_order`.`id`,
                         `$TBL_ecommerce_order`.`order_date`,
                         `$TBL_ecommerce_order`.`delivery_date`,
                         `$TBL_ecommerce_order`.`subtotal`,
                         `$TBL_ecommerce_order`.`payed_amount`,
                         `$TBL_ecommerce_customer`.`firstname`,
                         `$TBL_ecommerce_customer`.`lastname`,
                         `$TBL_ecommerce_customer`.`company`
            FROM `$TBL_ecommerce_order`,
                      `$TBL_ecommerce_customer`
            WHERE `$TBL_ecommerce_order`.`status`='ordered'
            AND      `$TBL_ecommerce_order`.`id_client`=`$TBL_ecommerce_customer`.`id`
            ORDER BY `$TBL_ecommerce_customer`.`firstname`,`$TBL_ecommerce_customer`.`lastname`";

      $request = request($requestString,$link);
      while($order = mysql_fetch_object($request)){
      	echo "<tr>";
      	echo "  <td>&nbsp;</td>";
      	echo "  <td><a href='./mod_invoice.php?action=add&idOrder=$order->id'>$order->id</a></td>";
      	echo "  <td>$order->firstname $order->lastname ($order->company)</td>";
      	echo "  <td>$order->order_date</td>";
      	echo "  <td>$order->delivery_date</td>";
      	echo "  <td>$order->subtotal $</td>";
      	echo "  <td>$order->payed_amount $</td>";
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
  </td>
</tr>
</table>
<?
include ("../html_footer.php");
mysql_close($link);
?>
