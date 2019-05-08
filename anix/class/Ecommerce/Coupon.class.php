<?php
class Coupon{
	var $id=0;
	var $code="";
	var $idClient = 0;
	var $type="";
	var $value=0;
	var $percentage=0;
	var $grid=array();
	var $usage="";
	var $maxUsage = 0;
	var $usageCount = 0;
	var $usableFrom = "";
	var $usableTo = "";
	var $validFrom = "";
	var $validUntil = "";

	var $valid=false;

	private $valid_types=array('fixed'=>1,'percentage'=>1,'percentage_no_transport'=>1,'transport'=>1,'grid'=>1);
	private $valid_usages=array('once'=>1,'count'=>1,'unlimited'=>1);


	/**
	 * Create a new coupon object.
	 * If the code was given, the coupon will be loaded from DB
	 *
	 * @param integer $code
	 */
	function __construct($code=0){
		if($code){
			$this->code= $code;
			$this->load();
		}
	}

	/**
	 * Create the new coupon and store it in database
	 *
	 * @return boolean
	 */
	public function create(){
		global $TBL_ecommerce_coupon;

		//Check if the coupon has already been created
		if($this->id) return FALSE;

		/**
		 * @todo Do complete verifications on the created object coherence.
		 */

		$link = dbConnect();
		//Create a random coupon code
		$found=true;
		while($found){
			$code = substr(strtoupper(md5(uniqid(rand(), true))),3,8);
			//check if the coupon exists in DB
			$request = request("SELECT `id` FROM `$TBL_ecommerce_coupon` WHERE `code`='$code'",$link);
			//break the loop, the generated code is unique
			if(!mysql_num_rows($request)) $found=false;
		}
		mysql_close($link);

		$this->code = $code;

		return $this->save();
	}

	/**
	 * Loads the coupon from database using the coupon code.
	 *
	 */
	private function load(){
		global $TBL_ecommerce_coupon;
		$link = dbConnect();
		$request = request("SELECT * FROM `$TBL_ecommerce_coupon` WHERE `code`='$this->code'",$link);
		mysql_close($link);
		if(!mysql_num_rows($request)) {
			return;
		}
		$coupon = mysql_fetch_object($request);
		$this->id=$coupon->id;
		$this->code=$coupon->code;
		$this->idClient=$coupon->id_client;
		$this->type=$coupon->type;
		$this->value=$coupon->value;
		$this->percentage=$coupon->percentage;
		$this->grid=EcommerceCoupon::parseGrid($coupon->grid);
		$this->usage = $coupon->usage;
		$this->maxUsage = $coupon->max_usage;
		$this->usageCount = $coupon->usage_count;
		$this->validFrom = $coupon->valid_from;
		$this->validUntil = $coupon->valid_until;

		$this->getStatus();
	}

	/**
	 * Saves the coupon in database. Inserts a new coupon if the coupon has not been created (id=0)
	 * Otherwise, it updates the existing coupon. Returns True if the saving was OK.
	 *
	 * @return boolean
	 */
	private function save($transactionStarted=FALSE){
		global $TBL_ecommerce_coupon;

		$link = dbConnect();

		if(!$transactionStarted){
			request("TRANSACTION START",$link);
		}
		$error = FALSE;
		if($this->id){//Update the coupon
			$strRequest = "UPDATE `$TBL_ecommerce_coupon` SET ";
			$strRequest.= "`id_client`='$this->idClient',";
			$strRequest.= "`type`='$this->type',";
			$strRequest.= "`value`='".number_format($this->value,2,".","")."',";
			$strRequest.= "`percentage`='".number_format($this->percentage,2,".","")."',";
			$strRequest.= "`grid`='".$this->gridToString()."',";
			$strRequest.= "`usage`='$this->usage',";
			$strRequest.= "`max_usage`='$this->maxUsage',";
			$strRequest.= "`usage_count`='$this->usageCount',";
			$strRequest.= "`valid_from`='$this->validFrom',";
			$strRequest.= "`valid_until`='$this->validUntil' ";
			$strRequest.= "WHERE `id`='$this->id'";
			request($strRequest,$link);
			if(mysql_errno($link)) $error=TRUE;
		} else { //Insert a new coupon

			$strRequest = "INSERT INTO `$TBL_ecommerce_coupon` (`code`,`id_client`,`type`,`value`,`percentage`,`grid`,`usage`,`max_usage`,`usage_count`,`valid_from`,`valid_until`) VALUES (";
			$strRequest.= "'$this->code','$this->idClient','$this->type','".number_format($this->value,2,".","")."','".number_format($this->percentage,2,".","")."','".$this->gridToString()."','$this->usage','$this->maxUsage','$this->usageCount','$this->validFrom','$this->validUntil'";
			$strRequest.= ")";
			request($strRequest,$link);
			if(mysql_errno($link)) $error=TRUE;
		}
		if(!$transactionStarted && !$error) request("COMMIT",$link);
		if(!$transactionStarted && $error) request("ROLLBACK",$link);

		mysql_close($link);

		//return FALSE if an error occured.
		return !$error;
	}

	/**
	 * Updates the coupon validity depending on it's type.
	 *
	 */
	private function getStatus(){
		$this->valid=$this->checkDatesValidity();
		if($this->usage=="once" && $this->usageCount) $this->valid = false;
		if($this->usage=="count" && $this->usageCount>=$this->maxUsage) $this->valid = false;
		if($this->type=="fixed" && $this->value<=0) $this->valid = false;
		if($this->type=="grid" && !count($this->grid)) $this->valid = false;
	}

	/**
	 * Return true if the current coupon is valid regarding the expiration dates
	 *
	 * @return boolean
	 */
	private function checkDatesValidity(){
		$valid=true;
		$today = date("Y-m-d");
		if($this->validFrom!="" && $this->validFrom!="0000-00-00" && $today<$this->validFrom) $valid=false;
		if($this->validUntil!="" && $this->validUntil!="0000-00-00" && $today>$this->validUntil) $valid=false;
		return $valid;
	}

	/**
	 * Compute coupon's value depending on it's type.
	 * If it's a fixed type, the coupon worths the it's remaining value
	 * If it's a percentage, the coupon worth that percentage on the subtotal
	 * If it's a free shipping coupon, it worths the shipping fees
	 *
	 * @param integer $subtotal
	 * @param integer $shippingFees
	 * @return integer
	 */
	public function getValue($subtotal=0,$shippingFees=0){
		if(!$this->valid) return 0;
		if($this->type=="fixed") return $this->value;
		if($this->type=="percentage"){
			return $subtotal*$this->percentage/100;
		}
		if($this->type=="percentage_no_transport"){
			return ($subtotal-$shippingFees)*$this->percentage/100;
		}
		if($this->type=="transport"){
			return $shippingFees;
		}
		if($this->type=="grid"){
			$value=0;
			foreach($this->grid as $echelon){
				if($subtotal>=$echelon[0]) $value=$echelon[1];
			}
			return $value;
		}
	}

	/**
	 * Reads a string with ; separated values containing the coupon values depending on the order amount
	 * Must be like: SUBTOTAL1=VALUE1;SUBTOTAL2=VALUE2;
	 * if the actual subtotal is less than SUBTOTAL1, the coupon worth VALUE1, ...
	 * the returned array will have the SUBTOTAL at index 0 and VALUE at index 1
	 *
	 * @param String $str
	 * @return Array
	 */
	private function parseGrid($str){
		//TODO
		return array();
	}

	/**
	 * Converts the grid from an array (as stored in the object) to a string to be stored in database.
	 * The string has ; separated values. Ex.: SUBTOTAL1=VALUE1;SUBTOTAL2=VALUE2;
	 *
	 * @return String
	 */
	private function gridToString(){
		//TODO
		$return = "";
		return $return;
	}


	/**
	 * Use the current coupon for a specific amount.
	 * Insert a new line into the database for the usage.
	 *
	 * @param integer $idClient
	 * @param decimal $amount
	 * @param decimal $shippingFees
	 * @param integer $idPayment
	 * @param integer $idOrder
	 * @param integer $idInvoice
	 * @return boolean
	 */
	public function useCoupon($idClient,$amount,$shippingFees=0,$idPayment=0,$idOrder=0,$idInvoice=0){
		global $TBL_ecommerce_coupon_usage;

		//TESTS
		//1.If the coupon is for one client and client does not match
		if($this->idClient!=0 && $this->idClient!=$idClient) return FALSE;
		//2.If the coupon is not valid
		if(!$this->valid) return FALSE;
		//3.if the value is not sufficient
		$maxValue=$this->getValue($amount,$shippingFees);
		if($amount>$maxValue) return FALSE;

		if($this->type=="fixed"){
			//Deduce the amount from the coupon value
			$this->value -= $amount;
		}

		$noError=true;
		$this->usageCount++;
		$link=dbConnect();
		request("TRANSACTION START",$link);
		$tmp=$this->save(true);
		$noError=$noError & $tmp;
		if($noError){
			request("INSERT INTO `$TBL_ecommerce_coupon_usage` (`id_coupon`,`id_client`,`id_payment`,`id_order`,`id_invoice`,`amount`,`usage_date`)
					 VALUES('$this->id','$idClient','$idPayment','$idOrder','$idInvoice','".number_format($amount,2,".","")."',NOW())",$link);
			if(mysql_errno($link)) $noError=false;
		}
		if($noError) request("COMMIT",$link);
		else request("ROLLBACK",$link);
		mysql_close($link);
		return $noError;
	}

	/**
	 * Returns the type of the coupon (in text)
	 *
	 * @return string
	 */
	public function getTypeString(){
		$typeStr="";
		switch($this->type){
			case "fixed": $typeStr=_("Montant fixe");break;
			case "percentage": $typeStr=_("Pourcentage");break;
			case "percentage_no_transport": $typeStr=_("Pourcentage sans transport");break;
			case "transport": $typeStr=_("Frais de port");break;
			case "grid": $typeStr=_("Variable");break;
		}
		return $typeStr;
	}

	/**
	 * Returns a description of the usability of this coupon
	 *
	 * @return string
	 */
	public function getUsageString(){
		$usageStr = "";
		switch($this->usage){
			case "once": $usageStr=_("Unique");break;
			case "count": $usageStr=_("Multiple")." (".$this->usageCount."/".$this->maxUsage.")";break;
			case "unlimited": $usageStr=_("Illimité");break;
		}
		return $usageStr;
	}

	/**
	 * Get all the coupons for a given client.
	 * If $all=true, the function will also return old couons (ie.: non valid)
	 *
	 * @param integer $idClient
	 * @param boolean $all
	 * @return array
	 */
	public static function getCouponsByClient($idClient,$all=false,$fixedOnly=false){
		global $TBL_ecommerce_coupon;
		$link = dbConnect();
		$requestStr = "SELECT `code` FROM `$TBL_ecommerce_coupon` WHERE `id_client`='$idClient' AND 1 ";
		$request = request($requestStr,$link);
		mysql_close($link);
		$ret = array();
		while($coupon = mysql_fetch_object($request)){
			$tmp = new EcommerceCoupon($coupon->code);
			if(($all || $tmp->valid) && (!$fixedOnly || $tmp->type=="fixed")) $ret[]= $tmp;
		}
		return $ret;
	}



	// SETTERS

	public function setIdClient($idClient){
		if($idClient) $this->idClient = $idClient;
	}

	public function setType($type){
		if(isset($this->valid_types[$type])) $this->type = $type;
	}

	public function setValue($value){
		if($value>=0) $this->value = $value;
	}

	public function setPercentage($percentage){
		if($percentage>=0) $this->percentage = $percentage;
	}

	public function resetGrid(){
		$this->grid = array();
	}

	public function addToGrid($echelon,$value){
		$nbLines = count($this->grid);
		$this->grid[$nbLines][0]=$echelon;
		$this->grid[$nbLines][1]=$value;
	}

	public function setUsage($usage){
		if(isset($this->valid_usages[$usage])) $this->usage=$usage;
	}

	public function setMaxUsage($maxUsage){
		if($maxUsage>=0) $this->maxUsage = $maxUsage;
	}

	public function setValidFrom($validFrom){
		$this->validFrom=$validFrom;
	}

	public function setValidUntil($validUntil){
		$this->validUntil=$validUntil;
	}

}
?>