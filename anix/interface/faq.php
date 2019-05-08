<?php
  // Requires class productCat and product
  class faq{
    //variables
    var $id=0;
    var $language=0;
    var $idCat = 0;
    var $question="";
    var $response = "";
    //constructors
    function faq($idFaq,$idLanguage,$link){
      global $TBL_faq_faq,$TBL_faq_info_faq;
      global $TBL_gen_languages;
      if($idFaq==0 || $idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $currentDate = date('Y-m-d',time());
      $request=request(
        "SELECT $TBL_faq_faq.id,
                $TBL_faq_faq.id_category,
                $TBL_faq_info_faq.question,
                $TBL_faq_info_faq.response
          FROM  $TBL_faq_faq,$TBL_faq_info_faq
          WHERE $TBL_faq_faq.id='$idFaq'
          AND   $TBL_faq_faq.active='Y'
          AND   $TBL_faq_info_faq.id_language='$idLanguage'
          AND   $TBL_faq_info_faq.id_faq=$TBL_faq_faq.id"
        ,$insideLink);
      if(!mysql_num_rows($request)){
        if(!$link) mysql_close($insideLink);
        return;
      }
      $row=mysql_fetch_object($request);
      $this->id=$idFaq;
      $this->idCat = $row->id_category;
      $this->language = $idLanguage;
      $this->question = $row->question;
      $this->response = $row->response;
      if(!$link) mysql_close($insideLink);
    }
  } //Class
?>
