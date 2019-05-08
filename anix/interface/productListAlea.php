<?php
  // Requires class productCat and product
  class productListAlea{
    //variables
    var $idCategory=0;
    var $idGroup=0;
    var $productTable=array();
    //constructors
    function productListAlea($idCategory,$idLanguage,$limit,$link){
      global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands;
      global $TBL_catalogue_attachments,$TBL_gen_languages,$TBL_catalogue_extrafields_values,$TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields;
      if($idCategory==0 || $idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $request=request(
        "SELECT $TBL_catalogue_info_products.name,
                $TBL_catalogue_products.id,
                $TBL_catalogue_products.brand,
                $TBL_catalogue_products.ref_store,
                $TBL_catalogue_products.public_price,
                $TBL_catalogue_products.is_in_special,
                $TBL_catalogue_products.public_special,
                $TBL_catalogue_products.special_price,
                $TBL_catalogue_products.image_file_large,
                $TBL_catalogue_products.image_file_small,
                $TBL_catalogue_info_products.description
          FROM  $TBL_catalogue_products,$TBL_catalogue_info_products
          WHERE $TBL_catalogue_products.id_category='$idCategory'
          AND   $TBL_catalogue_products.active='y'
          AND   $TBL_catalogue_info_products.id_language='$idLanguage'
          AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
          ORDER BY RAND()
          LIMIT $limit"
        ,$insideLink);
      if(!mysql_num_rows($request)){
        if(!$link) mysql_close($insideLink);
        return;
      }
      $this->idCategory = $idCategory;
      $this->language = $idLanguage;
      
      $this->productTable=array();
      while($row=mysql_fetch_object($request)){
        $this->productTable[$row->id]=array();
        $this->productTable[$row->id]["id"]=$row->id;
        $this->productTable[$row->id]["title"]=$row->name;
        $this->productTable[$row->id]["ref"]=$row->ref_store;
        $this->productTable[$row->id]["image_large"]=$row->image_file_large;
        $this->productTable[$row->id]["image_small"]=$row->image_file_small;
        $this->productTable[$row->id]["more_details"]=($row->description!="");
        $this->productTable[$row->id]["prices"]=array();
        $this->productTable[$row->id]["prices"]["public"]=array();
        $this->productTable[$row->id]["prices"]["public"]["name"]= "public";
        $this->productTable[$row->id]["prices"]["public"]["price"]= $row->public_price;
        if($row->is_in_special=="Y" && $row->public_special){
          $this->productTable[$row->id]["prices"]["public"]["in_special"]=true;
          $this->productTable[$row->id]["prices"]["public"]["special_price"]=$row->special_price;
        } else $this->productTable[$row->id]["prices"]["public"]["in_special"]=false;
        //Get the brand
        if($row->brand){
          $request2 = request(
            "SELECT name,image_file_small,URL from $TBL_catalogue_brands WHERE id='".$row->brand."'"
            ,$insideLink
          );
          $brand = mysql_fetch_object($request2);
          $this->productTable[$row->id]["brand"]=$brand->name;
          $this->productTable[$row->id]["brand_image"]=$brand->image_file_small;
          $this->productTable[$row->id]["brand_URL"]=$brand->URL;
        }
        //get the attachments
        $request2 = request(
                  "SELECT $TBL_catalogue_attachments.id,
                          $TBL_catalogue_attachments.file_name,
                          $TBL_catalogue_attachments.title,
                          $TBL_catalogue_attachments.description,
                          $TBL_gen_languages.name
                   FROM   $TBL_catalogue_attachments,$TBL_gen_languages
                   WHERE  $TBL_catalogue_attachments.id_product='".$row->id."'
                   AND    $TBL_gen_languages.id=$TBL_catalogue_attachments.id_language"
                ,$insideLink);
        $this->productTable[$row->id]["attachments"]=array();
        while($row2=mysql_fetch_object($request2)){
            $this->productTable[$row->id]["attachments"][$row2->id]=array();
            $this->productTable[$row->id]["attachments"][$row2->id]["title"]=$row2->title;
            $this->productTable[$row->id]["attachments"][$row2->id]["description"]= $row2->description;
            $this->productTable[$row->id]["attachments"][$row2->id]["file_name"]= $row2->file_name;
        }
        //get the extrafields
        $request2 = request(
                  "SELECT $TBL_catalogue_extrafields_values.id_extrafield id,
                          $TBL_catalogue_extrafields_values.value,
                          $TBL_catalogue_info_extrafields.name
                   FROM   $TBL_catalogue_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafields
                   WHERE  $TBL_catalogue_extrafields_values.id_product='".$row->id."'
                   AND    $TBL_catalogue_extrafields_values.id_language='".$this->language."'
                   AND    $TBL_catalogue_info_extrafields.id_language='".$this->language."'
                   AND    $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields_values.id_extrafield
                   AND    $TBL_catalogue_info_extrafields.id_extrafield=$TBL_catalogue_extrafields.id
                   ORDER BY $TBL_catalogue_extrafields.ordering"
                ,$insideLink);
        $this->productTable[$row->id]["extrafields"]=array();
        while($row2=mysql_fetch_object($request2)){
            $this->productTable[$row->id]["extrafields"][$row2->id]=array();
            $this->productTable[$row->id]["extrafields"][$row2->id]["id"]=$row2->id;
            $this->productTable[$row->id]["extrafields"][$row2->id]["name"]=$row2->name;
            $this->productTable[$row->id]["extrafields"][$row2->id]["value"]= $row2->value;
        }
      }
      if(!$link) mysql_close($insideLink);
      return $this->productTable;
    }
  } //Class
?>
