<?php
function addLinks(){
	$objResponse = new xajaxResponse();
	$cart = new LinkCart(); //Loads the session saved cart
	try{
		$cart->commitCart();
		$objResponse->addScript("alert(\""._("Les liens ont bien été ajoutés.")."\");");
		//Update the links on page
		$objResponse = updateLinks($objResponse,$cart->getFromModule(),$cart->getFromId(),$cart->getCategoryId());
	} catch (Exception $e){
		$objResponse->addScript("alert(\"".$e->getMessage()."\");");
	}
	return $objResponse;
}

function moveLinkUp($idLink){
	$objResponse = new xajaxResponse();

	$link = new Link($idLink);
	$link->moveUp();

	$objResponse = updateLinks($objResponse,$link->getFromModule(),$link->getFromItem(),$link->getCategoryId());
	return $objResponse;
}


function moveLinkDown($idLink){
	$objResponse = new xajaxResponse();

	$link = new Link($idLink);
	$link->moveDown();

	$objResponse = updateLinks($objResponse,$link->getFromModule(),$link->getFromItem(),$link->getCategoryId());
	return $objResponse;
}

function deleteLink($idLink,$confirmed=false){
	$objResponse = new xajaxResponse();
	if(!$confirmed){
		$objResponse->addScript("if(confirm(\""._("Êtes vous sûr de vouloir supprimer ce lien?")."\")) xajax_deleteLink($idLink,true);");
	} else {
		$link = new Link($idLink);
		$link->delete();
		$objResponse = updateLinks($objResponse,$link->getFromModule(),$link->getFromItem(),$link->getCategoryId());
	}
	return $objResponse;
}

function updateLinks(xajaxResponse $objResponse,$module, $item, $category){
	$links = new LinkList($module,$item,$category);
	$links->setIteratorCategory($category);
	$objResponse->addScript("links_table[$category]=Array();\n");
	$counter=0;
	if($links->categoryHasLinks($category)) foreach($links as $link){
		$objResponse->addScript("links_table[$category][$counter]=Array(".$link->getId().",\"".$link->getToInfos()."\");\n");
		$counter++;
	}
	$objResponse->addScript("updateLinks($category);\n");
	return $objResponse;
}
?>