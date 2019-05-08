<?php
	require_once("../xajax.inc.php");
	$xajax = new xajax("./mod_product.xserver.php");
	include("../mod_links/ajaxfunctions.xcommon.php");
	$xajax->registerFunction("acceptReview");
	$xajax->registerFunction("deleteReview");
?>