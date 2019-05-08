<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$action="";
$nb=0;
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
?>
<?
if($action=="search_invoice"){
	//retrieve search criteria
	if(isset($_POST["num"])) $sc_num=$_POST["num"]; elseif(isset($_GET["num"])) $sc_num=$_GET["num"]; else $sc_num="";
	if(isset($_POST["id_client"])) $sc_id_client=$_POST["id_client"]; elseif(isset($_GET["id_client"])) $sc_id_client=$_GET["id_client"]; else $sc_id_client="";
	if(isset($_POST["id_order"])) $sc_id_order=$_POST["id_order"]; elseif(isset($_GET["id_order"])) $sc_id_order=$_GET["id_order"]; else $sc_id_order="";
	if(isset($_POST["invoice_from"])) $sc_invoice_from=$_POST["invoice_from"]; elseif(isset($_GET["invoice_from"])) $sc_invoice_from=$_GET["invoice_from"]; else $sc_invoice_from="";
	if(isset($_POST["invoice_to"])) $sc_invoice_to=$_POST["invoice_to"]; elseif(isset($_GET["invoice_to"])) $sc_invoice_to=$_GET["invoice_to"]; else $sc_invoice_to="";
	if(isset($_POST["due_from"])) $sc_due_from=$_POST["due_from"]; elseif(isset($_GET["due_from"])) $sc_due_from=$_GET["due_from"]; else $sc_due_from="";
	if(isset($_POST["due_to"])) $sc_due_to=$_POST["due_to"]; elseif(isset($_GET["due_to"])) $sc_due_to=$_GET["due_to"]; else $sc_due_to="";
	if(isset($_POST["status"])) $sc_status=$_POST["status"]; elseif(isset($_GET["status"])) $sc_status=$_GET["status"]; else $sc_status="";


	$tbl_List = "`$TBL_ecommerce_invoice`";
	$requestString ="
    		SELECT DISTINCT `$TBL_ecommerce_invoice`.*,
    		`$TBL_ecommerce_customer`.`firstname`,
			`$TBL_ecommerce_customer`.`lastname`,
			`$TBL_ecommerce_customer`.`company`
    		FROM (`$TBL_ecommerce_invoice`)
    		LEFT JOIN `$TBL_ecommerce_customer` ON (`$TBL_ecommerce_invoice`.`id_client`=`$TBL_ecommerce_customer`.`id`)
     		WHERE 1 ";
	if($sc_id_client!=0){
		$requestString.=" AND `$TBL_ecommerce_invoice`.`id_client`='".$sc_id_client."' ";
		$nb++;
	}
	if($sc_num!=0){
		$requestString.=" AND `$TBL_ecommerce_invoice`.`id`='".$sc_num."' ";
		$nb++;
	}
	if($sc_id_order!=0){
		$requestString.=" AND `$TBL_ecommerce_invoice`.`id_order`='".$sc_id_order."' ";
		$nb++;
	}
	if($sc_invoice_from!=""){
		$requestString.=" AND `$TBL_ecommerce_invoice`.`invoice_date`>='".$sc_invoice_from."'";
		$nb++;
	}
	if($sc_invoice_to!=""){
		$requestString.=" AND `$TBL_ecommerce_invoice`.`invoice_date`<='".$sc_invoice_to."'";
		$nb++;
	}
	if($sc_due_from!=""){
		$requestString.=" AND `$TBL_ecommerce_invoice`.`due_date`>='".$sc_due_from."'";
		$nb++;
	}
	if($sc_due_to!=""){
		$requestString.=" AND `$TBL_ecommerce_invoice`.`due_date`<='".$sc_due_to."'";
		$nb++;
	}
	if($sc_status!="0"){
		$requestString.=" AND `$TBL_ecommerce_invoice`.`status`='".$sc_status."'";
		$nb++;
	}
	if($nb){
		$request=request($requestString,$link);
		$nbResults = mysql_num_rows($request);
	} else {
		$errors++;
		$errMessage.="- "._("Vous n'avez spécifié aucun critère de recherche.")."<br />";
	}
}
?>
<? $title = _("Recherche d'un facture (Vente)");$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php"); ?>
<form action='./search_SI.php' method='GET'>
<input type='hidden' name='action' value='search_invoice'>
<table border="0" align="center" width="40%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Recherche d'une facture (vente)");?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
        &nbsp;
    </td>
    </tr>
    <tr>
    <td colspan='2'>
        <table width='100%'>
        <tr>
        <td><?php echo _("Numéro de facture"); ?>:</td>
        <td><input type='text' name='num' size='20' <?
        if($action=="search_invoice") echo "value='".$sc_num."'";
        ?>></td>
        </tr>
        <tr>
        <td><?php echo _("Client"); ?>:</td>
        <td><select name='id_client'>
            <option value='0'>-- <?php echo _("TOUS");?> --</option>
            <?php
            $clients = request("SELECT `id`,`firstname`,`lastname`,`company`
                                FROM `$TBL_ecommerce_customer`
                                ORDER BY company,firstname,lastname",$link);
            while($client = mysql_fetch_object($clients)){
            	echo "<option value='".$client->id."'";
            	if($action=="search_invoice" && $sc_id_client==$client->id) echo " SELECTED";
            	echo ">";
            	echo "[".$client->company."] ".$client->firstname." ".$client->lastname;
            	echo "</option>";
            }
            ?>
        </select></td>
        </tr>
        <tr>
        <td><?php echo _("Numéro de commande"); ?>:</td>
        <td><input type='text' name='id_order' size='20' <?
        if($action=="search_invoice") echo "value='".$sc_id_order."'";
        ?>></td>
        </tr>
        <tr>
        <tr>
        <td><?php echo _("Date de facturation"); ?>:</td>
        <td>Du <input type='text' name='invoice_from' id='invoice_from' size='20' <?
        if($action=="search_invoice") echo "value='".$sc_invoice_from."'";
        ?> /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('invoice_from'),this);" style='vertical-align:bottom;' />
        <br />AU <input type='text' name='invoice_to' id='invoice_to' size='20' <?
        if($action=="search_invoice") echo "value='".$sc_invoice_to."'";
        ?> /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('invoice_to'),this);" style='vertical-align:bottom;' />
        </td>
        </tr>
        <tr>
        <td><?php echo _("Date de paiement"); ?>:</td>
        <td>Du <input type='text' name='due_from' id='due_from' size='20' <?
        if($action=="search_invoice") echo "value='".$sc_due_from."'";
        ?> /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('due_from'),this);" style='vertical-align:bottom;' />
        <br />AU <input type='text' name='due_to' id='due_to' size='20' <?
        if($action=="search_invoice") echo "value='".$sc_due_to."'";
        ?> /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('due_to'),this);" style='vertical-align:bottom;' />
        </td>
        </tr>
        <tr>
        <td><?php echo _("État de la facture"); ?>:</td>
        <td><select name='status'>
            <option value='0' <?php  if($action=="search_invoice" && $sc_status==0) echo " SELECTED";?>>-- <?php echo _("TOUS");?> --</option>
            <option value='created' <?php  if($action=="search_invoice" && $sc_status=="created") echo " SELECTED";?>><?php echo _("Factures créés (en édition)");?></option>
            <option value='issued' <?php  if($action=="search_invoice" && $sc_status=="issued") echo " SELECTED";?>><?php echo _("Factures émises");?></option>
            <option value='payed' <?php  if($action=="search_invoice" && $sc_status=="payed") echo " SELECTED";?>><?php echo _("Factures payées");?></option>
            <option value='refunded' <?php  if($action=="search_invoice" && $sc_status=="voided") echo " SELECTED";?>><?php echo _("Factures remboursées");?></option>
            <option value='voided' <?php  if($action=="search_invoice" && $sc_status=="voided") echo " SELECTED";?>><?php echo _("Factures annulées");?></option>
        </select></td>
        </tr>
        </table>
    </td>
    </tr>
    <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
    </td>
    <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
    </td>
    </tr>
</table>
</form>

<?
if(($action=="search_invoice" || $action=="search_category") && $nb){
?>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      <font class='edittable_header'><?php echo _("Résultats de la recherche"); ?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
      <?php
      if($nbResults){
      	echo "<a href='../download.php?file=invoices.csv' style='text-decoration:none;'><img src='../images/excel.gif' /> <b>"._("Télécharger")."</b></a>";
      } else echo "&nbsp;";
      ?>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <center><?php
      echo _("Votre recherche a retourné ");
      printf(ngettext("%d résultat", "%d résultats", $nbResults), $nbResults);
      ?></center><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width='100%' class='message'>
  <?
  if($nbResults){
  		$csv = new AnixCSV("invoices.csv");
  		$csv->addLine(array(
  			_("Facture#"),
  			_("Client"),
  			_("Compagnie"),
  			_("État de la facture"),
  			_("Date"),
  			_("Échéance"),
  			_("Sous-total"),
  			_("Total"),
  			_("Payé"),
  			_("Commande#")
  		));
  		$total_subtotal= 0;
	  	$total_grandtotal= 0;
	  	$total_payed= 0;
	  	$total_remaining=0;
  ?>
	  <tr>
	        <td width='100'>&nbsp;</td>
	        <td align='center'><b>#</b></td>
	        <td align='center'><b><?php echo _("Client");?></b></td>
	        <td align='center'><b><?php echo _("Status");?></b></td>
	        <td align='center'><b><?php echo _("Date");?></b></td>
	        <td align='center'><b><?php echo _("Échéance");?></b></td>
	        <td align='center'><b><?php echo _("Sous-total");?></b></td>
	        <td align='center'><b><?php echo _("Total");?></b></td>
	        <td align='center'><b><?php echo _("Payé");?></b></td>
	        <td align='center'><b><?php echo _("Reste");?></b></td>
	    </tr>
	  <?
	  while($result=mysql_fetch_object($request)){
	  	echo "<tr>";
	  	echo "<td valign='middle' width='100' bgcolor='#e7eff2' align='right'>";
	  	if($result->status=="payed" && $result->refund!="Y"){
	  		echo "<a href='javascript:confirmRefund($result->id)'><img src='../images/refund.jpg' border='0' alt="._("Rembourser la facture")."></a>";
	  	}
	  	if($result->status=='created')echo "<a href='./mod_invoice.php?idInvoice=$result->id&action=edit'><img src='../images/edit.gif' border='0' alt="._("Modifier")."></a>";
	  	echo "<a href='./view_invoice.php?id=$result->id'><img src='../images/view.gif' border='0' alt="._("Voir")."></a>";
	  	echo "<a href='./pdf/view_invoice.php?id=$result->id' target='_blank'><img src='../images/pdf.gif' border='0' alt="._("Version PDF")."></a>";
	  	if($result->status=="created"){
	  		echo "<a href='./issue_invoice.php?action=issue_confirm&idInvoice=$result->id'><img src='../images/issue_invoice.gif' border='0' alt="._("Émettre la facture")."></a>";
	  	}
	  	if($result->status=="issued"){
	  		$to_pay = number_format($result->grandtotal - $result->payed_amount,2,".","");
	  		echo "<a href='./mod_payment.php?action=add&idClient=$result->id_client&allocate=invoice&allocateid=$result->id&amount=$to_pay'><img src='../images/pay.gif' border='0' alt="._("Payer l'acompte")."></a>";
	  	}
	  	echo "<td align='center'>".($result->refund=="Y"?"R":"").$result->id."</td>";
	  	echo "<td><b>";
	  	echo "<a href='./view_client.php?idClient=$result->id_client'>$result->firstname $result->lastname</a>";
	  	if($result->company!="") echo "<br />".$result->company;
	  	echo "<td align='center'>";
	  	switch($result->status){
	  		case "created": echo $status=_("Créée");break;
	  		case "issued": echo $status=_("Émise");break;
	  		case "payed": echo $status=_("Payée");break;
	  		case "voided": echo $status=_("Annulée");break;
	  		case "refunded": echo $status=_("Remboursée")." ($result->id_refund)";break;
	  	}
	  	echo "</td>";
	  	echo "<td align='center'>";
	  	echo $result->invoice_date;
	  	echo "</td>";
	  	echo "<td align='center'>";
	  	if($result->due_date < date('Y-m-d',time()) && $result->status=="issued" && $result->payed_amount < $result->grandtotal) $late_pay=true; else $late_pay=false;
	  	if($late_pay) echo "<font color='red'>";
	  	echo $result->due_date;
	  	if($late_pay) echo "</font>";
	  	echo "</td>";
	  	echo "<td align='right'>";
	  	echo $result->subtotal." $currency_symbol";
	  	echo "</td>";
	  	echo "<td align='right'>";
	  	echo $result->grandtotal ." $currency_symbol";
	  	echo "</td>";
	  	echo "<td align='right'>";
	  	echo $result->payed_amount ." $currency_symbol";
	  	echo "</td>";
	  	echo "<td align='right'>";
	  	echo number_format($result->grandtotal-$result->payed_amount,2,".","")." $currency_symbol";
	  	echo "</td>";
	  	echo "</tr>";

	  	$total_subtotal+= $result->subtotal;
	  	$total_grandtotal+= $result->grandtotal;
	  	$total_payed+= $result->payed_amount;
	  	$total_remaining+= $result->grandtotal-$result->payed_amount;

	  	/**
	  	 * ADD CSV DATA
	  	 */
	  	$csv->addLine(array(
	  		$result->id,
  			unhtmlentities($result->firstname)." ".unhtmlentities($result->lastname),
  			unhtmlentities($result->company),
  			$status,
  			$result->invoice_date,
  			$result->due_date,
  			"'".$result->subtotal,
  			"'".$result->grandtotal,
  			"'".$result->payed_amount,
  			$result->id_order
  		));
	  }
	  /**
	  * WRITE TOTAL
	  */
	  $csv->addLine(array());
	  $csv->addLine(array());//2 empty lines
	  $csv->addLine(array("","","","","","TOTAL:","'".$total_subtotal, "'".$total_grandtotal,"'".$total_payed));
	  /**
	   * WRITE CSV DATA
	   */
	  	$csv->write("../download/");
	  	$csvCreated = true;
  }
  ?>
  	<tr>
  		<td colspan="6" style='text-align:right;border-top:1px solid #000000;'><b><?php echo _("TOTAL:"); ?></b></td>
  		<td style='text-align:right;border-top:1px solid #000000;'><b><?php echo number_format($total_subtotal,2,"."," ")." ".$currency_symbol; ?></b></td>
  		<td style='text-align:right;border-top:1px solid #000000;'><b><?php echo number_format($total_grandtotal,2,"."," ")." ".$currency_symbol; ?></b></td>
  		<td style='text-align:right;border-top:1px solid #000000;'><b><?php echo number_format($total_payed,2,"."," ")." ".$currency_symbol; ?></b></td>
  		<td style='text-align:right;border-top:1px solid #000000;'><b><?php echo number_format($total_remaining,2,"."," ")." ".$currency_symbol; ?></b></td>
  	</tr>
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
<?
} //if search
?>
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
