<?php

/**
 * SET THE TYPE OF ENVIRONMENT WE ARE ON DEPENDING ON THE SERVER NAME
 */

$ANIX_environments = array();
$ANIX_environments["production"] = array();
$ANIX_environments["development"] = array();
$ANIX_environments["test"] = array();
//production name servers
$ANIX_environments["production"][]=strtolower("www.anix-cms.com");
$ANIX_environments["production"][]=strtolower("anix-cms.com");
//development name servers
$ANIX_environments["development"][]=strtolower("localhost");
$ANIX_environments["development"][]=strtolower("anix");
$ANIX_environments["development"][]=strtolower("192.168.1.101");

foreach($ANIX_environments as $type=>$values){
	if(in_array(strtolower($_SERVER['SERVER_NAME']),$values)) $ANIX_environment=$type;
}
if(!isset($ANIX_environment)) $ANIX_environment="production";

if($ANIX_environment=="development"){
	#######################################################
	# DEVELOPMENT ENVIRONMENT SPECIFIC CONFIGURATIONS
	#######################################################
	//DB Configuration
	$hostName = "localhost";
	$userName = "root";
	$password ="";
	$dbName = "anixv2";
} elseif($ANIX_environment=="test") {
	#######################################################
	# TEST ENVIRONMENT SPECIFIC CONFIGURATIONS
	#######################################################
	$hostName = "localhost";
	$userName = "root";
	$password ="";
	$dbName = "cibaxion_anixv2";
} else {
	#######################################################
	# PRODUCTION ENVIRONMENT SPECIFIC CONFIGURATIONS
	#######################################################
	//DB Configuration
	$hostName = "localhost";
	$userName = "anixv2";
	$password ="";
	$dbName = "anixv2";
}

#######################################################
# SUPER CONFIGURATION
#######################################################
//Currency symbol used on Anix
$currency_symbol = "\$CAN";
$measure_symbol = "in";
$weight_symbol = "lbs";
//Table containing information about available modules
$modules = array();
$modules["General"]=array("name" => "content", "folder" => "mod_content", "display" => _("Structure"), "default" => true);
$modules["News"]=array("name" => "news", "folder" => "mod_news", "display" => _("Nouvelles"));
$modules["FAQ"]=array("name" => "faq", "folder" => "mod_faq", "display" => _("F.A.Q."));
$modules["Articles"]=array("name" => "articles", "folder" => "mod_articles", "display" => _("Articles"));
$modules["Gallery"]=array("name" => "gallery", "folder" => "mod_gallery", "display" => _("Gallerie"));
$modules["Flex"]=array("name" => "flex", "folder" => "mod_flex", "display" => _("Flex"));
$modules["Catalog"]=array("name" => "catalog", "folder" => "mod_catalogue", "display" => _("Catalogue"));
$modules["Ecommerce"]=array("name" => "ecommerce", "folder" => "mod_ecommerce", "display" => _("E-Commerce"));
$modules["Admin"]=array("name" => "admin", "folder" => "mod_admin", "display" => _("Admin"));
//Default language used by anix
$used_language = "fr_CA";
//Default module
$default_module="Catalog";
//Name of the website to administer with anix
$AdministredSiteName = "www.anixcms.com";
//Check if the user does not logout properly ?
$check_logout = false;
//How many times we allow a user to not logout properly before we locking him?
$max_nb_not_logout = 5;
//Life time of a session IN SECONDS
$session_lifetime = 3600;
//Put yes if the site is for demo (disable accounts modifications)
$anix_demo_mode = false;

#######################################################
# General Configuration
#######################################################


#######################################################
# Catalogue Configurations
#######################################################
//Folders (relative with no slash in the beginning)
$CATALOG_folder_images ="../UserImages/";
$CATALOG_folder_attachments ="../UserAttachments/";
//Default editor value (useful to setup the default background color in a table for example....)
$CATALOG_editor_default_value="";

$CATALOG_enable_qty_prices = true;
$CATALOG_qty_price_levels = 4;


//Default reference model for products if no one is given or when the product is copied.
//If a default reference model have been specified with the parent category or any
//top level category, that one will be used instead.
//%idPrd% = The automatic global generated id of the product
//%idCat% = The automatic generated id of the category
//%YY% = The two last digits of the year
//%YYYY% = The year in four digitd
//%MM% = The two digits representing the month
//%M% = The letter sybol of the month from A to L
//%DD% = The two digits representing the day of the month
//%DDD% = The three digits representing the day of the year
$CATALOG_default_products_ref = "AT-%idPrd%";
//If true, the reference of the copied category will follow the pattern defined in
//the category we are copying to.
//If false, the copied product will have the same reference as the original one.
$generateRefOnCopy = true;
//Featured categories
//Table of element, each element contains the name of the category and
//the number of items that it may contains
$featuredCategories = array();
$featuredCategories[1]=array("id" => 1,"name" =>"Default","nbAllowed" => -1,
"imglarge_maxW" => 250,
"imglarge_maxH" => 400,
"imgsmall_maxW" => 200,
"imgsmall_maxH" => 400);
$featuredField1Name = "Champs 1";
$featuredField2Name = "Champs 2";
$FEATURED_editor_default_value ="";
/**
   *Images max sizes (Nota: original ratio is preserved)
   **/
// Categories' images
$CATALOG_image_cat_large_max_width = 500;
$CATALOG_image_cat_large_max_height = 400;
$CATALOG_image_cat_small_max_width = 80;
$CATALOG_image_cat_small_max_height = 80;
// Products' images
$CATALOG_image_prd_orig_max_width = 400;
$CATALOG_image_prd_orig_max_height = 400;
$CATALOG_image_prd_large_max_width = 200;
$CATALOG_image_prd_large_max_height = 200;
$CATALOG_image_prd_small_max_width = 80;
$CATALOG_image_prd_small_max_height = 80;
$CATALOG_image_prd_icon_max_width = 80;
$CATALOG_image_prd_icon_max_height = 80;
// Brands' images
$CATALOG_image_brand_large_max_width = 200;
$CATALOG_image_brand_large_max_height = 100;
$CATALOG_image_brand_small_max_width = 80;
$CATALOG_image_brand_small_max_height = 80;
#######################################################
# Customers Configurations
#######################################################

#######################################################
# News Configurations
#######################################################
//Default RTF value (useful to setup the default background color in a table for example....)
$NEWS_editor_default_value="";
// News' images
$NEWS_image_news_orig_max_width = 400;
$NEWS_image_news_orig_max_height = 400;
$NEWS_image_news_large_max_width = 200;
$NEWS_image_news_large_max_height = 200;
$NEWS_image_news_small_max_width = 80;
$NEWS_image_news_small_max_height = 80;
$NEWS_image_news_icon_max_width = 80;
$NEWS_image_news_icon_max_height = 80;
#######################################################
# FAQ Configurations
#######################################################
//Default RTF value (useful to setup the default background color in a table for example....)
$FAQ_editor_default_value="";
#######################################################
# Links Configuration
#######################################################

#######################################################
# Content Configuration
#######################################################
//Default RTF value (useful to setup the default background color in a table for example....)

$PAGES_editor_default_value="";
//Featured categories
//Table of element, each element contains the name of the category and
//the number of items that it may contains
$pageCategories = array();
$pageCategories[10]=array("id" => 10,"name" =>"Category 1","nbAllowed" => -1, "linksAllowed" => true);
$pageCategories[20]=array("id" => 20,"name" =>"Category 2","nbAllowed" => -1, "linksAllowed" => true);

$menuCategories = array();
$menuCategories[0]=array("id" => 0,"name" =>"Main menu","nbAllowed" => -1,
"img_maxW" => 187,
"img_maxH" => 26,
"nbLevelsAllowed" => 2,
"nbAllowedInSublevels" => 10);
$menuCategories[1]=array("id" => 1,"name" =>"Footer","nbAllowed" => 7,
"img_maxW" => 187,
"img_maxH" => 26,
"nbLevelsAllowed" => 1,
"nbAllowedInSublevels" => 10);
/*$menuCategories[2]=array("id" => 3,"name" =>"Consommateurs","nbAllowed" => -1,
"img_maxW" => 187,
"img_maxH" => 26,
"nbLevelsAllowed" => 2,
"nbAllowedInSublevels" => 15);*/
#######################################################
# Ecommerce Configuration
#######################################################
$ECOMMERCE_default_country = "FRANCE";
$ECOMMERCE_min_login_legth = 6;
$ECOMMERCE_max_login_legth = 25;
$ECOMMERCE_min_password_legth = 6;
$ECOMMERCE_max_password_legth = 15;

$ECOMMERCE_product_prices_inclue_VAT = true;

$ECOMMERCE_product_prices_min_margin_percentage = 15;

//Fraud check methods
$ECOMMERCE_fraudcheck_methods=array();
$ECOMMERCE_fraudcheck_methods["manual"]=array();
$ECOMMERCE_fraudcheck_methods["manual"]["name"]=_("Evaluation manuelle");
$ECOMMERCE_fraudcheck_methods["manual"]["anix_url"]="./fraud_check/manual.php";
$ECOMMERCE_fraudcheck_methods["fia-net"]=array();
$ECOMMERCE_fraudcheck_methods["fia-net"]["name"]=_("Interrogation Fia-Net");
$ECOMMERCE_fraudcheck_methods["fia-net"]["site_id"]="8110";
$ECOMMERCE_fraudcheck_methods["fia-net"]["login"]="numeridog";
$ECOMMERCE_fraudcheck_methods["fia-net"]["password"]=urlencode("c1j\$t\$vx");
$ECOMMERCE_fraudcheck_methods["fia-net"]["anix_url"]="./fraud_check/fia-net.php";
if($_SERVER['SERVER_NAME']=="localhost"){
	$ECOMMERCE_fraudcheck_methods["fia-net"]["post_url"]="https://secure.fia-net.com/pprod/engine/redirect.cgi";
	$ECOMMERCE_fraudcheck_methods["fia-net"]["validation_url"]="https://secure.fia-net.com/pprod/engine/get_validation.cgi?SiteID=".$ECOMMERCE_fraudcheck_methods["fia-net"]["site_id"]."&Pwd=".$ECOMMERCE_fraudcheck_methods["fia-net"]["password"]."&RefID=%%ID_ORDER%%&Mode=mini";
	$ECOMMERCE_fraudcheck_methods["fia-net"]["details_url"]="https://secure.fia-net.com/pprod/commun/visucheck_detail.php?sid=".$ECOMMERCE_fraudcheck_methods["fia-net"]["site_id"]."&log=".$ECOMMERCE_fraudcheck_methods["fia-net"]["login"]."&pwd=".$ECOMMERCE_fraudcheck_methods["fia-net"]["password"]."&rid=%%ID_ORDER%%";
} else {
	$ECOMMERCE_fraudcheck_methods["fia-net"]["post_url"]="https://secure.fia-net.com/fscreener/engine/redirect.cgi";
	$ECOMMERCE_fraudcheck_methods["fia-net"]["validation_url"]="https://secure.fia-net.com/fscreener/engine/get_validation.cgi?SiteID=".$ECOMMERCE_fraudcheck_methods["fia-net"]["site_id"]."&Pwd=".$ECOMMERCE_fraudcheck_methods["fia-net"]["password"]."&RefID=%%ID_ORDER%%&Mode=mini";
	$ECOMMERCE_fraudcheck_methods["fia-net"]["details_url"]="https://secure.fia-net.com/fscreener/commun/visucheck_detail.php?sid=".$ECOMMERCE_fraudcheck_methods["fia-net"]["site_id"]."&log=".$ECOMMERCE_fraudcheck_methods["fia-net"]["login"]."&pwd=".$ECOMMERCE_fraudcheck_methods["fia-net"]["password"]."&rid=%%ID_ORDER%%";
}

//Fraud levels
$ECOMMERCE_faud_level_awaiting = -1;
$ECOMMERCE_faud_level_low = 2;
$ECOMMERCE_faud_level_medium = 5;
$ECOMMERCE_faud_level_high = 7;
$ECOMMERCE_faud_level_alert = 10;

//Emails categories
$ECOMMERCE_email_categories = array();
$ECOMMERCE_email_categories[1]=array("id" => 1,"name" =>"Commandes","nbAllowed" => 2);
$ECOMMERCE_email_categories[2]=array("id" => 2,"name" =>"Paiements","nbAllowed" => 3);
$ECOMMERCE_email_categories[3]=array("id" => 3,"name" =>"Livraisons","nbAllowed" => 3);
$ECOMMERCE_email_categories[4]=array("id" => 4,"name" =>"Autres","nbAllowed" => 1);

//Email ID attributions
$ECOMMERCE_email_ids = array();
$ECOMMERCE_email_ids["late_delivery"]=5;
$ECOMMERCE_email_ids["early_delivery"]=4;
$ECOMMERCE_email_ids["order_shipped"]=8;
$ECOMMERCE_email_ids["order_cancelled"]=1;
$ECOMMERCE_email_ids["order_expired"]=3;
$ECOMMERCE_email_ids["payment_received"]=2;
$ECOMMERCE_email_ids["payment_received_admin"]=6;
$ECOMMERCE_email_ids["payment_alert"]=7;
$ECOMMERCE_email_ids["comment_request"]=9;
$ECOMMERCE_email_ids["newaccount_credentials"]=10;


?>