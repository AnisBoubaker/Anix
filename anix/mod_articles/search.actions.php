<?php
  if($action=="search_article"){
    $requestString ="SELECT DISTINCT id from $TBL_articles_article,$TBL_articles_info_article WHERE ";
    if($_POST["short_desc"]!=""){
      $requestString.="short_desc like '%".$_POST["short_desc"]."%' ";
      $nb++;
    }
    if($_POST["category"]!=0){
      if($nb) $requestString.=" and ";
      $requestString.="id_category='".$_POST["category"]."'";
      $nb++;
    }
    //No need to run the conditions if the user was clever enough to check all of them...
    if(isset($_POST["active"]) || isset($_POST["disactivated"]) || isset($_POST["awaiting"]) || isset($_POST["expired"]) || isset($_POST["archived"]))
     if(!isset($_POST["active"]) || !isset($_POST["disactivated"]) || !isset($_POST["awaiting"]) || !isset($_POST["expired"]) || !isset($_POST["archived"])){
      $nb2=0;
      $currentDate = date('Y-m-d',time());
      if($nb) $requestString.=" and ";
      $requestString.="(";
      if(isset($_POST["active"])){
        $requestString.="active='Y' or (active='DATE' and from_date<='$currentDate' and to_date>='$currentDate')";
        $nb2++;
      }
      if(isset($_POST["awaiting"])){
        if($nb2) $requestString.=" or ";
        $requestString.="(active='DATE' and from_date>'$currentDate')";
        $nb2++;
      }
      if(isset($_POST["expired"])){
        if($nb2) $requestString.=" or ";
        $requestString.="(active='DATE' and to_date<'$currentDate')";
        $nb2++;
      }
      if(isset($_POST["archived"])){
        if($nb2) $requestString.=" or ";
        $requestString.="active='ARCHIVE'";
        $nb2++;
      }
      if(isset($_POST["disactivated"])){
        if($nb2) $requestString.=" or ";
        $requestString.="active='N'";
        $nb2++;
      }
      $requestString.=")";
      $nb++;
    }
    if(isset($_POST["home_page"])){
        if($nb) $requestString.=" and ";
        $requestString.="home_page='Y'";
        $nb++;
      }
    if($_POST["keywords"]!=""){
      $keywordList = explode(" ", $_POST["keywords"]);
      foreach($keywordList as $keyword){
        if($nb) $requestString.=" and ";
        $requestString.="response like '%".htmlentities($keyword)."%' ";
        $nb++;
      }
    }
    if($nb) $requestString.=" and ";
    $requestString.="$TBL_articles_info_article.id_article = $TBL_articles_article.id";
    if($nb){
      $request1=request($requestString,$link);
      if(mysql_num_rows($request1)){
        $requestString ="SELECT $TBL_articles_article.id,$TBL_articles_info_article.title from $TBL_articles_article,$TBL_articles_info_article,$TBL_gen_languages WHERE ";
        $nb2=0;
        while($foundID=mysql_fetch_object($request1)){
          if($nb2) $requestString.=",";
          else $requestString.="$TBL_articles_article.id in (";
          $requestString.=$foundID->id;
          $nb2++;
        }
        if($nb2) $requestString.=") and ";
        $requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_articles_info_article.id_article = $TBL_articles_article.id and $TBL_articles_info_article.id_language=$TBL_gen_languages.id";
        $request = request($requestString,$link);
        $nbResults = mysql_num_rows($request);
      } else {
        $nbResults=0;
      }
    } else {
      $errors++;
      $errMessage.=_("Vous n'avez pas spécifié de criteres de recherche");
    }
  }
?>
<?
  if($action=="search_category"){
    $requestString ="SELECT DISTINCT id from $TBL_articles_categories,$TBL_articles_info_categories WHERE ";
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
    $requestString.="$TBL_articles_info_categories.id_article_cat = $TBL_articles_categories.id";
    if($nb){
      $request1=request($requestString,$link);
      if(mysql_num_rows($request1)){
        $requestString ="SELECT $TBL_articles_categories.id,$TBL_articles_info_categories.name from $TBL_articles_categories,$TBL_articles_info_categories,$TBL_gen_languages WHERE ";
        $nb2=0;
        while($foundID=mysql_fetch_object($request1)){
          if($nb2) $requestString.=",";
          else $requestString.="$TBL_articles_categories.id in (";
          $requestString.=$foundID->id;
          $nb2++;
        }
        if($nb2) $requestString.=") and ";
        $requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_articles_info_categories.id_article_cat = $TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id";
        $request = request($requestString,$link);
        $nbResults = mysql_num_rows($request);
      } else {
        $nbResults=0;
      }
    } else {
      $errors++;
      $errMessage.=_("Vous n'avez pas spécifié de critères de recherche.");
    }
  }
?>