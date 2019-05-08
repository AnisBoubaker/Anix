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
		<td><?php echo _("Nom"); ?>(*):</td>
		<td><input type='text' name='name' size='20' <?
		if($action=="edit") echo " value=\"".$supplierObj->getName()."\"";
		if($action=="insert" || $action=="update")  echo " value=\"".$_POST["name"]."\"";
		?>>
		</td>
	</tr>
    <tr>
      <td>
        <?php echo _("Tel. service des ventes"); ?>:
      </td>
      <td>
        <input type='text' name='tel_sales' size='20' <?
        if($action=="edit") echo " value=\"".$supplierObj->getPhoneSales()."\"";
        if($action=="insert" || $action=="update")  echo " value=\"".$_POST["tel_sales"]."\"";
        ?>>
      </td>
    </tr>
    <tr>
      <td>
        <?php echo _("Tel. service de soutien"); ?>:
      </td>
      <td>
        <input type='text' name='tel_support' size='20' <?
        if($action=="edit") echo " value=\"".$supplierObj->getPhoneSupport()."\"";
        if($action=="insert" || $action=="update")  echo " value=\"".$_POST["tel_support"]."\"";
        ?>>
      </td>
    </tr>
    <tr>
	  <td>
	     <?php echo _("Délai de livraison"); ?>:
	  </td>
	  <td>
	    <input type='text' name='delivery_delay' size='20' <?
	    if($action=="edit") echo " value=\"".$supplierObj->getDeliveryDelay()."\"";
	    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["delivery_delay"]."\"";
	    ?>> <?php echo _("Jours"); ?>
	  </td>
	</tr>
    <tr>
	  <td>
	     <?php echo _("Site Web (URL)"); ?>:
	  </td>
	  <td>
	    <input type='text' name='url' size='40' <?
	    if($action=="edit") echo " value=\"".$supplierObj->getWebsiteURL()."\"";
	    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["url"]."\"";
	    ?>>
	  </td>
	</tr>
    </table>
</td><!-- col1 -->
<td style='vertical-align:top;'><!--col2-->
	<table>
	<tr>
	<td colspan="2"><b><?php echo _("Représentant"); ?>:</b></td>
	</tr>
	<tr>
      <td>
        <?php echo _("Nom"); ?>:
      </td>
      <td>
        <input type='text' name='contact' size='20' <?
        if($action=="edit") echo " value=\"".$supplierObj->getContact()."\"";
        if($action=="insert" || $action=="update")  echo " value=\"".$_POST["contact"]."\"";
        ?>>
      </td>
    </tr>
    <tr>
      <td>
        <?php echo _("Courriel"); ?>:
      </td>
      <td>
        <input type='text' name='contact_email' size='20' <?
        if($action=="edit") echo " value=\"".$supplierObj->getContactEmail()."\"";
        if($action=="insert" || $action=="update")  echo " value=\"".$_POST["contact_email"]."\"";
        ?>>
      </td>
    </tr>
	</table>
</td><!-- col2 -->
</tr>
</table>
<?php
TABS_closeTab();
TABS_addTab(2,_("Commandes"));
?>
<br /><br />
<table width="100%">
<tr>
	<td style="vertical-align:top;"><!--col1 -->
	<input type='checkbox' id='accept_email_orders' name='accept_email_orders'<?php
		if($action=="edit" && $supplierObj->isAcceptEmailOrders()) echo " checked='checked'";
		if(($action=="insert" || $action=="update") && isset($_POST["accept_email_orders"])) echo " checked='checked'";
	?> onchange="javascript:showHideEmailDetails();" />
	<?php echo _("Ce fournisseur accepte de recevoir des commandes par courriel"); ?>
	</td><!--col1-->
	<td><!--col2 -->
	<table id='orders_details'>
	<tr>
		<td><b><?php echo _("Courriel"); ?>:</b></td>
		<td>
			<input type='text' name='orders_email' size='20' <?
			if($action=="edit") echo " value=\"".$supplierObj->getOrdersEmail()."\"";
			if($action=="insert" || $action=="update")  echo " value=\"".$_POST["orders_email"]."\"";
		?> />
		</td>
	</tr>
	<tr>
		<td><b><?php echo _("Nom de l'expéditeur"); ?>:</b></td>
		<td>
			<input type='text' name='orders_sender' size='20' <?
			if($action=="edit") echo " value=\"".$supplierObj->getOrdersSenderName()."\"";
			if($action=="insert" || $action=="update")  echo " value=\"".$_POST["orders_sender"]."\"";
		?> />
		</td>
	</tr>
	<tr>
		<td><b><?php echo _("Courriel de l'expéditeur"); ?>:</b></td>
		<td>
			<input type='text' name='orders_sender_email' size='20' <?
			if($action=="edit") echo " value=\"".$supplierObj->getOrdersSenderEmail()."\"";
			if($action=="insert" || $action=="update")  echo " value=\"".$_POST["orders_sender_email"]."\"";
		?> />
		</td>
	</tr>
	</table>
	</td><!--col2 -->
</tr>
</table><br /><br />
<table id='orders_email_template'>
<tr>
	<td>
	<b><?php echo _("Modèle du courriel"); ?>:</b><br />
	<textarea name='email_template' class='mceNoEditor' cols='70' rows='20'><?php
	if($action=="edit") echo $supplierObj->getEmailTemplate();
	if($action=="insert" || $action=="update") echo $_POST["email_template"];
	?></textarea>
	</td>
	<td style="vertical-align:top;">
	<b><?php echo _("Champs authorisés");?>:</b><br /><br />
	<?php
	$fields = EcommerceSupplier::getAvailableOrderFields();
	foreach($fields as $str => $field){
		echo "<li><i>".$str."</i>: ";
		echo $field[0];
		//is the field mandatory?
		if($field[1]) echo " ["._("obligatoire")."]";
		echo "<br /><br /></li>";
	}
	?>
	</td>
</tr>
<tr>
	<td>
	<b><?php echo _("En-tête de ré-expédition"); ?>:</b><br />
	<textarea name='email_resend_header' class='mceNoEditor' cols='70' rows='20'><?php
	if($action=="edit") echo $supplierObj->getEmailResendHeader();
	if($action=="insert" || $action=="update") echo $_POST["email_resend_header"];
	?></textarea>
	</td>
	<td style="vertical-align:top;"><br /><br />
	<?php
	echo _("Cet en-tête, si renseigné, sera ajouté en haut du courriel si la commande a été ré-expédiée par courriel au fournissuer.");
	echo "<br /><br />";
	echo _("Ceci est utile pour avertir le founisseur que le courriel ne représente pas une nouvelle commande mais un rappel ou pour obtenir l'état de la commande.");
	echo "<br /><br />";
	echo "<b><i>"._("Note").":</i></b> "._("Les champs autorisés ci-dessus peuvent être ré-utilisés dans l'entête également.");
	?>
	</td>
</tr>
</table>
<?php
TABS_closeTab();
TABS_closeTabManager();
/*if($action=="add" || $action=="insert"){
	TABS_disableTab(2);
	TABS_disableTab(3);
}*/
?>