<?php
if($action=="search_faq"){
	$requestString ="SELECT DISTINCT id from $TBL_faq_faq,$TBL_faq_info_faq WHERE ";
	if($_POST["question"]!=""){
		$requestString.="question like '%".$_POST["question"]."%' ";
		$nb++;
	}
	if($_POST["category"]!=0){
		if($nb) $requestString.="and ";
		$requestString.="id_category='".$_POST["category"]."'";
		$nb++;
	}
	//No need to run the conditions if the user was clever enough to check all of them...
	if(isset($_POST["active"]) || isset($_POST["disactivated"]))
	if(!isset($_POST["active"]) || !isset($_POST["disactivated"])){
		$nb2=0;
		$currentDate = date('Y-m-d',time());
		if($nb) $requestString.=" and ";
		$requestString.="(";
		if(isset($_POST["active"])){
			$requestString.="active='Y'";
			$nb2++;
		}
		if(isset($_POST["disactivated"])){
			if($nb2) $requestString.=" or ";
			$requestString.="active='N'";
			$nb2++;
		}
		$requestString.=") ";
		$nb++;
	}
	if($_POST["keywords"]!=""){
		$keywordList = explode(" ", $_POST["keywords"]);
		foreach($keywordList as $keyword){
			if($nb) $requestString.="and ";
			$requestString.="response like '%".htmlentities($keyword)."%' ";
			$nb++;
		}
	}
	if($nb) $requestString.=" and ";
	$requestString.="$TBL_faq_info_faq.id_faq = $TBL_faq_faq.id";
	if($nb){
		$request1=request($requestString,$link);
		if(mysql_num_rows($request1)){
			$requestString ="SELECT $TBL_faq_faq.id,$TBL_faq_info_faq.question from $TBL_faq_faq,$TBL_faq_info_faq,$TBL_gen_languages WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request1)){
				if($nb2) $requestString.=",";
				else $requestString.="$TBL_faq_faq.id in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=") and ";
			$requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_faq_info_faq.id_faq = $TBL_faq_faq.id and $TBL_faq_info_faq.id_language=$TBL_gen_languages.id";
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
	$requestString ="SELECT DISTINCT id from $TBL_faq_categories,$TBL_faq_info_categories WHERE ";
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
	$requestString.="$TBL_faq_info_categories.id_faq_cat = $TBL_faq_categories.id";
	if($nb){
		$request1=request($requestString,$link);
		if(mysql_num_rows($request1)){
			$requestString ="SELECT $TBL_faq_categories.id,$TBL_faq_info_categories.name from $TBL_faq_categories,$TBL_faq_info_categories,$TBL_gen_languages WHERE ";
			$nb2=0;
			while($foundID=mysql_fetch_object($request1)){
				if($nb2) $requestString.=",";
				else $requestString.="$TBL_faq_categories.id in (";
				$requestString.=$foundID->id;
				$nb2++;
			}
			if($nb2) $requestString.=") and ";
			$requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_faq_info_categories.id_faq_cat = $TBL_faq_categories.id and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id";
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