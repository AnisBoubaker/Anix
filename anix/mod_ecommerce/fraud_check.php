<?php
include ("../config.php");
$error = false;
if(!isset($_GET["method"])) $error=true;
if(!isset($_GET["idOrder"])) $error=true;
if(!isset($ECOMMERCE_fraudcheck_methods[$_GET["method"]])) $error=true;
if($error){
	if(isset($_GET["idOrder"])){
		Header("Location: ./mod_order.php?idOrder=".$_GET["idOrder"]."&action=edit");
		exit();
	}
	else {
		Header("Location: ./");
		exit();
	}
}
Header("Location: ".$ECOMMERCE_fraudcheck_methods[$_GET["method"]]["anix_url"]."?idOrder=".$_GET["idOrder"]);
exit();
?>