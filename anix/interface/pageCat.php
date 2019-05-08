<?php
     class pageCat{
     //variables
     var $idCat=0;
     var $pagesTable = array();
     //constructors
     function pageCat($idCat,$idLanguage,$link){
        global $TBL_content_pages,$TBL_content_info_pages;
        global $TBL_gen_languages;
        if($idCat==0 || $idLanguage==0) return;
        if(!$link) $insideLink = dbConnect();
        else $insideLink=$link;
        $this->idCat = $idCat;
        $request=request(
            "SELECT $TBL_content_pages.id,$TBL_content_info_pages.title
            FROM  $TBL_content_pages,$TBL_content_info_pages
            WHERE $TBL_content_pages.id_category='$idCat'
            AND     $TBL_content_info_pages.id_page = $TBL_content_pages.id
            AND     $TBL_content_info_pages.id_language='$idLanguage'
            ORDER BY $TBL_content_pages.ordering
            "
            ,$insideLink);
        if(!mysql_num_rows($request)){
            if(!$link) mysql_close($insideLink);
            return;
        }
        $this->pagesTable = array();
        while($row = mysql_fetch_object($request)){
            $this->pagesTable[$row->id] = $row->title;
        }
        
        if(!$link) mysql_close($insideLink);
    }
} //Class
?>
