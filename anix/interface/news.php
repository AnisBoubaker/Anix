<?php
  // Requires class productCat and product
  class news{
    //variables
    var $id=0;
    var $language=0;
    var $date="";
    var $short_desc="";
    var $description = "";
    //constructors
    function news($idNews,$idLanguage,$link){
      global $TBL_news_news,$TBL_news_info_news;
      global $TBL_gen_languages;
      if($idNews==0 || $idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $currentDate = date('Y-m-d',time());
      $request=request(
        "SELECT $TBL_news_news.id,
                $TBL_news_news.id_category,
                $TBL_news_info_news.date,
                $TBL_news_info_news.short_desc,
                $TBL_news_info_news.details
          FROM  $TBL_news_news,$TBL_news_info_news
          WHERE $TBL_news_news.id='$idNews'
          AND   (
                $TBL_news_news.active='Y'
                OR
                ($TBL_news_news.active='DATE' AND $TBL_news_news.from_date<='$currentDate' AND $TBL_news_news.to_date>='$currentDate')
                )
          AND   $TBL_news_info_news.id_language='$idLanguage'
          AND   $TBL_news_info_news.id_news=$TBL_news_news.id"
        ,$insideLink);
      if(!mysql_num_rows($request)){
        if(!$link) mysql_close($insideLink);
        return;
      }
      $row=mysql_fetch_object($request);
      $this->id=$idNews;
      $this->idCat = $row->id_category;
      $this->language = $idLanguage;
      $this->date = $row->date;
      $this->short_desc = $row->short_desc;
      $this->description = $row->details;
      if(!$link) mysql_close($insideLink);
    }
  } //Class
?>
