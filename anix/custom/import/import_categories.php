<?php

$CATALOG_image_prd_orig_max_width = 400;
$CATALOG_image_prd_orig_max_height = 400;
$CATALOG_image_prd_large_max_width = 200;
$CATALOG_image_prd_large_max_height = 200;
$CATALOG_image_prd_small_max_width = 80;
$CATALOG_image_prd_small_max_height = 80;
$CATALOG_image_prd_icon_max_width = 40;
$CATALOG_image_prd_icon_max_height = 40;


/**
 * IMPORT THE MAIN CATEGORY TABLE
 */
request ("TRUNCATE TABLE `catalogue_categories`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_categories` ORDER BY ID",$dbLink,true);
$requestStr="INSERT INTO `catalogue_categories`
				  ( `id` , `ordering` , `deletable` , `id_parent` , `contain_products` ,
				  `reference_pattern` , `hide_products` , `image_file_large` , `image_file_small` ,
				  `alias_prepend` , `alias_prd_prepend` , `id_menu` , `productimg_icon_width` ,
				  `productimg_icon_height` , `productimg_small_width` , `productimg_small_height` ,
				  `productimg_large_width` , `productimg_large_height` , `created_on` , `created_by` ,
				  `modified_on` , `modified_by` )
				   VALUES ";
$first = true;
while($cat = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="('$cat->id' , '$cat->ordering', 'Y', '$cat->id_parent', '$cat->contain_products',
				   '$cat->reference_pattern', '$cat->hide_products', '$cat->image_file_large', '$cat->image_file_small',
				   '$cat->alias_prepend', '$cat->alias_prd_prepend', '0', '$CATALOG_image_prd_icon_max_width',
				   '$CATALOG_image_prd_icon_max_height', '$CATALOG_image_prd_small_max_width', '$CATALOG_image_prd_small_max_height',
				   '$CATALOG_image_prd_large_max_width', '$CATALOG_image_prd_large_max_height', '$cat->created_on', '$cat->created_by',
				   '$cat->modified_on', '$cat->modified_by')";
}
if(!$first) request($requestStr,$dbLink,false);
if(!mysql_errno($dbLink)) echo "CATEGORIES(1) - OK";
else echo "CATEGORIES(1) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_info_categories`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_info_categories`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_info_categories`
				( `id_catalogue_cat` , `id_language` , `name` , `description` ,
				`alias_name` , `keywords` , `htmltitle` ,`htmldescription` )
				VALUES ";
$first = true;
while($catinfo = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$catinfo->id_catalogue_cat', '$catinfo->id_language', '$catinfo->name', '$catinfo->description',
					'$catinfo->alias_name', '', '', '')";
}
if(!$first) request($requestStr,$dbLink,false);
if(!mysql_errno($dbLink)) echo "CATEGORIES(2) - OK";
else echo "CATEGORIES(2) - KO: ".mysql_error($dbLink);
echo "<br />";

/**
 * IMPORT THE BRANDS
 */
request ("TRUNCATE TABLE `catalogue_brands`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_brands`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_brands`
				( `id` , `name` , `image_file_large` , `image_file_small` , `URL` ,
				`customer_service_phone` , `customer_service_email` , `created_on` ,
				`created_by` , `modified_on` , `modified_by` )
				VALUES ";
$first = true;
while($brand = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$brand->id', '$brand->name', '$brand->image_file_large', '$brand->image_file_small', '$brand->URL',
					'$brand->customer_service_phone','$brand->customer_service_email','$brand->created_on',
					'$brand->created_by','$brand->modified_on','$brand->modified_by')";
}
if(!$first) request($requestStr,$dbLink,false);
if(!mysql_errno($dbLink)) echo "BRANDS - OK";
else echo "BRANDS - KO: ".mysql_error($dbLink);
echo "<br />";

/**
 * IMPORT THE ATTACHMENTS
 */
request ("TRUNCATE TABLE `catalogue_attachments`",$dbLink,false);
//Attachments are not used...
if(!mysql_errno($dbLink)) echo "ATTACHMENTS - OK";
else echo "ATTACHMENTS - KO: ".mysql_error($dbLink);
echo "<br />";

/**
 * IMPORT CATEGORIES EXTRA SECTIONS
 */
request ("TRUNCATE TABLE `catalogue_extracategorysection`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_extracategorysection`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_extracategorysection`
				( `id` , `id_cat` , `deletable` , `ordering` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id', '$infos->id_cat', 'Y', '$infos->ordering')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "EXTRA SECTIONS(1) - OK";
else echo "EXTRA SECTIONS(1) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_info_extracategorysection`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_info_extracategorysection`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_info_extracategorysection`
				( `id_extrasection` , `id_language` , `name` , `value` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id_extrasection', '$infos->id_language', '$infos->name', '$infos->value')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "EXTRA SECTIONS(2) - OK";
else echo "EXTRA SECTIONS(2) - KO: ".mysql_error($dbLink);
echo "<br />";

/**
 * IMPORT EXTRAFIELDS
 */
request ("TRUNCATE TABLE `catalogue_extrafields`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_extrafields`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_extrafields`
				( `id` , `datatype` , `id_cat` , `id_product` , `params` ,
				`copy_from` , `deletable` , `ordering` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id', '$infos->datatype', '$infos->id_cat', '$infos->id_product', '$infos->params',
					'$infos->copy_from', 'Y', '$infos->ordering')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "EXTRA FIELDS(1) - OK";
else echo "CATEGORIES - EXTRA FIELDS(1) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_info_extrafields`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_info_extrafields`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_info_extrafields`
				( `id_extrafield` , `id_language` , `name` , `description` , `selection_values` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id_extrafield', '$infos->id_language', '$infos->name', '' ,'$infos->selection_values')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "EXTRA FIELDS(2) - OK";
else echo "EXTRA FIELDS(2) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_extrafields_values`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_extrafields_values`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_extrafields_values`
				( `id_extrafield` , `id_product` , `id_language` , `value` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id_extrafield', '$infos->id_product', '$infos->id_language', '$infos->value')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "EXTRA FIELDS(3) - OK";
else echo "EXTRA FIELDS(3) - KO: ".mysql_error($dbLink);
echo "<br />";

/**
 * IMPORT FEATURED
 */
request ("TRUNCATE TABLE `catalogue_featured`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_featured`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_featured`
				( `id` , `id_category` , `id_catalogue_prd` , `id_catalogue_cat` , `active` ,
				`from_date` , `to_date` , `image_file_small` , `image_file_large` , `ordering` ,
				`created_on` , `created_by` , `modified_on` , `modified_by` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id', '$infos->id_category', '$infos->id_catalogue_prd', '$infos->id_catalogue_cat', '$infos->active',
					'$infos->from_date', '$infos->to_date', '$infos->image_file_small','$infos->image_file_large','$infos->ordering',
					'$infos->created_on','$infos->created_by','$infos->modified_on','$infos->modified_by')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "FEATURED(1) - OK";
else echo "FEATURED(1) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_info_featured`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_info_featured`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_info_featured`
				( `id_featured` , `id_language` , `title` , `field1` , `field2` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id_featured', '$infos->id_language', '$infos->title', '$infos->field1', '$infos->field2')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "FEATURED(2) - OK";
else echo "FEATURED(2) - KO: ".mysql_error($dbLink);
echo "<br />";


/**
 * IMPORT THE PRICES
 */
request ("TRUNCATE TABLE `catalogue_price_groups`",$dbLink,false);
//Price groups are not used...
if(!mysql_errno($dbLink)) echo "PRICES(1) - OK";
else echo "PRICES(1) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_product_prices`",$dbLink,false);
//Price groups are not used...
if(!mysql_errno($dbLink)) echo "PRICES(2) - OK";
else echo "PRICES(2) - KO: ".mysql_error($dbLink);
echo "<br />";

/**
 * IMPORT PRODUCT OPTIONS
 */
request ("TRUNCATE TABLE `catalogue_product_options`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_product_options`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_product_options`
				( `id` , `id_product` , `ordering` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id', '$infos->id_product', '$infos->ordering')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "PRODUCT OPTIONS(1) - OK";
else echo "PRODUCT OPTIONS(1) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_info_options`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_info_options`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_info_options` ( `id_option` , `id_language` , `name` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id_option', '$infos->id_language', '$infos->name')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "PRODUCT OPTIONS(2) - OK";
else echo "PRODUCT OPTIONS(2) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_product_option_choices`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_product_option_choices`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_product_option_choices`
				( `id` , `id_option` , `default_choice` , `price_diff` ,
				`price_value` , `price_method` , `ordering` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id','$infos->id_option', '$infos->default_choice', '$infos->price_diff',
					'$infos->price_value','$infos->price_method','$infos->ordering')";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "PRODUCT OPTIONS(3) - OK";
else echo "PRODUCT OPTIONS(3) - KO: ".mysql_error($dbLink);
echo "<br />";


/**
 * IMPORT PRODUCTS
 */
request ("TRUNCATE TABLE `catalogue_products`",$dbLink,false);
request ("TRUNCATE TABLE `catalogue_product_state`",$dbLink,false);

$request = request("SELECT * FROM `catalogue_products`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_products`
				( `id` , `id_category` , `active` , `product_type` , `is_in_special` , `ordering` ,
				`image_file_orig` , `image_file_large` , `image_file_small` , `image_file_icon` , `ref_store` ,
				`brand` , `ref_manufacturer` , `url_manufacturer` , `upc_code` , `dim_W` , `dim_H` , `dim_L` , `weight` ,
				`public_price` , `ecotaxe` , `public_special` , `special_price` , `stock` , `restocking_delay` , `id_supplier1` ,
				`cost_supplier1` , `id_supplier2` , `cost_supplier2` , `id_supplier3` , `cost_supplier3` , `id_supplier4` ,
				`cost_supplier4` , `created_on` , `created_by` , `modified_on` , `modified_by` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$restocking_delay="0";
	switch($infos->state){ //get the restocking delay ID
		case "stock": $restocking_delay=4; break;
		case "order3": $restocking_delay=1; break;
		case "order6": $restocking_delay=2; break;
		case "order10": $restocking_delay=3; break;
	}
	if($infos->new_product=="Y") request("INSERT INTO `catalogue_product_state` ( `id_state` , `id_product` ) VALUES ('1','$infos->id')",$dbLink,false);
	if($infos->best_seller=="Y") request("INSERT INTO `catalogue_product_state` ( `id_state` , `id_product` ) VALUES ('2','$infos->id')",$dbLink,false);
	if($infos->home_page=="Y") request("INSERT INTO `catalogue_product_state` ( `id_state` , `id_product` ) VALUES ('3','$infos->id')",$dbLink,false);
	if($infos->featured=="Y") request("INSERT INTO `catalogue_product_state` ( `id_state` , `id_product` ) VALUES ('4','$infos->id')",$dbLink,false);

	$requestStr.="('$infos->id', '$infos->id_category', '$infos->active', '$infos->product_type', '$infos->is_in_special', '$infos->ordering',
					'$infos->image_file_orig', '$infos->image_file_large', '$infos->image_file_small', '$infos->image_file_small', '$infos->ref_store',
					'$infos->brand', '$infos->ref_manufacturer', '$infos->url_manufacturer', '$infos->upc_code', '$infos->dim_W', '$infos->dim_H', '$infos->dim_L', '$infos->weight',
					'$infos->public_price', '$infos->ecotaxe', '$infos->public_special', '$infos->special_price', '$infos->stock', '$restocking_delay', '$infos->id_supplier1',
					'$infos->cost_supplier1', '$infos->id_supplier2', '$infos->cost_supplier2', '$infos->id_supplier3', '$infos->cost_supplier3', '$infos->id_supplier4',
					'$infos->cost_supplier4', '$infos->created_on', '$infos->created_by', '$infos->modified_on', '$infos->modified_by'
					)";
}
if(!$first) request($requestStr,$dbLink,false);

if(!mysql_errno($dbLink)) echo "PRODUCTS(1) - OK";
else echo "PRODUCTS(1) - KO: ".mysql_error($dbLink);
echo "<br />";

request ("TRUNCATE TABLE `catalogue_info_products`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_info_products`",$dbLink,true);
$requestHeader="INSERT INTO `catalogue_info_products`
				( `id_product` , `id_language` , `name` , `description` ,
				`alias_name` , `keywords` , `htmltitle` , `htmldescription` )
				VALUES ";
while($infos = mysql_fetch_object($request)){
	$requestStr=$requestHeader."( '$infos->id_product','$infos->id_language', '$infos->name', '$infos->description',
					'$infos->alias_name','','','')";
	request($requestStr,$dbLink,false);
}

if(!mysql_errno($dbLink)) echo "PRODUCTS(2) - OK";
else echo "PRODUCTS(2) - KO: ".mysql_error($dbLink);
echo "<br />";

/**
 * IMPORT THE SUPPLIERS
 */
request ("TRUNCATE TABLE `catalogue_suppliers`",$dbLink,false);
$request = request("SELECT * FROM `catalogue_suppliers`",$dbLink,true);
$requestStr="INSERT INTO `catalogue_suppliers`
				( `id` , `name` , `contact` , `tel_sales` , `tel_support` , `url` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '$infos->id', '$infos->name', '$infos->contact', '$infos->tel_sales', '$infos->tel_support', '$infos->url')";
}
if(!$first) request($requestStr,$dbLink,false);
if(!mysql_errno($dbLink)) echo "SUPPLIERS - OK";
else echo "SUPPLIERS - KO: ".mysql_error($dbLink);
echo "<br />";


/**
 * IMPORT THE Links
 */
request ("TRUNCATE TABLE `links_link`",$dbLink,false);


$request = request("SELECT * FROM `links_catalogue_catalogue`",$dbLink,true);
$requestStr="INSERT INTO `links_link`
				( `id_category` , `from_module` , `from_item` ,
				`to_module` , `to_item` , `ordering` )
				VALUES ";
$first = true;
while($infos = mysql_fetch_object($request)){
	if($first) $first=false;
	else $requestStr.=",";
	$requestStr.="( '1', '".($infos->catalogue1=="cat"?"2":"1")."', '$infos->id_catalogue1',
					'".($infos->catalogue2=="cat"?"2":"1")."', '$infos->id_catalogue2', '0'),";
	$requestStr.="( '1', '".($infos->catalogue2=="cat"?"2":"1")."', '$infos->id_catalogue2',
					'".($infos->catalogue1=="cat"?"2":"1")."', '$infos->id_catalogue1', '0')";
}
if(!$first) request($requestStr,$dbLink,false);
if(!mysql_errno($dbLink)) echo "LINKS(1) - OK";
else echo "LINKS(1) - KO: ".mysql_error($dbLink);

//LINK CLEANUP
$request = request("SELECT `links_link`.`id`, `catalogue_products`.`id` as product
					FROM `links_link`
					LEFT JOIN `catalogue_products` ON (`links_link`.`from_module`='1' AND `links_link`.`from_item`=`catalogue_products`.`id`)",$dbLink,false);
while($link = mysql_fetch_object($request)){
	if($link->product==null) request("DELETE FROM `links_link` WHERE id='$link->id'",$dbLink,false);
}

//ORDERINGS
$request = request("SELECT DISTINCT `from_module`,`from_item` FROM `links_link",$dbLink,false);
while($link = mysql_fetch_object($request)){
	$request2=request("SELECT `id` FROM `links_link` WHERE `from_module`='$link->from_module' AND `from_item`='$link->from_item'",$dbLink,false);
	$ordering=1;
	while($link2=mysql_fetch_object($request2)){
		request("UPDATE `links_link` SET `ordering`='$ordering' WHERE `id`='$link2->id'",$dbLink,false);
		$ordering++;
	}
}
echo "<br />";


?>

