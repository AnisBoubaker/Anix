<?php
class faqList{
    //attributes
    var $idCat=0;
    var $subcats;
    var $faqs;
    var $parentsPath;
    
    function faqList($idCat, $idLanguage, $link){
        global $TBL_faq_faq,$TBL_faq_info_faq,$TBL_faq_categories,$TBL_faq_info_categories;
        global $TBL_gen_languages;
        if($idCat==0 || $idLanguage==0) return;
        if(!$link) $insideLink = dbConnect();
        else $insideLink=$link;
        //Verify if the category id exists
        $request = request("SELECT id from $TBL_faq_categories where id='$idCat'",$insideLink);
        if(!mysql_num_rows($request)){
            if(!$link) mysql_close($insideLink);
            return;
        } else $this->idCat = $idCat;
        //Get the category faqs
        $request = request("
            SELECT $TBL_faq_faq.id, $TBL_faq_info_faq.question, $TBL_faq_info_faq.response
            FROM $TBL_faq_faq,$TBL_faq_info_faq
            WHERE $TBL_faq_faq.id_category = '$idCat'
            AND $TBL_faq_faq.active='Y'
            AND $TBL_faq_info_faq.id_faq = $TBL_faq_faq.id
            AND $TBL_faq_info_faq.id_language = '$idLanguage'
            ORDER BY $TBL_faq_faq.ordering
        ",$insideLink);
        $this->faqs = array();
        while($faq = mysql_fetch_object($request)){
            $this->faqs[$faq->id] = array();
            $this->faqs[$faq->id]["question"] = $faq->question;
            $this->faqs[$faq->id]["response"] = $faq->response;
        } 
        $this->getSubcategoriesList($idCat,false,$idLanguage,$insideLink);
        $this->getParentsPath($idLanguage,$insideLink);
        if(!$link) mysql_close($insideLink);
        //return $this->subcats;
    }

    function getSubcategoriesList($idCat, $includeSubCats, $idLanguage, $link){
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
      $this->subcats=array();$i=0;
      while($row=mysql_fetch_object($request)){
        $this->subcats[$i]=array();
        $this->subcats[$i]["id"]=$row->id;
        $this->subcats[$i]["name"]=$row->name;
        $i++;
      }
      $tmp = array();
      if($includeSubCats){
        foreach($this->subcats as $category){
            $tmp1 = getFaqSubcategoriesList($category["id"],$includeSubCats, $idLanguage, $insideLink);
            if($tmp1!==false)  $tmp = array_merge($tmp, $tmp1);
        }
        $this->subcats = array_merge($this->subcats,$tmp);
      }
      if(!$link) mysql_close($insideLink);
      return $this->subcats;
    }
    //private function
    function getParentsPath($idLanguage,$link){
        global $TBL_faq_faq,$TBL_faq_info_faq,$TBL_faq_categories,$TBL_faq_info_categories;
        global $TBL_gen_languages;
        $currentId = $this->idCat;
        if(!$link) $insideLink = dbConnect();
        else $insideLink=$link;
        $counter = 0;
        $this->parentsPath=array();
        while($currentId){
            $request = request("
                SELECT $TBL_faq_categories.id,$TBL_faq_categories.id_parent,$TBL_faq_info_categories.name 
                FROM $TBL_faq_categories,$TBL_faq_info_categories
                WHERE $TBL_faq_categories.id = '$currentId'
                AND $TBL_faq_info_categories.id_faq_cat = $TBL_faq_categories.id
                AND $TBL_faq_info_categories.id_language = '$idLanguage'
            ",$link);
            $catShortDesc = array();
            if(mysql_num_rows($request)){
               $cat = mysql_fetch_object($request); 
               $catShortDesc["id"] = $cat->id;
               $catShortDesc["name"] = $cat->name;
               $catShortDesc["id_parent"] = $cat->id_parent;
            }
            $this->parentsPath[$counter]=$catShortDesc;
            $currentId=$catShortDesc["id_parent"];
            $counter++;
        }
        $this->parentsPath=array_reverse($this->parentsPath);
        if(!$link) mysql_close($insideLink);
        return $this->parentsPath;
    }
    function isPermitted($idParentCat){
        foreach($this->parentsPath as $parent){
            if($parent["id"]==$idParentCat) return true;
        }
        return false;
    }
}
?>