<?php
  // Requires class productCat and product
  class productExchangeList{
    //variables
    var $categoryName = "";
    var $correspondingCategory = "";
    var $idCategory=0;
    var $idGroup=0;
    var $subCategories = array();
    var $productTable=array();
    //constructors
    //dbColumn is the specific column in the DB table for the price comparator (this column includes the price comparator's category name)
    function productExchangeList($idCategory, $idLanguage, $catTable, $link, $idPartner, $dbColumn=""){
      global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_categories,$TBL_catalogue_info_categories,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands;
      global $TBL_catalogue_attachments,$TBL_gen_languages,$TBL_catalogue_extrafields_values,$TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafieldselection,$TBL_catalogue_info_extrafieldselection;
      global $TBL_catalogue_partner_category , $TBL_catalogue_anix_partner, $TBL_catalogue_restocking_delay;
      if($idCategory==0 || $idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      //check if category exists & fetch the category name from Database
      $request = request(
      	"SELECT `$TBL_catalogue_categories`.*,`$TBL_catalogue_info_categories`.`name`,`$TBL_catalogue_partner_category`.`name` AS partner_cat
      	 FROM (`$TBL_catalogue_info_categories`,`$TBL_catalogue_categories`)
      	 LEFT JOIN (`$TBL_catalogue_partner_category` , `$TBL_catalogue_anix_partner`)
      	 	ON ( `$TBL_catalogue_anix_partner`.`id_partner` = '$idPartner'
      	 		  AND `$TBL_catalogue_anix_partner`.`id_catalogue_category` = `$TBL_catalogue_categories`.`id`
      	 		  AND `$TBL_catalogue_anix_partner`.`id_partner_category` = `$TBL_catalogue_partner_category`.`id` )
      	 WHERE `$TBL_catalogue_categories`.`id`='$idCategory'
      	 AND `id_catalogue_cat`='$idCategory'
      	 AND `id_language`='$idLanguage'"
      	,$insideLink);
      if(!mysql_num_rows($request)){
		if(!$link) mysql_close($insideLink);
        return;
      }
      $this->language = $idLanguage;
      $this->idCategory = $idCategory;

      $tmp = mysql_fetch_object($request);
      $this->categoryName = sanitizeStringExchange($tmp->name);
      if($tmp->partner_cat) $this->correspondingCategory = $tmp->partner_cat;
      //Add the main category as a subcategory (to get it's products if unclassified
	      $this->subCategories[$idCategory] = array();
		  //use the price comparator category in priority
	      if($this->correspondingCategory!="") $this->subCategories[$idCategory]["name"] = $this->correspondingCategory;
		  else $this->subCategories[$idCategory]["name"] = $this->categoryName;
		  $this->subCategories[$idCategory]["subCategories"] = "$idCategory"; //No subcats since we are specifying them underneath...
      //get the first level of subcategories
      $requestStr = "
         SELECT `$TBL_catalogue_categories`.`id`,`$TBL_catalogue_info_categories`.`name`,`$TBL_catalogue_partner_category`.`name` AS partner_cat
      	 FROM (`$TBL_catalogue_categories`,`$TBL_catalogue_info_categories`)
      	 LEFT JOIN (`$TBL_catalogue_partner_category` , `$TBL_catalogue_anix_partner`)
      	 	ON ( `$TBL_catalogue_anix_partner`.`id_partner` = '$idPartner'
      	 		  AND `$TBL_catalogue_anix_partner`.`id_catalogue_category` = `$TBL_catalogue_categories`.`id`
      	 		  AND `$TBL_catalogue_anix_partner`.`id_partner_category` = `$TBL_catalogue_partner_category`.`id` )
      	 WHERE `$TBL_catalogue_categories`.`id_parent`='$idCategory'
      	 AND `$TBL_catalogue_info_categories`.`id_catalogue_cat`=`$TBL_catalogue_categories`.`id`
      	 AND `$TBL_catalogue_info_categories`.`id_language`='$idLanguage'";
      $request = request($requestStr,$insideLink);
      while($category = mysql_fetch_object($request)){
      	$this->subCategories[$category->id] = array();
      	//If we've specified a DB column from which the category name should be used, use it first
      	if($category->partner_cat) $this->subCategories[$category->id]["name"]=$category->partner_cat;
      	//If we did specify a specifi DB column but it's epmty or does not exist, try to use the parent category instead
      	elseif($this->correspondingCategory!="") $this->subCategories[$category->id]["name"]=$this->correspondingCategory;
      	//Finally, use our regular category name if nothing else apply
      	else $this->subCategories[$category->id]["name"] = sanitizeStringExchange($this->categoryName." / ".$category->name,1024);
      	$this->subCategories[$category->id]["subCategories"] = getAllSubcats($category->id,$catTable);
      	//REM: "subCategories" inclues the category itself, so we can get the products even if they are not classified under a subcategory
      }
      //print_r($this->subCategories);
      $this->productTable=array();
      foreach ($this->subCategories as $subcat){
	      //get the products by subcategory
	      $request=request(
	        "SELECT $TBL_catalogue_products.id,
	        		$TBL_catalogue_info_products.name,
	                $TBL_catalogue_brands.name brand_name,
	                $TBL_catalogue_products.ref_store,
	                $TBL_catalogue_products.ref_manufacturer,
	                $TBL_catalogue_products.upc_code,
	                $TBL_catalogue_products.public_price,
	                $TBL_catalogue_products.is_in_special,
	                $TBL_catalogue_products.public_special,
	                $TBL_catalogue_products.special_price,
	                $TBL_catalogue_products.weight,
	                $TBL_catalogue_products.image_file_large,
	                $TBL_catalogue_products.stock,
	                $TBL_catalogue_restocking_delay.delay_days as restocking_delay,
	                $TBL_catalogue_products.ecotaxe,
	                $TBL_catalogue_info_products.description
	          FROM  ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_restocking_delay)
	          LEFT JOIN $TBL_catalogue_brands on ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id)
	          WHERE $TBL_catalogue_products.id_category IN (".$subcat["subCategories"].")
	          AND   $TBL_catalogue_products.active='y'
	          AND   $TBL_catalogue_info_products.id_language='$idLanguage'
	          AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
	          AND	$TBL_catalogue_products.restocking_delay = $TBL_catalogue_restocking_delay.id
	         "
	        ,$insideLink);

	      while($row=mysql_fetch_object($request)){
	        $this->productTable[$row->id]=array();
	        $this->productTable[$row->id]["id"]=$row->id;
	        $this->productTable[$row->id]["name"]=sanitizeStringExchange($row->name,255);
	        $this->productTable[$row->id]["brand_name"]=sanitizeStringExchange($row->brand_name,100);
	        $this->productTable[$row->id]["category"]=$subcat["name"];
	        $this->productTable[$row->id]["ref_store"]=$row->ref_store;
	        $this->productTable[$row->id]["ref_manufacturer"]=$row->ref_manufacturer;
	        $this->productTable[$row->id]["upc_code"]=$row->upc_code;
	        $this->productTable[$row->id]["image_large"]=$row->image_file_large;
	        $this->productTable[$row->id]["stock"]=$row->stock;
	        $this->productTable[$row->id]["restocking_delay"]=$row->restocking_delay;
	        if($row->is_in_special=="Y" && $row->public_special){
	        	$this->productTable[$row->id]["price"]=$row->special_price;
	        } else {
	        	$this->productTable[$row->id]["price"]=$row->public_price;
	        }
	        $this->productTable[$row->id]["ecotaxe"]=$row->ecotaxe;
	        $this->productTable[$row->id]["weight"]=$row->weight;
	        //$tmp = unhtmlentities($row->description);
	        //str_replace("&nbsp;"," ",$tmp);
	        //$tmp = strip_tags($tmp);
	        $this->productTable[$row->id]["description"]=sanitizeStringExchange($row->description,1024);
	      }
      }
      if(!$link) mysql_close($insideLink);
      return $this->productTable;
    }
  } //Class
?>
