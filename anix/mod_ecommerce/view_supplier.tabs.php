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
		<td colspan="2"><h2><?php echo $supplierObj->getName();?></h2></td>
	</tr>
	<tr>
		<td><b><?php echo _("Tél. ventes")?>:</b></td>
		<td><?php echo $supplierObj->getPhoneSales();?></td>
	</tr>
	<tr>
		<td><b><?php echo _("Tél. support")?>:</b></td>
		<td><?php echo $supplierObj->getPhoneSupport();?></td>
	</tr>
	<tr>
		<td><b><?php echo _("Site Web")?>:</b></td>
		<td><?php echo "<a href='http://".$supplierObj->getWebsiteURL()."' target='_blank'>".$supplierObj->getWebsiteURL()."</a>";?></td>
	</tr>
	<tr>
		<td><b><?php echo _("Commandes par courriel")?>:</b></td>
		<td><?php echo ($supplierObj->isAcceptEmailOrders()?_("Oui"):_("Non"));?></td>
	</tr>
	</table>
</td><!-- col1 -->
<td style='vertical-align:top;'><!--col2-->
	<table>
	<tr>
		<td colspan="2"><b><?php echo _("Représentant");?>:</b></td>
	</tr>
	<tr>
		<td><?php echo _("Nom")?>:</td>
		<td><?php echo $supplierObj->getContact();?></td>
	</tr>
	<tr>
		<td><?php echo _("Courriel")?>:</td>
		<td><?php echo "<a href='mailto:".$supplierObj->getContactEmail()."'>".$supplierObj->getContactEmail()."</a>";?></td>
	</tr>
	</table>
</td><!-- col2 -->
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