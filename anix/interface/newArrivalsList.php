<?php
  // Requires class productCat and product
  class newArrivalsList{
    //variables
    var $idCategory=0;
    var $idGroup=0;
    var $productTable=array();
    //constructors
    function newArrivalsList($idCategory,$idLanguage,$idGroup,$link){
      global $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_brands,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafieldselection,$TBL_catalogue_extrafields,$TBL_catalogue_extrafieldselection;
      if($idLanguage==0) return;
      if(!$link) $insideLink = dbConnect();
      else $insideLink=$link;
      $requestString="SELECT $TBL_catalogue_info_products.name,
                $TBL_catalogue_products.id,
                $TBL_catalogue_products.brand,
                $TBL_catalogue_products.ref_store,
                $TBL_catalogue_products.public_price,
                $TBL_catalogue_products.is_in_special,
                $TBL_catalogue_products.public_special,
                $TBL_catalogue_products.special_price,
                $TBL_catalogue_products.state,
                $TBL_catalogue_products.image_file_large,
                $TBL_catalogue_products.image_file_small
          FROM  $TBL_catalogue_products,$TBL_catalogue_info_products
          WHERE $TBL_catalogue_products.new_product='Y'
          AND   $TBL_catalogue_products.active='Y'";
      if($idCategory) $requestString.=" AND $TBL_catalogue_products.id_category='$idCategory' ";
      $requestString.="AND   $TBL_catalogue_products.active='Y'
          AND   $TBL_catalogue_info_products.id_language='$idLanguage'
          AND   $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id
          ORDER BY $TBL_catalogue_products.ordering";
      $request=request($requestString,$insideLink);
      if(!mysql_num_rows($request)){
        mysql_close($insideLink);
        return;
      }
      $this->idCategory = $idCategory;
      $this->productTable=array();
      while($row=mysql_fetch_object($request)){
        $this->productTable[$row->id]=array();
        $this->productTable[$row->id]["id"]=$row->id;
        $this->productTable[$row->id]["title"]=$row->name;
        $this->productTable[$row->id]["ref"]=$row->ref_store;
        $this->productTable[$row->id]["image_large"]=$row->image_file_large;
        $this->productTable[$row->id]["image_small"]=$row->image_file_small;
        $this->productTable[$row->id]["state"]=$row->state;
        $this->productTable[$row->id]["prices"]=array();
        $this->productTable[$row->id]["prices"]["public"]=array();
        $this->productTable[$row->id]["prices"]["public"]["name"]= "public";
        $this->productTable[$row->id]["prices"]["public"]["price"]= $row->public_price;
        if($row->is_in_special=="Y" && $row->public_special){
          $this->productTable[$row->id]["prices"]["public"]["in_special"]=true;
          $this->productTable[$row->id]["prices"]["public"]["special_price"]=$row->special_price;
        } else $this->productTable[$row->id]["prices"]["public"]["in_special"]=false;
        //Start of prices
         $request2 = request(
                  "SELECT $TBL_catalogue_info_price_groups.name,
                          $TBL_catalogue_product_prices.price,
                          $TBL_catalogue_product_prices.is_in_special,
                          $TBL_catalogue_product_prices.special_price
                   FROM   $TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups,$TBL_catalogue_product_prices
                   WHERE  $TBL_catalogue_price_groups.id_group='$idGroup'
                   AND    $TBL_catalogue_info_price_groups.id_language='$idLanguage'
                   AND    $TBL_catalogue_product_prices.id_product='".$row->id."'
                   AND    $TBL_catalogue_info_price_groups.id_price_group=$TBL_catalogue_price_groups.id
                   AND    $TBL_catalogue_product_prices.id_price_group=$TBL_catalogue_price_groups.id"
                ,$insideLink);
          while($row2=mysql_fetch_object($request2)){
            $this->productTable[$row->id]["prices"][$row2->name]=array();
            $this->productTable[$row->id]["prices"][$row2->name]["name"]= $row2->name;
            $this->productTable[$row->id]["prices"][$row2->name]["price"]= $row2->price;
            if($row->is_in_special=="Y" && $row2->is_in_special=="Y"){
              $this->productTable[$row->id]["prices"][$row2->name]["in_special"]=true;
              $this->productTable[$row->id]["prices"][$row2->name]["special_price"]=$row2->special_price;
            } else $this->productTable[$row->id]["prices"][$row2->name]["in_special"]=false;
          }
        //End of Prices
        //Get the brand
        if($row->brand){
          $request2 = request(
            "SELECT name from $TBL_catalogue_brands WHERE id='".$row->brand."'"
            ,$insideLink
          );
          $brand = mysql_fetch_object($request2);
          $this->productTable[$row->id]["brand"]=$brand->name;
        }
        //get the selection extrafields
        $request2 = request(
                  "SELECT $TBL_catalogue_extrafields_values.id_extrafield id,
                          $TBL_catalogue_info_extrafieldselection.value,
                          $TBL_catalogue_info_extrafields.name,
                          $TBL_catalogue_info_extrafieldselection.image_file_small
                   FROM   $TBL_catalogue_extrafields,$TBL_catalogue_extrafields_values,$TBL_catalogue_info_extrafields,$TBL_catalogue_extrafieldselection,$TBL_catalogue_info_extrafieldselection
                   WHERE  $TBL_catalogue_extrafields_values.id_product='".$row->id."'
                   AND	  $TBL_catalogue_extrafields.datatype='selection'
                   AND	  $TBL_catalogue_info_extrafieldselection.id_extrafieldselection=$TBL_catalogue_extrafields_values.value
                   AND    $TBL_catalogue_info_extrafieldselection.id_language='$idLanguage'
                   AND    $TBL_catalogue_info_extrafields.id_language='$idLanguage'
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
            $this->productTable[$row->id]["extrafields"][$row2->id]["image_file"]= $row2->image_file_small;
        }
      }
      if(!$link) mysql_close($insideLink);
      return $this->productTable;
    }
  } //Class
?>
