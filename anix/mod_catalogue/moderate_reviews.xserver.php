<?php
require_once("../config.php");

function acceptReview($id){
	$objResponse = new xajaxResponse();
	$review = new CatalogueReview($id); //Loads the session saved cart
	try{
		$review->acceptReview();
		$review->save();
		$objResponse->addScript("removeFromTable($id);");
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
		$objResponse->addScript("removeFromTable($idReview);");
	}
	return $objResponse;
}

require("./moderate_reviews.xcommon.php");
$xajax->processRequests();
?>