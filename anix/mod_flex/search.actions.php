<?php
if($action=="search_item"){
	if($_POST["keywords"]!=""){
		$tbl_List = "$TBL_lists_items,$TBL_lists_info_items,$TBL_lists_extrafields_values";
	} else {
		$tbl_List = "$TBL_lists_items,$TBL_lists_info_items";
	}
	$requestString ="SELECT DISTINCT id from $tbl_List WHERE ";
	if($_POST["name"]!=""){
		$requestString.="name like '%".$_POST["name"]."%' ";
		$nb++;
	}
	if($_POST["active"]!="0"){
		if($nb) $requestString.="and ";
		$requestString.="active='".$_POST["active"]."'";
		$nb++;
	}
	if($_POST["keywords"]!=""){
		$keywordList = explode(" ", $_POST["keywords"]);
		foreach($keywordList as $keyword){
			if($nb) $requestString.="and ";
			$requestString.="(description like '%".htmlentities($keyword)."%' ";
			$requestString.="or value like '%".htmlentities($keyword)."%') ";
			$nb++;
		}
	}
	if($nb) $requestString.="and ";
	$requestString.="$TBL_lists_info_items.id_item = $TBL_lists_items.id ";
	if ($_POST["keywords"]!="") $requestString.="and $TBL_lists_extrafields_values.id_item=$TBL_lists_items.id";
	if($nb){
		$request1=request($requestString,$link);
		if(mysql_num_rows($request1)){
			$requestString ="SELECT $TBL_lists_items.id,$TBL_lists_info_items.name from $TBL_lists_items,$TBL_lists_info_items,$TBL_gen_languages WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request1)){
				if($nb2) $requestString.=",";
				else $requestString.="$TBL_lists_items.id in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=") and ";
			$requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_lists_info_items.id_item = $TBL_lists_items.id and $TBL_lists_info_items.id_language=$TBL_gen_languages.id";
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
	$requestString ="SELECT DISTINCT id from $TBL_lists_categories,$TBL_lists_info_categories WHERE ";
	if($_POST["name"]!=""){
		$requestString.="name like '%".$_POST["name"]."%' ";
		$nb++;
	}
	if($_POST["keywords"]!=""){
		$keywordList = explode(" ", $_POST["keywords"]);
		foreach($keywordList as $keyword){
			if($nb) $requestString.="and ";
			$requestString.="description like '%".htmlentities($keyword)."%' ";
			$nb++;
		}
	}
	if($nb) $requestString.="and ";
	$requestString.="$TBL_lists_info_categories.id_lists_cat = $TBL_lists_categories.id";
	if($nb){
		$request1=request($requestString,$link);
		if(mysql_num_rows($request1)){
			$requestString ="SELECT $TBL_lists_categories.id,$TBL_lists_info_categories.name from $TBL_lists_categories,$TBL_lists_info_categories,$TBL_gen_languages WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request1)){
				if($nb2) $requestString.=",";
				else $requestString.="$TBL_lists_categories.id in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=") and ";
			$requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_lists_info_categories.id_lists_cat = $TBL_lists_categories.id and $TBL_lists_info_categories.id_language=$TBL_gen_languages.id";
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