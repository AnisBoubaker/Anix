<?php

class orderItem{
	var $id = 0;
	var $idOrder = 0;
	var $reference;
	var $description;
	var $details;
	var $qty;
	var $uprice;
	var $idProduct;

	function orderItem($id = 0, $link = 0, $idOrder = 0, $reference = "", $description = "", $details = "", $qty=0 , $uprice = 0, $idProduct=0){
		global $TBL_ecommerce_invoice_item;
		if($id){
			if(!$link) $insideLink = dbConnect();
      		else $insideLink=$link;
			//load order from database
			$request=request("SELECT * FROM `$TBL_ecommerce_invoice_item` WHERE `id`='$id'",$insideLink);
			if(mysql_num_rows($request)){
				$dbItem = mysql_fetch_object($request);
				$this->id = $dbItem->id;
				$this->idOrder = $dbItem->id_order;
				$this->reference = $dbItem->reference;
				$this->description = $dbItem->description;
				$this->details = $dbItem->details;
				$this->qty = $dbItem->qty;
				$this->uprice = $dbItem->uprice;
				$this->idProduct = $dbItem->id_product;
			}
			if(!$link) mysql_close($insideLink);
		} else {
			//create blank order
			$this->id=$id;
			$this->idOrder = $idOrder;
			$this->reference = $reference;
			$this->description = $description;
			$this->details = $details;
			$this->qty = $qty;
			$this->uprice = $uprice;
			$this->idProduct = $idProduct;
		}
	}

	function save($link=0){
		global $TBL_ecommerce_invoice_item;
		if(!$link) $insideLink = dbConnect();
      	else $insideLink=$link;
      	$return=true;
      	if($this->id==0){
	      	request("INSERT INTO `$TBL_ecommerce_invoice_item` (`id_order`,`reference`,`description`,`details`,`qty`,`uprice`,`id_product`)
	              VALUES ('$this->idOrder',
	                      '$this->reference',
	                      '$this->description',
	                      '$this->details',
	                      '$this->qty',
	                      '$this->uprice',
	                      '$this->idProduct')",$insideLink);
	      	if(mysql_errno($insideLink)) $return=false;
	      	else $this->id=mysql_insert_id($insideLink);
      	} else {
      		request("UPDATE `$TBL_ecommerce_invoice_item` SET
      				`id_order`='$this->idOrder',
      				`reference`='$this->reference',
      				`description`='$this->description',
      				`details`='$this->details',
      				`qty`='$this->qty',
      				`uprice`='$this->uprice',
      				`id_product`='$this->idProduct'
      				WHERE `id`='$this->id'",$insideLink);
      		if(mysql_errno($insideLink)) $return=false;
      	}
      	if(!$link) mysql_close($insideLink);
		return $return;
	}

}