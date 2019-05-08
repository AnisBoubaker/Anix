<?php
  class page{
    //variables
    var $id=0;
    var $title = "";
    var $content = "";
    var $keywords = "";
    //constructors
    function page($idPage,$idLanguage,$link){
      global $TBL_content_info_pages,$TBL_content_pages;
      global $TBL_gen_languages;
      if($idPage==0 || $idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $request=request(
        "SELECT $TBL_content_info_pages.title,
                $TBL_content_info_pages.short_desc,
                $TBL_content_info_pages.content,
                $TBL_content_info_pages.keywords,
                $TBL_content_pages.id_category
          FROM  $TBL_content_info_pages,$TBL_content_pages
          WHERE $TBL_content_info_pages.id_page='$idPage'
          AND   $TBL_content_info_pages.id_language='$idLanguage'
          AND $TBL_content_pages.id = '$idPage'
          "
        ,$insideLink);
      if(!mysql_num_rows($request)){
        if(!$link) mysql_close($insideLink);
        return;
      }
      $this->id = $idPage;
      $row = mysql_fetch_object($request);
      $this->title = $row->title;
      $this->short_desc = $row->short_desc;
      $this->content = $row->content;
      $this->keywords = $row->keywords;
      $this->category = $row->id_category;
      if(!$link) mysql_close($insideLink);
    }
  } //Class
?>
