<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$errMessage="";
$message ="";
$action="";
$errors=0;
$nb=0;
?>

<? $title = _("Module E-Commerce");$menu_ouvert = -1;$module_name="ecommerce";include("../html_header.php"); ?>
<!-- Sales informations -->
<table border="0" width="48%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Ventes");?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
        &nbsp;
    </td>
    </tr>
    <tr>
    <td colspan='2'><br />
        <h3><?php echo _("Commandes").":";?></h3>
        <ul>
            <?php
            //Stand by orders
            $request = request("SELECT COUNT(*) nb FROM `$TBL_ecommerce_order` WHERE `status`='stand by'",$link);
            $tmp = mysql_fetch_object($request);
            echo "<li>";
            if($tmp->nb) echo "<a href='./search_SO.php?action=search_order&status=stand by&sc_id_client=&sc_num=&sc_order_from=&sc_order_to=&sc_delivery_from=&sc_delivery_to='>";
            printf(ngettext("%d commande en attente d'accompte", "%d commandes en attente d'accompte", $tmp->nb), $tmp->nb);
            if($tmp->nb) echo "</a>";
            echo "</li>\n";
            //Late delivery orders
            $request = request("SELECT COUNT(*) nb FROM `$TBL_ecommerce_order` WHERE (`status`='ordered' || `status`='invoiced') AND `shipping_date`='0000-00-00'",$link);
            $tmp = mysql_fetch_object($request);
            echo "<li>";
            //$t="./search_SO.php?action=search_order&delivery_to=".date('Y-m-d',time())."&status=ordered&shipped=non_shipped";
            if($tmp->nb) echo "<a href='./search_SO.php?action=search_order&status=ordered_invoiced&shipped=non_shipped'>";
            printf(ngettext("%d commande validée à livrer", "%d commandes validées à livrer", $tmp->nb), $tmp->nb);
            if($tmp->nb) echo "</a>";
            echo "</li>\n";
            //Not invoiced orders
            $request = request("SELECT COUNT(*) nb FROM `$TBL_ecommerce_order` WHERE `status`='ordered' AND `shipping_date`!='0000-00-00'",$link);
            $tmp = mysql_fetch_object($request);
            echo "<li>";
            if($tmp->nb) echo "<a href='./search_SO.php?action=search_order&status=ordered&shipped=shipped'>";
            printf(ngettext("%d commande livrées et non facturée", "%d commandes livrées mais non facturées", $tmp->nb), $tmp->nb);
            if($tmp->nb) echo "</a>";
            echo "</li>\n";
            ?>
        </ul>
        <h3><?php echo _("Factures").":";?></h3>
        <ul>
            <?php
            //Non issued invoices
            $request = request("SELECT COUNT(*) nb FROM `$TBL_ecommerce_invoice` WHERE `status`='created'",$link);
            $tmp = mysql_fetch_object($request);
            echo "<li>";
            if($tmp->nb) echo "<a href='./search_SI.php?action=search_invoice&status=created'>";
            printf(ngettext("%d facture non émise", "%d factures non émises", $tmp->nb), $tmp->nb);
            if($tmp->nb) echo "</a>";
            echo "</li>\n";
            //Late payment orders
            $request = request("SELECT COUNT(*) nb FROM `$TBL_ecommerce_invoice` WHERE `due_date`<='".date('Y-m-d',time()) ."' AND `status`='issued'",$link);
            $tmp = mysql_fetch_object($request);
            echo "<li>";
            if($tmp->nb) echo "<a href='./search_SI.php?action=search_invoice&due_to=".date('Y-m-d',time())."&status=issued'>";
            printf(ngettext("%d facture en retard de paiement", "%d factures en retard de paiement", $tmp->nb), $tmp->nb);
            if($tmp->nb) echo "</a>";
            echo "</li>\n";
            ?>
        </ul>
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

<?
include ("../html_footer.php");
mysql_close($link);
?>
