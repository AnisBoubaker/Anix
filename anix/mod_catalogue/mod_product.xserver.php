<?php
require_once("../config.php");

require_once("../mod_links/ajaxfunctions.xserver.php");

function acceptReview($id){
	$objResponse = new xajaxResponse();
	$review = new CatalogueReview($id); //Loads the session saved cart
	try{
		$review->acceptReview();
		$review->save();
		$objResponse->addScript("acceptReview($id);");
	} catch (Exception $e){
		$objResponse->addScript("alert(\"".$e->getMessage()."\");");
	}
	return $objResponse;
}

function deleteReview($idReview,$confirmed=false){
	$objResponse = new xajaxResponse();
	if(!$confirmed){
		$objResponse->addScript("if(confirm(\""._("Êtes vous sûr de vouloir supprimer cet avis?")."\")) xajax_deleteReview($idReview,true);");
	} else {
		$review = new CatalogueReview($idReview);
		$review->delete();
		$objResponse->addScript("deleteReview($idReview);");
	}
	return $objResponse;
}

require("./mod_product.xcommon.php");
$xajax->processRequests();
?>