<?php
	require_once("../xajax.inc.php");
	$xajax = new xajax("./mod_invoice.xserver.php");
	$xajax->registerFunction("addProduct");
	$xajax->registerFunction("updateStock");
?>