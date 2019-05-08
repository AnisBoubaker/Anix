<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
<table style="width:100%">
<tr>
<td style='vertical-align:top;'><!--col1-->
	<b><?php echo _("Date de facturation").":";?></b><br />
      <input type='text' name='invoice_date' id='invoice_date' size='10' Maxlength='10'<?
      if($action=="edit") echo " value='".$edit->invoice_date."'";
      if($action=="insert" || $action=="update") echo " value='".$_POST["invoice_date"]."'";
      if($action=="add") echo " value='".date('Y-m-d',time())."'";
    ?> READONLY /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('invoice_date'),this);" style='vertical-align:bottom;' />
    <br /><br />
    <b><?php echo _("Termes").":";?></b>
    <?php
    $request=request("SELECT *
                            FROM `$TBL_ecommerce_terms`
                            ORDER BY `ordering`",$link);
    echo "<select name='id_terms'>";
    while($term = mysql_fetch_object($request)){
    	echo "<option value='$term->id'";
    	if(($action=="add" || $action=="insert") && $client->id_terms==$term->id) echo " selected='selected'";
    	if(($action=="edit" || $action=="update") && $edit->id_terms==$term->id) echo " selected='selected'";
    	echo ">$term->name</option>";
    }
    echo "</select><br /><br />";

    if($action=="edit" || $action=="update"){
    	echo "<b>"._("Date limite").":</b>&nbsp;";
    	echo $edit->due_date;
    	echo "<br /><br />";
    }
    ?>
</td><!--col1-->
<td style='vertical-align:top;'><!--col2-->
	<table>
	<tr><td colspan="2"><h3><?php echo _("Client"); ?>:</h3></td></tr>
	<tr>
		<td><?php echo _("Nom"); ?>:</td>
		<td><?php echo $client->firstname." ".$client->lastname;?></td>
	</tr>
	<tr>
		<td><?php echo _("Compagnie"); ?>:</td>
		<td><?php echo $client->company;?></td>
	</tr>
	<tr>
		<td><?php echo _("Téléphone"); ?>:</td>
		<td><?php echo $client->phone;?></td>
	</tr>
	<tr>
		<td><?php echo _("Cellulaire"); ?>:</td>
		<td><?php echo $client->cell;?></td>
	</tr>
	<tr>
		<td><?php echo _("Télécopie"); ?>:</td>
		<td><?php echo $client->fax;?></td>
	</tr>
	<tr>
		<td><?php echo _("Courriel"); ?>:</td>
		<td><?php echo "<a href='mailto:".$client->email."'>".$client->email."</a>";?></td>
	</tr>
	</table>
</td><!--col2-->
<td style='vertical-align:top;'><!--col3-->
	<b><?php echo _("Adresse de livraison").":";?></b><br><br />
	<?php echo nl2br($order_mailing); ?><br /><br />
	<b><?php echo _("Adresse de facturation").":";?></b><br><br>
    <textarea class='mceNoEditor' name='billing_address' cols='30' rows='5'><?
    if($action=="edit") echo $edit->billing_address;
    if($action=="insert" || $action=="update") echo $_POST["billing_address"];
    if($action=="add"){
    	echo $invoice_billing;
    }
    ?></textarea>
</td><!--col3-->
</tr>
</table>
<?php
TABS_closeTab();
TABS_closeTabManager();
if($action=="add" || $action=="insert"){
	/*TABS_disableTab(2);
	TABS_disableTab(3);*/
}
?>