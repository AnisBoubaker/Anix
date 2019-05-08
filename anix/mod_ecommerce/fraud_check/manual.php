<?php
require_once("../../config.php");
$link = dbConnect();
//plugin config
$method="manual";
if(!isset($_GET["idOrder"]) && !isset($_POST["idOrder"])) die ("Commande non specifiee...");
$idOrder = $_REQUEST["idOrder"];
//load the order
$request = request("SELECT * FROM `$TBL_ecommerce_order` WHERE `id`='$idOrder'",$link);
if(!mysql_num_rows($request)) die ("Commande inexistante...");
$order = mysql_fetch_object($request);
if($order->status!='ordered' && $order->status!='invoiced' && $order->status!='payed') die ("Etat de la commande non valide pour verification...");

if(isset($_POST["addCheck"])){
	$requestStr="
		INSERT INTO `$TBL_ecommerce_fraud_check` (`id_order`,`method`,`result`,`info`,`check_date`)
		VALUES ('$idOrder','$method','".addslashes($_POST["result"])."','".addslashes($_POST["info"])."', NOW())
	";
	request($requestStr,$link);

	$requestStr ="UPDATE `$TBL_ecommerce_order` SET ";
	$requestStr.="`fraud_check_mode`='$method', ";
	$requestStr.="`fraud_check_result`='".addslashes($_POST["result"])."', ";
	$requestStr.="`fraud_check_info`='".addslashes($_POST["info"])."', ";
	$requestStr.="`fraud_check_date`=NOW() ";
	$requestStr.="WHERE `id`='$idOrder' ";
	request($requestStr,$link);
}
?>
<?php
	include("./html_header.php");
	if(!isset($_POST["addCheck"])){
?>
<script type='text/javascript'>
function submitForm(){
	box = document.checkForm.result;
	box_value = box.options[box.selectedIndex].value;
	if(box_value==0){
		alert('Veuillez choisir une probabilité de fraude.');
		return;
	}
	if(document.getElementById('info').value==""){
		alert('Veuillez décrire cette évaluation dans le champs Détails.');
		return;
	}
	document.checkForm.submit();
}
</script>
<br /><br /><br />
<form name='checkForm' method="post" action="./manual.php">
<table>
<tr>
<td><b>Probabilité de fraude: </b></td><td><select name='result' id='result'>
	<option value='0'>-- CHOISISSEZ --</option>
	<option value='<?php echo $ECOMMERCE_faud_level_low;?>'>Basse</option>
	<option value='<?php echo $ECOMMERCE_faud_level_medium;?>'>Moyenne</option>
	<option value='<?php echo $ECOMMERCE_faud_level_high;?>'>Élevée</option>
	<option value='<?php echo $ECOMMERCE_faud_level_alert;?>'>Critique</option>
</select></td>
</tr>
<tr>
<td><b>Détails:</b></td><td><input type='text' id='info' style='width:200px;' name='info' /></td>
</tr>
<tr><td>&nbsp;</td><td><input type='button' onclick='submitForm();' value='Valider' /></td></tr>
</table>
<input type='hidden' name='addCheck' value='1' />
<input type='hidden' name='idOrder' value='<?php echo $idOrder; ?>' />
</form>
<?php
	}else{
?>
<br /><br />
<p style='color:#0000ff;text-align:center'>L'évaluation a été prise en compte.</p>
<script type='text/javascript'>
	window.opener.hideFraud();
</script>
<?php
	}
	mysql_close($link);
	include("./html_footer.php");
?>