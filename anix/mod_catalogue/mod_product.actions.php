<?
if($action=="insert"){
	if(!$errors){
		if(isset($_POST["idCat"])){
			$idCat=$_POST["idCat"];
		} elseif(isset($_GET["idCat"])){
			$idCat=$_GET["idCat"];
		} else $idCat="";
		$ordering=getMaxProductsOrder($idCat,$link);
		if(!$ordering){
			$errors++;
			$errMessage.=_("La catéorie spéifiée n'est pas valide")."<br />";
		} else {
			$parentCategories = getParentsPathIds($idCat,$link);
		}
	}
	//Check the product qty prices
	if(isset($CATALOG_enable_qty_prices) && $CATALOG_enable_qty_prices) for($i=0;$i<$CATALOG_qty_price_levels;$i++){
		if($_POST["qtyprice_qty$i"]!=""){
			if($_POST["qtyprice_qty$i"]<=1){
				$errors++;
				$errMessage.=_("Prix à la quantité: La quantité doit être supérieure à 1.")."<br />";
			}
			if(!$errors && ($_POST["qtyprice_price$i"]=="" || $_POST["qtyprice_price$i"]<0)){
				$errors++;
				$errMessage.=_("Le prix du produit n'est pas valide pour la quantité: ").$_POST["qtyprice_qty$i"]."<br />";
			}
		}
	}
	if(!$errors){
		$requestString ="INSERT INTO `$TBL_catalogue_products` (`id_category`,`active`,`product_type`,`is_in_special`,`ordering`,`ref_store`,`brand`,`ref_manufacturer`,`url_manufacturer`,`upc_code`,`dim_W`,`dim_H`,`dim_L`,`weight`,`public_price`,`ecotaxe`,`public_special`,`special_price`,`stock`,`stock_alert`,`restocking_delay`,`id_supplier1`,`ref_supplier1`,`cost_supplier1`,`id_supplier2`,`ref_supplier2`,`cost_supplier2`,`id_supplier3`,`ref_supplier3`,`cost_supplier3`,`id_supplier4`,`ref_supplier4`,`cost_supplier4`,`created_on`,`created_by`,`modified_on`,`modified_by`) values (";
		$requestString.="'$idCat',";
		$requestString.="'".$_POST["active"]."',";
		$requestString.="'".$_POST["product_type"]."',";
		$requestString.="'".(isset($_POST["is_in_special"])?"Y":"N")."',";
		$requestString.="'$ordering',";
		$requestString.="'".$_POST["ref_store"]."',";
		$requestString.="'".$_POST["brand"]."',";
		$requestString.="'".htmlentities($_POST["ref_manufacturer"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="'".htmlentities($_POST["url_manufacturer"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="'".htmlentities($_POST["upc_code"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="'".htmlentities($_POST["dim_W"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="'".htmlentities($_POST["dim_H"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="'".htmlentities($_POST["dim_L"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="'".htmlentities($_POST["weight"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="'".$_POST["public_price"]."',";
		$requestString.="'".$_POST["ecotaxe"]."',";
		$requestString.="'".(isset($_POST["public_special"])?"Y":"N")."',";
		$requestString.="'".$_POST["special_price"]."',";
		$requestString.="'".$_POST["stock"]."',";
		$requestString.="'".$_POST["stock_alert"]."',";
		$requestString.="'".$_POST["restocking_delay"]."',";
		$requestString.="'".$_POST["id_supplier1"]."',";
		$requestString.="'".$_POST["ref_supplier1"]."',";
		$requestString.="'".$_POST["cost_supplier1"]."',";
		$requestString.="'".$_POST["id_supplier2"]."',";
		$requestString.="'".$_POST["ref_supplier2"]."',";
		$requestString.="'".$_POST["cost_supplier2"]."',";
		$requestString.="'".$_POST["id_supplier3"]."',";
		$requestString.="'".$_POST["ref_supplier3"]."',";
		$requestString.="'".$_POST["cost_supplier3"]."',";
		$requestString.="'".$_POST["id_supplier4"]."',";
		$requestString.="'".$_POST["ref_supplier4"]."',";
		$requestString.="'".$_POST["cost_supplier4"]."',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username',";
		$requestString.="'".getDBDate()."',";
		$requestString.="'$anix_username')";
		request($requestString,$link);
		if(!mysql_errno($link)) {
			$idProduct=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion du produit. Le produit n'a pas été inséré.")."<br />";
			$errors++;
		}
	}
	//check the public price against the supplier prices
	$costs = array();
	if($_POST["cost_supplier1"]>0) $costs[]=$_POST["cost_supplier1"];
	if($_POST["cost_supplier2"]>0) $costs[]=$_POST["cost_supplier2"];
	if($_POST["cost_supplier3"]>0) $costs[]=$_POST["cost_supplier3"];
	if($_POST["cost_supplier4"]>0) $costs[]=$_POST["cost_supplier4"];
	if(count($costs)) $min_cost = min($costs);
	else $min_cost = 0;
	if($min_cost>0 && $_POST["public_price"]<$min_cost*(1+$ECOMMERCE_product_prices_min_margin_percentage/100)){
		$ANIX_messages->addWarning("ATTENTION: Le prix public spécifié est inférieur à votre coût.");
	}
	//Insert the product states
	if(!$errors){
		$requestString = "SELECT `id` FROM `$TBL_catalogue_state`";
		$request = request($requestString,$link);
		while($state = mysql_fetch_object($request)){
			if(isset($_POST["state_".$state->id])){
				request("INSERT INTO `$TBL_catalogue_product_state` (`id_state`,`id_product`) VALUES ('$state->id','$idProduct')",$link);
			}
		}
	}
	if(!$errors){
		//get the image sizes from the category
		$requestString="SELECT * FROM `$TBL_catalogue_categories` WHERE `id`='$idCat'";
		$tmp = request($requestString,$link);
		if(mysql_num_rows($tmp))
		$catInfos = mysql_fetch_object($tmp);
		else {
			$errMessage.="La categorie specifiee n'existe pas.";
			$errors++;
		}
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="INSERT INTO `$TBL_catalogue_info_products` (`id_product`,`id_language`,`name`,`description`,`keywords`,`htmltitle`,`htmldescription`) values (";
			$requestString.="'$idProduct',";
			$requestString.="'".$row_languages->id."',";
			$requestString.="'".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="'".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=")";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de l'insertion des informations du produit.")."<br />";
				$errors++;
			}
		}
	}
	//Insert and resize the image of the product
	$imageUploaded=false;
	if(!$errors && $_POST["image_action"]=="change"){
		$imageUploaded = (strcmp ($_FILES['image_file']['name'],"")!=0);
		if($imageUploaded && !isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$errMessage.=_("Ce type de fichier image n'est pas pris en charge.")."<br />";
		}
		if($imageUploaded){
			$fileName = 'imgprd_tmp_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$fileName_orig = 'imgprd_orig_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgprd_large_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgprd_small_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$fileName_icon = 'imgprd_icon_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addWarning(_("Vous n'avez pas spécifié d'image ou une erreur s'est produite lors de l'envoi de l'image du produit au serveur."));
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_prd_orig_max_width,$CATALOG_image_prd_orig_max_height);
				$imageEditor->outputFile($fileName_orig,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->productimg_large_width!=0?$catInfos->productimg_large_width:$CATALOG_image_prd_large_max_width,$catInfos->productimg_large_height!=0?$catInfos->productimg_large_height:$CATALOG_image_prd_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->productimg_small_width!=0?$catInfos->productimg_small_width:$CATALOG_image_prd_small_max_width,$catInfos->productimg_small_height!=0?$catInfos->productimg_small_height:$CATALOG_image_prd_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->productimg_icon_width!=0?$catInfos->productimg_icon_width:$CATALOG_image_prd_icon_max_width,$catInfos->productimg_icon_height!=0?$catInfos->productimg_icon_height:$CATALOG_image_prd_icon_max_height);
				$imageEditor->outputFile($fileName_icon,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'image temporaire."));
				}
			}
		} else { //Not uploaded
			$ANIX_messages->addWarning(_("Vous n'avez pas spécifié d'image ou une erreur s'est produite lors de l'envoi de l'image du produit au serveur."));
		}
	}
	if(!$errors){
		//Update different fields using the product id
		$nb=0;
		$requestString ="UPDATE `$TBL_catalogue_products` SET ";
		if($imageUploaded){
			$requestString .="`image_file_orig`='".$fileName_orig."', ";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
			$requestString .="`image_file_icon`='".$fileName_icon."'";
			$nb++;
		}
		$generatedRef = getProductRef($_POST["ref_store"],$idProduct,$idCat);
		if($generatedRef!=""){
			if($nb) $requestString.=",";
			$requestString.="`ref_store`='".$generatedRef."'";
			$nb++;
		}
		$requestString .="where `id`='".$idProduct."'";
		if($nb){
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la spécification de l'image du produit ou de la création de la référence.")."<br />";
			}
		}
	}
	//Inserting the product prices
	$pricecats=request("SELECT id FROM `$TBL_catalogue_price_groups`", $link);
	while (!$errors && $priceCat=mysql_fetch_object($pricecats)){
		$requestString="INSERT INTO `$TBL_catalogue_product_prices` (`id_product`,`id_price_group`,`price`,`is_in_special`,`special_price`) values (";
		$requestString.="'$idProduct',";
		$requestString.="'".$priceCat->id."',";
		//If the price was left empty, we use the public price...
		$priceForCat =($_POST["price_".$priceCat->id]!=""?$_POST["price_".$priceCat->id]:$_POST["public_price"]);
		$requestString.="'".$priceForCat."',";
		$requestString.="'".(isset($_POST["is_in_special_".$priceCat->id])?"Y":"N")."',";
		$requestString.="'".$_POST["special_price_".$priceCat->id]."'";
		$requestString.=")";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion de prix du produit.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		$insertStr="";
		if(isset($CATALOG_enable_qty_prices) && $CATALOG_enable_qty_prices){
			for($i=0;$i<$CATALOG_qty_price_levels;$i++)
				if($_POST["qtyprice_qty$i"]!=""){
					if($insertStr!="") $insertStr.=",";
					$insertStr.="('$idProduct','".$_POST["qtyprice_qty$i"]."','".$_POST["qtyprice_price$i"]."')";
				}
			if($insertStr!=""){
				request("INSERT INTO `$TBL_catalogue_product_qty_price` (`id_product`,`qty`,`price`) VALUES ".$insertStr,$link);
			}
		}
	}
	//Update the extrafields
	if(!$errors){
		$requestString="select $TBL_catalogue_extrafields.id,$TBL_gen_languages.id idLanguage from $TBL_catalogue_extrafields,$TBL_gen_languages where (";
		$first=true;
		foreach($parentCategories as $cat){
			if(!$first) $requestString.=" OR ";
			$requestString.="$TBL_catalogue_extrafields.id_cat='$cat'";
			$first = false;
		}
		$requestString.=") and $TBL_gen_languages.used = 'Y' order by $TBL_catalogue_extrafields.id_cat,$TBL_gen_languages.default";
		$request=request($requestString,$link);
		while($field=mysql_fetch_object($request)){
			//we insert
			$requestString ="INSERT INTO `$TBL_catalogue_extrafields_values` (`id_extrafield`,`id_product`,`id_language`,`value`) VALUES (";
			$requestString.="'".$field->id."',";
			$requestString.="'$idProduct',";
			$requestString.="'".$field->idLanguage."',";
			$requestString.="'".htmlentities($_POST["field".$field->id."_".$field->idLanguage],ENT_QUOTES,"UTF-8")."')";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'insertion d'une valeur de champs additionnel")." (".$field->id.",".$field->idLanguage.").<br>";
			}
		}
	}
	if(!$errors){
		$message = _("Le produit a été inséré correctement")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$request=request("SELECT id_category,ordering from $TBL_catalogue_products where id='$idProduct'",$link);
	if($product=mysql_fetch_object($request)){
		$idCat = $product->id_category;
	} else {
		$errors++;
		$errMessage.=_("Le produit spécifié est invalide.")."<br />";
	}
	//Check the product qty prices
	if(isset($CATALOG_enable_qty_prices) && $CATALOG_enable_qty_prices) for($i=0;$i<$CATALOG_qty_price_levels;$i++){
		if($_POST["qtyprice_qty$i"]!=""){
			if($_POST["qtyprice_qty$i"]<=1){
				$errors++;
				$errMessage.=_("Prix à la quantité: La quantité doit être supérieure à 1.")."<br />";
			}
			if(!$errors && ($_POST["qtyprice_price$i"]=="" || $_POST["qtyprice_price$i"]<0)){
				$errors++;
				$errMessage.=_("Le prix du produit n'est pas valide pour la quantité: ").$_POST["qtyprice_qty$i"]."<br />";
			}
		}
	}
	if(!$errors){
		$parentCategories = getParentsPathIds($idCat,$link);
		$requestString ="UPDATE `$TBL_catalogue_products` set ";
		$requestString.="`active`='".$_POST["active"]."',";
		$requestString.="`product_type`='".$_POST["product_type"]."',";
		$requestString.="`is_in_special`='".(isset($_POST["is_in_special"])?"Y":"N")."',";
		$requestString.="`ref_store`='".$_POST["ref_store"]."',";
		$requestString.="`brand`='".$_POST["brand"]."',";
		$requestString.="`ref_manufacturer`='".htmlentities($_POST["ref_manufacturer"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`url_manufacturer`='".htmlentities($_POST["url_manufacturer"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`upc_code`='".htmlentities($_POST["upc_code"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`dim_W`='".htmlentities($_POST["dim_W"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`dim_H`='".htmlentities($_POST["dim_H"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`dim_L`='".htmlentities($_POST["dim_L"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`weight`='".htmlentities($_POST["weight"],ENT_QUOTES,"UTF-8")."',";
		$requestString.="`public_price`='".$_POST["public_price"]."',";
		$requestString.="`ecotaxe`='".$_POST["ecotaxe"]."',";
		$requestString.="`public_special`='".(isset($_POST["public_special"])?"Y":"N")."',";
		$requestString.="`special_price`='".$_POST["special_price"]."',";
		$requestString.="`stock`='".$_POST["stock"]."',";
		$requestString.="`stock_alert`='".$_POST["stock_alert"]."',";
		$requestString.="`restocking_delay`='".$_POST["restocking_delay"]."',";
		$requestString.="`id_supplier1`='".$_POST["id_supplier1"]."',";
		$requestString.="`ref_supplier1`='".$_POST["ref_supplier1"]."',";
		$requestString.="`cost_supplier1`='".$_POST["cost_supplier1"]."',";
		$requestString.="`id_supplier2`='".$_POST["id_supplier2"]."',";
		$requestString.="`ref_supplier2`='".$_POST["ref_supplier2"]."',";
		$requestString.="`cost_supplier2`='".$_POST["cost_supplier2"]."',";
		$requestString.="`id_supplier3`='".$_POST["id_supplier3"]."',";
		$requestString.="`ref_supplier3`='".$_POST["ref_supplier3"]."',";
		$requestString.="`cost_supplier3`='".$_POST["cost_supplier3"]."',";
		$requestString.="`id_supplier4`='".$_POST["id_supplier4"]."',";
		$requestString.="`ref_supplier4`='".$_POST["ref_supplier4"]."',";
		$requestString.="`cost_supplier4`='".$_POST["cost_supplier4"]."',";
		$requestString .="modified_on='".getDBDate()."',modified_by='$anix_username'";
		$requestString.="WHERE id='$idProduct'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour du produit.")."<br />";
			$errors++;
		}
	}
	//check the public price against the supplier prices
	$costs = array();
	if($_POST["cost_supplier1"]>0) $costs[]=$_POST["cost_supplier1"];
	if($_POST["cost_supplier2"]>0) $costs[]=$_POST["cost_supplier2"];
	if($_POST["cost_supplier3"]>0) $costs[]=$_POST["cost_supplier3"];
	if($_POST["cost_supplier4"]>0) $costs[]=$_POST["cost_supplier4"];
	if(count($costs)) $min_cost = min($costs);
	else $min_cost=0;
	if($min_cost>0 && $_POST["public_price"]<$min_cost*(1+$ECOMMERCE_product_prices_min_margin_percentage/100)){
		$ANIX_messages->addWarning("ATTENTION: Le prix public spécifié est inférieur à votre coût.");
	}
	//update the product state
	if(!$errors){
		$requestString="
        	SELECT `$TBL_catalogue_state`.`id`, `$TBL_catalogue_product_state`.`id_product`
        	FROM `$TBL_catalogue_state`
        	LEFT JOIN `$TBL_catalogue_product_state` ON ( `$TBL_catalogue_state`.`id` = `$TBL_catalogue_product_state`.`id_state`
        											  AND `$TBL_catalogue_product_state`.`id_product` ='$idProduct' )";
		$request=request($requestString,$link);
		while($state = mysql_fetch_object($request)){
			if(isset($_POST["state_".$state->id])){
				//if it does not exist we insert it
				if($state->id_product==null) request("INSERT INTO `$TBL_catalogue_product_state` (`id_state`,`id_product`) VALUE ('$state->id','$idProduct')",$link);
				//otherwise we leave
			} else {
				//if it exists we delete it
				if($state->id_product!=null) request("DELETE FROM `$TBL_catalogue_product_state` WHERE `id_state`='$state->id' AND `id_product`='$idProduct'",$link);
			}
		}
	}
	if(!$errors){
		//get the image sizes from the category
		$requestString="SELECT * FROM `$TBL_catalogue_categories` WHERE `id`='$idCat'";
		$tmp = request($requestString,$link);
		if(mysql_num_rows($tmp))
		$catInfos = mysql_fetch_object($tmp);
		else {
			$errMessage.="La categorie specifiee n'existe pas.";
			$errors++;
		}
	}
	if(!$errors){
		$languages=request("SELECT id FROM `$TBL_gen_languages` WHERE used = 'Y'", $link);
		while (!$errors && $row_languages=mysql_fetch_object($languages)){
			$requestString="UPDATE `$TBL_catalogue_info_products` set ";
			$requestString.="`name`='".htmlentities($_POST["name_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`description`='".htmlentities($_POST["description_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`keywords`='".htmlentities($_POST["keywords_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmltitle`='".htmlentities($_POST["htmltitle_".$row_languages->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`htmldescription`='".htmlentities($_POST["htmldescription_".$row_languages->id],ENT_QUOTES,"UTF-8")."'";
			$requestString.=" WHERE `id_product`='$idProduct' and id_language='".$row_languages->id."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour des informations du produit.")."<br />";
				$errors++;
			}
		}
	}
	//Insert and resize the image of the product
	$imageUploaded=false;
	if(!$errors && $_POST["image_action"]=="change" && (strcmp ($_FILES['image_file']['name'],"")!=0)){
		$imageUploaded = true;
		if(!isImageAllowed($_FILES['image_file']['name'])){
			$imageUploaded=false;
			$ANIX_messages->addWarning(_("Ce type de fichier image n'est pas pris en charge."));
		}
		if($imageUploaded){
			$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_catalogue_products where id=$idProduct",$link);
			$editCategory=mysql_fetch_object($request);
			if($editCategory->image_file_orig!=""){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_orig)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image du produit."));
				}
			}
			if($editCategory->image_file_small!="imgprd_small_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image du produit."));
				}
			}
			if($editCategory->image_file_large!="imgprd_large_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne grande image du produit."));
				}
			}
			if($editCategory->image_file_icon!="imgprd_icon_no_image.jpg"){
				if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_icon)){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne icone du produit."));
				}
			}
		}
		if($imageUploaded){
			$fileName = 'imgprd_tmp_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$fileName_orig = 'imgprd_orig_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$fileName_large = 'imgprd_large_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$fileName_small = 'imgprd_small_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$fileName_icon = 'imgprd_icon_'.$idProduct.'_'.$_FILES['image_file']['name'];
			$filePath = $CATALOG_folder_images.$fileName;
			if(!move_uploaded_file($_FILES['image_file']['tmp_name'],'../'.$filePath)){
				$ANIX_messages->addWarning(_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image du produit au serveur."));
				$imageUploaded=false;
			} else{
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($CATALOG_image_prd_orig_max_width,$CATALOG_image_prd_orig_max_height);
				$imageEditor->outputFile($fileName_orig,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->productimg_large_width!=0?$catInfos->productimg_large_width:$CATALOG_image_prd_large_max_width,$catInfos->productimg_large_height!=0?$catInfos->productimg_large_height:$CATALOG_image_prd_large_max_height);
				$imageEditor->outputFile($fileName_large,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->productimg_small_width!=0?$catInfos->productimg_small_width:$CATALOG_image_prd_small_max_width,$catInfos->productimg_small_height!=0?$catInfos->productimg_small_height:$CATALOG_image_prd_small_max_height);
				$imageEditor->outputFile($fileName_small,'../'.$CATALOG_folder_images);
				$imageEditor = new ImageEditor($fileName, '../'.$CATALOG_folder_images);
				$imageEditor->resize($catInfos->productimg_icon_width!=0?$catInfos->productimg_icon_width:$CATALOG_image_prd_icon_max_width,$catInfos->productimg_icon_height!=0?$catInfos->productimg_icon_height:$CATALOG_image_prd_icon_max_height);
				$imageEditor->outputFile($fileName_icon,'../'.$CATALOG_folder_images);
				if(!unlink('../'.$filePath)){
					$errMessage.=_("Une erreur s'est produite lors de la suppression de l'image temporaire.")."<br />";
				}
			}
		}else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spéifié d'image ou une erreur s'est produite lors de l'envoi de l'image du produit au serveur.")."<br />";
		}

	}
	//DELETE THE IMAGE = REPLACE BY THE NO_IMAGE
	if(!$errors && $_POST["image_action"]=="delete"){
		$imageUploaded=true;
		$request=request("SELECT image_file_orig,image_file_large,image_file_small,image_file_icon from $TBL_catalogue_products where id=$idProduct",$link);
		$editCategory=mysql_fetch_object($request);
		if($editCategory->image_file_orig!=""){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_orig)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image du produit."));
			}
		}
		if($editCategory->image_file_small!="imgprd_small_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_small)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne petite image du produit."));
			}
		}
		if($editCategory->image_file_large!="imgprd_large_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_large)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne grande image du produit."));
			}
		}
		if($editCategory->image_file_icon!="imgprd_icon_no_image.jpg"){
			if(!unlink("../".$CATALOG_folder_images.$editCategory->image_file_icon)){
				$ANIX_messages->addWarning(_("Une erreur s'est produite lors de la suppression de l'ancienne icone du produit."));
			}
		}
		$fileName_orig="";
		$fileName_large="imgprd_large_no_image.jpg";
		$fileName_small="imgprd_small_no_image.jpg";
		$fileName_icon="imgprd_icon_no_image.jpg";
	}
	if(!$errors){
		//Update different fields using the product id
		$nb=0;
		$requestString ="UPDATE `$TBL_catalogue_products` SET ";
		if($imageUploaded){
			$requestString .="`image_file_orig`='".$fileName_orig."',";
			$requestString .="`image_file_large`='".$fileName_large."',";
			$requestString .="`image_file_small`='".$fileName_small."',";
			$requestString .="`image_file_icon`='".$fileName_icon."'";
			$nb++;
		}
		$generatedRef = getProductRef($_POST["ref_store"],$idProduct,$idCat);
		if($generatedRef!=""){
			if($nb) $requestString.=",";
			$requestString.="`ref_store`='".$generatedRef."'";
			$nb++;
		}
		$requestString .="where `id`='".$idProduct."'";
		if($nb){
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la spécification de l'image du produit ou de la création de la référence.")."<br />";
			}
		}
	}
	//Update the product prices
	$pricecats=request("SELECT id FROM `$TBL_catalogue_price_groups`", $link);
	while (!$errors && $priceCat=mysql_fetch_object($pricecats)){
		//is the pricegroup existing?
		$tmpRequest = request("SELECT `id_product` FROM `$TBL_catalogue_product_prices` WHERE `id_product`='$idProduct' AND `id_price_group`='$priceCat->id'",$link);
		if(mysql_num_rows($tmpRequest)){
			$requestString="UPDATE `$TBL_catalogue_product_prices` SET ";
			//If the price was left empty, we use the public price...
			$priceForCat =($_POST["price_".$priceCat->id]!=""?$_POST["price_".$priceCat->id]:$_POST["public_price"]);
			$requestString.="`price`='$priceForCat',";
			$requestString.="`is_in_special`='".(isset($_POST["is_in_special_".$priceCat->id])?"Y":"N")."',";
			$requestString.="`special_price`='".$_POST["special_price_".$priceCat->id]."' ";
			$requestString.="WHERE `id_product`='$idProduct' and `id_price_group`='".$priceCat->id."'";
			request($requestString,$link);
		} else {
			$requestString ="INSERT INTO `$TBL_catalogue_product_prices` (`id_product`,`price`,`is_in_special`,`special_price`) VALUES (";
			$requestString.="'$idProduct',";
			$requestString.="'$priceForCat',";
			$requestString.="'".(isset($_POST["is_in_special_".$priceCat->id])?"Y":"N")."',";
			$requestString.="'".$_POST["special_price_".$priceCat->id]."')";
			request($requestString,$link);
		}
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de prix du produit.")."<br />";
			$errors++;
		}
	}
	//Update the qty prices
	//Check the product qty prices
	if(!$errors){
		request("DELETE FROM `$TBL_catalogue_product_qty_price` WHERE `id_product`='$idProduct'",$link);
		$insertStr="";
		if(isset($CATALOG_enable_qty_prices) && $CATALOG_enable_qty_prices){
			for($i=0;$i<$CATALOG_qty_price_levels;$i++)
				if($_POST["qtyprice_qty$i"]!=""){
					if($insertStr!="") $insertStr.=",";
					$insertStr.="('$idProduct','".$_POST["qtyprice_qty$i"]."','".$_POST["qtyprice_price$i"]."')";
				}
			if($insertStr!=""){
				request("INSERT INTO `$TBL_catalogue_product_qty_price` (`id_product`,`qty`,`price`) VALUES ".$insertStr,$link);
			}
		}
	}
	//Update the extrafields
	if(!$errors){
		$requestString="select $TBL_catalogue_extrafields.id,$TBL_gen_languages.id idLanguage from $TBL_catalogue_extrafields,$TBL_gen_languages where (";
		$first=true;
		foreach($parentCategories as $cat){
			if(!$first) $requestString.=" OR ";
			$requestString.="$TBL_catalogue_extrafields.id_cat='$cat'";
			$first = false;
		}
		$requestString.=") and $TBL_gen_languages.used = 'Y' order by $TBL_catalogue_extrafields.id_cat,$TBL_gen_languages.default";
		$request=request($requestString,$link);
		while($field=mysql_fetch_object($request)){
			$tmp=request("SELECT id_extrafield from $TBL_catalogue_extrafields_values where id_extrafield='".$field->id."' and id_product='$idProduct' and id_language='".$field->idLanguage."'",$link);
			if(mysql_num_rows($tmp)){
				//we update first
				$requestString ="UPDATE `$TBL_catalogue_extrafields_values` SET ";
				$requestString.="`value`='".htmlentities($_POST["field".$field->id."_".$field->idLanguage],ENT_QUOTES,"UTF-8")."' ";
				$requestString.="WHERE id_product='$idProduct' AND id_extrafield='".$field->id."' and id_language='".$field->idLanguage."'";
				request($requestString,$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.=_("Une erreur s'est produite lors de la mise à jour d'un champs additionnel")." (".$field->id.",".$field->idLanguage.").<br>";
				}
			} else {
				//we insert
				$requestString ="INSERT INTO `$TBL_catalogue_extrafields_values` (`id_extrafield`,`id_product`,`id_language`,`value`) VALUES (";
				$requestString.="'".$field->id."',";
				$requestString.="'$idProduct',";
				$requestString.="'".$field->idLanguage."',";
				$requestString.="'".htmlentities($_POST["field".$field->id."_".$field->idLanguage],ENT_QUOTES,"UTF-8")."')";
				request($requestString,$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.=_("Une erreur s'est produite lors de l'insertion d'une valeur de champs additionnel")." (".$field->id.",".$field->idLanguage.").<br>";
				}
			}
		}
	}
	if(!$errors){
		$message = _("Le produit a été mis à jour correctement.")."<br />";
		$action="edit";
	}
}
?>
<?
if($action=="addAttachment"){
	//get the ordering
	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_attachments` WHERE id_product='$idProduct' GROUP BY id_product",$link);
	if(mysql_num_rows($tmp)) {
		$maxOrder= mysql_fetch_object($tmp);
		$maxOrderValue = $maxOrder->maximum+1;
	} else $maxOrderValue=1;
	$requestString ="INSERT INTO `$TBL_catalogue_attachments` (`id_product`,`id_language`,`title`,`description`,`ordering`) values (";
	$requestString.="'$idProduct',";
	$requestString.="'".$_POST['id_language']."',";
	$requestString.="'".htmlentities($_POST['title'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="'".htmlentities($_POST['description'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="'$maxOrderValue')";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.");
		$errors++;
	} else {
		$idAttachment=mysql_insert_id($link);
		$fileUploaded=false;
	}
	if(!$errors){
		$fileUploaded = (strcmp($_FILES['attachment_file']['name'],"")!=0);
		if($fileUploaded && isFileProhibited($_FILES['attachment_file']['name'])){
			$errors++;
			$errMessage.=_("Ce type de fichiers est interdit.")."<br />";
		}
		if($fileUploaded && !$errors){
			$fileName = "catalogue".$idAttachment."_".$_FILES['attachment_file']['name'];
			$filePath = $CATALOG_folder_attachments.$fileName;
			if(!move_uploaded_file($_FILES['attachment_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifié de fichier ou une erreur s'est produite lors de l'envoi du fichier attaché au serveur.")."<br />";
				$errors++;
				$fileUploaded=false;
			}
		} else { //Not uploaded
			$errMessage.=_("Vous n'avez pas spécifié de fichier.")."<br />";
			$errors++;
		}
	}
	//On définit le nouveau nom de fichier...
	if(!$errors && $fileUploaded){
		$requestString="UPDATE `$TBL_catalogue_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.")."<br />";
			$errors++;
		}
	} else {
		$requestString = "DELETE from `$TBL_catalogue_attachments` where id=$idAttachment";
		request($requestString,$link);
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idProduct'",$link);
	}
	if(!$errors) $message.=_("Le fichier a été correctement attaché au produit.")."<br />";
	$action = "edit";
	$ANIX_TabSelect=4;
}
?>
<?
if($action=="updateAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$requestString ="UPDATE `$TBL_catalogue_attachments` set ";
	$requestString.="`id_language`='".$_POST['id_language']."',";
	$requestString.="`title`='".htmlentities($_POST['title'],ENT_QUOTES,"UTF-8")."',";
	$requestString.="`description`='".htmlentities($_POST['description'],ENT_QUOTES,"UTF-8")."' ";
	$requestString.="WHERE `id`='$idAttachment'";
	request($requestString,$link);
	if(mysql_errno($link)) {
		$errMessage.=_("Une erreur s'est produite lors de la mise à jour du fichier attaché.")."<br />";
		$errors++;
	}
	$fileUploaded=false;
	if(!$errors){
		$fileUploaded = (strcmp($_FILES['attachment_file']['name'],"")!=0);
		if($fileUploaded && isFileProhibited($_FILES['attachment_file']['name'])){
			$errors++;
			$errMessage.=_("Ce type de fichiers est interdit.")."<br />";
			$fileUploaded=false;
		}
		if($fileUploaded && !$errors){
			$fileName = "catalogue".$idAttachment."_".$_FILES['attachment_file']['name'];
			$filePath = $CATALOG_folder_attachments.$fileName;
			if(!move_uploaded_file($_FILES['attachment_file']['tmp_name'],'../'.$filePath)){
				$errMessage.=_("Vous n'avez pas spécifié de fichier ou une erreur s'est produite lors de l'envoi du fichier attaché au serveur.")."<br />";
				$errors++;
				$fileUploaded=false;
			}
		}
	}
	//On définit le nouveau nom de fichier...
	if($fileUploaded){
		$request = request("SELECT file_name from `$TBL_catalogue_attachments` where id='$idAttachment'",$link);
		$oldFile = mysql_fetch_object($request);
		if($oldFile->file_name!="" && $oldFile->file_name!=$fileName) if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
			$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancien fichier.")."<br />";
		}
		$requestString="UPDATE `$TBL_catalogue_attachments` SET `file_name`='".$fileName."' where id='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de l'ajout du fichier attaché.")."<br />";
			$errors++;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idProduct'",$link);
	}
	if(!$errors) $message = _("Le fichier a été mis à jour courrectement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=4;
}
?>
<?
if($action=="delAttachment"){
	$idAttachment = $_POST["idAttachment"];
	$request = request("SELECT file_name,ordering from `$TBL_catalogue_attachments` where id='$idAttachment'",$link);
	$oldFile = mysql_fetch_object($request);
	if($oldFile->file_name!="") if(!unlink("../".$CATALOG_folder_attachments.$oldFile->file_name)){
		$errMessage.=_("Une erreur s'est produite lors de la suppression de l'ancien fichier.")."<br />";
		$errors++;
	}
	if(!$errors){
		$requestString ="DELETE from `$TBL_catalogue_attachments` WHERE `id`='$idAttachment'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.=_("Une erreur s'est produite lors de la suppression des informations du fichier attaché.")."<br />";
			$errors++;
		}
	}
	//update the orderings
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering=ordering-1 where id_product='$idProduct' and ordering > ".$oldFile->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des fichiers attachés.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products SET modified_on='".getDBDate()."',modified_by='$anix_username' WHERE id='$idProduct'",$link);
	}
	if(!$errors) $message = _("Le fichier attaché a été supprimé correctement.")."<br />";
	$action="edit";
	$ANIX_TabSelect=4;
}
?>
<?
if($action=="delOption"){
	if(isset($_POST["idOption"])){
		$idOption=$_POST["idOption"];
	} elseif(isset($_GET["idOption"])){
		$idOption=$_GET["idOption"];
	} else $idOption=0;
	$request = request("SELECT id,ordering from $TBL_catalogue_product_options WHERE id='$idOption'",$link);
	if(mysql_num_rows($request)){
		$option = mysql_fetch_object($request);
	} else{
		$errors++;
		$errMessage.=_("L'option spécifiée est invalide.")."<br />";
	}
	if(!$errors){
		request("DELETE $TBL_catalogue_product_option_choices,$TBL_catalogue_info_choices
              FROM $TBL_catalogue_product_option_choices,$TBL_catalogue_info_choices
              WHERE $TBL_catalogue_product_option_choices.id_option='$idOption'
              AND $TBL_catalogue_info_choices.id_choice=$TBL_catalogue_product_option_choices.id",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression des choix de l'option.")."<br />";
		}
	}
	if(!$errors){
		request("DELETE $TBL_catalogue_product_options,$TBL_catalogue_info_options
              FROM $TBL_catalogue_product_options,$TBL_catalogue_info_options
              WHERE $TBL_catalogue_product_options.id='$idOption'
              AND $TBL_catalogue_info_options.id_option=$TBL_catalogue_product_options.id",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression de l'option.")."<br />";
		}
	}
	//update the orderings
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_options` set ordering=ordering-1 where id_product='$idProduct' and ordering > ".$option->ordering,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour de l'ordre des options.")."<br />";
		}
	}
	if(!$errors){
		$message.=_("L'option a été supprimée correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="moveOptionUp"){
	if(isset($_POST["idOption"])){
		$idOption=$_POST["idOption"];
	} elseif(isset($_GET["idOption"])){
		$idOption=$_GET["idOption"];
	} else {
		$errors++;
		$errMessage.=_("L'option n'a pas été spécifiée.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_product_options` where id='$idOption'",$link);
		if(mysql_num_rows($request)){
			$option=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'option spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_product_options` where id_product='$idProduct' and ordering='".($option->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upOption=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'option est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_options` set ordering='".($option->ordering-1)."' where id='$idOption'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre de l'option.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_options` set ordering='".$option->ordering."' where id='".$upOption->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre de l'option.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idProduct'",$link);
		$message.=_("L'ordre de l'option a été mis à jour correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="moveOptionDown"){
	if(isset($_POST["idOption"])){
		$idOption=$_POST["idOption"];
	} elseif(isset($_GET["idOption"])){
		$idOption=$_GET["idOption"];
	} else {
		$errors++;
		$errMessage.=_("L'option n'a pas été spécifiée.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_product_options` where id='$idOption'",$link);
		if(mysql_num_rows($request)){
			$option=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'option spécifiée est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_product_options` where id_product='$idProduct' and ordering='".($option->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downOption=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'option est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_options` set ordering='".($option->ordering+1)."' where id='$idOption'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre de l'option.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_product_options` set ordering='".$option->ordering."' where id='".$downOption->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre de l'option.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idProduct'",$link);
		$message.=_("L'ordre de l'option a été mis à jour correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=3;
}
?>
<?
if($action=="moveAttachmentUp"){
	if(isset($_POST["idAttachment"])){
		$idAttachment=$_POST["idAttachment"];
	} elseif(isset($_GET["idAttachment"])){
		$idAttachment=$_GET["idAttachment"];
	} else {
		$errors++;
		$errMessage.=_("Le fichier attaché n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_attachments` where id_product='$idProduct' and ordering='".($attachment->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upAttachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché est déjà au plus haut niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering='".($attachment->ordering-1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering='".$attachment->ordering."' where id='".$upAttachment->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idProduct'",$link);
		$message.=_("L'ordre du fichier attaché a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=4;
}
?>
<?
if($action=="moveAttachmentDown"){
	if(isset($_POST["idAttachment"])){
		$idAttachment=$_POST["idAttachment"];
	} elseif(isset($_GET["idAttachment"])){
		$idAttachment=$_GET["idAttachment"];
	} else {
		$errors++;
		$errMessage.=_("Le fichier attaché n'a pas été spécifié.")."<br />";
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_attachments` where id='$idAttachment'",$link);
		if(mysql_num_rows($request)){
			$attachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché spécifié est invalide.")."<br />";
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from `$TBL_catalogue_attachments` where id_product='$idProduct' and ordering='".($attachment->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downAttachment=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("Le fichier attaché est déjà au plus bas niveau.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering='".($attachment->ordering+1)."' where id='$idAttachment'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_catalogue_attachments` set ordering='".$attachment->ordering."' where id='".$downAttachment->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la modification de l'ordre du fichier attaché.")."<br />";
		}
	}
	if(!$errors){
		request("UPDATE $TBL_catalogue_products set modified_on='".getDBDate()."',modified_by='$anix_username' where id='$idProduct'",$link);
		$message.=_("L'ordre du fichier attaché a été modifié correctement.")."<br />";
	}
	$action="edit";
	$ANIX_TabSelect=4;
}
?>