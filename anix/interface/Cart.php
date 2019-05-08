<?php
class Cart{
	//variables
	var $userId=0;
	var $nbItems;
	var $items;
	var $subTotal;
	var $total;
	var $grandTotal;
	var $nextId; //stores the next id in the items table (for insertion)
	var $currentPos; //stores the current id for the fetch_items function
	var $totalWeight=0; //weight in grams
	var $shippingCalculator=null;
	var $selectedShipping=0;
	var $shippingFees = 0;
	var $insuranceFees = 0;
	var $ecotaxe = 0;
	var $insuranceSubscribed = true;
	var $shippingOptions = array();
	var $insuranceOptions = array();
	var $deliveryDelay=0;
	var $taxes = array();

	//constructor
	function Cart($userId=0){
		$this->nbItems=0;
		$this->items=array();
		$this->nextId = 0;
		$this->currentPos = 0;
		$this->subTotal = 0;
		$this->total = 0;
		$this->grandTotal = 0;
		$this->totalWeight=0;
		$this->taxes = array();
	}

	function setUserId($id){
		$this->userId = $id;
		$this->updateTaxes();
	}

	//add to cart
	//returns the id of the added item in the cart
	function addItem($itemId,$itemName,$itemRef,$price,$qty,$weight,$ecotaxe,$deliveryDelay=0){
		//check if domain already present in the cart
		if(isset($this->items[$itemId])){
			$this->changeQty($itemId, $this->items[$itemId]["qty"]+$qty,$price);
			/*$this->items[$itemId]["qty"]+=$qty;
			$this->items[$itemId]["subtotal"]+=$this->items[$itemId]["uprice"]*$qty;
			$this->subTotal+=$this->items[$itemId]["uprice"]*$qty;
			$this->totalWeight+=$this->items[$itemId]["uweight"]*$qty;
			$this->ecotaxe+=$this->items[$itemId]["ecotaxe"]*$qty;*/
		}
		else{
			$this->items[$itemId]=array();
			$this->items[$itemId]["id"]=$itemId;
			$this->items[$itemId]["name"]=$itemName;
			$this->items[$itemId]["ref"]=$itemRef;
			$this->items[$itemId]["uprice"]=$price;
			$this->items[$itemId]["uweight"]=$weight;
			$this->items[$itemId]["qty"]=$qty;
			$this->items[$itemId]["ecotaxe"]=$ecotaxe;
			$this->items[$itemId]["subtotal"]=$price*$qty;
			$this->subTotal+=$price*$qty;
			$this->totalWeight+=$weight*$qty;
			$this->ecotaxe+=$ecotaxe*$qty;
			$this->nbItems++;
			$this->items[$itemId]["deliveryDelay"]=$deliveryDelay;
			//$this->deliveryDelay = $this->items[$itemId]["deliveryDelay"]>$this->deliveryDelay?$this->items[$itemId]["deliveryDelay"]:$this->deliveryDelay;
			$this->computeDelays();
		}
		$this->updateShippingInsuranceFees();
		$this->updateTaxes();
	}

	/**
	 * Returns the qty added to cart for a particular item.
	 * Returns 0 if the item is not present in Cart.
	 *
	 * @param int $itemId
	 * @return int
	 */
	function getItemQty($itemId){
		if(isset($this->items[$itemId])){
			return $this->items[$itemId]["qty"];
		} else {
			return 0;
		}
	}

	function incrementItem($itemId){
		if(isset($this->items[$itemId])){
			$this->items[$itemId]["qty"]++;
			$this->items[$itemId]["subtotal"]+=$this->items[$itemId]["uprice"];
			$this->subTotal+=$this->items[$itemId]["uprice"];
			$this->totalWeight+=$this->items[$itemId]["uweight"];
			$this->ecotaxe+=$this->items[$itemId]["ecotaxe"];
			//$this->updateShippingInsuranceFees();
			return true;
		}
		return false;
	}

	function decrementItem($item){
		if(isset($this->items[$itemId])){
			$this->items[$itemId]["qty"]--;
			$this->items[$itemId]["subtotal"]-=$this->items[$itemId]["uprice"];
			$this->subTotal-=$this->items[$itemId]["uprice"];
			$this->totalWeight-=$this->items[$itemId]["uweight"];
			$this->ecotaxe-=$this->items[$itemId]["ecotaxe"];
			//$this->updateShippingInsuranceFees();
			return true;
		}
		return false;
	}

	function changeQty($idItem,$qty, $uprice=false){
		if(isset($this->items[$idItem])){
			$this->subTotal-=$this->items[$idItem]["subtotal"];
			$this->ecotaxe-=$this->items[$idItem]["ecotaxe"]*$this->items[$idItem]["qty"];
			$this->totalWeight-=$this->items[$idItem]["uweight"]*$this->items[$idItem]["qty"];
			$this->items[$idItem]["qty"] = $qty;
			if($uprice){ //change the item price (probably qty dependant)
				$this->items[$idItem]["uprice"] = $uprice;
			}
			$this->items[$idItem]["subtotal"] = $this->items[$idItem]["uprice"]*$qty;
			$this->subTotal+=$this->items[$idItem]["uprice"]*$qty;
			$this->ecotaxe+=$this->items[$idItem]["ecotaxe"]*$qty;
			$this->totalWeight+=$this->items[$idItem]["uweight"]*$qty;
			$this->updateShippingInsuranceFees();
			$this->updateTaxes();
			if($qty==0) $this->computeDelays();
			return true;
		}
		return false;
	}

	//Remove from the cart
	//Returns true if OK, false if the id is incorrect
	function remove($id){
		if(!isset($this->items[$id])) return false;
		//substract the item price from subtotal
		$this->subTotal-=$this->items[$id]["subtotal"];
		$this->ecotaxe-=$this->items[$id]["ecotaxe"]*$this->items[$id]["qty"];
		$this->totalWeight-=$this->items[$id]["uweight"]*$this->items[$id]["qty"];
		unset($this->items[$id]);
		$this->nbItems--;
		if($this->nbItems){
			$this->updateShippingInsuranceFees();
			$this->updateTaxes();
		} else {
			$this->subTotal=0;
			$this->shippingFees=0;
			$this->insuranceFees=0;
			$this->total=0;
			$this->updateTaxes();
		}
		$this->computeDelays();
		return true;
	}

	function computeDelays(){
		$maxDelay = 0;
		foreach($this->items as $item){
			$maxDelay = $item["deliveryDelay"]>$maxDelay?$item["deliveryDelay"]:$maxDelay;
		}
		$this->deliveryDelay = $maxDelay;
	}

	function getItems(){
		return $this->items;
	}

	function fetch_item(){
		while($this->currentPos<$this->nextId && !isset($this->items[$this->currentPos])){
			$this->currentPos++;
		}
		if($this->currentPos<$this->nextId){
			$this->currentPos++;
			return $this->items[$this->currentPos-1];
		} else {
			$this->currentPos = 0;
			return false;
		}
	}

	function sortItems(){
		if(! function_exists( 'cmpItems' )){
			function cmpItems($a,$b){
				if($a["type"]==$b["type"]){
					if($a["type"]=="domain") return strcmp($a["domain_name"],$b["domain_name"]);
					if($a["type"]=="server") return 0;
				} else return strcmp($a["type"],$b["type"]);
				return $a["price_diff"]-$b["price_diff"];
			}
		}
		usort($this->items,"cmpItems");
	}

	function getShippingInsurance(){
		if(isset($_SESSION["webuserid"])) $this->shippingCalculator = new shippingCalculator($_SESSION["webuserid"],$this->totalWeight,$this->subTotal);
		$this->updateShippingInsuranceFees();
	}

	function updateShippingInsuranceFees(){
		if($this->nbItems && $this->shippingCalculator!=null) {
			//update weight and amouunt
			$this->shippingCalculator->total_amount=$this->subTotal;
			$this->shippingCalculator->total_weight=$this->totalWeight;
			if($this->shippingCalculator->id_shipping_destination!=0) $this->shippingCalculator->selectTransporters();
			if(count($this->shippingCalculator->available_transporters)) {
				$this->shippingCalculator->computeShippingInsuranceFees();
			}
			//update the shippingOptions array
			$this->shippingOptions=array();
			foreach($this->shippingCalculator->available_transporters as $idTransporter => $transporter){
				$this->shippingOptions[$idTransporter] = array();
				$this->shippingOptions[$idTransporter]["name"]=$transporter["name"];
				$this->shippingOptions[$idTransporter]["price"]=$transporter["shipping_fees"];
				$this->shippingOptions[$idTransporter]["insurance"]	=array();
				if($transporter["method_insurance_fees"]!="none"){
					$this->shippingOptions[$idTransporter]["insurance"]["on"]=$transporter["insurance_fees"];
					if($transporter["insurance_optional"]=="Y") $this->shippingOptions[$idTransporter]["insurance"]["off"]=0;
				}
			}
			//update the cart now with the new shipping and insurance fees
			if($this->selectedShipping==0 || !isset($this->shippingCalculator->available_transporters[$this->selectedShipping])){
				//select the first available one
				foreach($this->shippingOptions as $idTransporter => $transporter){
					$this->selectedShipping=$idTransporter;
					$this->shippingFees = $transporter["price"];
					break;
				}
			} else {
				$this->shippingFees = $this->shippingOptions[$this->selectedShipping]["price"];
			}
			if($this->insuranceSubscribed && count($this->shippingOptions) && count($this->shippingOptions[$this->selectedShipping]["insurance"])){
				$this->insuranceFees = $this->shippingOptions[$this->selectedShipping]["insurance"]["on"];
			} else $this->insuranceFees=0;
		}
		$this->total = $this->subTotal+$this->shippingFees+$this->insuranceFees+$this->ecotaxe;
		$this->updateTaxes();
	}

	function getNbItems(){
		$count=0;
		foreach($this->items as $id => $item){
			$count+=$item["qty"];
		}
		return $count;
	}

	/**
	 * Function to clean the Cart:
	 * - Removes all the lines with 0 in Qty.
	 *
	 */
	function clean(){
		foreach($this->items as $id => $item){
			if($item["qty"]==0) $this->remove($id);
		}
	}

 	function updateTaxes(){
 		global $TBL_ecommerce_tax_group, $TBL_ecommerce_tax_authority, $TBL_ecommerce_tax_group_authority, $TBL_ecommerce_customer;
 		$link = dbConnect();
 		$taxGroup = 0;
 		/**
 		 * SELECT THE TAX GROUP
 		 */
		if($this->userId){
			$request = request("SELECT `id_tax_group` FROM `$TBL_ecommerce_customer` WHERE `id`='$this->userId'",$link);
			if(mysql_num_rows($request)){
				$user = mysql_fetch_object($request);
				$taxGroup = $user->id_tax_group;
			}
		}
		if(!$taxGroup){
			$request = request("SELECT `id` FROM `$TBL_ecommerce_tax_group` WHERE `default`='Y'",$link);
			$tax = mysql_fetch_object($request);
			$taxGroup = $tax->id;
		}
		/**
		 * COMPUTE THE TAXES
		 */
		//APPLY CUSTOMER TAXES
		$request = request("SELECT `$TBL_ecommerce_tax_group`.`method` as groupmethod,
	                                                    `$TBL_ecommerce_tax_authority`.*
	                        FROM `$TBL_ecommerce_tax_group`,`$TBL_ecommerce_tax_authority`,`$TBL_ecommerce_tax_group_authority`
	                        WHERE `$TBL_ecommerce_tax_group`.`id` = '$taxGroup'
	                        AND `$TBL_ecommerce_tax_group_authority`.`id_tax_group`=`$TBL_ecommerce_tax_group`.`id`
	                        AND `$TBL_ecommerce_tax_group_authority`.`id_tax_authority`=`$TBL_ecommerce_tax_authority`.`id`
	                        ORDER BY $TBL_ecommerce_tax_authority.`ordering`",$link);

		$amount = 0;
		$subtotalTaxes = $this->total;
		$this->grandTotal = $this->total;
		$this->taxes = array();
		while($taxes = mysql_fetch_object($request)){
			switch($taxes->method){
				case "percentage": $amount = number_format(($subtotalTaxes * $taxes->value)/100,2,".","");break;
				case "fixed": $amount = number_format($taxes->value,2,".","");break;
			}
			if($taxes->groupmethod="cumulate") $subtotalTaxes+=$amount;
			$this->grandTotal+=$amount;
			$this->taxes[$taxes->name]=$amount;
		}
 	}
}
?>