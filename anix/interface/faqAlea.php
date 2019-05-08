<?php
    function getFaqAlea($idCat, $idLanguage, $includeSubCats, $limit, $link){
        global $TBL_faq_faq,$TBL_faq_info_faq,$TBL_faq_categories,$TBL_faq_info_categories;
        global $TBL_gen_languages;
        $return = array();
        if($idCat){
            if(!$link) $insideLink = dbConnect();
            else $insideLink=$link;
            $categories = getFaqSubcategoriesList($idCat,$includeSubCats,$idLanguage,$insideLink);
            $categoriesList = "$idCat";
            if($categories!==false) foreach($categories as $category){
                $categoriesList.=",".$category["id"];
            }
            $request=request(
                "SELECT $TBL_faq_faq.id, $TBL_faq_info_faq.question,$TBL_faq_info_faq.response
                FROM $TBL_faq_info_faq,$TBL_faq_faq,$TBL_faq_categories
                WHERE $TBL_faq_categories.id in ($categoriesList)
                AND $TBL_faq_faq.id_category = $TBL_faq_categories.id
                AND $TBL_faq_info_faq.id_faq = $TBL_faq_faq.id
                AND $TBL_faq_info_faq.id_language='$idLanguage'
                ORDER BY RAND()
                LIMIT $limit"
                ,$insideLink
            );
            while($faq = mysql_fetch_object($request)){
                $return[]=array("id"=>$faq->id,"question"=>$faq->question,"response"=>$faq->response);
            }
            if(!$link) mysql_close($insideLink);
            return $return;
        }
    }

    function getFaqSubcategoriesList($idCat, $includeSubCats, $idLanguage, $link){
      global $TBL_faq_faq,$TBL_faq_info_faq,$TBL_faq_categories,$TBL_faq_info_categories;
      global $TBL_gen_languages;
      if($idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $request=request(
        "SELECT $TBL_faq_categories.id,
                $TBL_faq_info_categories.name
          FROM  $TBL_faq_categories,$TBL_faq_info_categories
          WHERE $TBL_faq_categories.id_parent='$idCat'
          AND   $TBL_faq_info_categories.id_language='$idLanguage'
          AND   $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id
          ORDER BY $TBL_faq_categories.ordering"
        ,$insideLink);
      if(!mysql_num_rows($request)){
        return false;
      }
      $catTable=array();$i=0;
      while($row=mysql_fetch_object($request)){
        $catTable[$i]=array();
        $catTable[$i]["id"]=$row->id;
        $catTable[$i]["name"]=$row->name;
        $i++;
      }
      $tmp = array();
      if($includeSubCats){
        foreach($catTable as $category){
            $tmp1 = getFaqSubcategoriesList($category["id"],$includeSubCats, $idLanguage, $insideLink);
            if($tmp1!==false)  $tmp = array_merge($tmp, $tmp1);
        }
        $catTable = array_merge($catTable,$tmp);
      }
      if(!$link) mysql_close($insideLink);
      return $catTable;
    }
?>