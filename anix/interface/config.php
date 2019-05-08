<?php
 #######################################################
 # DB Configuration
 #######################################################
   $hostName = "localhost";
   $userName = "root";
   $password ="";
   $dbName = "anix";
   
 #######################################################
 # General Configuration
 #######################################################
   //General DB tables
   $TBL_gen_languages="gen_languages";

 #######################################################
 # Catalogue Configurations
 #######################################################
   //DB tables
   $TBL_catalogue_categories = "catalogue_categories";
   $TBL_catalogue_info_categories = "catalogue_info_categories";
   $TBL_catalogue_products = "catalogue_products";
   $TBL_catalogue_info_products = "catalogue_info_products";
   $TBL_catalogue_extrafields = "catalogue_extrafields";
   $TBL_catalogue_info_extrafields = "catalogue_info_extrafields";
   $TBL_catalogue_extrafield_selection_values = "catalogue_extrafield_selection_values";
   $TBL_catalogue_extrafields_values = "catalogue_extrafields_values";
   $TBL_catalogue_extracategorysection = "catalogue_extracategorysection";
   $TBL_catalogue_info_extracategorysection = "catalogue_info_extracategorysection";
   $TBL_catalogue_price_groups="catalogue_price_groups";
   $TBL_catalogue_product_prices="catalogue_product_prices";
   $TBL_catalogue_attachments="catalogue_attachments";
   $TBL_catalogue_brands="catalogue_brands";
   $TBL_catalogue_product_options ="catalogue_product_options";
   $TBL_catalogue_info_options="catalogue_info_options";
   $TBL_catalogue_product_option_choices="catalogue_product_option_choices";
   $TBL_catalogue_info_choices="catalogue_info_choices";
   $TBL_catalogue_featured = "catalogue_featured";
   $TBL_catalogue_info_featured = "catalogue_info_featured";
 #######################################################
 # Customers Configurations
 #######################################################
   //DB tables
   $TBL_cust_customers = "cust_customers";
   $TBL_cust_customer_groups= "cust_customer_groups";
 #######################################################
 # News Configurations
 #######################################################
   //DB tables
   $TBL_news_categories = "news_categories";
   $TBL_news_info_categories = "news_info_categories";
   $TBL_news_info_news = "news_info_news";
   $TBL_news_news = "news_news";
 #######################################################
 # FAQ Configurations
 #######################################################
   //DB tables
   $TBL_faq_categories = "faq_categories";
   $TBL_faq_info_categories = "faq_info_categories";
   $TBL_faq_info_faq = "faq_info_faq";
   $TBL_faq_faq = "faq_faq";
 #######################################################
 # Links Configuration
 #######################################################
   //DB tables
   $TBL_links_catalogue_faq = "links_catalogue_faq";
   $TBL_links_catalogue_news = "links_catalogue_news";
   $TBL_links_catalogue_catalogue = "links_catalogue_catalogue";
 #######################################################
 # Content Configuration
 #######################################################
   //DB tables
   $TBL_content_pages = "content_pages";
   $TBL_content_info_pages = "content_info_pages";
   $TBL_content_menuitems = "content_menuitems";
   $TBL_content_info_menuitems = "content_info_menuitems";
 #######################################################
 # Admin Configuration
 #######################################################
    //DB tables
    $TBL_admin_admin = "admin_admin";
    $TBL_admin_groups = "admin_groups";
    $TBL_admin_sessions = "admin_sessions";
 #######################################################
 # GENERAL FUNCTIONS
 #######################################################
   //Funtion htmlentities()
   //Reformat a string back into html
   function unhtmlentities ($string){
  	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
  	$trans_tbl = array_flip ($trans_tbl);
  	return strtr ($string, $trans_tbl);
   }
   //Funtion dbConnect()
   //Connects to the database using the access names defined on the config file
   function dbConnect(){
   	global $hostName;
  	global $userName;
   	global $password;
   	return mysql_connect($hostName,$userName,$password);
   }
   //Function request
   //Send an sql query to the database and returns the result
   function request($query, $link){
   	global $dbName;
   	return mysql_db_query($dbName,$query,$link);
   }
?>
