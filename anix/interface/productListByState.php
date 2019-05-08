<?php
// Requires class productCat and product
class productListByState{
	//variables
	var $idCategory=0;
	var $idGroup=0;
	var $productTable=array();
	//constructors
	function productListByState($state, $idCategory,$idLanguage,$idGroup,$link, $random = false, $limit=0){
		global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafieldselection,$TBL_catalogue_extrafields,$TBL_catalogue_extrafieldselection,$TBL_catalogue_categories;
		global $TBL_catalogue_product_state,$TBL_catalogue_restocking_delay,$TBL_catalogue_info_restocking_delay;
		if($idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$this->idGroup = $idGroup;
		$requestString="SELECT $TBL_catalogue_info_products.name,
                $TBL_catalogue_products.id,
                $TBL_catalogue_products.brand,
                $TBL_catalogue_products.ref_store,
                $TBL_catalogue_products.public_price,
                $TBL_catalogue_products.is_in_special,
                $TBL_catalogue_products.stock,
                $TBL_catalogue_products.public_special,
                $TBL_catalogue_products.special_price,
            	$TBL_catalogue_product_prices.price as group_price,
	            $TBL_catalogue_product_prices.is_in_special as group_in_special,
	            $TBL_catalogue_product_prices.special_price as group_special_price,
                $TBL_catalogue_products.image_file_large,
                $TBL_catalogue_products.image_file_small,
                $TBL_catalogue_products.image_file_icon,
                $TBL_catalogue_restocking_delay.delay_days as restocking_delay,
                $TBL_catalogue_info_restocking_delay.name as restocking_info,
                $TBL_catalogue_brands.name as brand_name,
		        $TBL_catalogue_brands.image_file_small as brand_image
          FROM  ($TBL_catalogue_product_state,$TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_categories,$TBL_catalogue_restocking_delay,$TBL_catalogue_info_restocking_delay)
          LEFT JOIN `$TBL_catalogue_brands` ON (`$TBL_catalogue_brands`.`id`=`$TBL_catalogue_products`.`brand`)
		  LEFT JOIN `$TBL_catalogue_product_prices` ON (`$TBL_catalogue_product_prices`.`id_price_group`='$this->idGroup' AND `$TBL_catalogue_product_prices`.`id_product`=`$TBL_catalogue_products`.`id`)
          WHERE $TBL_catalogue_product_state.`id_state`='$state'
          AND 	$TBL_catalogue_products.id=$TBL_catalogue_product_state.`id_product`";
		if($idCategory) $requestString.=" AND $TBL_catalogue_products.id_category='$idCategory' ";
		$requestString.="AND   $TBL_catalogue_info_products.id_language='$idLanguage'
          AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
          AND 	$TBL_catalogue_products.id_category = $TBL_catalogue_categories.id
          AND   $TBL_catalogue_info_restocking_delay.id_language='$idLanguage'
          AND	$TBL_catalogue_products.restocking_delay = $TBL_catalogue_restocking_delay.id
          AND	$TBL_catalogue_info_restocking_delay.id_delay = $TBL_catalogue_restocking_delay.id ";
		if($random){
			$requestString.="ORDER BY RAND() ";
		} else {
			$requestString.="ORDER BY $TBL_catalogue_categories.id_parent,$TBL_catalogue_categories.ordering,$TBL_catalogue_products.ordering ";
		}
		if($limit){
			$requestString.="LIMIT 0,$limit";
		}

		$request=request($requestString,$insideLink);
		if(!mysql_num_rows($request)){
			mysql_close($insideLink);
			return;
		}
		$this->state = $state;
		$this->idCategory = $idCategory;
		$this->productTable=array();
		while($row=mysql_fetch_object($request)){
			$this->productTable[$row->id]=array();
			$this->productTable[$row->id]["id"]=$row->id;
			$this->productTable[$row->id]["title"]=$row->name;
			$this->productTable[$row->id]["ref"]=$row->ref_store;
			$this->productTable[$row->id]["image_large"]=$row->image_file_large;
			$this->productTable[$row->id]["image_small"]=$row->image_file_small;
			$this->productTable[$row->id]["image_icon"]=$row->image_file_icon;
			$this->productTable[$row->id]["stock"]=$row->stock;
			$this->productTable[$row->id]["stockState"]=$row->restocking_info;
			$this->productTable[$row->id]["prices"]=array();
			$this->productTable[$row->id]["prices"]["public"]=array();
			$this->productTable[$row->id]["prices"]["public"]["name"]= "public";
			$this->productTable[$row->id]["prices"]["public"]["price"]= $row->public_price;
			if($row->is_in_special=="Y" && $row->public_special){
				$this->productTable[$row->id]["prices"]["public"]["in_special"]=true;
				$this->productTable[$row->id]["prices"]["public"]["special_price"]=$row->special_price;
			} else $this->productTable[$row->id]["prices"]["public"]["in_special"]=false;
			if($row->group_price){
				$this->productTable[$row->id]["prices"]["group"]= array();
				$this->productTable[$row->id]["prices"]["group"]["price"]= $row->group_price;
				if($row->is_in_special=="Y" && $row->group_in_special){
					$this->productTable[$row->id]["prices"]["group"]["in_special"]= true;
					$this->productTable[$row->id]["prices"]["group"]["special_price"]= $row->group_special_price;
				}
			}
			if($row->brand_name){
				$this->productTable[$row->id]["brand"]=$row->brand_name;
				$this->productTable[$row->id]["brand_image"]=$row->brand_image;
			}
			//get the selection extrafields
			/*
			$request2 = request(
			"SELECT $TBL_catalogue_extrafields_values.id_extrafield id,
			$TBL_catalogue_info_extrafieldselection.value,
			$TBL_catalogue_info_extrafields.name,
			$TBL_catalogue_info_extrafieldselection.image_file_small
			FROM   $TBL_catalogue_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafieldselection,$TBL_catalogue_info_extrafieldselection
			WHERE  $TBL_catalogue_extrafields_values.id_product='".$row->id."'
			AND	  $TBL_catalogue_extrafields.datatype='selection'
			AND	  $TBL_catalogue_info_extrafieldselection.id_extrafieldselection=$TBL_catalogue_extrafields_values.value
			AND    $TBL_catalogue_info_extrafieldselection.id_language='$idLanguage'
			AND    $TBL_catalogue_info_extrafields.id_language='$idLanguage'
			AND    $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields_values.id_extrafield
			AND    $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields.id
			ORDER BY $TBL_catalogue_extrafields.ordering"
			,$insideLink);
			$this->productTable[$row->id]["extrafields"]=array();
			while($row2=mysql_fetch_object($request2)){
			$this->productTable[$row->id]["extrafields"][$row2->id]=array();
			$this->productTable[$row->id]["extrafields"][$row2->id]["id"]=$row2->id;
			$this->productTable[$row->id]["extrafields"][$row2->id]["name"]=$row2->name;
			$this->productTable[$row->id]["extrafields"][$row2->id]["value"]= $row2->value;
			$this->productTable[$row->id]["extrafields"][$row2->id]["image_file"]= $row2->image_file_small;
			}*/

			//get the product states
			$request2 = request(
			"SELECT `id_state` FROM `$TBL_catalogue_product_state` WHERE `id_product`='$row->id'"
			,$insideLink);
			$this->productTable[$row->id]["productStates"]=array();
			while($state = mysql_fetch_object($request2)){
				$this->productTable[$row->id]["productStates"][$state->id_state]=true;
			}
		}


		if(!$link) mysql_close($insideLink);
		return $this->productTable;
	}
} //Class
?>
