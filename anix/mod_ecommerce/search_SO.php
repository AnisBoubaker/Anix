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
if($action=="search_order"){
	//retrieve search criteria
	if(isset($_POST["num"])) $sc_num=$_POST["num"]; elseif(isset($_GET["num"])) $sc_num=$_GET["num"]; else $sc_num="";
	if(isset($_POST["id_client"])) $sc_id_client=$_POST["id_client"]; elseif(isset($_GET["id_client"])) $sc_id_client=$_GET["id_client"]; else $sc_id_client=0;
	if(isset($_POST["order_from"])) $sc_order_from=$_POST["order_from"]; elseif(isset($_GET["order_from"])) $sc_order_from=$_GET["order_from"]; else $sc_order_from="";
	if(isset($_POST["order_to"])) $sc_order_to=$_POST["order_to"]; elseif(isset($_GET["order_to"])) $sc_order_to=$_GET["order_to"]; else $sc_order_to="";
	if(isset($_POST["delivery_from"])) $sc_delivery_from=$_POST["delivery_from"]; elseif(isset($_GET["delivery_from"])) $sc_delivery_from=$_GET["delivery_from"]; else $sc_delivery_from="";
	if(isset($_POST["delivery_to"])) $sc_delivery_to=$_POST["delivery_to"]; elseif(isset($_GET["delivery_to"])) $sc_delivery_to=$_GET["delivery_to"]; else $sc_delivery_to="";
	if(isset($_POST["status"])) $sc_status=$_POST["status"]; elseif(isset($_GET["status"])) $sc_status=$_GET["status"]; else $sc_status="0";
	if(isset($_POST["shipped"])) $sc_shipped=$_POST["shipped"]; elseif(isset($_GET["shipped"])) $sc_shipped=$_GET["shipped"]; else $sc_shipped="0";
	if(isset($_POST["transporter"])) $sc_transporter=$_POST["transporter"]; elseif(isset($_GET["transporter"])) $sc_transporter=$_GET["transporter"]; else $sc_transporter="-1";
	if(isset($_POST["fraud_level"])) $sc_fraud_level=$_POST["fraud_level"]; elseif(isset($_GET["fraud_level"])) $sc_fraud_level=$_GET["fraud_level"]; else $sc_fraud_level="-3";
	//$tbl_List = "`$TBL_ecommerce_order`";
	$requestString ="SELECT DISTINCT `$TBL_ecommerce_order`.*,
    				 `$TBL_ecommerce_customer`.`firstname`,
    				 `$TBL_ecommerce_customer`.`lastname`,
    				 `$TBL_ecommerce_customer`.`company`
      				 FROM `$TBL_ecommerce_order`
      				 LEFT JOIN `$TBL_ecommerce_customer` ON (`$TBL_ecommerce_order`.`id_client`=`$TBL_ecommerce_customer`.`id`)
    				 WHERE 1 ";
	if($sc_id_client!=0){
		$requestString.=" AND `$TBL_ecommerce_order`.`id_client`='".$sc_id_client."' ";
		$nb++;
	}
	if($sc_num!=""){
		$requestString.=" AND `$TBL_ecommerce_order`.`id`='".$sc_num."' ";
		$nb++;
	}
	if($sc_order_from!=""){
		$requestString.=" AND `$TBL_ecommerce_order`.`order_date`>='".$sc_order_from."'";
		$nb++;
	}
	if($sc_order_to!=""){
		$requestString.=" AND `$TBL_ecommerce_order`.`order_date`<='".$sc_order_to."'";
		$nb++;
	}
	if($sc_delivery_from!=""){
		$requestString.=" AND `$TBL_ecommerce_order`.`delivery_date`>='".$sc_delivery_from."'";
		$nb++;
	}
	if($sc_delivery_to!=""){
		$requestString.=" AND `$TBL_ecommerce_order`.`delivery_date`<='".$sc_delivery_to."'";
		$nb++;
	}
	if($sc_status!="0"){
		if($sc_status=='ordered_invoiced')	$requestString.=" AND (`$TBL_ecommerce_order`.`status`='ordered' OR `$TBL_ecommerce_order`.`status`='invoiced')";
		else $requestString.=" AND `$TBL_ecommerce_order`.`status`='".$sc_status."'";
		$nb++;
	}
	if($sc_shipped!="0"){
		if($sc_shipped=="shipped") $requestString.=" AND `$TBL_ecommerce_order`.`shipping_date`!='0000-00-00'";
		else  $requestString.=" AND `$TBL_ecommerce_order`.`shipping_date`='0000-00-00'";
		$nb++;
	}
	if($sc_transporter!="-1"){
		$requestString.=" AND `$TBL_ecommerce_order`.`id_transporter`='".$sc_transporter."' ";
		$nb++;
	}
	if($sc_fraud_level!="-3"){
		if($sc_fraud_level=="-2") $requestString.=" AND `$TBL_ecommerce_order`.`fraud_check_mode`='none' ";
		elseif($sc_fraud_level=="-1") $requestString.=" AND `$TBL_ecommerce_order`.`fraud_check_mode`<>'none' AND `$TBL_ecommerce_order`.`fraud_check_result`='-1'";
		else{
			$requestString.=" AND `$TBL_ecommerce_order`.`fraud_check_mode`<>'none'";
			if($sc_fraud_level==$ECOMMERCE_faud_level_low) $requestString.=" AND `$TBL_ecommerce_order`.`fraud_check_result`>='0' AND `$TBL_ecommerce_order`.`fraud_check_result`<='$ECOMMERCE_faud_level_low'";
			elseif($sc_fraud_level==$ECOMMERCE_faud_level_medium) $requestString.=" AND `$TBL_ecommerce_order`.`fraud_check_result`>'$ECOMMERCE_faud_level_low' AND `$TBL_ecommerce_order`.`fraud_check_result`<='$ECOMMERCE_faud_level_medium'";
			elseif($sc_fraud_level==$ECOMMERCE_faud_level_high) $requestString.=" AND `$TBL_ecommerce_order`.`fraud_check_result`>'$ECOMMERCE_faud_level_medium' AND `$TBL_ecommerce_order`.`fraud_check_result`<='$ECOMMERCE_faud_level_high'";
			elseif($sc_fraud_level==$ECOMMERCE_faud_level_alert) $requestString.=" AND `$TBL_ecommerce_order`.`fraud_check_result`>'$ECOMMERCE_faud_level_high' AND `$TBL_ecommerce_order`.`fraud_check_result`<='$ECOMMERCE_faud_level_alert'";
		}

		$nb++;
	}
	$requestString.= " ORDER BY `$TBL_ecommerce_order`.`id`";
	$requestString.= " LIMIT $MAX_SEARCH_RESULTS";
	if($nb){
		$request=request($requestString,$link);
		$nbResults = mysql_num_rows($request);
	} else {
		$errors++;
		$errMessage.="- "._("Vous n'avez spécifié aucun critère de recherche.")."<br />";
	}
}
?>
<? $title = _("Recherche d'une commande (Vente)");$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php"); ?>
<form action='./search_SO.php' method='GET'>
<input type='hidden' name='action' value='search_order'>
<table border="0" align="center" width="40%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Recherche d'une commande (vente)");?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
        &nbsp;
    </td>
    </tr>
    <tr>
    <td colspan='2'>
        <table width='100%'>
        <tr>
	        <td><?php echo _("Numéro de commande"); ?>:</td>
	        <td><input type='text' name='num' size='20' <?
	        if($action=="search_order") echo "value='".$sc_num."'";
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
	            	if($action=="search_order" && $sc_id_client==$client->id) echo " SELECTED";
	            	echo ">";
	            	echo "[".$client->company."] ".$client->firstname." ".$client->lastname;
	            	echo "</option>";
	            }
	            ?>
	        </select></td>
        </tr>
        <tr>
	        <td><?php echo _("Date de commade"); ?>:</td>
	        <td>Du <input type='text' name='order_from' id='order_from' size='20' <?
	        if($action=="search_order") echo "value='".$sc_order_from."'";
	        ?> /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('order_from'),this);" style='vertical-align:bottom;' />
	        <br />AU <input type='text' name='order_to' id='order_to' size='20' <?
	        if($action=="search_order") echo "value='".$sc_order_to."'";
	        ?> /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('order_to'),this);" style='vertical-align:bottom;' />
	        </td>
        </tr>
        <tr>
	        <td><?php echo _("Date de livraison"); ?>:</td>
	        <td>Du <input type='text' name='delivery_from' id='delivery_from' size='20' <?
	        if($action=="search_order") echo "value='".$sc_delivery_from."'";
	        ?> /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('delivery_from'),this);" style='vertical-align:bottom;' />
	        <br />AU <input type='text' name='delivery_to' id='delivery_to' size='20' <?
	        if($action=="search_order") echo "value='".$sc_delivery_to."'";
	        ?> /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('delivery_to'),this);" style='vertical-align:bottom;' />
	        </td>
        </tr>
        <tr>
	        <td><?php echo _("État de la commande"); ?>:</td>
	        <td><select name='status'>
	            <option value='0' <?php  if($action=="search_order" && $sc_status=="0") echo " SELECTED";?>>-- <?php echo _("TOUS");?> --</option>
	            <option value='stand by' <?php  if($action=="search_order" && $sc_status=="stand by") echo " SELECTED";?>><?php echo _("En attente d'accompte");?></option>
	            <option value='ordered' <?php  if($action=="search_order" && $sc_status=="ordered") echo " SELECTED";?>><?php echo _("Commandes validées");?></option>
	            <option value='invoiced' <?php  if($action=="search_order" && $sc_status=="invoiced") echo " SELECTED";?>><?php echo _("Commandes facturées");?></option>
	            <option value='ordered_invoiced' <?php  if($action=="search_order" && $sc_status=="ordered_invoiced") echo " SELECTED";?>><?php echo _("Commandes validées ou facturées");?></option>
	            <option value='voided' <?php  if($action=="search_order" && $sc_status=="voided") echo " SELECTED";?>><?php echo _("Commandes annulées");?></option>
	        </select></td>
        </tr>
        <tr>
	        <td><?php echo _("État d'éxpedition"); ?>:</td>
	        <td><select name='shipped'>
	            <option value='0' <?php  if($action=="search_order" && $sc_shipped=="0") echo " SELECTED";?>>-- <?php echo _("TOUS");?> --</option>
	            <option value='shipped' <?php  if($action=="search_order" && $sc_shipped=="shipped") echo " SELECTED";?>><?php echo _("Commandes expédiées");?></option>
	            <option value='non_shipped' <?php  if($action=="search_order" && $sc_shipped=="non_shipped") echo " SELECTED";?>><?php echo _("Commandes NON expédiées");?></option>
	        </select></td>
        </tr>
        <tr>
	        <td><?php echo _("Transporteur"); ?>:</td>
	        <td><select name='transporter'>
	        	<option value='-1' <?php  if($action=="search_order" && $sc_transporter=="-1") echo " SELECTED";?>>-- <?php echo _("TOUS");?> --</option>
	        	<option value='0' <?php  if($action=="search_order" && $sc_transporter=="0") echo " SELECTED";?>>-- <?php echo _("Non défini");?> --</option>
		        <?php
		        $transportersRequest = request("SELECT `id`,`name` FROM `$TBL_ecommerce_shipping_transporters`,`$TBL_ecommerce_info_transporter` WHERE `$TBL_ecommerce_info_transporter`.`id_language`='$used_language_id' AND `$TBL_ecommerce_info_transporter`.`id_transporter`=`$TBL_ecommerce_shipping_transporters`.`id` ORDER BY `ordering`",$link);
		        while($transporter=mysql_fetch_object($transportersRequest)){
		        	echo "<option value='$transporter->id'";
		        	if($action=="search_order" && $sc_transporter=="$transporter->id") echo " SELECTED";
		        	echo ">".$transporter->name."</option>";
		        }
		        ?>
	        </select></td>
        </tr>
        <tr>
	        <td><?php echo _("Évaluation de fraude"); ?>:</td>
	        <td><select name='fraud_level'>
	        	<option value='-3' <?php  if($action=="search_order" && $sc_fraud_level=="-3") echo " SELECTED";?>>-- <?php echo _("TOUS");?> --</option>
	        	<option value='-2' <?php  if($action=="search_order" && $sc_fraud_level=="-2") echo " SELECTED";?>><?php echo _("Non évaluées");?></option>
	        	<option value='-1' <?php  if($action=="search_order" && $sc_fraud_level=="-1") echo " SELECTED";?>><?php echo _("En cours");?></option>
	        	<option value='<?php echo $ECOMMERCE_faud_level_low; ?>' <?php  if($action=="search_order" && $sc_fraud_level==$ECOMMERCE_faud_level_low) echo " SELECTED";?>><?php echo _("Bas");?></option>
	        	<option value='<?php echo $ECOMMERCE_faud_level_medium; ?>' <?php  if($action=="search_order" && $sc_fraud_level==$ECOMMERCE_faud_level_medium) echo " SELECTED";?>><?php echo _("Moyen");?></option>
	        	<option value='<?php echo $ECOMMERCE_faud_level_high; ?>' <?php  if($action=="search_order" && $sc_fraud_level==$ECOMMERCE_faud_level_high) echo " SELECTED";?>><?php echo _("Élevé");?></option>
	        	<option value='<?php echo $ECOMMERCE_faud_level_alert; ?>' <?php  if($action=="search_order" && $sc_fraud_level==$ECOMMERCE_faud_level_alert) echo " SELECTED";?>><?php echo _("Critique");?></option>
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
if(($action=="search_order" || $action=="search_category") && $nb){
?>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      <font class='edittable_header'><?php echo _("Résultats de la recherche"); ?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
       <?php
       if($nbResults){
       	echo "<a href='../download.php?file=orders.csv' style='text-decoration:none;'><img src='../images/excel.gif' /> <b>"._("Télécharger")."</b></a>";
       } else echo "&nbsp;";
      ?>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <center><?php
      if($nbResults<$MAX_SEARCH_RESULTS){
      	echo _("Votre recherche a retourné ");
      	printf(ngettext("%d résultat", "%d résultats", $nbResults), $nbResults);
      } else {
      	echo "<b>"._("ATTENTION:")."</b>"._("Votre recherche a retourné plus de ");
      	printf(ngettext("%d résultat", "%d résultats", $MAX_SEARCH_RESULTS), $MAX_SEARCH_RESULTS);
      	echo "<br />";
      	printf(ngettext("Seul le %d résultat a été retenu.", "Seuls les %d résultats on été retenus.", $MAX_SEARCH_RESULTS), $MAX_SEARCH_RESULTS);
      }
      ?></center><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width='100%' class='message'>
  <?
  if($nbResults){
  	$csv = new AnixCSV("orders.csv");
  	$csv->addLine(array(
  	_("Commande#"),
  	_("Client"),
  	_("Compagnie"),
  	_("État de la commande"),
  	_("Date"),
  	_("Livraison prévue le"),
  	_("Sous-total"),
  	_("Accompte requis"),
  	_("Payé"),
  	_("Facture#"),
  	));
  	$total_subtotal= 0;
  	$total_deposit= 0;
  	$total_payed= 0;
  ?>
	  <tr>
	        <td>&nbsp;</td>
	        <td align='center'><b>#<?php //echo _("Commande");?></b></td>
	        <td><b><?php echo _("Client");?></b></td>
	        <td align='center'><b><?php echo _("Status");?></b></td>
	        <td align='center'><b><?php echo _("Date");?></b></td>
	        <td align='center'><b><?php echo _("Livraison");?></b></td>
	        <td align='center'><b><?php echo _("Sous-total");?></b></td>
	        <td align='center'><b><?php echo _("Acompte requis");?></b></td>
	        <td align='center'><b><?php echo _("Acompte payé");?></b></td>
	    </tr>
	  <?
	  while($result=mysql_fetch_object($request)){
	  	echo "<tr>";
	  	echo "<td valign='middle' width='120' bgcolor='#e7eff2' align='right'>";
	  	if($result->status=="ordered" || $result->status=="stand by") echo "<a href='./del_order.php?id=$result->id'><img src='../images/del.gif' border='0' alt="._("Supprimer")."></a>";
	  	if($result->status=="ordered" || $result->status=="stand by") echo "<a href='./mod_order.php?idOrder=$result->id&action=edit'><img src='../images/edit.gif' border='0' alt="._("Modifier")."></a>";
	  	if($result->status!="voided" && $result->shipping_date=="0000-00-00") echo "<a href='./mod_order.php?idOrder=$result->id&action=edit'><img src='../images/shipping.gif' border='0' alt="._("Livraison")."></a>";
	  	echo "<a href='./view_order.php?id=$result->id'><img src='../images/view.gif' border='0' alt="._("Voir")."></a>";
	  	echo "<a href='./pdf/view_order.php?id=$result->id' target='_blank'><img src='../images/pdf.gif' border='0' alt="._("Version PDF")."></a>";
	  	if($result->status=="ordered"){
	  		echo "<a href='./mod_invoice.php?action=add&idOrder=$result->id'><img src='../images/invoice.gif' border='0' alt="._("Facturer")."></a>";
	  	}
	  	if($result->status=="stand by"){
	  		$to_pay = number_format($result->deposit_amount - $result->payed_amount,2,".","");
	  		echo "<a href='./mod_payment.php?action=add&idClient=$result->id_client&allocate=order&allocateid=$result->id&amount=$to_pay'><img src='../images/pay.gif' border='0' alt="._("Payer l'acompte")."></a>";
	  	}
	  	echo "</td>";
	  	//Display the ID and the fraud check result
	  	echo "<td align='center'>";
	  	if($result->fraud_check_mode=="none") echo "<img src='../images/fraud_level_unknown.jpg' alt=\""._("L\'évaluation de fraude n'a pas été effectuée.")."\" title=\""._("L'évaluation de fraude n'a pas été effectuée.")."\" style='vertical-align:middle;' />";
	  	else {
	  		$link_fraud_details="";
	  		$link_fraud_details_closing="";
	  		if(isset($ECOMMERCE_fraudcheck_methods[$result->fraud_check_mode]) && isset($ECOMMERCE_fraudcheck_methods[$result->fraud_check_mode]["details_url"])){
	  			$link_fraud_details="<a href='".$ECOMMERCE_fraudcheck_methods[$result->fraud_check_mode]["details_url"]."' target='_blank'>";
	  			$link_fraud_details = str_replace("%%ID_ORDER%%",$result->id,$link_fraud_details);
	  			$link_fraud_details_closing="</a>";
	  		}
	  		if($result->fraud_check_result==$ECOMMERCE_faud_level_awaiting) echo $link_fraud_details."<img src='../images/fraud_level_awaiting.jpg' alt='"._("Niveau de fraude: ÉVALUATION EN COURS")."' title='"._("Niveau de fraude: ÉVALUATION EN COURS")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
	  		elseif($result->fraud_check_result<=$ECOMMERCE_faud_level_low) echo $link_fraud_details."<img src='../images/fraud_level_low.jpg' alt='"._("Niveau de fraude: BAS")."' title='"._("Niveau de fraude: BAS")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
	  		elseif($result->fraud_check_result<=$ECOMMERCE_faud_level_medium) echo $link_fraud_details."<img src='../images/fraud_level_medium.jpg' alt='"._("Niveau de fraude: MOYEN")."' title='"._("Niveau de fraude: MOYEN")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
	  		elseif($result->fraud_check_result<=$ECOMMERCE_faud_level_high) echo $link_fraud_details."<img src='../images/fraud_level_high.jpg' alt='"._("Niveau de fraude: ÉLEVÉ")."' title='"._("Niveau de fraude: ÉLEVÉ")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
	  		elseif($result->fraud_check_result<=$ECOMMERCE_faud_level_alert) echo $link_fraud_details."<img src='../images/fraud_level_alert.jpg' alt='"._("Niveau de fraude: CRITIQUE")."' title='"._("Niveau de fraude: CRITIQUE")."' style='vertical-align:middle;border:0px;' />".$link_fraud_details_closing;
	  	}
	  	echo "&nbsp;<b>".$result->id."</b>";
	  	echo "</td>";
	  	echo "<td><b>";
	  	if($result->firstname!=null){
	  		echo "<a href='./view_client.php?idClient=$result->id_client'>$result->firstname $result->lastname</a>";
	  		if($result->company!="") echo "<br />".$result->company;
	  	} else {
	  		echo "<i>Supprimé</i>";
	  	}
	  	echo "</b></td>";
	  	echo "<td align='center'>";
	  	switch($result->status){
	  		case "stand by": echo $status=_("En attente d'accompte");"<font color='red'>$status</font>";break;
	  		case "ordered": echo $status=_("Validée");break;
	  		case "invoiced": echo $status=_("Facturée");break;
	  		case "voided": echo $status=_("Annulée");break;
	  	}
	  	echo "</td>";
	  	echo "<td align='center'>";
	  	echo $result->order_date;
	  	echo "</td>";
	  	echo "<td align='center'>";
	  	if($result->delivery_date < date('Y-m-d',time()) && ($result->status=="ordered" || $result->status=="stand by")) $late_order=true; else $late_order=false;
	  	if($late_order) echo "<font color='red'>";
	  	echo $result->delivery_date;
	  	if($late_order) echo "</font>";
	  	echo "</td>";
	  	echo "<td align='right'>";
	  	echo $result->subtotal." $currency_symbol";
	  	echo "</td>";
	  	echo "<td align='right'>";
	  	echo $result->deposit_amount ." $currency_symbol";
	  	echo "</td>";
	  	echo "<td align='right'>";
	  	echo $result->payed_amount ." $currency_symbol";
	  	echo "</td>";
	  	echo "</tr>";

	  	$total_subtotal+= $result->subtotal;
	  	$total_deposit+= $result->deposit_amount;
	  	$total_payed+= $result->payed_amount;

	  	/**
	  	 * ADD CSV DATA
	  	 */
	  	$csv->addLine(array(
	  	$result->id,
	  	unhtmlentities($result->firstname)." ".unhtmlentities($result->lastname),
	  	unhtmlentities($result->company),
	  	$status,
	  	$result->order_date,
	  	$result->delivery_date,
	  	"'".$result->subtotal,
	  	"'".$result->deposit_amount,
	  	"'".$result->payed_amount,
	  	($result->id_invoice==0?_("Aucune"):$result->id_invoice)
	  	));
	  } //while

	  /**
	  * WRITE TOTAL
	  */
	  $csv->addLine(array());
	  $csv->addLine(array());//2 empty lines
	  $csv->addLine(array("","","","","","TOTAL:","'".$total_subtotal, "'".$total_deposit,"'".$total_payed));

	  /**
	   * WRITE CSV DATA
	   */
	  $csv->write("../download/");
	  $csvCreated = true;
  }
  ?>
  	<tr>
  		<td colspan="6" style='text-align:right;border-top:1px solid #000000;'><b><?php echo _("TOTAL:"); ?></b></td>
  		<td style='text-align:right;border-top:1px solid #000000;'><b><?php echo number_format($total_subtotal,"2","."," ")." ".$currency_symbol; ?></b></td>
  		<td style='text-align:right;border-top:1px solid #000000;'><b><?php echo number_format($total_deposit,"2","."," ")." ".$currency_symbol; ?></b></td>
  		<td style='text-align:right;border-top:1px solid #000000;'><b><?php echo number_format($total_payed,"2","."," ")." ".$currency_symbol; ?></b></td>
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
<?
include ("../html_footer.php");
mysql_close($link);
?>
