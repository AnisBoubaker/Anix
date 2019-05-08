<?php
class product{
	//variables
	var $id=0;
	var $id_category=0;
	var $language=0;
	var $title = "";
	var $active;
	var $description = "";
	var $product_type= "";
	var $is_in_special="";
	var $prices=array();
	var $image_small="";
	var $image_large="";
	var $image_orig="";
	var $ref_store="";
	var $ref_manufacturer="";
	var $url_manufacturer="";
	var $brand;
	var $dimensions=array();
	var $weight;
	var $stock=0;
	var $alias_name="";
	var $attachments=array();
	var $options = array();
	var $extrafields = array();
	var $parentsPath;
	var $productCatLinks = array();
	var $productLinks = array();
	var $deliveryDelay=0;
	var $productStates = array();
	var $stockState = "";
	var $html_title="";
	var $html_description="";
	var $id_menu = 0;


	//constructors
	function product($idProduct,$idLanguage,$idGroup,$link){
		global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands,$TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafields,$TBL_catalogue_attachments,$TBL_catalogue_extrafields,$TBL_catalogue_restocking_delay,$TBL_catalogue_info_restocking_delay,$TBL_catalogue_product_qty_price;
		global $TBL_catalogue_product_options,$TBL_catalogue_info_options,$TBL_catalogue_product_option_choices,$TBL_catalogue_info_choices,$TBL_catalogue_extrafieldselection,$TBL_catalogue_info_extrafieldselection;
		global $TBL_catalogue_product_state,$TBL_catalogue_state;
		global $TBL_gen_languages;
		if($idProduct==0 || $idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$request=request(
		"SELECT $TBL_catalogue_info_products.name,
                $TBL_catalogue_info_products.description,
                $TBL_catalogue_products.id,
                $TBL_catalogue_products.id_category,
                $TBL_catalogue_products.product_type,
                $TBL_catalogue_products.active,
                $TBL_catalogue_products.image_file_orig,
                $TBL_catalogue_products.image_file_small,
                $TBL_catalogue_products.image_file_large,
                $TBL_catalogue_products.ref_store,
                $TBL_catalogue_products.ref_manufacturer,
                $TBL_catalogue_products.url_manufacturer,
                $TBL_catalogue_brands.name as brand_name,
                $TBL_catalogue_brands.image_file_large as brand_image_large,
                $TBL_catalogue_brands.image_file_small as brand_image_small,
                $TBL_catalogue_brands.URL as brand_url,
                $TBL_catalogue_products.dim_W,
                $TBL_catalogue_products.dim_H,
                $TBL_catalogue_products.dim_L,
                $TBL_catalogue_products.weight,
                $TBL_catalogue_products.public_price,
                $TBL_catalogue_products.ecotaxe,
                $TBL_catalogue_products.is_in_special,
                $TBL_catalogue_products.public_special,
                $TBL_catalogue_products.special_price,
                $TBL_catalogue_product_prices.price as group_price,
                $TBL_catalogue_product_prices.is_in_special as group_in_special,
                $TBL_catalogue_product_prices.special_price as group_special_price,
                $TBL_catalogue_products.stock,
                $TBL_catalogue_info_products.alias_name,
                $TBL_catalogue_info_products.htmltitle,
                $TBL_catalogue_info_products.htmldescription,
                $TBL_catalogue_restocking_delay.delay_days as restocking_delay,
                $TBL_catalogue_info_restocking_delay.name as restocking_info
          FROM  ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_restocking_delay,$TBL_catalogue_info_restocking_delay)
		  LEFT JOIN `$TBL_catalogue_brands` ON ($TBL_catalogue_brands.id = $TBL_catalogue_products.brand)
		  LEFT JOIN `$TBL_catalogue_product_prices` ON (`$TBL_catalogue_product_prices`.`id_price_group`='$idGroup' AND `$TBL_catalogue_product_prices`.`id_product`=`$TBL_catalogue_products`.`id`)
          WHERE $TBL_catalogue_products.id='$idProduct'
          AND   $TBL_catalogue_info_products.id_language='$idLanguage'
          AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
          AND   $TBL_catalogue_info_restocking_delay.id_language='$idLanguage'
          AND	$TBL_catalogue_products.restocking_delay = $TBL_catalogue_restocking_delay.id
          AND	$TBL_catalogue_info_restocking_delay.id_delay = $TBL_catalogue_restocking_delay.id"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return;
		}
		$this->id = $idProduct;
		$this->language = $idLanguage;
		$row = mysql_fetch_object($request);
		$this->id_category = $row->id_category;
		$this->active=($row->active=="Y");
		$this->title = $row->name;
		$this->description = $row->description;
		$this->product_type = $row->product_type;
		$this->image_orig = $row->image_file_orig;
		$this->image_small = $row->image_file_small;
		$this->image_large = $row->image_file_large;
		$this->ref_store = $row->ref_store;
		$this->ref_manufacturer = $row->ref_manufacturer;
		$this->url_manufacturer = $row->url_manufacturer;
		$this->html_title = $row->htmltitle;
		$this->html_description = $row->htmldescription;
		$this->stock = $row->stock;
		if($row->stock>0){
			$this->deliveryDelay = 0;
			$this->stockState = _("En stock");
		} else {
			$this->deliveryDelay = $row->restocking_delay;
			$this->stockState = $row->restocking_info;
		}
		$this->weight = $row->weight;
		$this->is_in_special = $row->is_in_special;
		$this->ecotaxe = $row->ecotaxe;
		//Start of prices
		$this->prices=array();
		$this->prices["public"]=array();
		$this->prices["public"]["name"]= "public";
		$this->prices["public"]["id"]= 0;
		$this->prices["public"]["price"]= $row->public_price;

		if($row->is_in_special=="Y" && $row->public_special=="Y"){
			$this->prices["public"]["in_special"]=true;
			$this->prices["public"]["special_price"]=$row->special_price;
		} else $this->prices["public"]["in_special"]=false;
		//Group prices
		if($idGroup){
			$this->prices["group"]=array();
			$this->prices["group"]["name"]= "group";
			$this->prices["group"]["id"]= $idGroup;
			$this->prices["group"]["price"]= $row->group_price;
			if($row->is_in_special=="Y" && $row->group_in_special=="Y"){
				$this->prices["group"]["in_special"]=true;
				$this->prices["group"]["special_price"]=$row->special_price;
			} else $this->prices["group"]["in_special"]=false;
		}
		//QTY Prices
		if(!$idGroup){ //Qty prices does not apply to groups
			$requestStr = "SELECT `qty`,`price`
							FROM `$TBL_catalogue_product_qty_price`
							WHERE `id_product`='$row->id'
							ORDER BY `qty` DESC	";
			$request2 = request($requestStr,$insideLink);
			while($qtyPrice = mysql_fetch_object($request2)){
				if(!isset($this->prices["qty_prices"])) $this->prices["qty_prices"]=array();
				$this->prices["qty_prices"][$qtyPrice->qty]=$qtyPrice->price;
			}
		}
		//Get the brand
		if($row->brand_name){
			$this->brand=array();
			$this->brand["name"]=$row->brand_name;
			$this->brand["image_small"]=$row->brand_image_small;
			$this->brand["image_large"]=$row->brand_image_large;
		}
		//get the text and rich extrafields
		$request2 = request(
		"SELECT $TBL_catalogue_extrafields_values.id_extrafield id,
                        $TBL_catalogue_extrafields.datatype,
                        $TBL_catalogue_extrafields_values.value,
                        $TBL_catalogue_info_extrafields.name
                 FROM   $TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafields
                 WHERE  $TBL_catalogue_extrafields_values.id_product='".$row->id."'
                 AND 	($TBL_catalogue_extrafields.datatype='rich' OR $TBL_catalogue_extrafields.datatype='text')
                 AND    $TBL_catalogue_extrafields_values.id_language='".$this->language."'
                 AND    $TBL_catalogue_info_extrafields.id_language='".$this->language."'
                 AND    $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields_values.id_extrafield
                 AND    $TBL_catalogue_extrafields.id=$TBL_catalogue_extrafields_values.id_extrafield
                 ORDER BY $TBL_catalogue_extrafields.ordering"
		,$insideLink);
		$this->extrafields=array();
		while($row2=mysql_fetch_object($request2)){
			$this->extrafields[$row2->id]["id"]=$row2->id;
			$this->extrafields[$row2->id]["name"]=$row2->name;
			$this->extrafields[$row2->id]["type"]=$row2->datatype;
			$this->extrafields[$row2->id]["value"]= $row2->value;
		}
		//get the selection extrafields
		/*$request2 = request(
		"SELECT $TBL_catalogue_extrafields_values.id_extrafield id,
		$TBL_catalogue_extrafields.datatype,
		$TBL_catalogue_info_extrafieldselection.value,
		$TBL_catalogue_info_extrafields.name,
		$TBL_catalogue_info_extrafieldselection.image_file_small
		FROM   $TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafields,$TBL_catalogue_extrafieldselection,$TBL_catalogue_info_extrafieldselection
		WHERE  $TBL_catalogue_extrafields_values.id_product='".$row->id."'
		AND	$TBL_catalogue_extrafields.datatype='selection'
		AND	$TBL_catalogue_info_extrafieldselection.id_extrafieldselection=$TBL_catalogue_extrafields_values.value
		AND    $TBL_catalogue_info_extrafieldselection.id_language='".$this->language."'
		AND    $TBL_catalogue_info_extrafields.id_language='".$this->language."'
		AND    $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields_values.id_extrafield
		AND    $TBL_catalogue_extrafields.id=$TBL_catalogue_extrafields_values.id_extrafield
		ORDER BY $TBL_catalogue_extrafields.ordering"
		,$insideLink);
		$this->extrafields=array();
		while($row2=mysql_fetch_object($request2)){
		$this->extrafields[$row2->id]["id"]=$row2->id;
		$this->extrafields[$row2->id]["name"]=$row2->name;
		$this->extrafields[$row2->id]["type"]=$row2->datatype;
		$this->extrafields[$row2->id]["value"]= $row2->value;
		$this->extrafields[$row2->id]["image_file"]= $row2->image_file_small;
		}*/
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
		$this->attachments=array();
		while($row2=mysql_fetch_object($request2)){
			$this->attachments[$row2->id]["title"]=$row2->title;
			$this->attachments[$row2->id]["description"]= $row2->description;
			$this->attachments[$row2->id]["file_name"]= $row2->file_name;
		}
		//Get the options
		$request2 = request(
		"SELECT $TBL_catalogue_product_options.id,
                        $TBL_catalogue_info_options.name
                 FROM   $TBL_catalogue_product_options,$TBL_catalogue_info_options
                 WHERE  $TBL_catalogue_product_options.id_product='".$row->id."'
                 AND    $TBL_catalogue_info_options.id_language='".$this->language."'
                 AND    $TBL_catalogue_info_options.id_option=$TBL_catalogue_product_options.id
                 ORDER BY $TBL_catalogue_info_options.name"
		,$insideLink);
		$this->options = array();
		while($row2=mysql_fetch_object($request2)){
			$this->options[$row2->id]=array();
			$this->options[$row2->id]["id"]=$row2->id;
			$this->options[$row2->id]["name"]=$row2->name;
			$this->options[$row2->id]["choices"]=array();
			//Get the choices
			$request3 = request(
			"SELECT $TBL_catalogue_product_option_choices.id,
                          $TBL_catalogue_info_choices.value,
                          $TBL_catalogue_product_option_choices.default_choice,
                          $TBL_catalogue_product_option_choices.price_diff,
                          $TBL_catalogue_product_option_choices.price_value,
                          $TBL_catalogue_product_option_choices.price_method
                   FROM   $TBL_catalogue_product_option_choices,$TBL_catalogue_info_choices
                   WHERE  $TBL_catalogue_product_option_choices.id_option='".$row2->id."'
                   AND    $TBL_catalogue_info_choices.id_language='".$this->language."'
                   AND    $TBL_catalogue_info_choices.id_choice=$TBL_catalogue_product_option_choices.id"
			,$insideLink);
			while($row3=mysql_fetch_object($request3)){
				$this->options[$row2->id]["choices"][$row3->id]=array();
				$this->options[$row2->id]["choices"][$row3->id]["id"]=$row3->id;
				$this->options[$row2->id]["choices"][$row3->id]["name"]=$row3->value;
				if($row3->price_method=="currency") $priceDiff=$row3->price_value;
				if($row3->price_method=="percentage") $priceDiff=($row3->price_value*$row->public_price)/100;
				if($row3->price_diff=="decrement") $priceDiff=-$priceDiff;
				$this->options[$row2->id]["choices"][$row3->id]["price_diff"]=$priceDiff;
				$this->options[$row2->id]["choices"][$row3->id]["default"]=($row3->default_choice=="Y");
			}
			//Internal function to sort the choices
			if(! function_exists( 'choicesSort' )){
				function choicesSort($a,$b){
					return $a["price_diff"]-$b["price_diff"];
				}
			}
			usort($this->options[$row2->id]["choices"],"choicesSort");
		}

		//get the product states
		$request2 = request(
		"SELECT `id_state` FROM `$TBL_catalogue_product_state` WHERE `id_product`='$this->id'"
		,$insideLink);
		$this->productStates=array();
		while($state = mysql_fetch_object($request2)){
			$this->productStates[$state->id_state]=true;
		}

		if(!$link) mysql_close($insideLink);
	}

	function getParentsPath(){
		$currentId = $this->id_category;
		if($currentId) $link = dbConnect();
		$counter = 0;
		$this->parentsPath=array();
		while($currentId){
			$this->parentsPath[$counter]=$this->catShortDesc($currentId,$this->language,$link);
			$currentId=$this->parentsPath[$counter]["id_parent"];
			$counter++;
		}
		if(isset($link)) mysql_close($link);
		return $this->parentsPath;
	}
	// Internal function
	function catShortDesc($idCat,$idLanguage,$link){
		global $TBL_catalogue_categories,$TBL_catalogue_info_categories;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$request=request(
		"SELECT $TBL_catalogue_info_categories.name,
                $TBL_catalogue_categories.id,
                $TBL_catalogue_categories.id_parent,
                $TBL_catalogue_categories.image_file_large,
                $TBL_catalogue_categories.image_file_small
          FROM  $TBL_catalogue_categories,$TBL_catalogue_info_categories
          WHERE $TBL_catalogue_categories.id='$idCat'
          AND   $TBL_catalogue_info_categories.id_language='$idLanguage'
          AND   $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return null;
		}
		$row = mysql_fetch_object($request);
		$returnTable=array();
		$returnTable["title"] = $row->name;
		$returnTable["id"] = $row->id;
		$returnTable["id_parent"] = $row->id_parent;
		$returnTable["image_large"] = $row->image_file_large;
		$returnTable["image_small"] = $row->image_file_small;
		if(!$link) mysql_close($insideLink);
		return $returnTable;
	}

	function getProductCatLinks($linkCat,$dbLink){
		global $TBL_catalogue_categories,$TBL_catalogue_info_categories,$TBL_links_link;
		if(!$dbLink) $insideLink = dbConnect();
		else $insideLink=$dbLink;

		$request=request(
		"SELECT $TBL_catalogue_info_categories.name,
                $TBL_catalogue_categories.id,
                $TBL_catalogue_categories.image_file_large,
                $TBL_catalogue_categories.image_file_small
          FROM  $TBL_catalogue_categories,$TBL_catalogue_info_categories,$TBL_links_link
          WHERE $TBL_links_link.id_category = '$linkCat'
          AND	$TBL_links_link.from_module='1'
          AND	$TBL_links_link.from_item='$this->id'
          AND 	$TBL_links_link.to_module='2'
          AND	$TBL_catalogue_categories.id=$TBL_links_link.to_item
          AND   $TBL_catalogue_info_categories.id_language='".$this->language."'
          AND   $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id
          ORDER BY $TBL_links_link.ordering"
		,$insideLink);

		$this->productCatLinks=array();
		while($row = mysql_fetch_object($request)){
			$this->productCatLinks[$row->id]=array();
			$this->productCatLinks[$row->id]["title"] = $row->name;
			$this->productCatLinks[$row->id]["id"] = $row->id;
			$this->productCatLinks[$row->id]["image_large"] = $row->image_file_large;
			$this->productCatLinks[$row->id]["image_small"] = $row->image_file_small;
		}

		if(!$dbLink) mysql_close($insideLink);
		return $this->productCatLinks;
	}

	function getProductLinks($linkCat,$idGroup,$dbLink){
		global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_links_link,$TBL_catalogue_brands,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_info_categories;
		if(!$dbLink) $insideLink = dbConnect();
		else $insideLink=$dbLink;
		$requestStr="SELECT $TBL_catalogue_info_products.name,
                $TBL_catalogue_products.id,
                $TBL_catalogue_products.id_category,
                $TBL_catalogue_products.brand,
                $TBL_catalogue_products.image_file_large,
                $TBL_catalogue_products.image_file_small,
                $TBL_catalogue_products.image_file_icon,
                $TBL_catalogue_products.public_price,
                $TBL_catalogue_products.is_in_special,
                $TBL_catalogue_products.public_special,
                $TBL_catalogue_products.special_price,
                $TBL_catalogue_product_prices.price as group_price,
                $TBL_catalogue_product_prices.is_in_special as group_in_special,
                $TBL_catalogue_product_prices.special_price as group_special_price,
                $TBL_catalogue_brands.name as brand_name,
		        $TBL_catalogue_brands.image_file_small as brand_image,
		        $TBL_catalogue_info_categories.name as category_name
          FROM  ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_links_link,$TBL_catalogue_info_categories)
		  LEFT JOIN `$TBL_catalogue_brands` ON (`$TBL_catalogue_brands`.`id`=`$TBL_catalogue_products`.`brand`)
		  LEFT JOIN `$TBL_catalogue_product_prices` ON (`$TBL_catalogue_product_prices`.`id_price_group`='$idGroup' AND `$TBL_catalogue_product_prices`.`id_product`=`$TBL_catalogue_products`.`id`)
		  WHERE $TBL_links_link.id_category = '$linkCat'
          AND	$TBL_links_link.from_module='1'
          AND	$TBL_links_link.from_item='$this->id'
          AND 	$TBL_links_link.to_module='1'
          AND   $TBL_catalogue_products.id=$TBL_links_link.to_item
          AND 	$TBL_catalogue_products.`active`='Y'
          AND   $TBL_catalogue_info_products.id_language='".$this->language."'
          AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
          AND 	$TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_products.id_category
          AND 	$TBL_catalogue_info_categories.id_language='".$this->language."'
          ORDER BY $TBL_links_link.ordering";
		$request=request($requestStr,$insideLink);

		$this->productLinks = array();
		while($row = mysql_fetch_object($request)){
			$this->productLinks[$row->id]=array();
			$this->productLinks[$row->id]["title"] = $row->name;
			$this->productLinks[$row->id]["id"] = $row->id;
			$this->productLinks[$row->id]["image_large"] = $row->image_file_large;
			$this->productLinks[$row->id]["image_small"] = $row->image_file_small;
			$this->productLinks[$row->id]["image_icon"] = $row->image_file_icon;
			$this->productLinks[$row->id]["category_id"] = $row->id_category;
			$this->productLinks[$row->id]["category_name"] = $row->category_name;
			//Start of prices
			$this->productLinks[$row->id]["prices"]=array();
			$this->productLinks[$row->id]["prices"]["public"]=array();
			$this->productLinks[$row->id]["prices"]["public"]["name"]= "public";
			$this->productLinks[$row->id]["prices"]["public"]["price"]= $row->public_price;
			if($row->is_in_special=="Y" && $row->public_special){
				$this->productLinks[$row->id]["prices"]["public"]["in_special"]=true;
				$this->productLinks[$row->id]["prices"]["public"]["special_price"]=$row->special_price;
			} else $this->productLinks[$row->id]["prices"]["public"]["in_special"]=false;
			if($row->group_price){
				$this->productLinks[$row->id]["prices"]["group"]= array();
				$this->productLinks[$row->id]["prices"]["group"]["price"]= $row->group_price;
				if($row->is_in_special=="Y" && $row->group_in_special){
					$this->productLinks[$row->id]["prices"]["group"]["in_special"]= true;
					$this->productLinks[$row->id]["prices"]["group"]["special_price"]= $row->group_special_price;
				}
			}
			if($row->brand_name){
				$this->productLinks[$row->id]["brand"]=$row->brand_name;
				$this->productLinks[$row->id]["brand_image"]=$row->brand_image;
			}
		}
		if(!$dbLink) mysql_close($insideLink);
		return $this->productLinks;
	}
} //Class
?>
