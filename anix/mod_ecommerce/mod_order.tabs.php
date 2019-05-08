<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
<table style='width:100%'>
<tr>
<td style='vertical-align:top;'><!--col1-->
	<table>
	<tr>
		<td colspan="2"><b><?php echo _("Date de commande").":";?></b><br />
		<input type='text' name='order_date' id='order_date' size='10' Maxlength='10'<?
		  if($action=="edit") echo " value='".$edit->order_date."'";
		  if($action=="insert" || $action=="update") echo " value='".$_POST["order_date"]."'";
		  if($action=="add") echo " value='".date('Y-m-d',time())."'";
		?> READONLY /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('order_date'),this);" style='vertical-align:bottom;' /></td>
	</tr>
	<tr>
		<td colspan="2"><b><?php echo _("Livraison prévue le").":";?></b><br />
		<input type='text' name='delivery_date' id='delivery_date' size='10' Maxlength='10'<?
		  if($action=="edit") echo " value='".$edit->delivery_date."'";
		  if($action=="insert" || $action=="update") echo " value='".$_POST["delivery_date"]."'";
		  if($action=="add") echo " value='".date('Y-m-d',time())."'";
		?> READONLY onchange='showHideDeliveryNotification()' /><img src='../images/calendar.gif' onclick="scwNextAction=showHideDeliveryNotification.runsAfterSCW(this);scwShow(document.getElementById('delivery_date'),this);" style='vertical-align:bottom;' /><br /></td>
	</tr>
	<tr>
	<td colspan="2" id='delivery_notification_control' style='color:#ff0000;<?php
		if($action=="update" && isset($_POST["notify_delivery_change"])) echo "";
		else echo "display:none;";
		?>'>
		<b><?php echo _("Informer le client du changement");?></b> <input type='checkbox' name='notify_delivery_change' <?php
			if($action=="update" && isset($_POST["notify_delivery_change"])) echo "checked='checked'";
		?> />
	</td>
	</tr>
	<tr><td colspan="2"><br /><br /></td></tr>
	<tr>
		<td colspan="2"><b><?php echo _("Acompte requis");?>:</b><br />
		<input type='text' name='deposit_requested' size='5' Maxlength='10'<?php
	          if($action=="edit") echo " value='".$edit->deposit_requested."'";
	          if($action=="insert" || $action=="update") echo " value='".$_POST["deposit_requested"]."'";
	            ?>> %
	    </td>
	</tr>
	<?php if($action=="edit"){ ?>
	<tr>
		<td colspan="2"><?php echo _("Soit")." ".$edit->deposit_amount." ".$currency_symbol; ?></i></td>
	</tr>
	<?php } ?>
	<?php if($action=="edit"){ ?>
	<tr>
		<td colspan="2"><b><?php echo _("Montant perçu"); ?>:</b>
		<?php echo $edit->payed_amount." ".$currency_symbol; ?></td>
	</tr>
	<?php } ?>
	<tr><td colspan="2"><br /><br /></td></tr>
	<tr>
		<td><b><?php echo _("Demander un commentaire");?>:</b> <input type='checkbox' name='notify_comment_request' <?php
    	if($action=="update" && isset($_POST["notify_comment_request"])) echo "checked='checked'";
    	?> /></td>
	</tr>
	</table>
</td><!-- col1-->
<?php
/**
 * EXISTING CLIENT
 */
if(isset($client)){
?>
<td style='vertical-align:top;'><!--col2-->
	<table>
	<tr>
	<td colspan="2"><h3><?php echo _("Client");?>:</h3></td>
	</tr>
	<tr>
		<td><b><?php echo _("Nom");?>:</b></td>
		<td><?php echo $client->greating." ".$client->firstname." ".$client->lastname;?></td>
	</tr>
	<tr>
		<td><b><?php echo _("Compagnie");?>:</b></td>
		<td><?php echo $client->company;?></td>
	</tr>
	<tr>
		<td><b><?php echo _("Téléphone");?>:</b></td>
		<td><?php echo $client->phone;?></td>
	</tr>
	<tr>
		<td><b><?php echo _("Cellulaire");?>:</b></td>
		<td><?php echo $client->cell;?></td>
	</tr>
	<tr>
		<td><b><?php echo _("Fax");?>:</b></td>
		<td><?php echo $client->fax;?></td>
	</tr>
	<tr>
		<td><b><?php echo _("Courriel");?>:</b></td>
		<td><?php if($client->email!="") echo "<a href='mailto:$client->email'>$client->email</a>";?></td>
	</tr>
	</table>
</td><!-- col2-->
<td style='vertical-align:top;'><!--col3-->
	<b><?php echo _("Adresse de livraison").":";?></b><br><br>
    <TEXTAREA class='mceNoEditor' name='mailing_address' cols='30' rows='4'><?
    if($action=="edit") echo $edit->mailing_address;
    if($action=="insert" || $action=="update") echo $_POST["mailing_address"];
    if($action=="add"){
    	if($client->company!="") echo $client->company."\n";
    	echo $client->firstname." ".$client->lastname."\n";
    	echo $client_mailing->num." ".$client_mailing->street1."\n";
    	if($client_mailing->street2!="") echo $client_mailing->street2."\n";
    	$extraAddress="";
    	if($client_mailing->building!="") $extraAddress.="Bat.:".$client_mailing->building." ";
    	if($client_mailing->stairs!="") $extraAddress.="Esc.:".$client_mailing->stairs." ";
    	if($client_mailing->floor!="") $extraAddress.="Étage:".$client_mailing->floor." ";
    	if($client_mailing->code!="") $extraAddress.="Code:".$client_mailing->code." ";
    	if($extraAddress!="") echo $extraAddress."\n";
    	echo $client_mailing->city." ".$client_mailing->province."\n";
    	echo $client_mailing->zip." ".$client_mailing->country;
    }
    ?></TEXTAREA><br /><br />
    <b><?php echo _("Adresse de facturation").":";?></b><br><br>
    <TEXTAREA class='mceNoEditor' name='billing_address' cols='30' rows='4'><?
    if($action=="edit") echo $edit->billing_address;
    if($action=="insert" || $action=="update") echo $_POST["billing_address"];
    if($action=="add"){
    	if($client->company!="") echo $client->company."\n";
    	echo $client->firstname." ".$client->lastname."\n";
    	echo $client_billing->num." ".$client_billing->street1."\n";
    	if($client_billing->street2!="") echo $client_billing->street2."\n";
    	$extraAddress="";
    	if($client_billing->building!="") $extraAddress.="Bat.:".$client_billing->building." ";
    	if($client_billing->stairs!="") $extraAddress.="Esc.:".$client_billing->stairs." ";
    	if($client_billing->floor!="") $extraAddress.="Étage:".$client_billing->floor." ";
    	if($client_billing->code!="") $extraAddress.="Code:".$client_billing->code." ";
    	if($extraAddress!="") echo $extraAddress."\n";
    	echo $client_billing->city." ".$client_billing->province."\n";
    	echo $client_billing->zip." ".$client_billing->country;
    }
    ?></TEXTAREA>
</td><!-- col3-->
<?php
} //existing client
/**
 * NEW CLIENT
 */
elseif($action=="add" || $action=="insert"){
?>
<td style='vertical-align:top;'><!--col2-->
	<table>
	<td colspan="2"><h2><?php echo _("Nouveau client");?>:</h2></td>
	<tr>
		<td><b><?php echo _("Prénom"); ?>*:</b></td>
		<td>
		<select name='newclient_greating'>
		<option value='M' <?php if($action=="insert" && $_POST["newclient_greating"]=="M") echo "selected='selected'"?>>M</option>
		<option value='Mme' <?php if($action=="insert" && $_POST["newclient_greating"]=="Mme") echo "selected='selected'"?>>Mme</option>
		<option value='Mlle' <?php if($action=="insert" && $_POST["newclient_greating"]=="Mlle") echo "selected='selected'"?>>Mlle</option>
		</select>
		<input type='text' name='newclient_firstname' style='width:95px;' <?php
		if($action=="insert") echo " value=\"".$_POST["newclient_firstname"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Nom"); ?>*:</b></td>
		<td><input type='text' name='newclient_lastname'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_lastname"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Compagnie"); ?>:</b></td>
		<td><input type='text' name='newclient_company'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_company"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Téléphone"); ?>:</b></td>
		<td><input type='text' name='newclient_phone'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_phone"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Courriel"); ?>*:</b></td>
		<td><input type='text' name='newclient_email'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_email"]."\"";
		?> /></td>
	</tr>
	<tr><td><br /><br /></td></tr>
	<tr>
		<td><b><?php echo _("Login"); ?>*:</b></td>
		<td><input type='text' name='newclient_login'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_login"]."\"";
		?> /></td>
	</tr>
	<tr>
      <td><b><?php echo _("Langue")."*:";?></b></td>
      <td>
        <select name='newclient_language'>
          <option value='0'>--<?php echo _("Choisissez");?>--</option>
        <?
        $languages=request("SELECT id,name FROM `$TBL_gen_languages` WHERE used='Y' ORDER BY name",$link);
        while($language=mysql_fetch_object($languages)){
        	echo "<option value='".$language->id."'";
        	if(($action=="insert") && $_POST["newclient_language"]==$language->id)  echo " selected='selected'";
        	echo ">".$language->name."</option>";
        }
    	?>
        </select>
      </td>
    </tr>
	<tr>
        <td><b><?php echo _("Termes").":"; ?></b></td>
        <td>
        <?php
        $terms = request("SELECT * from `$TBL_ecommerce_terms` ORDER BY `ordering`",$link);
        echo "<select name='newclient_terms'>";
        while($term = mysql_fetch_object($terms)){
        	echo "<option value='".$term->id."'";
        	if(($action=="insert") && $_POST["newclient_terms"]==$term->id)  echo " selected='selected'";
        	echo ">".$term->name."</option>";
        }
        echo "</select>";
        ?>
        </td>
    </tr>
    <tr>
        <td><b><?php echo _("Taxes").":"; ?></b></td>
        <td>
        <?php
        $tax_groups = request("SELECT * from `$TBL_ecommerce_tax_group` ORDER BY ordering",$link);
        echo "<select name='newclient_tax_group'>";
        while($tax_group = mysql_fetch_object($tax_groups)){
        	echo "<option value='".$tax_group->id."'";
        	if(($action=="insert") && $_POST["newclient_tax_group"]==$tax_group->id)  echo " selected='selected'";
        	echo ">".$tax_group->name."</option>";
        }
        echo "</select>";
        ?>
        </td>
    </tr>
    <tr>
    	<td colspan="2">
    	<input type='checkbox' name='newclient_send_login' <?php
    		if($action=="insert" && isset($_POST["newclient_send_login"]))  echo " checked='checked'";
    		if($action=="add")  echo " checked='checked'";
    	?> /> <b><?php echo _("Envoyer les nouveaux codes d'accès au client"); ?></b>
    	</td>
    </tr>
	</table>
</td><!-- col2-->
<td style='vertical-align:top;'><!--col3-->
	<h3><?php echo _("Adresse"); ?>:</h3><br />
	<table>
	<tr>
		<td><b><?php echo _("Num."); ?>*:</b></td>
		<td><input type='text' name='newclient_num'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_num"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Rue."); ?>*:</b></td>
		<td><input type='text' name='newclient_street1'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_street1"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type='text' name='newclient_street2'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_street2"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Bâtiment"); ?>:</b></td>
		<td><input type='text' name='newclient_building'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_building"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Escalier"); ?>:</b></td>
		<td><input type='text' name='newclient_stairs'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_stairs"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Étage"); ?>:</b></td>
		<td><input type='text' name='newclient_floor'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_floor"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Code d'accès"); ?>:</b></td>
		<td><input type='text' name='newclient_code'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_code"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Ville"); ?>:*</b></td>
		<td><input type='text' name='newclient_city'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_city"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Code postal"); ?>:*</b></td>
		<td><input type='text' name='newclient_zip'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_zip"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Province"); ?>:</b></td>
		<td><input type='text' name='newclient_province'<?php
		if($action=="insert") echo " value=\"".$_POST["newclient_province"]."\"";
		?> /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Pays"); ?>:*</b></td>
		<td>
			<select name='newclient_country'>
          	<?php
          	$countryList = getCountriesList();
          	foreach($countryList as $countryCode => $countryName){
          		if($countryName!="separator"){
          			echo "<option value='".$countryCode."'";
          			if(($action=="insert") && $_POST["newclient_country"]==$countryCode)  echo " selected='selected'";
          			echo ">".$countryName."</option>";
          		} else {
          			echo "<option value='' disabled='disabled'>----------</option>";
          		}
          	}
			?>
          	</select>
    </td>
	</tr>
	</table>
</td><!-- col3-->
<?php
}//new client
?>
</tr>
</table>
<?php
TABS_closeTab();
/**
 * TAB2: E-COMMERCE
 */
TABS_addTab(2,_("Livraison"));
?>
<table>
<tr>
	<td><b><?php echo _("Transporteur"); ?>:</b></td>
	<td><select name='id_transporter'>
      	<option value='0'>-- <?php echo _("Non défini"); ?> --</option>
      	<?php
      	$jsCode_Shipping = "transportersTracking = new Array();\n";
      	$request2 = request("SELECT `id`,`name`,`tracking_url` FROM `$TBL_ecommerce_shipping_transporters`,`$TBL_ecommerce_info_transporter` WHERE `$TBL_ecommerce_info_transporter`.`id_language`='$used_language_id' AND `$TBL_ecommerce_info_transporter`.`id_transporter`=`$TBL_ecommerce_shipping_transporters`.`id` ORDER BY `ordering`",$link);
      	while($transporter = mysql_fetch_object($request2)){
      		echo "<option value='$transporter->id'";
      		if($action=="edit" && $edit->id_transporter==$transporter->id) echo " selected='selected'";
      		if(($action=="insert" || $action=="update") && $_POST["id_transporter"]==$transporter->id) echo " selected='selected'";
      		echo ">$transporter->name</option>";
      		$jsCode_Shipping.="transporters[$transporter->id]=";
      		if($transporter->tracking_url!='') $jsCode_Shipping.="true;\n";
      		else $jsCode_Shipping.="false;\n";
      	}
      	?>
      </select>
    </td>
</tr>
<tr>
	<td><b><?php echo _("Num. Suivi");?>:</b></td>
	<td>
		<input type='text' name='tracking' size='30' Maxlength='200'<?php
		if($action=="edit") echo " value='".$edit->tracking."'";
		if($action=="insert" || $action=="update") echo " value='".$_POST["tracking"]."'";
		?>>
	</td>
</tr>
<tr>
	<td><b><?php echo _("Date d'expédition");?>:</b></td>
	<td>
		<input type='text' name='shipping_date' id='shipping_date' size='10' Maxlength='10'<?php
		if($action=="edit" && $edit->shipping_date!="0000-00-00") echo " value='".$edit->shipping_date."'";
		if($action=="insert" || $action=="update") echo " value='".$_POST["shipping_date"]."'";
		?> onchange='showHideShippingNotification()' /><img src='../images/calendar.gif' onclick="scwNextAction=showHideShippingNotification.runsAfterSCW(this);scwShow(document.getElementById('shipping_date'),this);" style='vertical-align:bottom;' />
	</td>
</tr>
<tr id='shipping_notification_control' style='<?php
if($action=="update" && isset($_POST["notify_shipping_change"])) echo "";
else echo "display:none;";
?>'>
	<td><b><?php echo _("Informer le client");?>:</b></td>
	<td><input type='checkbox' name='notify_shipping_change' <?php
		if($action=="update" && isset($_POST["notify_delivery_change"])) echo "checked='checked'";
		?> />
	</td>
</tr>
</table>
<?php
TABS_closeTab();
/**
 * TAB2: Fraud detection
 */
$tabTitle = _("Fraude")." ";
if($action=="edit" && $edit->fraud_check_mode=="none") $tabTitle.="(?)";
TABS_addTab(3,$tabTitle);
if($action=="edit"){
?>
<?php
if($edit->fraud_check_mode=="none") echo "<img src='../images/fraud_level_unknown.jpg' alt=\""._("L\'évaluation de fraude n'a pas été effectuée.")."\" title=\""._("L'évaluation de fraude n'a pas été effectuée.")."\" style='vertical-align:middle;' /> "._("Non effectuée")."<br /><br />";
else{
	if($edit->fraud_check_result==$ECOMMERCE_faud_level_awaiting) echo "<img src='../images/fraud_level_awaiting.jpg' alt='"._("Niveau de fraude: ÉVALUATION EN COURS")."' title='"._("Niveau de fraude: ÉVALUATION EN COURS")."' style='vertical-align:middle;' /> "._("En cours");
	elseif($edit->fraud_check_result<=$ECOMMERCE_faud_level_low) echo "<img src='../images/fraud_level_low.jpg' alt='"._("Niveau de fraude: BAS")."' title='"._("Niveau de fraude: BAS")."' style='vertical-align:middle;' /> "._("Niveau de fraude: BAS");
	elseif($edit->fraud_check_result<=$ECOMMERCE_faud_level_medium) echo "<img src='../images/fraud_level_medium.jpg' alt='"._("Niveau de fraude: MOYEN")."' title='"._("Niveau de fraude: MOYEN")."' style='vertical-align:middle;' /> "._("Niveau de fraude: MOYEN");
	elseif($edit->fraud_check_result<=$ECOMMERCE_faud_level_high) echo "<img src='../images/fraud_level_high.jpg' alt='"._("Niveau de fraude: ÉLEVÉ")."' title='"._("Niveau de fraude: ÉLEVÉ")."' style='vertical-align:middle;' /> "._("Niveau de fraude: ÉLEVÉ");
	elseif($edit->fraud_check_result<=$ECOMMERCE_faud_level_alert) echo "<img src='../images/fraud_level_alert.jpg' alt='"._("Niveau de fraude: CRITIQUE")."' title='"._("Niveau de fraude: CRITIQUE")."' style='vertical-align:middle;' /> "._("Niveau de fraude: CRITIQUE");
	echo "<br /><br />";
	echo _("<u>Méthode d'évaluation:</u>")." ";
	if(isset($ECOMMERCE_fraudcheck_methods[$edit->fraud_check_mode])) echo $ECOMMERCE_fraudcheck_methods[$edit->fraud_check_mode]["name"];
	else echo "Méthode inconnue";
	echo "<br />";
	echo _("<u>Infos:</u> ").($edit->fraud_check_info!=""?$edit->fraud_check_info:_("Aucune information disponible"))."<br /><br />";
	//affiche un lien vers les details
	if(isset($ECOMMERCE_fraudcheck_methods[$edit->fraud_check_mode]["details_url"])) {
		$fraudcheck_url_details = str_replace("%%ID_ORDER%%",$idOrder,$ECOMMERCE_fraudcheck_methods[$edit->fraud_check_mode]["details_url"]);
		echo "<a href='".$fraudcheck_url_details."' target='_blank'>";
		echo _("Voir le détail");
		echo "</a>&nbsp;&nbsp;&nbsp;";
	}
	echo "<a href=\"javascript:void(window.open('./fraud_check/check_history.php?idOrder=$idOrder','fraud_history','resizable=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,fullscreen=no,dependent=no,width=800,height=500,left=200,top=150'))\">"._("Voir l'historique")."</a> ";
	echo "<br /><br />";
}
echo "<u>"._("Nouvelle/MAJ de l'évaluation:")."</u><br />";
echo "<select id='fraud_check_method'>";
echo "<option value='0'>-- CHOISISSEZ --";
foreach ($ECOMMERCE_fraudcheck_methods as $id => $method){
	echo "<option value='$id'>".$method["name"]."</option>";
}
echo "</select>";
echo "<input type='button' onclick='doFraudCheck(".($action=="edit"?$idOrder:0).")' value='GO' />";
?>
<div id='confirm_refresh' style='display:none'>
<p style='text-align:center;'><input type='button' onclick='pageRefresh();' value='Rafraichir la page' /></p>
</div>
<?php }//if action=edit ?>
<?php
TABS_closeTab();
TABS_closeTabManager();
if($action=="add" || $action=="insert"){
	TABS_disableTab(2);
	TABS_disableTab(3);
}
?>