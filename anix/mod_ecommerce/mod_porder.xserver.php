<?php
require_once("../config.php");

function addProduct($id,$idSupplier){
	global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages,$TBL_catalogue_brands;
	global $used_language_id;
	$objResponse = new xajaxResponse();
	$link = dbConnect();
	$requestStr = "SELECT  $TBL_catalogue_products.id,
						   $TBL_catalogue_products.ref_store,
						   $TBL_catalogue_info_products.name,
						   $TBL_catalogue_brands.name brand_name,
						   $TBL_catalogue_products.id_supplier1,
						   $TBL_catalogue_products.ref_supplier1,
						   $TBL_catalogue_products.cost_supplier1,
						   $TBL_catalogue_products.id_supplier2,
						   $TBL_catalogue_products.ref_supplier2,
						   $TBL_catalogue_products.cost_supplier2,
						   $TBL_catalogue_products.id_supplier3,
						   $TBL_catalogue_products.ref_supplier3,
						   $TBL_catalogue_products.cost_supplier3,
						   $TBL_catalogue_products.id_supplier4,
						   $TBL_catalogue_products.ref_supplier4,
						   $TBL_catalogue_products.cost_supplier4
					FROM ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages)
					LEFT JOIN $TBL_catalogue_brands ON ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id)
					WHERE $TBL_catalogue_products.id='$id'
					AND $TBL_catalogue_products.id=$TBL_catalogue_info_products.id_product
					AND $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id
					AND $TBL_gen_languages.id='$used_language_id'
					ORDER BY $TBL_catalogue_products.ordering";
	$request = request($requestStr,$link);

	if(!mysql_num_rows($request)) {
		return $objResponse;
	}

	$product = mysql_fetch_object($request);
	$productName = $product->name;
	if($product->brand_name!=null) $productName="[$product->brand_name] ".$productName;
	$productName=addslashes(unhtmlentities($productName));

	//determine the supplier price and reference
	$refSupplier = "";
	$costSupplier = "";
	for($i=1;$i<=4;$i++){
		$colId = "id_supplier".$i;
		$colRef = "ref_supplier".$i;
		$colCost = "cost_supplier".$i;
		if($product->$colId==$idSupplier){
			$refSupplier = $product->$colRef;
			$costSupplier = $product->$colCost;
			break;
		}
	}

	$objResponse->addScript("addPOrderLineItem('1',\"$product->ref_store\",\"$refSupplier\",\"$productName\",\"$costSupplier\",\"$product->id\");");

	return $objResponse;
}

function updateStock($idPOrder,$dummy=0){
	$objResponse = new xajaxResponse();

	try{
		$porder = new EcommercePOrder($idPOrder);
	} catch (Exception $e){
		$objResponse->addScript("alert(\"".$e->getMessage()."\")");
		return $objResponse;
	}

	try{
		$porder->updateStock();
	} catch (Exception $e){
		$objResponse->addScript("alert(\"".$e->getMessage()."\")");
		return $objResponse;
	}

	$objResponse->addScript("alert(\""._("Le stock a été mis à jour correctement.")."\");");


	return $objResponse;
}


require("./mod_porder.xcommon.php");
$xajax->processRequests();
?>