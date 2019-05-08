<?php
// Requires class productCat and product
class featuredList{
	//variables
	var $idCategory=0;
	var $featuredTable=array();
	//constructors
	function featuredList($idCategory,$idLanguage,$idGroup,$link){
		global $TBL_catalogue_featured,$TBL_catalogue_info_featured;
		global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_info_categories,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands;
		global $TBL_gen_languages;
		if($idCategory==0 || $idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$currentDate = date('Y-m-d',time());
		$request=request(
		"SELECT $TBL_catalogue_info_featured.title,
                $TBL_catalogue_info_featured.field1,
                $TBL_catalogue_info_featured.field2,
                $TBL_catalogue_featured.image_file_large,
                $TBL_catalogue_featured.image_file_small,
                $TBL_catalogue_featured.id_catalogue_prd,
                $TBL_catalogue_featured.id_catalogue_cat
          FROM  $TBL_catalogue_featured,$TBL_catalogue_info_featured
          WHERE $TBL_catalogue_featured.id_category='$idCategory'
          AND   (
                $TBL_catalogue_featured.active='Y'
                OR
                ($TBL_catalogue_featured.active='DATE' AND $TBL_catalogue_featured.from_date<='$currentDate' AND $TBL_catalogue_featured.to_date>='$currentDate')
                )
          AND   $TBL_catalogue_info_featured.id_language='$idLanguage'
          AND   $TBL_catalogue_info_featured.id_featured=$TBL_catalogue_featured.id
          ORDER BY $TBL_catalogue_featured.ordering"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return;
		}
		$this->idCategory = $idCategory;
		$this->featuredTable=array();$i=0;
		while($row=mysql_fetch_object($request)){
			$this->featuredTable[$i]=array();
			$this->featuredTable[$i]["title"]=$row->title;
			$this->featuredTable[$i]["field1"]=$row->field1;
			$this->featuredTable[$i]["field2"]=$row->title;
			$this->featuredTable[$i]["image_large"]=$row->image_file_large;
			$this->featuredTable[$i]["image_small"]=$row->image_file_small;
			if($row->id_catalogue_prd){
				$this->featuredTable[$i]["product"]=array();
				$request2=request(
				"SELECT $TBL_catalogue_info_products.name,
	                    $TBL_catalogue_products.id,
	                    $TBL_catalogue_products.brand,
	                    $TBL_catalogue_products.ref_store,
	                    $TBL_catalogue_products.public_price,
	                    $TBL_catalogue_products.is_in_special,
	                    $TBL_catalogue_products.public_special,
	                    $TBL_catalogue_products.special_price,
	                    $TBL_catalogue_products.image_file_large,
	                    $TBL_catalogue_products.image_file_small
	              FROM  $TBL_catalogue_products,$TBL_catalogue_info_products
	              WHERE $TBL_catalogue_products.id='".$row->id_catalogue_prd."'
	              AND   $TBL_catalogue_products.active='y'
	              AND   $TBL_catalogue_info_products.id_language='$idLanguage'
	              AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
	              ORDER BY $TBL_catalogue_products.ordering"
				,$insideLink);
				$row2=mysql_fetch_object($request2);
				$this->featuredTable[$i]["product"]=array();
				$this->featuredTable[$i]["product"]["id"]=$row2->id;
				$this->featuredTable[$i]["product"]["title"]=$row2->name;
				$this->featuredTable[$i]["product"]["ref"]=$row2->ref_store;
				$this->featuredTable[$i]["product"]["image_large"]=$row2->image_file_large;
				$this->featuredTable[$i]["product"]["image_small"]=$row2->image_file_small;
				$this->featuredTable[$i]["product"]["prices"]=array();
				$this->featuredTable[$i]["product"]["prices"]["public"]=array();
				$this->featuredTable[$i]["product"]["prices"]["public"]["name"]= "public";
				$this->featuredTable[$i]["product"]["prices"]["public"]["price"]= $row2->public_price;
				if($row2->is_in_special=="Y" && $row2->public_special){
					$this->featuredTable[$i]["product"]["prices"]["public"]["in_special"]=true;
					$this->featuredTable[$i]["product"]["prices"]["public"]["special_price"]=$row2->special_price;
				} else $this->featuredTable[$i]["product"]["prices"]["public"]["in_special"]=false;
				//Start of prices
				$request3 = request(
				"SELECT $TBL_catalogue_info_price_groups.name,
                              $TBL_catalogue_product_prices.price,
                              $TBL_catalogue_product_prices.is_in_special,
                              $TBL_catalogue_product_prices.special_price
                       FROM   $TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices
                       WHERE  $TBL_catalogue_price_groups.id_group='$idGroup'
                       AND    $TBL_catalogue_info_price_groups.id_language='$idLanguage'
                       AND    $TBL_catalogue_product_prices.id_product='".$row2->id."'
                       AND    $TBL_catalogue_info_price_groups.id_price_group=$TBL_catalogue_price_groups.id
                       AND    $TBL_catalogue_product_prices.id_price_group=$TBL_catalogue_price_groups.id"
				,$insideLink);
				while($row3=mysql_fetch_object($request3)){
					$this->featuredTable[$i]["product"]["prices"][$row3->name]=array();
					$this->featuredTable[$i]["product"]["prices"][$row3->name]["name"]= $row3->name;
					$this->featuredTable[$i]["product"]["prices"][$row3->name]["price"]= $row3->price;
					if($row2->is_in_special=="Y" && $row3->is_in_special=="Y"){
						$this->featuredTable[$i]["product"]["prices"][$row3->name]["in_special"]=true;
						$this->featuredTable[$i]["product"]["prices"][$row3->name]["special_price"]=$row3->special_price;
					} else $this->featuredTable[$i]["product"]["prices"][$row3->name]["in_special"]=false;
				}
				//End of Prices
				//Get the brand
				if($row2->brand){
					$request3 = request(
					"SELECT name from $TBL_catalogue_brands WHERE id='".$row2->brand."'"
					,$insideLink
					);
					$brand = mysql_fetch_object($request3);
					$this->featuredTable[$i]["product"]["brand"]=$brand->name;
				}
			}
			if($row->id_catalogue_cat){
				$request2=request(
				"SELECT $TBL_catalogue_info_categories.name
	              FROM  $TBL_catalogue_info_categories
	              WHERE $TBL_catalogue_info_categories.id_catalogue_cat='".$row->id_catalogue_cat."'
	              AND $TBL_catalogue_info_categories.id_language='$idLanguage'"
				,$insideLink);
				$row2=mysql_fetch_object($request2);
				$this->featuredTable[$i]["category"]=array();
				$this->featuredTable[$i]["category"]["id"]=$row->id_catalogue_cat;
				$this->featuredTable[$i]["category"]["title"]=$row2->name;
			}
			$i++;
		}
		if(!$link) mysql_close($insideLink);
	}
} //Class
?>
