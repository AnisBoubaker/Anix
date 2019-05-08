<?php
require_once("../config.php");
require_once("../custom/config.php");

function addProduct($id,$taxGroup){
	global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages,$TBL_catalogue_brands;
	global $TBL_ecommerce_tax_group,$TBL_ecommerce_tax_authority,$TBL_ecommerce_tax_group_authority;
	global $ECOMMERCE_product_prices_inclue_VAT,$used_language_id;
	$objResponse = new xajaxResponse();
	$link = dbConnect();

	$requestStr = "SELECT $TBL_catalogue_products.id,
						   $TBL_catalogue_products.ref_store,
						   $TBL_catalogue_products.public_price,
						   $TBL_catalogue_info_products.name,
						   $TBL_catalogue_brands.name brand_name
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

	$uprice = $product->public_price;

	$objResponse->addScript("addLineItem('1',\"$product->ref_store\",\"$productName\",\"$uprice\",\"$product->id\");");

	return $objResponse;
}

function updateStock($idOrder,$idInvoice,$confirmNoStock=true){
	global $TBL_catalogue_products, $TBL_ecommerce_invoice_item;

	$notInStock = array();

	$objResponse = new xajaxResponse();

	$link = dbConnect();
	//get the line items
	if($idOrder) $request = request("SELECT `id`,`id_product`,`qty`,`unstocked_qty` FROM `$TBL_ecommerce_invoice_item` WHERE `id_order`='$idOrder' AND `id_product`<>0 AND `unstocked_qty`<>`qty`",$link);
	elseif($idInvoice) $request = request("SELECT `id`,`id_product`,`qty`,`unstocked_qty` FROM `$TBL_ecommerce_invoice_item` WHERE `id_invoice`='$idInvoice' AND `id_product`<>0 AND `unstocked_qty`<>`qty`",$link);
	else return $objResponse;

	while($item = mysql_fetch_object($request)){
		$itemInStock = true;
		$difference = $item->qty - $item->unstocked_qty;
		if($difference>0 && $confirmNoStock){
			//If one of the items is not in stock, ask the user to confirm first
			$request2 = request("SELECT `stock`,`ref_store` FROM `$TBL_catalogue_products` WHERE `id`='$item->id_product'",$link);
			if(mysql_num_rows($request2)){
				$product = mysql_fetch_object($request2);
				if($product->stock<$difference){
					$itemInStock=false;
					$notInStock[]=array("ref"=>$product->ref_store,"stock"=>$product->stock,"required"=>$difference);
				}
			}
		}
		//update the product stock
		if($itemInStock || !$confirmNoStock){
			request("UPDATE `$TBL_catalogue_products` SET `stock`=`stock`-'$difference' WHERE `id`='$item->id_product'",$link);
			request("UPDATE `$TBL_ecommerce_invoice_item` SET `unstocked_qty`=`unstocked_qty`+'$difference' WHERE `id`='$item->id'",$link);
		}
	}
	mysql_close($link);

	if(!count($notInStock)) $objResponse->addScript("alert(\""._("Le stock a été mis à jour correctement.")."\");");
	else {
		$confirmStr = _("Certains produits ne sont pas en stock:")."\\n\\n";
		foreach ($notInStock as $id=>$item){
			if($id!=0) $confirmStr.=", ";
			$confirmStr.= $item["ref"]." ("._("Dispo").": ".$item["stock"]." / "._("Requis").": ".$item["required"].")";
		}
		$confirmStr.="\\n\\n"._("Souhaitez-vous quand même mettre à jour le stock pour ces produits? (le stock sera négatif)");
		$objResponse->addScript("if(confirm(\"$confirmStr\")) xajax_updateStock($idOrder,$idInvoice,0);");
	}

	return $objResponse;
}


require("./mod_invoice.xcommon.php");
$xajax->processRequests();
?>