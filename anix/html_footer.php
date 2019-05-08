</div>
</div>
<?php
/*$ANIX_messages->addError("Erreur de test");
$ANIX_messages->addError("Erreur de test2");
$ANIX_messages->addError("Erreur de test3");
$ANIX_messages->addWarning("Warning de test");
$ANIX_messages->addMessage("Message de test");
$ANIX_messages->addMessage("Message de test");
$ANIX_messages->addMessage("Message de test");*/
$ANIX_messages->convertOldErrors($message,$errors,$errMessage);
if($ANIX_messages->hasMessages()){
?>
<div id='message_header' style='cursor:pointer;' onclick='showHideAnixMessage("<?php echo _("Détails"); ?>","<?php echo _("Fermer");?>")'>
	<table cellpadding="0" cellspacing="0">
	<tr style='height:20px;'>
		<?php
		if($ANIX_messages->nbMessages) echo "<td class='message' nowrap='nowrap'><img src='../images/icon_info_small.jpg' /> ".$ANIX_messages->nbMessages." "._("Message(s)")."</td>";
		if($ANIX_messages->nbWarnings) echo "<td class='warning' nowrap='nowrap'><img src='../images/icon_warning_small.jpg' /> ".$ANIX_messages->nbWarnings." "._("Avertissement(s)")."</td>";
		if($ANIX_messages->nbErrors) echo "<td class='error' nowrap='nowrap'><img src='../images/icon_error_small.gif' /> ".$ANIX_messages->nbErrors." "._("Erreur(s)")."</td>";
		?>
		<td id='message_button' class='closed'></td>
	</tr>
	</table>
</div>
<div id='message_details' style='display:none;z-index:1000;'>
<table width='100%;'>
<?php
	while($error=$ANIX_messages->getError()){
		echo "<tr>";
		echo "<td style='width:50px'><img src='../images/icon_error.jpg' alt='' /></td>";
		echo "<td style='padding:0 0 0 10px;border-bottom:1px dotted #000000;'>$error</td>";
		echo "</tr>";
	}
	while($error=$ANIX_messages->getWarning()){
		echo "<tr>";
		echo "<td style='width:50px'><img src='../images/icon_warning.jpg' alt='' /></td>";
		echo "<td style='padding:0 0 0 10px;border-bottom:1px dotted #000000;'>$error</td>";
		echo "</tr>";
	}
	while($error=$ANIX_messages->getMessage()){
		echo "<tr>";
		echo "<td style='width:50px'><img src='../images/icon_info.jpg' alt='' /></td>";
		echo "<td style='padding:0 0 0 10px;border-bottom:1px dotted #000000;'>$error</td>";
		echo "</tr>";
	}
	//if($ANIX_messages->nbErrors) echo "<script type='text/javascript'>showHideAnixMessage(\""._("Détails")."\",\""._("Fermer")."\");</script>";
?>
</table>
</div>
<?php
} //if($ANIX_messages->hasMessages())
?>
<script type='text/javascript'>
	<?php if($ANIX_messages->nbErrors) echo "showHideAnixMessage(\""._("Détails")."\",\""._("Fermer")."\");\n"; ?>
	hideSpinner();
</script>
</body>
</html>
