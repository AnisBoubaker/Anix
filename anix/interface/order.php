<?php

class order{
	var $id = 0;
	var $idClient = 0;
	var $items = array();
	var $subtotal = 0;
	var $depositRequested = 0;
	var $depositAmount = 0;
	var $payedAmount = 0;
	var $mailingAddress = "";
	var $billingAddress = "";
	var $orderDate = "";
	var $deliveryDate = "";
	var $receptionDate = "";
	var $deliveryDelay =0;
	var $status;
	var $idInvoice;
	var $order_script="";
	var $idTransporter=0;
	var $IPAddress = "";
	var $timeStamp = "";
	var $xml_address = "";

	//doempty: boolean: empty the order (delete order items) if the order is in standby
	function order($id = 0, $doEmpty , $link = 0, $idClient=0, $idTransporter =0, $billingAddress="", $mailingAddress="", $orderDate="", $deliveryDate="", $receptionDate="", $depositRequested=0){
		global $TBL_ecommerce_order,$TBL_ecommerce_invoice_item;
		if($id){
			if(!$link) $insideLink = dbConnect();
      		else $insideLink=$link;
			//load order from database
			$requestStr = "SELECT * FROM `$TBL_ecommerce_order` WHERE `id`='$id'";
			if($idClient!=0) $requestStr.=" AND `id_client`='$idClient'";
			$request=request($requestStr,$insideLink);
			if(mysql_num_rows($request)){
				$dbOrder = mysql_fetch_object($request);
				if($billingAddress=="") $this->billingAddress=$dbOrder->billing_address;
				else $this->billingAddress=$billingAddress;
				if($mailingAddress=="") $this->mailingAddress=$dbOrder->mailing_address;
				else $this->mailingAddress=$mailingAddress;
				$this->orderDate=$dbOrder->order_date;
				if($deliveryDate!="") $this->deliveryDate = $deliveryDate;
				else $this->deliveryDate = $dbOrder->delivery_date;
				if($receptionDate!="") $this->receptionDate= $receptionDate;
				else $this->receptionDate = $dbOrder->reception_date;
				$this->deliveryDelay = $dbOrder->delivery_delay;
				$this->depositRequested = $dbOrder->deposit_requested;
				$this->items= array();
				$this->subtotal = 0;
				$this->idTransporter = $idTransporter;
				//$this->depositRequested = $dbOrder->;
				$this->id = $dbOrder->id;
				$this->status = $dbOrder->status;
				$this->idClient = $dbOrder->id_client;
				$this->xml_address = $dbOrder->xml_address;
				//EMPTY THE ORDER ITEMS IF ORDER IS IN STAND BY (NOTHING PAYED YET)
				if($doEmpty==true && $this->status=="stand by"){
					request("START TRANSACTION",$insideLink);
					request("DELETE FROM `$TBL_ecommerce_invoice_item` WHERE `id_order`='$id'",$insideLink);
					$err = mysql_errno($insideLink);
					if(!$err){
			      		request("COMMIT",$insideLink);
			      	}else {
			      		request("ROLLBACK",$insideLink);
			      	}
				} elseif($doEmpty==false) { //else load the order items from database
					$this->loadItems($insideLink);
					$this->subtotal = $dbOrder->subtotal;
					$this->idTransporter = $dbOrder->id_transporter;
				} else $id=0;
			} else $id=0;
			if(!$link) mysql_close($insideLink);
		}
		if($id==0){
			//create blank order
			$this->id=0;
			$this->idClient = $idClient;
			$this->billingAddress=$billingAddress;
			$this->mailingAddress=$mailingAddress;
			$this->orderDate=$orderDate;
			$this->deliveryDate = $deliveryDate;
			$this->receptionDate = $receptionDate;
			$this->depositRequested = $depositRequested;
			$this->items= array();
			$this->subtotal = 0;
			$this->depositRequested = 0;
			$this->idTransporter = $idTransporter;
		}
	}

	function setXMLAddress($xml_address){
		$this->xml_address= addslashes($xml_address);
	}

	function loadItems($link){
		global $TBL_ecommerce_invoice_item;
		if(!$this->id) return;
		$request = request("SELECT `id` FROM `$TBL_ecommerce_invoice_item` WHERE `id_order`='".$this->id."' ORDER BY `id`",$link);
		while($item = mysql_fetch_object($request)){
			$this->items[] = new orderItem($item->id,$link,$this->id);
		}
	}

	function calculateSubtotal(){
		$this->subtotal = 0;
		foreach ($this->items as $item) {
			$this->sutotal+= $item->uprice * $item->qty;
		}
		return $this->sutotal;
	}

	function addItem($reference, $description, $details, $qty, $uprice, $idProduct){
		if($qty==0) return;
		$this->items[] = new orderItem(0,0,$this->id,$reference,$description,$details,$qty,$uprice,$idProduct);
		$this->subtotal+= $uprice * $qty;
	}

	function setOrderScript($script){
		$this->order_script=$script;
	}

	function setIPTimeStamp($IPAddress,$timeStamp){
		$this->IPAddress = $IPAddress;
		$this->timeStamp = $timeStamp;
	}

	//Saves the order in database
	//If it's a new order, the order and the items are inserted.
	//If this is an existing order, the order is updated. If update items is set to true, items are updated as well.
	function save($link = 0, $updateItems = false){
		global $TBL_ecommerce_order;
		if(!$link) $insideLink = dbConnect();
      	else $insideLink=$link;
      	$err=0;
      	//start transaction
      	request("START TRANSACTION",$insideLink);
      	if($this->id==0){
	      	//insert the order
	      	request("INSERT INTO `$TBL_ecommerce_order` (`id_client`,`mailing_address`,`billing_address`,`xml_address`,`order_date`,`delivery_date`,`reception_date`,`delivery_delay`,`subtotal`,`deposit_requested`,`deposit_amount`,`status`,`order_script`,`id_transporter`,`remote_ip`,`order_timestamp`)
	              VALUES ('$this->idClient','$this->mailingAddress','$this->billingAddress','$this->xml_address','$this->orderDate','$this->deliveryDate','$this->receptionDate','$this->deliveryDelay','".number_format($this->subtotal,2,".","")."','".number_format($this->depositRequested,3,".","")."','".number_format($this->depositAmount,2,".","")."','stand by','".addslashes($this->order_script)."','".$this->idTransporter."','".$this->IPAddress."','".$this->timeStamp."')",$insideLink);
	      	if(mysql_errno($insideLink)) {echo "Erreur d'insertion de la commande\n";$err++;}
	      	else $this->id = mysql_insert_id($insideLink);
      	} else {
      		$requestStr="UPDATE `$TBL_ecommerce_order` SET
      				`id_client`='".addslashes($this->idClient)."',
      				`mailing_address`='".addslashes($this->mailingAddress)."',
      				`billing_address`='".addslashes($this->mailingAddress)."',
      				`xml_address`='".addslashes($this->xml_address)."',
      				`order_date`='".addslashes($this->orderDate)."',
      				`delivery_date`='".addslashes($this->deliveryDate)."',
      				`reception_date`='".addslashes($this->receptionDate)."',
      				`delivery_delay`='".addslashes($this->deliveryDelay)."',
      				`subtotal`='".number_format($this->subtotal,2,".","")."',
      				`deposit_requested`='".number_format($this->depositRequested,3,".","")."',
      				`deposit_amount`='".number_format($this->depositAmount,2,".","")."',
      				`status`='stand by',
      				`order_script`='".addslashes($this->order_script)."',
      				`id_transporter`='".$this->idTransporter."',
      				`remote_ip`='".$this->IPAddress."',
      				`order_timestamp`='".$this->timeStamp."'
      				WHERE `id`='$this->id'";
      		request($requestStr,$insideLink);
      		if(mysql_errno($insideLink)) {echo "Erreur de mise a jour de la commande: \n".mysql_error($insideLink); $err++;}
      	}
      	//save order items

      	if(!$err)
	      	foreach ($this->items as $item){
	      		$item->idOrder = $this->id;
	      		if(!$item->save($insideLink)) $err++;
	      	}
      	//commit transaction
      	if(!$err){
      		request("COMMIT",$insideLink);
      	}else {
      		request("ROLLBACK",$insideLink);
      	}
      	if(!$link) mysql_close($insideLink);
      	if(!$err) return true;
      	else return false;
	}

	function updateDeliveryDates($deliveryDate, $receptionDate){
		$this->deliveryDate=$deliveryDate;
		$this->receptionDate=$receptionDate;
	}

}