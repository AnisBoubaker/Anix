<?php
	require_once("../xajax.inc.php");
	$xajax = new xajax("./moderate_reviews.xserver.php");
	$xajax->registerFunction("acceptReview");
	$xajax->registerFunction("deleteReview");
?>