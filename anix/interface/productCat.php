<?php
class productCat{
	//variables
	var $id=0;
	var $language = 0;
	var $title = "";
	var $id_parent = 0;
	var $id_menu = 0;
	var $description = "";
	var $image_large = "";
	var $image_small = "";
	var $extraSections = array();
	var $parentsPath = array();
	var $subcategories = array();
	var $productCatLinks = array();
	var $productLinks = array();
	var $htmlTitle = "";
	var $htmlDescription = "";
	//constructors
	function productCat($idCategory,$idLanguage,$link){
		global $TBL_catalogue_categories,$TBL_catalogue_info_categories;
		if($idCategory==0 || $idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$request=request(
		"SELECT $TBL_catalogue_info_categories.name,
                $TBL_catalogue_info_categories.description,
                $TBL_catalogue_categories.image_file_large,
                $TBL_catalogue_categories.image_file_small,
                $TBL_catalogue_categories.id_parent,
                $TBL_catalogue_info_categories.htmltitle,
                $TBL_catalogue_info_categories.htmldescription,
                $TBL_catalogue_categories.id_menu
          FROM  $TBL_catalogue_categories,$TBL_catalogue_info_categories
          WHERE $TBL_catalogue_categories.id='$idCategory'
          AND   $TBL_catalogue_info_categories.id_language='$idLanguage'
          AND   $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return;
		}
		$this->id = $idCategory;
		$this->language = $idLanguage;
		$row = mysql_fetch_object($request);
		$this->title = $row->name;
		$this->description = $row->description;
		$this->image_large = $row->image_file_large;
		$this->image_small = $row->image_file_small;
		$this->id_parent =$row->id_parent;
		$this->id_menu = $row->id_menu;
		if(!$link) mysql_close($insideLink);
	}

	function getParentsPath(){
		$currentId = $this->id_parent;
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

	/**
     * Retrieve the list of subcategories of the current category
     * If id=0 (or not specified), it'll use the current category ID
     * If recursive is true, it'll retrieve subcategories recursively
     *
     * @param int $link
     * @param int $id
     * @param boolean $recursive
     * @return array
     */
	function getSubcategories($link,$recursive=false,$id=0){
		global $TBL_catalogue_categories,$TBL_catalogue_info_categories;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		if($id==0) $this->subcategories=array();
		if($id==0) $id=$this->id;
		$request=request(
		"SELECT $TBL_catalogue_info_categories.name,
                $TBL_catalogue_categories.id,
                $TBL_catalogue_categories.image_file_large,
                $TBL_catalogue_categories.image_file_small,
                $TBL_catalogue_categories.hide_products
          FROM  $TBL_catalogue_categories,$TBL_catalogue_info_categories
          WHERE $TBL_catalogue_categories.id_parent='".$id."'
          AND   $TBL_catalogue_info_categories.id_language='".$this->language."'
          AND   $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id
          ORDER BY $TBL_catalogue_categories.ordering"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return $this->subcategories;
		}
		while($row = mysql_fetch_object($request)){
			$this->subcategories[$row->id]=array();
			$this->subcategories[$row->id]["title"] = $row->name;
			$this->subcategories[$row->id]["id"] = $row->id;
			$this->subcategories[$row->id]["image_large"] = $row->image_file_large;
			$this->subcategories[$row->id]["image_small"] = $row->image_file_small;
			$this->subcategories[$row->id]["hide_products"] = $row->hide_products;
			$this->subcategories[$row->id]["parent"] = $id;
			if($recursive && $row->hide_products=='N') $this->getSubcategories($insideLink,true,$row->id);
		}
		if(!$link) mysql_close($insideLink);
		return $this->subcategories;
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
          AND	$TBL_links_link.from_module='2'
          AND	$TBL_links_link.from_item='$this->id'
          AND 	$TBL_links_link.to_module='2'
          AND	$TBL_catalogue_categories.id=$TBL_links_link.to_item
          AND   $TBL_catalogue_info_categories.id_language='".$this->language."'
          AND   $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id
          ORDER BY $TBL_links_link.ordering"
		,$insideLink);

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
		global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_links_link,$TBL_catalogue_brands,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices;

		if(!$dbLink) $insideLink = dbConnect();
		else $insideLink=$dbLink;
		$request=request(
		"SELECT $TBL_catalogue_info_products.name,
                $TBL_catalogue_products.id,
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
		        $TBL_catalogue_brands.image_file_small as brand_image
          FROM  ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_links_link)
		  LEFT JOIN `$TBL_catalogue_brands` ON (`$TBL_catalogue_brands`.`id`=`$TBL_catalogue_products`.`brand`)
		  LEFT JOIN `$TBL_catalogue_product_prices` ON (`$TBL_catalogue_product_prices`.`id_price_group`='$idGroup' AND `$TBL_catalogue_product_prices`.`id_product`=`$TBL_catalogue_products`.`id`)
		  WHERE $TBL_links_link.id_category = '$linkCat'
          AND	$TBL_links_link.from_module='2'
          AND	$TBL_links_link.from_item='$this->id'
          AND 	$TBL_links_link.to_module='1'
          AND   $TBL_catalogue_products.id=$TBL_links_link.to_item
          AND 	$TBL_catalogue_products.`active`='Y'
          AND   $TBL_catalogue_info_products.id_language='".$this->language."'
          AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
          ORDER BY $TBL_links_link.ordering"
		,$insideLink);

		while($row = mysql_fetch_object($request)){
			$this->productLinks[$row->id]=array();
			$this->productLinks[$row->id]["title"] = $row->name;
			$this->productLinks[$row->id]["id"] = $row->id;
			$this->productLinks[$row->id]["image_large"] = $row->image_file_large;
			$this->productLinks[$row->id]["image_small"] = $row->image_file_small;
			$this->productLinks[$row->id]["image_icon"] = $row->image_file_icon;
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

	// Internal function
	function getExtraSectionsList($idLanguage,$link){
		global $TBL_catalogue_extracategorysection, $TBL_catalogue_info_extracategorysection ;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$idCat = $this->id;
		$request=request(
		"SELECT $TBL_catalogue_extracategorysection.id,
                $TBL_catalogue_info_extracategorysection.name,
		$TBL_catalogue_info_extracategorysection.value
          FROM  $TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection
          WHERE $TBL_catalogue_extracategorysection.id_cat='$idCat'
          AND   $TBL_catalogue_info_extracategorysection.id_language='$idLanguage'
          AND   $TBL_catalogue_info_extracategorysection.id_extrasection=$TBL_catalogue_extracategorysection.id
          ORDER BY $TBL_catalogue_extracategorysection.ordering"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return $this->extraSections;
		}
		$i=0;
		while($row = mysql_fetch_object($request)){
			$this->extraSections[$i]["id"]=$row->id;
			$this->extraSections[$i]["name"]=$row->name;
			$this->extraSections[$i]["value"]=$row->value;
			$i++;
		}
		if(!$link) mysql_close($insideLink);
		return $this->extraSections;
	}

	function getExtraSection($idSection,$idLanguage,$link){
		global $TBL_catalogue_extracategorysection, $TBL_catalogue_info_extracategorysection ;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$idCat = $this->id;
		$request=request(
		"SELECT $TBL_catalogue_info_extracategorysection.name,
                $TBL_catalogue_info_extracategorysection.value
          FROM  $TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection
          WHERE $TBL_catalogue_extracategorysection.id='$idSection'
          AND   $TBL_catalogue_extracategorysection.id_cat='$idCat'
          AND   $TBL_catalogue_info_extracategorysection.id_language='$idLanguage'
          AND   $TBL_catalogue_info_extracategorysection.id_extrasection=$TBL_catalogue_extracategorysection.id"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return null;
		}
		$row = mysql_fetch_object($request);
		$section = array();
		$section["name"]=$row->name;
		$section["desc"]=$row->value;
		if(!$link) mysql_close($insideLink);
		return $section;
	}

	// Internal function
	function getAttachments($link){
		global $TBL_catalogue_attachments;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$request=request(
		"SELECT $TBL_catalogue_attachments.title,
                $TBL_catalogue_attachments.file_name
          FROM  $TBL_catalogue_attachments
          WHERE $TBL_catalogue_attachments.id_category='".$this->id."'
          ORDER BY $TBL_catalogue_attachments.ordering"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return null;
		}
		$i=0;
		$returnTable=array();
		while($row = mysql_fetch_object($request)){
			$returnTable[$i] = array();
			$returnTable[$i]["title"]=$row->title;
			$returnTable[$i]["file_name"]=$row->file_name;
			$i++;
		}
		if(!$link) mysql_close($insideLink);
		return $returnTable;
	}

	// Internal function
	function getBrands($link){
		global $TBL_catalogue_products,$TBL_catalogue_brands;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$request=request(
		"SELECT DISTINCT $TBL_catalogue_brands.name,
                $TBL_catalogue_brands.image_file_large,
                $TBL_catalogue_brands.image_file_small,
                $TBL_catalogue_brands.URL
          FROM  $TBL_catalogue_products,$TBL_catalogue_brands
          WHERE $TBL_catalogue_products.id_category='".$this->id."'
          AND   $TBL_catalogue_products.brand=$TBL_catalogue_brands.id
          ORDER BY $TBL_catalogue_brands.name"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return null;
		}
		$i=0;
		$returnTable=array();
		while($row = mysql_fetch_object($request)){
			$returnTable[$i] = array();
			$returnTable[$i]["name"]=$row->name;
			$returnTable[$i]["image_large"]=$row->image_file_large;
			$returnTable[$i]["image_small"]=$row->image_file_small;
			$returnTable[$i]["URL"]=$row->URL;
			$i++;
		}
		if(!$link) mysql_close($insideLink);
		return $returnTable;
	}

	function getHTMLTitle(){
		if($this->htmlTitle!="") return $this->htmlTitle;
		return $this->title;
	}
	function getHTMLDescription(){
		if($this->htmlDescription!="") return $this->htmlDescription;
		return "";
	}
} //Class
?>
