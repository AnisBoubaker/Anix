<?php
if($action=="search_product"){
	if($_REQUEST["keywords"]!=""){
		$tbl_List = "$TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_extrafields_values";
	} else {
		$tbl_List = "$TBL_catalogue_products,$TBL_catalogue_info_products";
	}
	//filter on product state
	$request = request("SELECT `id` FROM `$TBL_catalogue_state`",$link);
	$stateFilter = false;
	$stateRequest="";
	while($productState = mysql_fetch_object($request)){
		if(isset($_REQUEST["state_".$productState->id])){
			$stateFilter=true;
			$stateRequest.="OR `$TBL_catalogue_product_state`.`id_state`='$productState->id' ";
		}
	}
	if($stateFilter){
		$tbl_List.=",$TBL_catalogue_product_state";
		$nb++;
	}
	$requestString ="SELECT DISTINCT id from $tbl_List WHERE 1 ";
	if($stateFilter){
		$requestString.="AND ";
		$requestString.="`catalogue_product_state`.`id_product` = `catalogue_products`.`id` AND (0 ";
		$requestString.=$stateRequest;
		$requestString.=")";
	}
	if($_REQUEST["name"]!=""){
		$requestString.="and ";
		$requestString.="name like '%".$_REQUEST["name"]."%' ";
		$nb++;
	}
	if($_REQUEST["ref"]!=""){
		$requestString.="and ";
		$requestString.="ref_store like '%".$_REQUEST["ref"]."%' ";
		$nb++;
	}
	if($_REQUEST["upc_code"]!=""){
		$requestString.="and ";
		$requestString.="`upc_code` like '%".$_REQUEST["upc_code"]."%' ";
		$nb++;
	}
	if($_REQUEST["category"]!=0){
		$requestString.="and ";
		$requestString.="id_category='".$_REQUEST["category"]."'";
		$nb++;
	}
	if($_REQUEST["brand"]!=0){
		$requestString.="and ";
		$requestString.="brand='".$_REQUEST["brand"]."'";
		$nb++;
	}
	if($_REQUEST["product_type"]!="0"){
		$requestString.="and ";
		$requestString.="product_type='".$_REQUEST["product_type"]."'";
		$nb++;
	}
	if($_REQUEST["active"]!="0"){
		$requestString.="and ";
		$requestString.="active='".$_REQUEST["active"]."'";
		$nb++;
	}
	if($_REQUEST["stock_min"]!=""){
		$requestString.="and ";
		$requestString.="`stock`>='".$_REQUEST["stock_min"]."'";
		$nb++;
	}
	if($_REQUEST["stock_max"]!=""){
		$requestString.="and ";
		$requestString.="`stock`>='".$_REQUEST["stock_max"]."'";
		$nb++;
	}
	if($_REQUEST["restocking_delay"]!="0"){
		$requestString.="and ";
		$requestString.="`restocking_delay`='".$_REQUEST["restocking_delay"]."'";
		$nb++;
	}
	if($_REQUEST["id_supplier"]!="0"){
		$requestString.="and (";
		$requestString.="`id_supplier1`='".$_REQUEST["id_supplier"]."' OR ";
		$requestString.="`id_supplier2`='".$_REQUEST["id_supplier"]."' OR ";
		$requestString.="`id_supplier3`='".$_REQUEST["id_supplier"]."' OR ";
		$requestString.="`id_supplier4`='".$_REQUEST["id_supplier"]."'";
		$requestString.=")";
		$nb++;
	}
	if(isset($_REQUEST["is_in_special"])){
		$requestString.="and ";
		$requestString.="is_in_special='Y'";
		$nb++;
	}
	if($_REQUEST["keywords"]!=""){
		$keywordList = explode(" ", $_REQUEST["keywords"]);
		foreach($keywordList as $keyword){
			$requestString.="and ";
			$requestString.="(description like '%".htmlentities($keyword)."%' ";
			$requestString.="or value like '%".htmlentities($keyword)."%') ";
			$nb++;
		}
	}
	$requestString.="and ";
	$requestString.="$TBL_catalogue_info_products.id_product = $TBL_catalogue_products.id ";
	if ($_REQUEST["keywords"]!="") $requestString.="and $TBL_catalogue_extrafields_values.id_product=$TBL_catalogue_products.id";
	if($nb){
		$request1=request($requestString,$link);
		if(mysql_num_rows($request1)){
			$requestString ="SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.ref_store,$TBL_catalogue_products.image_file_orig,$TBL_catalogue_info_products.name from $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request1)){
				if($nb2) $requestString.=",";
				else $requestString.="$TBL_catalogue_products.id in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=") and ";
			$requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_products.id_product = $TBL_catalogue_products.id and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id";
			$request = request($requestString,$link);
			$nbResults = mysql_num_rows($request);
		} else {
			$nbResults=0;
		}
	} else {
		$errors++;
		$errMessage.=_("Vous n'avez pas spécifié de critères de recherche.")."<br />";
	}
}
?>
<?
if($action=="search_category"){
	$requestString ="SELECT DISTINCT id from $TBL_catalogue_categories,$TBL_catalogue_info_categories WHERE ";
	if($_REQUEST["name"]!=""){
		$requestString.="name like '%".$_REQUEST["name"]."%' ";
		$nb++;
	}
	if($_REQUEST["keywords"]!=""){
		$keywordList = explode(" ", $_REQUEST["keywords"]);
		foreach($keywordList as $keyword){
			if($nb) $requestString.="and ";
			$requestString.="description like '%".htmlentities($keyword)."%' ";
			$nb++;
		}
	}
	if($nb) $requestString.="and ";
	$requestString.="$TBL_catalogue_info_categories.id_catalogue_cat = $TBL_catalogue_categories.id";
	if($nb){
		$request1=request($requestString,$link);
		if(mysql_num_rows($request1)){
			$requestString ="SELECT $TBL_catalogue_categories.id,$TBL_catalogue_info_categories.name from $TBL_catalogue_categories,$TBL_catalogue_info_categories,$TBL_gen_languages WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request1)){
				if($nb2) $requestString.=",";
				else $requestString.="$TBL_catalogue_categories.id in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=") and ";
			$requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_categories.id_catalogue_cat = $TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id";
			$request = request($requestString,$link);
			$nbResults = mysql_num_rows($request);
		} else {
			$nbResults=0;
		}
	} else {
		$errors++;
		$errMessage.=_("Vous n'avez pas spécifié de critères de recherche.")."<br />";
	}
}
?>