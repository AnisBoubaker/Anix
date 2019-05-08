<?php

Class Cart {
	var $cartType; //Order or Invoice

	var $LineItems; //Array of items. Each item is a LineItem instance.
	var $linesCount; //Number of items in the cart

	var $clientId;
	var $clientInfos; //Custumer informations (phone, fax, email)

	var $orderNumber; //Use for invoices

	var $GST;
	var $QST;

	//Constructor
	function Cart($cartType){
		$this->cartType = $cartType;
		$this->LineItems = array();
		$this->linesCount = 0;
	}
	//Add an item to cart and returns its Index in the cart table
	function addItem($qty,$code,$description,$details,$price){
		$this->LineItems[$this->linesCount]=new LineItem($qty,$code,$description,$details,$price);
		$this->linesCount++;

		return $this->linesCount-1;
	}

	function delItem($index){
		if(isset($index) && $index<$this->linesCount){
			for($i=$index;$i<$this->linesCount-1;$i++){
				$this->LineItems[$i]=$this->LineItems[$i+1];
			}
			unset($this->LineItems[$this->linesCount-1]);
			$this->linesCount--;
		}
	}
}

Class LineItem{
	var $qty;
	var $code;
	var $description;
	var $details;
	var $price;

	var $total;

	function LineItem($qty,$code,$description,$details,$price){
		$this->qty = $qty;
		$this->code = $code;
		$this->description = $description;
		$this->details = $details;
		$this->price = $price;
		$this->total = $this->price * $this->qty;
	}
}

?>
