<?php
  // Requires class productCat and product
  class newsList{
    //variables
    var $newsTable=array();
    var $idLanguage;
    var $idCat;
    var $categoryName;
    //constructors
    function newsList($idCat,$idLanguage,$link){
      global $TBL_news_info_categories;
      $this->idLanguage= $idLanguage;
      $this->idCat= $idCat;
      if($idCat){
        if(!$link) $insideLink = dbConnect();
        else $insideLink=$link;
        $request=request(
          "SELECT $TBL_news_info_categories.name
           FROM $TBL_news_info_categories
           WHERE $TBL_news_info_categories.id_news_cat='$idCat'
           AND $TBL_news_info_categories.id_language='$idLanguage'"
          ,$insideLink
        );
        if(!mysql_num_rows($request)){
          $this->idCat=0;
        } else {
          $tmp = mysql_fetch_object($request);
          $this->categoryName = $tmp->name;
        }
        if(!$link) mysql_close($insideLink);    
      }
    }
    function getAllActive($link){
      global $TBL_news_news,$TBL_news_info_news,$TBL_news_categories;
      global $TBL_gen_languages;
      $idLanguage=$this->idLanguage;
      if($idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $currentDate = date('Y-m-d',time());
      $request=request(
        "SELECT $TBL_news_news.id,
                $TBL_news_info_news.date,
                $TBL_news_info_news.short_desc,
                $TBL_news_info_news.details
          FROM  $TBL_news_news,$TBL_news_info_news,$TBL_news_categories
          WHERE (
                $TBL_news_news.active='Y'
                OR
                ($TBL_news_news.active='DATE' AND $TBL_news_news.from_date<='$currentDate' AND $TBL_news_news.to_date>='$currentDate')
                )
          AND   $TBL_news_info_news.id_language='$idLanguage'
          AND   $TBL_news_info_news.id_news=$TBL_news_news.id
          AND   $TBL_news_categories.id = $TBL_news_news.id_category
          ORDER BY $TBL_news_info_news.date DESC"
        ,$insideLink);
        //ORDER BY $TBL_news_categories.ordering,$TBL_news_news.ordering
      if(!mysql_num_rows($request)){
        mysql_close($insideLink);
        return null;
      }
      $this->newsTable=array();$i=0;
      while($row=mysql_fetch_object($request)){
        $this->newsTable[$i]=array();
        $this->newsTable[$i]["id"]=$row->id;
        $this->newsTable[$i]["date"]=$row->date;
        $this->newsTable[$i]["short_desc"]=$row->short_desc;
        $this->newsTable[$i]["more_details"]=($row->details!="");
        $i++;
      }
      if(!$link) mysql_close($insideLink);
      return $this->newsTable;
    }
    function getActive($link){
      global $TBL_news_news,$TBL_news_info_news,$TBL_news_categories;
      global $TBL_gen_languages;
      $idLanguage=$this->idLanguage;
      if($idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $currentDate = date('Y-m-d',time());
      $request=request(
        "SELECT $TBL_news_news.id,
                $TBL_news_info_news.date,
                $TBL_news_info_news.short_desc,
                $TBL_news_info_news.details
          FROM  $TBL_news_news,$TBL_news_info_news,$TBL_news_categories
          WHERE (
                $TBL_news_news.active='Y'
                OR
                ($TBL_news_news.active='DATE' AND $TBL_news_news.from_date<='$currentDate' AND $TBL_news_news.to_date>='$currentDate')
                )
          AND   $TBL_news_news.id_category='".$this->idCat."'
          AND   $TBL_news_info_news.id_language='$idLanguage'
          AND   $TBL_news_info_news.id_news=$TBL_news_news.id
          AND   $TBL_news_categories.id = $TBL_news_news.id_category
          ORDER BY $TBL_news_info_news.date DESC"
        ,$insideLink);
        //ORDER BY $TBL_news_categories.ordering,$TBL_news_news.ordering
      if(!mysql_num_rows($request)){
        mysql_close($insideLink);
        return null;
      }
      $this->newsTable=array();$i=0;
      while($row=mysql_fetch_object($request)){
        $this->newsTable[$i]=array();
        $this->newsTable[$i]["id"]=$row->id;
        $this->newsTable[$i]["date"]=$row->date;
        $this->newsTable[$i]["short_desc"]=$row->short_desc;
        $this->newsTable[$i]["more_details"]=($row->details!="");
        $i++;
      }
      if(!$link) mysql_close($insideLink);
      return $this->newsTable;
    }
    function getArchived($link){
      global $TBL_news_news,$TBL_news_info_news,$TBL_news_categories;
      global $TBL_gen_languages;
      $idLanguage=$this->idLanguage;
      if($idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $currentDate = date('Y-m-d',time());
      $request=request(
        "SELECT $TBL_news_news.id,
                $TBL_news_info_news.date,
                $TBL_news_info_news.short_desc,
                $TBL_news_info_news.details
          FROM  $TBL_news_news,$TBL_news_info_news,$TBL_news_categories
          WHERE $TBL_news_news.active='ARCHIVE'
          AND   $TBL_news_news.id_category='".$this->idCat."'
          AND   $TBL_news_info_news.id_language='$idLanguage'
          AND   $TBL_news_info_news.id_news=$TBL_news_news.id
          AND   $TBL_news_categories.id = $TBL_news_news.id_category
          ORDER BY $TBL_news_info_news.date DESC"
        ,$insideLink);
        //ORDER BY $TBL_news_categories.ordering,$TBL_news_news.ordering
      if(!mysql_num_rows($request)){
        mysql_close($insideLink);
        $this->newsTable=array();
        return null;
      }
      $this->newsTable=array();$i=0;
      while($row=mysql_fetch_object($request)){
        $this->newsTable[$i]=array();
        $this->newsTable[$i]["id"]=$row->id;
        $this->newsTable[$i]["date"]=$row->date;
        $this->newsTable[$i]["short_desc"]=$row->short_desc;
        $this->newsTable[$i]["more_details"]=($row->details!="");
        $i++;
      }
      if(!$link) mysql_close($insideLink);
      return $this->newsTable;
    }
    
    function getSubcategoriesList($idCat,$link){
      global $TBL_news_news,$TBL_news_info_news,$TBL_news_categories,$TBL_news_info_categories;
      global $TBL_gen_languages;
      $idLanguage=$this->idLanguage;
      if($idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $request=request(
        "SELECT $TBL_news_categories.id,
                $TBL_news_info_categories.name
          FROM  $TBL_news_categories,$TBL_news_info_categories
          WHERE $TBL_news_categories.id_parent='$idCat'
          AND   $TBL_news_info_categories.id_language='$idLanguage'
          AND   $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id
          ORDER BY $TBL_news_categories.ordering"
        ,$insideLink);
        
      if(!mysql_num_rows($request)){
        mysql_close($insideLink);
        return null;
      }
      $catTable=array();$i=0;
      while($row=mysql_fetch_object($request)){
        $catTable[$i]=array();
        $catTable[$i]["id"]=$row->id;
        $catTable[$i]["name"]=$row->name;
        $i++;
      }
      if(!$link) mysql_close($insideLink);
      return $catTable;
    }
  } //Class
?>
