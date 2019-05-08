<?php
// Requires class productCat and product
class productList{
	//variables
	var $idCategory=0;
	var $idGroup=0;
	var $nbProducts = 0;
	var $productTable=array();
	//constructors
	function productList($idCategory,$idLanguage,$link,$order,$offset=0,$limit=12,$idGroup=0){
		global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands, $TBL_catalogue_product_qty_price;
		global $TBL_catalogue_attachments,$TBL_gen_languages,$TBL_catalogue_extrafields_values,$TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafieldselection,$TBL_catalogue_info_extrafieldselection;
		global $TBL_catalogue_restocking_delay,$TBL_catalogue_info_restocking_delay;
		if(is_array($idCategory) && !count($idCategory)) return;
		elseif(!is_array($idCategory) && $idCategory==0) return;
		if($idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		//construct the list of categories
		if(is_array($idCategory)) {
			$nb2=0;$catList="(";
			foreach($idCategory as $category){

				if($nb2) $catList.=",";
				$catList.="'".$category."'";
				$nb2++;
			}
			$catList.=")";
		}
		$this->idGroup = $idGroup;
		//Get number of results
		$sqlQuery ="
	      		SELECT count(*) as nbProducts
	      		FROM $TBL_catalogue_products
	      		WHERE 1 ";
		if(is_array($idCategory)){
			$sqlQuery.="AND $TBL_catalogue_products.id_category IN $catList ";
		}	else {
			$sqlQuery.="AND $TBL_catalogue_products.id_category='$idCategory' ";
		}
		$sqlQuery.="AND $TBL_catalogue_products.active='Y'";
		$request=request($sqlQuery,$insideLink);
		$tmp = mysql_fetch_object($request);
		$this->nbProducts = $tmp->nbProducts;
		$orderSQL = "";
		foreach($order as $orderClause){
			if($orderSQL!="") $orderSQL.=",";
			switch($orderClause){
				case "random":$orderSQL.=" RAND() ";break;
				case "price":$orderSQL.=" CASE WHEN ($TBL_catalogue_products.is_in_special='Y' AND $TBL_catalogue_products.public_special='Y') THEN $TBL_catalogue_products.special_price ELSE public_price END ";break;
				case "price_desc":$orderSQL.=" CASE WHEN ($TBL_catalogue_products.is_in_special='Y' AND $TBL_catalogue_products.public_special='Y') THEN $TBL_catalogue_products.special_price ELSE public_price END DESC";break;
				case "name":$orderSQL.=" $TBL_catalogue_info_products.name ";break;
				case "brand":$orderSQL.=" $TBL_catalogue_brands.name IS NULL, $TBL_catalogue_brands.name ";break;
				case "ordering":$orderSQL.=" $TBL_catalogue_products.ordering ";break;
			}
		}

		$sqlQuery="SELECT $TBL_catalogue_info_products.name,
		                $TBL_catalogue_products.id,
		                $TBL_catalogue_products.ref_store,
		                $TBL_catalogue_products.public_price,
		                $TBL_catalogue_products.is_in_special,
		                $TBL_catalogue_products.public_special,
		                $TBL_catalogue_product_prices.price as group_price,
		                $TBL_catalogue_product_prices.is_in_special as group_in_special,
		                $TBL_catalogue_product_prices.special_price as group_special_price,
		                $TBL_catalogue_products.special_price,
		                $TBL_catalogue_products.image_file_large,
		                $TBL_catalogue_products.image_file_small,
		                $TBL_catalogue_products.stock,
		                $TBL_catalogue_restocking_delay.delay_days as restocking_delay,
                		$TBL_catalogue_info_restocking_delay.name as restocking_info,
		                $TBL_catalogue_info_products.description,
		                $TBL_catalogue_brands.name as brand_name,
		                $TBL_catalogue_brands.image_file_small as brand_image
		          FROM  ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_restocking_delay,$TBL_catalogue_info_restocking_delay)
		          LEFT JOIN `$TBL_catalogue_brands` ON (`$TBL_catalogue_brands`.`id`=`$TBL_catalogue_products`.`brand`)
		          LEFT JOIN `$TBL_catalogue_product_prices` ON (`$TBL_catalogue_product_prices`.`id_price_group`='$this->idGroup' AND `$TBL_catalogue_product_prices`.`id_product`=`$TBL_catalogue_products`.`id`)
		          WHERE 1 ";
		if(is_array($idCategory)){
			$sqlQuery.="AND 	$TBL_catalogue_products.id_category IN $catList ";
		} else {
			$sqlQuery.="AND 	$TBL_catalogue_products.id_category='$idCategory' ";
		}
		$sqlQuery.="AND   $TBL_catalogue_products.active='y'
		          AND   $TBL_catalogue_info_products.id_language='$idLanguage'
		          AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
		          AND   $TBL_catalogue_info_restocking_delay.id_language='$idLanguage'
		          AND	$TBL_catalogue_products.restocking_delay = $TBL_catalogue_restocking_delay.id
		          AND	$TBL_catalogue_info_restocking_delay.id_delay = $TBL_catalogue_restocking_delay.id
		          ORDER BY $orderSQL ";
		if($limit) $sqlQuery.="LIMIT $offset,$limit";
		$request=request($sqlQuery,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return;
		}
		$this->idCategory = $idCategory;
		$this->language = $idLanguage;

		$this->productTable=array();
		while($row=mysql_fetch_object($request)){
			$this->productTable[$row->id]=array();
			$this->productTable[$row->id]["id"]=$row->id;
			$this->productTable[$row->id]["title"]=$row->name;
			$this->productTable[$row->id]["ref"]=$row->ref_store;
			$this->productTable[$row->id]["image_large"]=$row->image_file_large;
			$this->productTable[$row->id]["image_small"]=$row->image_file_small;
			$this->productTable[$row->id]["more_details"]=($row->description!="");
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
			//get the QTY prices
			if(!$this->idGroup){ //Qty prices does not apply to groups
				$requestStr = "SELECT `qty`,`price`
								FROM `$TBL_catalogue_product_qty_price`
								WHERE `id_product`='$row->id'
								ORDER BY `qty` DESC	";
				$request2 = request($requestStr,$insideLink);
				while($qtyPrice = mysql_fetch_object($request2)){
					if(!isset($this->productTable[$row->id]["prices"]["qty_prices"])) $this->productTable[$row->id]["prices"]["qty_prices"]=array();
					$this->productTable[$row->id]["prices"]["qty_prices"][$qtyPrice->qty]=$qtyPrice->price;
				}
			}
			//get the attachments
			$request2 = request(
			"SELECT $TBL_catalogue_attachments.id,
                          $TBL_catalogue_attachments.file_name,
                          $TBL_catalogue_attachments.title,
                          $TBL_catalogue_attachments.description,
                          $TBL_gen_languages.name
                   FROM   $TBL_catalogue_attachments,$TBL_gen_languages
                   WHERE  $TBL_catalogue_attachments.id_product='".$row->id."'
                   AND    $TBL_gen_languages.id=$TBL_catalogue_attachments.id_language"
			,$insideLink);
			$this->productTable[$row->id]["attachments"]=array();
			while($row2=mysql_fetch_object($request2)){
				$this->productTable[$row->id]["attachments"][$row2->id]=array();
				$this->productTable[$row->id]["attachments"][$row2->id]["title"]=$row2->title;
				$this->productTable[$row->id]["attachments"][$row2->id]["description"]= $row2->description;
				$this->productTable[$row->id]["attachments"][$row2->id]["file_name"]= $row2->file_name;
			}
			//get the text and rich extrafields
			$request2 = request(
			"SELECT $TBL_catalogue_extrafields_values.id_extrafield id,
                          $TBL_catalogue_extrafields_values.value,
                          $TBL_catalogue_info_extrafields.name
                   FROM   $TBL_catalogue_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafields
                   WHERE  $TBL_catalogue_extrafields_values.id_product='".$row->id."'
                   AND	  ($TBL_catalogue_extrafields.datatype='rich' OR $TBL_catalogue_extrafields.datatype='text')
                   AND    $TBL_catalogue_extrafields_values.id_language='".$this->language."'
                   AND    $TBL_catalogue_info_extrafields.id_language='".$this->language."'
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
			}
			//get the selection extrafields
			/*$request2 = request(
			"SELECT $TBL_catalogue_extrafields_values.id_extrafield id,
			$TBL_catalogue_info_extrafieldselection.value,
			$TBL_catalogue_info_extrafields.name,
			$TBL_catalogue_info_extrafieldselection.image_file_small
			FROM   $TBL_catalogue_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafieldselection,$TBL_catalogue_info_extrafieldselection
			WHERE  $TBL_catalogue_extrafields_values.id_product='".$row->id."'
			AND	  $TBL_catalogue_extrafields.datatype='selection'
			AND	  $TBL_catalogue_info_extrafieldselection.id_extrafieldselection=$TBL_catalogue_extrafields_values.value
			AND    $TBL_catalogue_info_extrafieldselection.id_language='".$this->language."'
			AND    $TBL_catalogue_info_extrafields.id_language='".$this->language."'
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
		}
		if(!$link) mysql_close($insideLink);
		return $this->productTable;
	}
} //Class
?>
