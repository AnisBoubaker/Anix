<?php
class shippingCalculator{
	var $id_customer=0;
	var $destination_country="";
	var $destination_province="";
	var $destination_city="";
	var $destination_postal="";
	var $total_amount=0;
	var $total_weight=0;
	var $id_shipping_destination=0;
	var $available_transporters = array();

	function shippingCalculator($customer,$weight,$amount){
		global $TBL_ecommerce_shipping_transporters,$TBL_ecommerce_shipping_destinations,$TBL_ecommerce_shipping_destination_transporter;
		$this->id_customer=$customer;
		$this->total_weight=$weight;
		$this->total_amount=$amount;
		//NB REMOVE THE COMMENT BELOW !!!!!
		$this->selectDestination();
		//if($this->id_shipping_destination!=0) $this->selectTransporters();
		//if(count($this->available_transporters)) $this->computeShippingFees();
	}

	/*function selectDestination2($country,$name){
		global $TBL_ecommerce_shipping_transporters,$TBL_ecommerce_shipping_destinations,$TBL_ecommerce_shipping_destination_transporter,$TBL_ecommerce_customer,$TBL_ecommerce_address;
		$link = dbConnect();
		$this->destination_country = $country;
		$this->destination_city = "";
		$this->destination_postal = "";
		//Select the appropriate destination
		$cond_country=false;$cond_province=false;$cond_city=false;$cond_postal=false;
		$request = request("SELECT * FROM `$TBL_ecommerce_shipping_destinations`",$link);
		$shippingName = "";
		$this->id_shipping_destination=0;
		while($this->id_shipping_destination==0 && $destination = mysql_fetch_object($request)){
			if($destination->cond_country!=""){
				$country_list = explode(";",$destination->cond_country);
				foreach($country_list as $country){
					if(strtolower($country)==strtolower($this->destination_country)){
						$cond_country=true;
						//echo strtolower($country)."==".strtolower($this->destination_country)."<br />\n";
						break;
					}
				}
			} else $cond_country=true;
			if($destination->cond_province!=""){
				$province_list = explode(";",$destination->cond_province);
				foreach($province_list as $province){
					if(strtolower($province)==strtolower($this->destination_province)){
						$cond_province=true;
						//echo strtolower($country)."==".strtolower($this->destination_country)."<br />\n";
						break;
					}
				}
			} elseif($cond_country) $cond_province=true;
			if($cond_province && $destination->cond_city!=""){
				$city_list = explode(";",$destination->cond_city);
				foreach($city_list as $city){
					if(strtolower($city)==strtolower($this->destination_city)){
						$cond_city=true;
						break;
					}
				}
			} elseif($cond_province) $cond_city=true;
			if($cond_city && $destination->cond_postal!=""){
				$postal_list = explode(";",$destination->cond_postal);
				foreach($postal_list as $postal){
					if(strtolower($postal)==strtolower($this->destination_postal)){
						$cond_postal=true;
						break;
					}
				}
			} elseif($cond_city) $cond_postal=true;

			if($cond_postal){ //all conditions met, select this destination
				$this->id_shipping_destination = $destination->id;
				$shippingName = $destination->name;
			}
		}
		mysql_close($link);
		return $shippingName;
	}*/

	function selectDestination(){
		global $TBL_ecommerce_shipping_transporters,$TBL_ecommerce_shipping_destinations,$TBL_ecommerce_shipping_destination_transporter,$TBL_ecommerce_customer,$TBL_ecommerce_address;
		$link = dbConnect();
		//get the customer infos
		$requestStr = "SELECT `$TBL_ecommerce_address`.`country_code`,`$TBL_ecommerce_address`.`province`,`$TBL_ecommerce_address`.`city`,`$TBL_ecommerce_address`.`zip`
					   FROM `$TBL_ecommerce_address`,`$TBL_ecommerce_customer`
					   WHERE `$TBL_ecommerce_customer`.`id`='".$this->id_customer."'
					   AND `$TBL_ecommerce_address`.`id`=`$TBL_ecommerce_customer`.`id_address_mailing`";
		$request = request($requestStr,$link);
		$customer_adresse = mysql_fetch_object($request);
		$this->destination_country = $customer_adresse->country_code;
		$this->destination_province = $customer_adresse->province;
		$this->destination_city = $customer_adresse->city;
		$this->destination_postal = $customer_adresse->zip;
		//Select the appropriate destination
		$cond_country=false;$cond_province=false;$cond_city=false;$cond_postal=false;
		$request = request("SELECT * FROM `$TBL_ecommerce_shipping_destinations`",$link);
		while($this->id_shipping_destination==0 && $destination = mysql_fetch_object($request)){
			if($destination->cond_country!=""){
				$country_list = explode(";",$destination->cond_country);
				foreach($country_list as $country){
					if(strtolower($country)==strtolower($this->destination_country)){
						$cond_country=true;
						//echo strtolower($country)."==".strtolower($this->destination_country)."<br />\n";
						break;
					}
				}
			} else $cond_country=true;
			if($destination->cond_province!=""){
				$province_list = explode(";",$destination->cond_province);
				foreach($province_list as $province){
					if(strtolower($province)==strtolower($this->destination_province)){
						$cond_province=true;
						//echo strtolower($country)."==".strtolower($this->destination_country)."<br />\n";
						break;
					}
				}
			} elseif($cond_country) $cond_province=true;
			if($cond_province && $destination->cond_city!=""){
				$city_list = explode(";",$destination->cond_city);
				foreach($city_list as $city){
					if(strtolower($city)==strtolower($this->destination_city)){
						$cond_city=true;
						break;
					}
				}
			} elseif($cond_province) $cond_city=true;
			if($cond_city && $destination->cond_postal!=""){
				$postal_list = explode(";",$destination->cond_postal);
				foreach($postal_list as $postal){
					if(strtolower($postal)==strtolower($this->destination_postal)){
						$cond_postal=true;
						break;
					}
				}
			} elseif($cond_city) $cond_postal=true;

			if($cond_postal){ //all conditions met, select this destination
				$this->id_shipping_destination = $destination->id;
			}
		}
		mysql_close($link);
	}

	function selectTransporters(){
		global $TBL_ecommerce_shipping_transporters, $TBL_ecommerce_info_transporter,$TBL_ecommerce_shipping_destinations,$TBL_ecommerce_shipping_destination_transporter,$TBL_ecommerce_customer,$TBL_ecommerce_address;
		global $used_language_id;
		$link = dbConnect();
		$this->available_transporters=array();
		//get the available transporters
		$requestStr = "SELECT `$TBL_ecommerce_info_transporter`.`name`,
							  `$TBL_ecommerce_shipping_transporters`.`method_shiping_fees`,
							  `$TBL_ecommerce_shipping_transporters`.`method_insurance_fees`,
							  `$TBL_ecommerce_shipping_transporters`.`insurance_optional`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`id_destination`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`id_transporter`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`shipping_min_fees`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`shipping_min_weight`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`shipping_max_weight`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`shipping_price_per_unit`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`shipping_flat_rate`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`shipping_table_weight`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`shipping_table_amount`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`insurance_min_fees`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`insurance_flat_rate`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`insurance_percentage`,
							  `$TBL_ecommerce_shipping_destination_transporter`.`insurance_table_amount`
					   FROM `$TBL_ecommerce_shipping_transporters`,`$TBL_ecommerce_shipping_destination_transporter`,`$TBL_ecommerce_info_transporter`
					   WHERE `$TBL_ecommerce_shipping_destination_transporter`.`id_destination`='".$this->id_shipping_destination."'
					   AND `$TBL_ecommerce_shipping_transporters`.`id`=`$TBL_ecommerce_shipping_destination_transporter`.`id_transporter`
					   AND `$TBL_ecommerce_info_transporter`.`id_transporter`=`$TBL_ecommerce_shipping_transporters`.`id`
					   AND `$TBL_ecommerce_info_transporter`.`id_language`='$used_language_id'
					   ORDER BY `$TBL_ecommerce_shipping_transporters`.`ordering`";
		$request = request($requestStr,$link);
		while($transporter = mysql_fetch_object($request)){
			if($this->total_weight>$transporter->shipping_min_weight && ($transporter->shipping_max_weight==0 || $this->total_weight<=$transporter->shipping_max_weight)){
				$id = $transporter->id_transporter;
				$this->available_transporters[$id]=array();
				$this->available_transporters[$id]["name"]=$transporter->name;
				$this->available_transporters[$id]["method_shipping_fees"]=$transporter->method_shiping_fees;
				$this->available_transporters[$id]["method_insurance_fees"]=$transporter->method_insurance_fees;
				$this->available_transporters[$id]["insurance_optional"]=$transporter->insurance_optional;
				if($this->available_transporters[$id]["method_shipping_fees"]=="flat_rate"){
					$this->available_transporters[$id]["shipping_flat_rate"]=$transporter->shipping_flat_rate;
				}
				if($this->available_transporters[$id]["method_shipping_fees"]=="weight"){
					$this->available_transporters[$id]["shipping_price_per_unit"]=$transporter->shipping_price_per_unit;
				}
				if($this->available_transporters[$id]["method_shipping_fees"]=="table_weight"){
					$this->available_transporters[$id]["shipping_table_weight"]=array();
					$rates = explode(";",$transporter->shipping_table_weight);
					foreach($rates as $rate){
						$tmp = explode("=",$rate);
						$this->available_transporters[$id]["shipping_table_weight"][$tmp[0]]=$tmp[1];
					}
				}
				if($this->available_transporters[$id]["method_shipping_fees"]=="table_amount"){
					$this->available_transporters[$id]["shipping_table_amount"]=array();
					$rates = explode(";",$transporter->shipping_table_amount);
					foreach($rates as $rate){
						$tmp = explode("=",$rate);
						$this->available_transporters[$id]["shipping_table_amount"][$tmp[0]]=$tmp[1];
					}
				}
				if($this->available_transporters[$id]["method_insurance_fees"]=="flat_rate"){
					$this->available_transporters[$id]["insurance_flat_rate"]=$transporter->insurance_flat_rate;
				}
				if($this->available_transporters[$id]["method_insurance_fees"]=="percentage"){
					$this->available_transporters[$id]["insurance_percentage"]=$transporter->insurance_percentage;
				}
				if($this->available_transporters[$id]["method_insurance_fees"]=="table"){
					$this->available_transporters[$id]["insurance_table"]=array();
					$rates = explode(";",$transporter->insurance_table_amount);
					foreach($rates as $rate){
						$tmp = explode("=",$rate);
						$this->available_transporters[$id]["insurance_table"][$tmp[0]]=$tmp[1];
					}
				}
			}
		}
	}

	function computeShippingInsuranceFees(){
		//$this->selectTransporters();
		foreach($this->available_transporters as $idTransporter => $transporter){
			if($transporter["method_shipping_fees"]=="flat_rate"){
				$this->available_transporters[$idTransporter]["shipping_fees"] = $transporter["shipping_flat_rate"];
			}
			if($transporter["method_shipping_fees"]=="weight"){
				$this->available_transporters[$idTransporter]["shipping_fees"] = $transporter["shipping_price_per_unit"]*$this->total_weight;
			}
			if($transporter["method_shipping_fees"]=="table_weight"){
				foreach($transporter["shipping_table_weight"] as $max_weight => $price){
					if($this->total_weight<=$max_weight){
						$this->available_transporters[$idTransporter]["shipping_fees"] = $price;
						break;
					}
				}
			}
			if($transporter["method_shipping_fees"]=="table_amount"){
				foreach($transporter["shipping_table_amount"] as $max_amount => $price){
					if($this->total_amount<=$max_amount){
						$this->available_transporters[$idTransporter]["shipping_fees"] = $price;
						break;
					}
				}
			}
			if($transporter["method_insurance_fees"]=="none"){
				$this->available_transporters[$idTransporter]["insurance_fees"] = 0;
			}
			if($transporter["method_insurance_fees"]=="flat_rate"){
				$this->available_transporters[$idTransporter]["insurance_fees"] = $transporter["insurance_flat_rate"];
			}
			if($transporter["method_insurance_fees"]=="percentage"){
				$this->available_transporters[$idTransporter]["insurance_fees"] = $this->total_amount*$this->available_transporters[$idTransporter]["insurance_percentage"]/100;
			}
			if($transporter["method_insurance_fees"]=="table"){
				foreach($transporter["insurance_table"] as $max_amount => $price){
					if($this->total_amount<=$max_amount){
						$this->available_transporters[$idTransporter]["insurance_fees"] = $price;
						break;
					}
				}
				//Use the maximum price if not found...
				if(!isset($this->available_transporters[$idTransporter]["insurance_fees"])) $this->available_transporters[$idTransporter]["insurance_fees"]=$price;
			}
		}
	}
}
?>