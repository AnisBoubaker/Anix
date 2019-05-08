<?php
// Requires class productCat and product
class search{
	//variables
	var $resultTable=array();
	var $productSubcategories=array();
	var $nbResults = 0;
	var $query = "";
	var $highestScore = 0;
	//constructors
	function search($query,$idLanguage){
		$this->query = $query;
		$this->idLanguage = $idLanguage;
	}

	function searchProduct($idCategory,$brand,$extrafields,$link,$offset=0,$limit=0,$idGroup=0){
		global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands,$TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_categories,$TBL_catalogue_info_categories,$TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection,$TBL_catalogue_info_extracategorysection;
		global $TBL_gen_languages,$TBL_catalogue_extrafields_values;
		$query = htmlentities($this->query,ENT_QUOTES,"UTF-8");
		$idLanguage = $this->idLanguage;
		if($idLanguage==0 || ($query=="" && !$idCategory && !$brand)) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$this->highestScore=0;
		//get the subcategories table
		$this->productSubcategories = array();
		$this->getProductSubcategories($idCategory,$insideLink);

		//Requests
		/*$words = explode(" ",$query);
		$query2 = "";
		foreach($words as $word){
			if($word!="") $query2.="+%$word% ";
		}*/

		//$query2 = "+".str_replace(" "," +",$query);
		$query2=$query;
		//if($extrafields) $from_extrafields=",$TBL_catalogue_extrafields_values"; else $from_extrafields="";
		$requestString ="SELECT DISTINCT $TBL_catalogue_products.id, ";
		if($query!="")
		$requestString.="				   MATCH($TBL_catalogue_info_categories.name,$TBL_catalogue_info_categories.description,$TBL_catalogue_info_categories.alias_name) AGAINST ('$query2' IN BOOLEAN MODE) +
	      								   MATCH($TBL_catalogue_info_products.name,$TBL_catalogue_info_products.description,$TBL_catalogue_info_products.alias_name) AGAINST ('$query2' IN BOOLEAN MODE)*4 +
	      								   MATCH($TBL_catalogue_brands.name) AGAINST ('$query' IN BOOLEAN MODE)*2 AS score,";
		$requestString.="				   $TBL_catalogue_info_products.name,
                                       $TBL_catalogue_info_categories.id_catalogue_cat id_category,
                                       $TBL_catalogue_info_categories.name category,
                                       $TBL_catalogue_products.ref_store,
                                       $TBL_catalogue_products.image_file_icon,
                                       $TBL_catalogue_products.image_file_small,
                                       $TBL_catalogue_products.image_file_large,
                                       $TBL_catalogue_products.public_price,
                                       $TBL_catalogue_products.public_special,
                                       $TBL_catalogue_products.special_price,
                                       $TBL_catalogue_products.is_in_special,
                                       $TBL_catalogue_brands.name as brand,
                                       $TBL_catalogue_brands.image_file_small as brand_image
                       FROM ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_categories,$TBL_catalogue_info_categories)
                       LEFT JOIN $TBL_catalogue_brands on ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id)
                       WHERE ";
		$nb2=1;
		$requestString.="$TBL_catalogue_products.`active`='Y'";
		$requestString.="and $TBL_catalogue_categories.id in ($idCategory";
		foreach($this->productSubcategories as $subcat){
			if($nb2) $requestString.=",";
			//else $requestString.="$TBL_catalogue_categories.id in (";
			$requestString.=$subcat["id"];
			$nb2++;
		}
		$requestString.=") and ";
		if($brand!=0) $requestString.="$TBL_catalogue_brands.id='$brand' AND ";
		$nb=0;
		if($query!=""){
			$requestString.="(";
			$requestString.="MATCH($TBL_catalogue_info_categories.name,$TBL_catalogue_info_categories.description,$TBL_catalogue_info_categories.alias_name) AGAINST ('$query2' IN BOOLEAN MODE) ";
			$requestString.="OR MATCH($TBL_catalogue_info_products.name,$TBL_catalogue_info_products.description,$TBL_catalogue_info_products.alias_name) AGAINST ('$query2' IN BOOLEAN MODE) ";
			$requestString.="OR MATCH($TBL_catalogue_brands.name) AGAINST ('$query' IN BOOLEAN MODE) ";
			$requestString.="OR $TBL_catalogue_products.ref_store LIKE '%$query%' ";
			$requestString.="OR $TBL_catalogue_products.upc_code LIKE '%$query%' ";
			$requestString.=") AND ";
		}
		/*if($extrafields){
		$requestString.="AND $TBL_catalogue_extrafields_values.id_product=$TBL_catalogue_products.id ";
		$first=true;
		$extrafieldIds="("; $extrafieldValues="(";
		foreach($extrafields as $extrafieldId => $extrafield){
		if(!$first) {
		$extrafieldIds.=",";
		$extrafieldValues.=",";
		}
		$extrafieldIds.=$extrafieldId;
		$extrafieldValues.="'".$extrafield."'";
		$first=false;
		}
		$extrafieldIds.=")"; $extrafieldValues.=")";
		$requestString.="AND $TBL_catalogue_extrafields_values.id_extrafield IN $extrafieldIds ";
		$requestString.="AND $TBL_catalogue_extrafields_values.value IN $extrafieldValues ";
		}*/
		$requestString.="$TBL_catalogue_info_products.id_language='".$this->idLanguage."'
	                     AND $TBL_catalogue_info_categories.id_language='".$this->idLanguage."'
	    				 AND $TBL_catalogue_info_products.id_product = $TBL_catalogue_products.id
	                     AND $TBL_catalogue_products.id_category=$TBL_catalogue_categories.id
	                     AND $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id ";
		$requestString.="ORDER BY ";
		if($query!="")
		$requestString.="MATCH($TBL_catalogue_info_categories.name,$TBL_catalogue_info_categories.description,$TBL_catalogue_info_categories.alias_name) AGAINST ('$query2' IN BOOLEAN MODE) +
	      								   MATCH($TBL_catalogue_info_products.name,$TBL_catalogue_info_products.description,$TBL_catalogue_info_products.alias_name) AGAINST ('$query2' IN BOOLEAN MODE)*4 +
	      								   MATCH($TBL_catalogue_brands.name) AGAINST ('$query' IN BOOLEAN MODE)*2
	      							  DESC,";
		$requestString.="    $TBL_catalogue_categories.ordering,$TBL_catalogue_products.ordering ";
		$request = request($requestString,$insideLink);
		$this->nbResults = mysql_numrows($request);
		if($limit){
			$requestString.="LIMIT $offset,$limit ";
			$request = request($requestString,$insideLink);
		}
		$scores = array();
		while($row=mysql_fetch_object($request)){
			$this->resultTable[$row->id]=array();
			$this->resultTable[$row->id]["id"]=$row->id;
			$this->resultTable[$row->id]["name"]=$row->name;
			$this->resultTable[$row->id]["brand"]=$row->brand;
			$this->resultTable[$row->id]["brand_image"]=$row->brand_image;
			$this->resultTable[$row->id]["ref"]=$row->ref_store;
			$this->resultTable[$row->id]["id_category"]=$row->id_category;
			$this->resultTable[$row->id]["category"]=$row->category;
			$this->resultTable[$row->id]["image_icon"]=$row->image_file_icon;
			$this->resultTable[$row->id]["image_small"]=$row->image_file_small;
			$this->resultTable[$row->id]["image_large"]=$row->image_file_large;
			$this->resultTable[$row->id]["prices"]=array();
			$this->resultTable[$row->id]["prices"]["public"]=array();
			$this->resultTable[$row->id]["prices"]["public"]["name"]= "public";
			$this->resultTable[$row->id]["prices"]["public"]["price"]= $row->public_price;
			if($query!="") $this->resultTable[$row->id]["score"]=$row->score;
			else $this->resultTable[$row->id]["score"]=0;
			if($row->is_in_special=="Y" && $row->public_special){
				$this->resultTable[$row->id]["prices"]["public"]["in_special"]=true;
				$this->resultTable[$row->id]["prices"]["public"]["special_price"]=$row->special_price;
			} else $this->resultTable[$row->id]["prices"]["public"]["in_special"]=false;
		}
		//cleanup results depending on extrafields
		if($extrafields && count($this->resultTable)){
			$idsTable = array();
			foreach($this->resultTable as $result) {
				$idsTable[$result["id"]]=0;
			}
			$counter = 1;
			foreach($extrafields as $extrafieldId => $extrafieldValue) if($extrafieldValue){
				$ids="("; $first = true;
				//get the product IDs
				foreach($idsTable as $id => $result) {
					if(!$first) $ids.=",";
					$ids.="$id";
					$first=false;
				} $ids.=")";
				$requestString="SELECT $TBL_catalogue_products.id FROM $TBL_catalogue_products,$TBL_catalogue_extrafields_values
      						WHERE $TBL_catalogue_products.id IN $ids
      						AND $TBL_catalogue_extrafields_values.id_product=$TBL_catalogue_products.id
      						AND $TBL_catalogue_extrafields_values.id_extrafield='$extrafieldId'
      						AND $TBL_catalogue_extrafields_values.value='$extrafieldValue'";
				$request2 = request($requestString,$insideLink);
				while($cleanProduct = mysql_fetch_object($request2)){
					if(isset($idsTable[$cleanProduct->id])) $idsTable[$cleanProduct->id]++;
				}
				$counter++;
			}
			//Delete the ids that do not satisfy all the extrafields
			$counter--;
			foreach($idsTable as $id=>$value){
				if($value!=$counter) {
					unset($this->resultTable[$id]);
					$this->nbResults--;
				}

			}
		}
		if(!$link) mysql_close($insideLink);
	}

	function searchProductProduct($idCategory,$link){
		global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands,$TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_categories,$TBL_catalogue_info_categories,$TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection,$TBL_catalogue_info_extracategorysection;
		global $TBL_gen_languages;
		$query = $this->query;
		$idLanguage = $this->idLanguage;
		if($query=="" || $idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$this->highestScore=0;
		//get the subcategories table
		$this->productSubcategories = array();
		$this->resultTable = array();
		$this->getProductSubcategories($idCategory,$insideLink);
		//Requests
		$requestString ="SELECT $TBL_catalogue_products.id, COUNT(*) as score
                       FROM $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_categories,$TBL_catalogue_info_categories
                       LEFT JOIN $TBL_catalogue_brands on ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id)
                       LEFT JOIN $TBL_catalogue_extrafields on ($TBL_catalogue_extrafields.id_cat = $TBL_catalogue_categories.id)
                       LEFT JOIN $TBL_catalogue_info_extrafields on ($TBL_catalogue_info_extrafields.id_extrafield = $TBL_catalogue_extrafields.id)
                       LEFT JOIN $TBL_catalogue_extrafields_values on ($TBL_catalogue_extrafields_values.id_extrafield = $TBL_catalogue_extrafields.id)
                       LEFT JOIN $TBL_catalogue_extracategorysection on ($TBL_catalogue_extracategorysection.id_cat = $TBL_catalogue_categories.id)
                       LEFT JOIN $TBL_catalogue_info_extracategorysection on ($TBL_catalogue_info_extracategorysection.id_extrasection = $TBL_catalogue_extracategorysection.id)
                       WHERE ";
		$nb2=0;
		foreach($this->productSubcategories as $subcat){
			if($nb2) $requestString.=",";
			else $requestString.="$TBL_catalogue_categories.id in (";
			$requestString.=$subcat["id"];
			$nb2++;
		}
		if($nb2) $requestString.=") and ";
		$nb=0;
		//$query=htmlentities($query,ENT_QUOTES);
		if($query!=""){
			$keywordList = explode(" ", $query);
			foreach($keywordList as $keyword){
				if($nb) $requestString.="and ";
				$requestString.="($TBL_catalogue_info_products.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_products.description like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_brands.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_extrafields.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_extrafields_values.value like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_categories.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_categories.description like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_extracategorysection.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_extracategorysection.value like '%".htmlentities($keyword)."%' ";
				$keyword=htmlentities($keyword,ENT_QUOTES);
				$requestString.="or $TBL_catalogue_info_products.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_products.description like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_brands.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_extrafields.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_extrafields_values.value like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_categories.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_categories.description like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_extracategorysection.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_catalogue_info_extracategorysection.value like '%".htmlentities($keyword)."%') ";
				$nb++;
			}
			$requestString.="AND $TBL_catalogue_info_products.id_product = $TBL_catalogue_products.id
                         AND $TBL_catalogue_products.id_category=$TBL_catalogue_categories.id
                         AND $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id
                         GROUP BY $TBL_catalogue_products.id
                         ORDER BY score DESC";
		}
		//echo $requestString;
		$request = request($requestString,$insideLink);
		$scores = array();
		if(mysql_num_rows($request)){
			$requestString = "SELECT $TBL_catalogue_products.id,$TBL_catalogue_info_products.name,$TBL_catalogue_products.id_category,$TBL_catalogue_info_categories.name as category,$TBL_catalogue_brands.name as brand
                          FROM $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_info_categories
                          LEFT JOIN $TBL_catalogue_brands on ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id)
                          WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request)){
				$scores[$foundID->id]=$foundID->score;
				if($foundID->score > $this->highestScore) $this->highestScore=$foundID->score;
				if($nb2) $requestString.=",";
				else $requestString.="$TBL_catalogue_products.id in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=") and ";
			$requestString.="$TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
                         AND $TBL_catalogue_products.id_category=$TBL_catalogue_info_categories.id_catalogue_cat";
			//echo "<br><br>".$requestString;
			$request2=request($requestString,$insideLink);
			$this->nbResults=0;
			$this->resultTable=array();
			while($row=mysql_fetch_object($request2)){
				$this->nbResults++;
				$this->resultTable[$row->id]=array();
				$this->resultTable[$row->id]["id"]=$row->id;
				$this->resultTable[$row->id]["name"]=$row->name;
				$this->resultTable[$row->id]["score"]=$scores[$row->id];
				$this->resultTable[$row->id]["brand"]=$row->brand;
				$this->resultTable[$row->id]["id_category"]=$row->id_category;
				$this->resultTable[$row->id]["category"]=$row->category;
			}
			function sortByScore2($a,$b){
				return($b["score"]-$a["score"]);
			}
			usort($this->resultTable,"sortByScore2");
		}
		if(!$link) mysql_close($insideLink);
	}

	function searchNews($link){
		global $TBL_news_news,$TBL_news_info_news,$TBL_news_categories,$TBL_news_info_categories;
		global $TBL_gen_languages;
		$query = $this->query;
		$idLanguage = $this->idLanguage;
		if($query=="" || $idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$this->highestScore=0;
		$this->resultTable = array();
		//Requests
		$requestString ="SELECT $TBL_news_news.id, COUNT(*) as score
                       FROM $TBL_news_news,$TBL_news_info_news,$TBL_news_info_categories
                       WHERE ";
		$nb=0;
		if($query!=""){
			$keywordList = explode(" ", $query);
			foreach($keywordList as $keyword){
				if($nb) $requestString.="and ";
				$requestString.="($TBL_news_info_news.date like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_news_info_news.short_desc like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_news_info_news.details like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_news_info_categories.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_news_info_categories.description like '%".htmlentities($keyword)."%' ";
				$keyword=htmlentities($keyword,ENT_QUOTES);
				$requestString.="or $TBL_news_info_news.date like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_news_info_news.short_desc like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_news_info_news.details like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_news_info_categories.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_news_info_categories.description like '%".htmlentities($keyword)."%') ";
				$nb++;
			}
			$requestString.="AND $TBL_news_info_news.id_news = $TBL_news_news.id
                         AND $TBL_news_news.id_category=$TBL_news_info_categories.id_news_cat
                         GROUP BY $TBL_news_news.id
                         ORDER BY score DESC";
		}
		$request = request($requestString,$insideLink);
		$scores = array();
		if(mysql_num_rows($request)){
			$requestString = "SELECT $TBL_news_info_news.id_news,$TBL_news_info_news.date,$TBL_news_info_news.short_desc,$TBL_news_info_news.details
                          FROM $TBL_news_info_news
                          WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request)){
				$scores[$foundID->id]=$foundID->score;
				if($foundID->score > $this->highestScore) $this->highestScore=$foundID->score;
				if($nb2) $requestString.=",";
				else $requestString.="id_news in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=")";
			$request2=request($requestString,$insideLink);
			$this->nbResults=0;
			$this->resultTable=array();
			while($row=mysql_fetch_object($request2)){
				$this->nbResults++;
				$this->resultTable[$row->id_news]=array();
				$this->resultTable[$row->id_news]["id"]=$row->id_news;
				$this->resultTable[$row->id_news]["date"]=$row->date;
				$this->resultTable[$row->id_news]["desc"]=$row->short_desc;
				if($row->details!="") $this->resultTable[$row->id_news]["more_details"]=true; else $this->resultTable[$row->id_news]["more_details"]=false;
				$this->resultTable[$row->id_news]["score"]=$scores[$row->id_news];
			}
			function sortByScore3($a,$b){
				//return 0;
				return($b["score"]-$a["score"]);
			}
			usort($this->resultTable,"sortByScore3");
		}
		if(!$link) mysql_close($insideLink);
	}

	function searchFaq($link){
		global $TBL_faq_faq,$TBL_faq_info_faq,$TBL_faq_categories,$TBL_faq_info_categories;
		global $TBL_gen_languages;
		$query = $this->query;
		$idLanguage = $this->idLanguage;
		if($query=="" || $idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$this->highestScore=0;
		$this->resultTable = array();
		//Requests
		$requestString ="SELECT $TBL_faq_faq.id, COUNT(*) as score
                       FROM $TBL_faq_faq,$TBL_faq_info_faq,$TBL_faq_info_categories
                       WHERE ";
		$nb=0;
		if($query!=""){
			$keywordList = explode(" ", $query);
			foreach($keywordList as $keyword){
				if($nb) $requestString.="and ";
				$requestString.="($TBL_faq_info_faq.question like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_faq_info_faq.response like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_faq_info_categories.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_faq_info_categories.description like '%".htmlentities($keyword)."%' ";
				$keyword=htmlentities($keyword,ENT_QUOTES);
				$requestString.="or $TBL_faq_info_faq.question like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_faq_info_faq.response like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_faq_info_categories.name like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_faq_info_categories.description like '%".htmlentities($keyword)."%') ";
				$nb++;
			}
			$requestString.="AND $TBL_faq_info_faq.id_faq = $TBL_faq_faq.id
                         AND $TBL_faq_faq.id_category=$TBL_faq_info_categories.id_faq_cat
                         GROUP BY $TBL_faq_faq.id
                         ORDER BY score DESC";
		}
		$request = request($requestString,$insideLink);
		$scores = array();
		if(mysql_num_rows($request)){
			$requestString = "SELECT $TBL_faq_info_faq.id_faq,$TBL_faq_info_faq.question
                          FROM $TBL_faq_info_faq
                          WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request)){
				$scores[$foundID->id]=$foundID->score;
				if($foundID->score > $this->highestScore) $this->highestScore=$foundID->score;
				if($nb2) $requestString.=",";
				else $requestString.="id_faq in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=")";
			$request2=request($requestString,$insideLink);
			$this->nbResults=0;
			$this->resultTable=array();
			while($row=mysql_fetch_object($request2)){
				$this->nbResults++;
				$this->resultTable[$row->id_faq]=array();
				$this->resultTable[$row->id_faq]["id"]=$row->id_faq;
				$this->resultTable[$row->id_faq]["date"]=$row->date;
				$this->resultTable[$row->id_faq]["desc"]=$row->short_desc;
				if($row->details!="") $this->resultTable[$row->id_faq]["more_details"]=true; else $this->resultTable[$row->id_faq]["more_details"]=false;
				$this->resultTable[$row->id_faq]["score"]=$scores[$row->id_faq];
			}
			function sortByScore3($a,$b){
				//return 0;
				return($b["score"]-$a["score"]);
			}
			usort($this->resultTable,"sortByScore3");
		}
		if(!$link) mysql_close($insideLink);
	}

	function searchPage($link){
		global $TBL_content_pages,$TBL_content_info_pages;
		global $TBL_gen_languages;
		$query = $this->query;
		$idLanguage = $this->idLanguage;
		if($query=="" || $idLanguage==0) return;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		$this->highestScore=0;
		$this->resultTable = array();
		//Requests
		$requestString ="SELECT $TBL_content_info_pages.id_page, COUNT(*) as score
                       FROM $TBL_content_info_pages,$TBL_content_pages
                       WHERE ";
		$nb=0;
		if($query!=""){
			$keywordList = explode(" ", $query);
			foreach($keywordList as $keyword){
				if($nb) $requestString.="and ";
				$requestString.="($TBL_content_info_pages.title like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_content_info_pages.content like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_content_info_pages.keywords like '%".htmlentities($keyword)."%' ";
				$keyword=htmlentities($keyword,ENT_QUOTES);
				$requestString.="or $TBL_content_info_pages.title like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_content_info_pages.content like '%".htmlentities($keyword)."%' ";
				$requestString.="or $TBL_content_info_pages.keywords like '%".htmlentities($keyword)."%') ";
				$nb++;
			}
			$requestString.="AND $TBL_content_info_pages.id_page=$TBL_content_pages.id
                         GROUP BY $TBL_content_info_pages.id_page
                         ORDER BY score DESC";
		}
		$request = request($requestString,$insideLink);
		$scores = array();
		if(mysql_num_rows($request)){
			$requestString = "SELECT $TBL_content_info_pages.id_page,$TBL_content_info_pages.title,$TBL_content_pages.id_category
                          FROM $TBL_content_pages,$TBL_content_info_pages
                          WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request)){
				$scores[$foundID->id_page]=$foundID->score;
				if($foundID->score > $this->highestScore) $this->highestScore=$foundID->score;
				if($nb2) $requestString.=",";
				else $requestString.="id_page in (";
				$requestString.=$foundID->id_page;
				$nb2++;
			}
			if($nb2) $requestString.=") AND $TBL_content_info_pages.id_page=$TBL_content_pages.id AND $TBL_content_info_pages.id_language='".$this->idLanguage."'";
			$request2=request($requestString,$insideLink);
			$this->nbResults=0;
			$this->resultTable=array();
			while($row=mysql_fetch_object($request2)){
				$this->nbResults++;
				$this->resultTable[$row->id_page]=array();
				$this->resultTable[$row->id_page]["id"]=$row->id_page;
				$this->resultTable[$row->id_page]["title"]=$row->title;
				$this->resultTable[$row->id_page]["id_category"]=$row->id_category;
				$this->resultTable[$row->id_page]["score"]=$scores[$row->id_page];
			}
			function sortByScore4($a,$b){
				//return 0;
				return($b["score"]-$a["score"]);
			}
			usort($this->resultTable,"sortByScore4");
		}
		if(!$link) mysql_close($insideLink);
	}

	function getProductSubcategories($idCategory,$link){
		global $TBL_catalogue_categories,$TBL_catalogue_info_categories;
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;
		if(!$this->productSubcategories) $this->productSubcategories=array();
		$request=request(
		"SELECT $TBL_catalogue_info_categories.name,
                $TBL_catalogue_categories.id,
                $TBL_catalogue_categories.image_file_large,
                $TBL_catalogue_categories.image_file_small
          FROM  $TBL_catalogue_categories,$TBL_catalogue_info_categories
          WHERE $TBL_catalogue_categories.id_parent='$idCategory'
          AND   $TBL_catalogue_info_categories.id_language='".$this->idLanguage."'
          AND   $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id
          ORDER BY $TBL_catalogue_categories.ordering"
		,$insideLink);
		if(!mysql_num_rows($request)){
			if(!$link) mysql_close($insideLink);
			return;
		}
		while($row = mysql_fetch_object($request)){
			$this->productSubcategories[$row->id]=array();
			$this->productSubcategories[$row->id]["id"] = $row->id;
			$this->getProductSubcategories($row->id,$insideLink);
		}
		if(!$link) mysql_close($insideLink);
		return;
	}

	function getCatTableList(){
		global $used_language_id,$TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories;
		$link = dbConnect();
		$result=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
		$catalogueCat=array();
		$updateSubcatsPool = array();
		$lastParent=0;$lastInserted=0;$first=true;
		while($row=mysql_fetch_object($result)){
			if(!$first && $lastParent==$row->id_parent) $catalogueCat[$lastInserted]["last"]=false;
			$catalogueCat[$row->id]=array();
			$catalogueCat[$row->id]["id"]=$row->id;
			$catalogueCat[$row->id]["subcats"]=array();
			$catalogueCat[$row->id]["ordering"]=$row->ordering;
			$catalogueCat[$row->id]["name"]=$row->name;
			$catalogueCat[$row->id]["description"]=$row->description;
			$catalogueCat[$row->id]["id_parent"]=$row->id_parent;
			$catalogueCat[$row->id]["contain_products"]=$row->contain_products;
			$catalogueCat[$row->id]["last"]=true;
			if($first || $lastParent!=$row->id_parent) $catalogueCat[$row->id]["first"]=true;
			else $catalogueCat[$row->id]["first"]=false;
			if($row->id_parent!=0) {
				//Declare the child to his parent
				if(isset($catalogueCat[$row->id_parent])) $catalogueCat[$row->id_parent]["subcats"][$row->id]=$row->id;
				else {
					//if the parent have not been processed yet, put the declaration in updaye pool; declaration will be made later on
					$updateSubcatsPool[$row->id]=array();
					$updateSubcatsPool[$row->id]["child"]=$row->id;
					$updateSubcatsPool[$row->id]["parent"]=$row->id_parent;
				}
			}
			$lastInserted=$row->id;
			$lastParent=$row->id_parent;
			$first=false;
		}
		foreach($updateSubcatsPool as $toUpdate){
			$catalogueCat[$toUpdate["parent"]]["subcats"][$toUpdate["child"]]=$toUpdate["child"];
		}
		mysql_close($link);
		return $catalogueCat;
	}

	function showCategoriesForSelect($table,$idParent,$level,$selected){
		$returnStr="";
		foreach($table as $row) if($row["id_parent"]==$idParent){
			$returnStr.="<option value='".$row["id"]."'";
			if($row["id"]==$selected) $returnStr.=" selected='selected'";
			$returnStr.="'>";
			if($level==0) $returnStr.="<b>";
			for($i=0;$i<$level;$i++){
				$returnStr.="&nbsp;&nbsp;";
			}
			$returnStr.=$row["name"];
			if($level==0) $returnStr.="</b>";
			$returnStr.="</option>";
			if(count($row["subcats"])) $returnStr.=$this->showCategoriesForSelect($table,$row["id"],$level+1,$selected);
		}
		return $returnStr;
	}
	function getBrandsList(){
		global $TBL_catalogue_brands;
		$link=dbConnect();
		$result=array();
		$request = request("SELECT id,name FROM `$TBL_catalogue_brands` ORDER BY `name`",$link);
		while($brand = mysql_fetch_object($request)){
			$result[] = array("id"=>$brand->id,"name"=>$brand->name);
		}
		return $result;
	}

	function getParentsPathIds($idFirstParent,$link){
		global $TBL_catalogue_categories;
		global $TBL_catalogue_info_categories;
		global $TBL_catalogue_info_products;
		global $TBL_catalogue_products;
		global $TBL_gen_languages;
		$ret=array();
		if(!$idFirstParent) return $ret;
		$category=request("select $TBL_catalogue_categories.id,$TBL_catalogue_categories.id_parent from  $TBL_catalogue_categories where $TBL_catalogue_categories.id='$idFirstParent'", $link);
		$row = mysql_fetch_object($category);
		$ret[$row->id] = $row->id;
		while($row->id_parent!=0){
			$category=request("select $TBL_catalogue_categories.id,$TBL_catalogue_categories.id_parent from  $TBL_catalogue_categories where $TBL_catalogue_categories.id='".$row->id_parent."'", $link);
			$row = mysql_fetch_object($category);
			$ret[$row->id]=$row->id;
		}
		return $ret;
	}

	function getCategoryExtrafields($idCat){
		global $used_language_id,$TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafieldselection,$TBL_catalogue_info_extrafieldselection;
		$return = array();
		/*
		$link = dbConnect();
		if(!$idCat) return $return;
		$parentCategories = $this->getParentsPathIds($idCat,$link);
		$requestString="select * from $TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields where datatype='selection' and (";
		$first=true;
		foreach($parentCategories as $cat){
		if(!$first) $requestString.=" OR ";
		$requestString.="id_cat='$cat'";
		$first = false;
		}
		$requestString.=") and id_language='$used_language_id' and id_extrafield=id order by id_cat,ordering";
		$extra_fields=request($requestString,$link);
		while($field=mysql_fetch_object($extra_fields)){
		$return[$field->id]=array();
		$return[$field->id]["id"]=$field->id;
		$return[$field->id]["name"]=$field->name;
		$requestString="SELECT * from `$TBL_catalogue_extrafieldselection`,`$TBL_catalogue_info_extrafieldselection`
		WHERE `id_extrafield`='$field->id'
		AND `$TBL_catalogue_info_extrafieldselection`.`id_extrafieldselection`=`$TBL_catalogue_extrafieldselection`.`id`
		AND `$TBL_catalogue_info_extrafieldselection`.`id_language`='$used_language_id'
		ORDER BY `$TBL_catalogue_extrafieldselection`.`ordering`";
		$selection_options = request($requestString,$link);
		if(mysql_num_rows($selection_options)){
		$return[$field->id]["options"]=array();
		while($selection_option = mysql_fetch_object($selection_options)){
		$return[$field->id]["options"][$selection_option->id]=array();
		$return[$field->id]["options"][$selection_option->id]["id"]=$selection_option->id;
		$return[$field->id]["options"][$selection_option->id]["value"]=$selection_option->value;
		}
		}
		}
		mysql_close($link);*/
		return $return;
	}

} //Class
?>
