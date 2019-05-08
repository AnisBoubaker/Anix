<?php
  if($action=="search_page"){
    $requestString ="SELECT DISTINCT id_page id from $TBL_content_info_pages WHERE ";
    if($_POST["title"]!=""){
      $requestString.="title like '%".$_POST["title"]."%' ";
      $nb++;
    }
    if($_POST["content"]!=""){
      $requestString.="content like '%".$_POST["content"]."%' ";
      $nb++;
    }
    if($_POST["keywords"]!=""){
      $keywordList = explode(" ", $_POST["keywords"]);
      foreach($keywordList as $keyword){
        if($nb) $requestString.=" and ";
        $requestString.="keywords like '%".htmlentities($keyword)."%' ";
        $nb++;
      }
    }
    if($nb){
      $request1=request($requestString,$link);
      if(mysql_num_rows($request1)){
        $requestString ="SELECT $TBL_content_info_pages.id_page id,$TBL_content_info_pages.title from $TBL_content_info_pages,$TBL_gen_languages WHERE ";
        $nb2=0;
        while($foundID=mysql_fetch_object($request1)){
          if($nb2) $requestString.=",";
          else $requestString.="$TBL_content_info_pages.id_page in (";
          $requestString.=$foundID->id;
          $nb2++;
        }
        if($nb2) $requestString.=") and ";
        $requestString.="$TBL_gen_languages.id='$used_language_id' and $TBL_content_info_pages.id_language=$TBL_gen_languages.id";
        $request = request($requestString,$link);
        $nbResults = mysql_num_rows($request);
      } else {
        $nbResults=0;
      }
    } else {
      $errors++;
      $errMessage.=_("Vous n'avez pas specifie de criteres de recherche.")."<br>";//$str1;
    }
  }
?>