<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
<table style='width:100%'>
<tr>
<td> <!-- col1 -->
	<table>
	<tr>
		<td colspan="2"><h3><?php echo _("Fournisseur").": ".$supplier->getName();?></h3></td>
	</tr>
	<tr>
		<td><b><?php echo _("Date");?>:</b></td>
		<td><input type='text' name='porder_date' id='porder_date' size='10' Maxlength='10'<?
		if($action=="edit") echo " value='".$porder->getOrderDate()."'";
		if($action=="insert" || $action=="update") echo " value='".$_POST["porder_date"]."'";
		if($action=="add") echo " value='".date('Y-m-d',time())."'";
		?> READONLY /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('porder_date'),this);" style='vertical-align:bottom;' /></td>
	</tr>
	<tr>
		<td><b><?php echo _("Réception prévue le");?>:</b></td>
		<td><input type='text' name='porder_expected_date' id='porder_expected_date' size='10' Maxlength='10'<?
		if($action=="edit") echo " value='".$porder->getExpectedReceptionDate()."'";
		if($action=="insert" || $action=="update") echo " value='".$_POST["porder_expected_date"]."'";
		if($action=="add"){
			$date = mktime();
			$date1= mktime(0,0,0,date("m",$date),date("d",$date)+$supplier->getDeliveryDelay(),date("Y",$date));
			$expectedDate = date("Y-m-d",$date1);
			echo " value='$expectedDate'";
		}
		?> READONLY /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('porder_expected_date'),this);" style='vertical-align:bottom;' /></td>
	</tr>
	</table>
</td> <!-- col1 -->
<td> <!-- col2 -->
	<table>
	<tr>
		<td>
			<input type="checkbox" id='order_sent' name='order_sent' <?php
			if($action=="edit" && $porder->isOrderSent()) echo "checked='checked'";
			if(($action=="insert" || $action=="update") && isset($_POST["order_sent"])) echo "checked='checked'";
			?> onchange="javascript:showHideSentDate();" />
			<b><?php echo _("Commande envoyée");?></b>
		</td>
		<td id='sent_date'>
			<?php echo _("Le");?>: <input type='text' name='porder_sent_date' id='porder_sent_date' size='10' Maxlength='10'<?
			if($action=="edit"){
				if($porder->isOrderSent()) echo " value='".$porder->getSentDate()."'";
				else echo " value='".date("Y-m-d")."'";
			}
			if($action=="insert" || $action=="update") echo " value='".$_POST["porder_sent_date"]."'";
			?> READONLY /><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('porder_sent_date'),this);" style='vertical-align:bottom;' />
		</td>
	</tr>
	</table>
</td> <!-- col2 -->
</tr>
</table>
<?php
TABS_closeTab();
TABS_closeTabManager();
?>