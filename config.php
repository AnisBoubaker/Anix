<?
 #######################################################
 #  Site wide inclusions
 #######################################################

 if($_SERVER['SERVER_NAME']=="localhost"){
 	#######################################################
	# DEVELOPMENT ENVIRONMENT SPECIFIC CONFIGURATIONS
	#######################################################
		//DB Configuration
		$hostName = "localhost";
		$userName = "root";
		$password ="";
		$dbName = "cgc";
		// Anix main folder
		$web_path = "/cgc_web/anix";
		//folder for anix languages files
		$folder_locales="/cgc_web/locales";
		//path of the WYSWYG editor
		$folder_editor = "/3rdparty/editor";
		$folder_calendar = "/3rdparty/calendar";
		//The relative location of the locales for the website
		$folder_webLocalesRoot = "../weblocalesroot/";
 } else {
	#######################################################
	# PRODUCTION ENVIRONMENT SPECIFIC CONFIGURATIONS
	#######################################################
		//DB Configuration
		$hostName = "localhost";
		$userName = "cibaxionsite2";
		$password ="";
		$dbName = "cibaxionsite2";

		// Anix main folder
		$web_path = "/anix";
		//folder for anix languages files
		$folder_locales="/locales";
		//path of the WYSWYG editor
		$folder_editor = "/3rdparty/editor";
		$folder_calendar = "/3rdparty/calendar";
		//The relative location of the locales for the website
		$folder_webLocalesRoot = "../weblocalesroot/";
 }

 #######################################################
 # DB Configuration
 #######################################################

	//Default language used
	$idLanguage = 1; //french


 //DEBUG MODE
 //ini_set('display_errors', 1);
 //error_reporting(E_ALL^E_NOTICE);

 #######################################################
 # SUPER CONFIGURATION
 #######################################################
   //Currency symbol used on Anix
   $currency_symbol = "\$CAN +Tx";
   //Table containing information about available modules
   $modules = array();
   $modules["General"]=array("name" => "content", "folder" => "content");
   $modules["Catalog"]=array("name" => "catalog", "folder" => "catalogue");
   $modules["News"]=array("name" => "news", "folder" => "news");
   $modules["FAQ"]=array("name" => "faq", "folder" => "faq");
   $modules["Admin"]=array("name" => "admin", "folder" => "admin");
   //Default language used by anix
   $used_language = "en_CA";
   //Default module
   $default_module="Catalog";
   //Name of the website to administer with anix
   $AdministredSiteName = "www.airtechni.com";
   //Check if the user does not logout properly ?
   $check_logout = false;
   //How many times we allow a user to not logout properly before we locking him?
   $max_nb_not_logout = 5;
   //Life time of a session IN SECONDS
   $session_lifetime = 3600;
   //Put yes if the site is for demo (disable accounts modifications)
   $anix_demo_mode = false;

   $defaultTitle = "CIBAXION - Le specialiste de l'informatique pour PME au Quebec";

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
   $TBL_catalogue_info_price_groups="catalogue_info_price_groups";
   $TBL_catalogue_product_prices="catalogue_product_prices";
   $TBL_catalogue_attachments="catalogue_attachments";
   $TBL_catalogue_brands="catalogue_brands";
   $TBL_catalogue_product_options ="catalogue_product_options";
   $TBL_catalogue_info_options="catalogue_info_options";
   $TBL_catalogue_product_option_choices="catalogue_product_option_choices";
   $TBL_catalogue_info_choices="catalogue_info_choices";
   $TBL_catalogue_featured = "catalogue_featured";
   $TBL_catalogue_info_featured = "catalogue_info_featured";
   //Folders (relative with no slash in the beginning)
   $CATALOG_folder_images ="./catalogue_images/";
   $CATALOG_folder_attachments ="./catalogue_attachments/";
   //Default editor value (useful to setup the default background color in a table for example....)
   $CATALOG_editor_default_value="";
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
   $featuredCategories[1]=array("id" => 1,"name" =>"Banniere Publicitaire","nbAllowed" => -1,
                                "imglarge_maxW" => 250,
                                "imglarge_maxH" => 400,
                                "imgsmall_maxW" => 200,
                                "imgsmall_maxH" => 400);
   $featuredCategories[2]=array("id" => 2,"name" =>"Professionnels:Produits en vedette","nbAllowed" => 10,
                                "imglarge_maxW" => 200,
                                "imglarge_maxH" => 300,
                                "imgsmall_maxW" => 100,
                                "imgsmall_maxH" => 150);
   $featuredCategories[3]=array("id" => 3,"name" =>"Consommateurs:Banniere Publicitaire","nbAllowed" => 2,
                                "imglarge_maxW" => 250,
                                "imglarge_maxH" => 400,
                                "imgsmall_maxW" => 200,
                                "imgsmall_maxH" => 400);
   $featuredCategories[4]=array("id" => 4,"name" =>"Consommateurs:Produits en vedette","nbAllowed" => 10,
                                "imglarge_maxW" => 200,
                                "imglarge_maxH" => 300,
                                "imgsmall_maxW" => 100,
                                "imgsmall_maxH" => 150);
   $featuredField1Name = "Champs 1";
   $featuredField2Name = "Champs 2";
   $FEATURED_editor_default_value ="";
   /**
   *Images max sizes (Nota: original ratio is preserved)
   **/
   // Categories' images
   $CATALOG_image_cat_large_max_width = 500;
   $CATALOG_image_cat_large_max_height = 400;
   $CATALOG_image_cat_small_max_width = 250;
   $CATALOG_image_cat_small_max_height = 200;
   // Products' images
   $CATALOG_image_prd_orig_max_width = 400;
   $CATALOG_image_prd_orig_max_height = 400;
   $CATALOG_image_prd_large_max_width = 200;
   $CATALOG_image_prd_large_max_height = 200;
   $CATALOG_image_prd_small_max_width = 150;
   $CATALOG_image_prd_small_max_height = 100;
   // Brands' images
   $CATALOG_image_brand_large_max_width = 200;
   $CATALOG_image_brand_large_max_height = 100;
   $CATALOG_image_brand_small_max_width = 100;
   $CATALOG_image_brand_small_max_height = 50;
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
   //Default RTF value (useful to setup the default background color in a table for example....)
   $NEWS_editor_default_value="";
 #######################################################
 # FAQ Configurations
 #######################################################
   //DB tables
   $TBL_faq_categories = "faq_categories";
   $TBL_faq_info_categories = "faq_info_categories";
   $TBL_faq_info_faq = "faq_info_faq";
   $TBL_faq_faq = "faq_faq";
   //Default RTF value (useful to setup the default background color in a table for example....)
   $FAQ_editor_default_value="";
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
   //Default RTF value (useful to setup the default background color in a table for example....)

   $PAGES_editor_default_value="";
   //Featured categories
   //Table of element, each element contains the name of the category and
   //the number of items that it may contains
   $pageCategories = array();
   $pageCategories[1]=array("id" => 1,"name" =>"Default","nbAllowed" => -1);

   $menuCategories = array();
   $menuCategories[1]=array("id" => 1,"name" =>"Principal","nbAllowed" => -1,
                                  "img_maxW" => 187,
                                  "img_maxH" => 26,
                                  "nbLevelsAllowed" => 2,
                                  "nbAllowedInSublevels" => 15);
   /*$menuCategories[2]=array("id" => 3,"name" =>"Consommateurs","nbAllowed" => -1,
                                  "img_maxW" => 187,
                                  "img_maxH" => 26,
                                  "nbLevelsAllowed" => 2,
                                  "nbAllowedInSublevels" => 15);*/
 #######################################################
 # Ecommerce Configuration
 #######################################################
 //DB Tables
 $TBL_ecommerce_customer = "ecomm_customer";
 $TBL_ecommerce_address = "ecomm_address";
 $TBL_ecommerce_invoice = "ecomm_invoice";
 $TBL_ecommerce_order = "ecomm_order";
 $TBL_ecommerce_order_item = "ecomm_order_item";
 $TBL_ecommerce_tax = "ecomm_tax";
 $TBL_ecommerce_tax_group = "ecomm_tax_group";
 $TBL_ecommerce_tax_to_group = "ecom_tax_to_group";
 $TBL_ecommerce_tax_items = "ecomm_tax_items";
 $TBL_ecommerce_tax_authority = "ecomm_tax_authority";
 $TBL_ecommerce_tax_group_authority = "ecomm_tax_group_authority";

 $ECOMMERCE_default_country = "Canada";
 $ECOMMERCE_min_login_legth = 6;
 $ECOMMERCE_max_login_legth = 25;
 $ECOMMERCE_min_password_legth = 6;
 $ECOMMERCE_max_password_legth = 15;
 #######################################################
 # Admin Configuration
 #######################################################
    //DB tables
    $TBL_admin_admin = "admin_admin";
    $TBL_admin_groups = "admin_groups";
    $TBL_admin_sessions = "admin_sessions";
 #######################################################
 # INITIALISATIONS
 #######################################################
   session_start();
   if(isset($_GET["idLanguage"])){
   	$link=dbConnect();
   	$request=request("SELECT * FROM `$TBL_gen_languages` WHERE `id`='".$_GET["idLanguage"]."'",$link);
   	if(mysql_num_rows($request)){
   		$language=mysql_fetch_object($request);
   		$_SESSION["used_languageid2"]=$language->id;
   		$_SESSION["used_language2"]=$language->locales_folder;
   	}
   	mysql_close($link);
   }
   $action="";
   $errors=0;
   $errMessage="";
   $message="";
   if(isset($_SESSION["used_language2"])) $used_language=$_SESSION["used_language2"];
   if(isset($_SESSION["used_languageid2"])) $used_language_id=$_SESSION["used_languageid2"];
   else $used_language_id=1;
   //Loading PO language file
   putenv("LC_ALL=$used_language");
   setlocale(LC_ALL, $used_language);
   bindtextdomain("cibaxion_web", "./locales");
   textdomain("cibaxion_web");
   bind_textdomain_codeset("cibaxion_web", 'UTF-8');
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
	mysql_db_query($dbName,"SET NAMES 'utf8'",$link);
   	return mysql_db_query($dbName,$query,$link);
   }
   //Function getDBDate()
   //Returns the current date to store in database.
   //May make a time gap correction
   function getDBDate(){
    return date('Y-m-d H:i:s',time());
   }
   //Function validDate($date)
   //Returne true if the given date is valid. Date must be : YYYY-MM-DD
   function validDate($date){
    return 1;
   }
   //Function isFileProhibited
   //Returns true if the file uploaded is not allowed
   function isFileProhibited($filename) {
  	//$file_exts = array("xlthtml","shtml","xhtml","386","acm","asp","aspx","bas","bat","cmd","sh","cfm","com","css","d","dll","drv","dvb","dwg","eml","exe","ht","hta","js","jse","jsp","msc","msg","mtx","php","php3","php4","php5","pif","reg","res","scf","scr","sct","scx","shtm","html","upx","vba","vbe","vbx","vxd","vxe","wsc","wsf","wsh","xhtm","xlt","xml","xtp","pl","v","pc","vb","bs");
  	$file_exts = array("pdf","doc","gif","gz","jpeg","jpg","png","ppt","rar","tif","tiff","xls","zip");
  	foreach($file_exts as $this_ext) {
  		if (preg_match("/\.$this_ext$/", strtolower($filename))) {
  			return FALSE;
  		}
  	}
  	return TRUE;
   }

   //Function isImageAllowed
   //Returns true if the image uploaded is allowed
   function isImageAllowed($filename) {
  	$file_exts = array("jpg","jpeg","png");
  	foreach($file_exts as $this_ext) {
  		if (preg_match("/\.$this_ext$/", strtolower($filename))){
  			return TRUE;
  		}
  	}
  	return FALSE;
   }

    /*function sessionGo() {
      global $PHPSESSID;
      if (!$PHPSESSID) {
        ini_alter("session.auto_start","1");
        session_start();
        session_register();
      }
    }*/
    function checkUser($fromIndex,$link) {
      global $TBL_admin_sessions;
      session_start();
      $sessionId=session_id();
    	// see if they're logged in
    	$result = request("SELECT id_admin
    	FROM $TBL_admin_sessions
    	WHERE id_session = '$sessionId'",$link) or die(mysql_error());
  		//if not, send them to the login page
  		if (mysql_num_rows($result)) {
	        $user = mysql_fetch_object($result);
  			$_SESSION["webuserid"]=$user->id;
    	} else {
    		$_SESSION["webuserid"]=0;
  	  	}
    }

    function getWhenSessionExpires(){
      global $session_lifetime;
      return date('Y-m-d H:i:s' , time()+$session_lifetime);
    }

    function loginPassValid($text){
      return preg_match('/[a-zA-Z0-9]+/', $text); //
    }

    //user login
    //Returns true if the credentials are correct, false if not.
    function login($username,$password){
    	global $TBL_ecommerce_customer,$TBL_gen_languages;
    	if($username=="" || $password=="" || !loginPassValid($username)) return false;
    	$crypted=crypt($password,substr($username,0,2));
    	$link = dbConnect();
    	$request=request(
    		"SELECT `$TBL_ecommerce_customer`.`id`,
    				`$TBL_ecommerce_customer`.`firstname`,
    				`$TBL_ecommerce_customer`.`lastname`,
    				`$TBL_gen_languages`.`id` id_language,
    				`$TBL_gen_languages`.`locales_folder`
             FROM $TBL_ecommerce_customer,$TBL_gen_languages
             WHERE `login`='$username' AND `password`='$crypted'
             AND $TBL_ecommerce_customer.`language`=$TBL_gen_languages.`id`",$link);
    	if(!mysql_num_rows($request)) return false;
    	$row=mysql_fetch_object($request);
    	$_SESSION["webuser_id"]=$row->id;
    	$_SESSION["webuser_name"]=$row->firstname." ".$row->lastname;
    	$_SESSION["used_languageid2"] = $row->id_language;
    	$_SESSION["used_languege2"] = $row->locales_folder;
    	mysql_close($link);
    }

    function usernameValid($username){
    	global $TBL_ecommerce_customer;
    	$returnCode=-100;
    	if($username=="" || !loginPassValid($username)) return -1;
    	$request = request(
    		"SELECT login FROM `$TBL_ecommerce_customer` WHERE login='$username'"
    		,$link);
    	if(mysql_numrows($request)) $returnCode=0;
    	else $returnCode=1;
    	mysql_close($link);
    	return $returnCode;
    }

    function emailValid($email){
    return ((preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email)) ||
        (preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/',$email)));
   }

   function cleanWhoisCache(){
	  	//global $_SESSION;
	  	if(!isset($_SESSION["whois_cache"])) $_SESSION["whois_cache"]=array();
	  	foreach($_SESSION["whois_cache"] as $key => $domain_checked){
	    	if($domain_checked["check_date"]<date('Y-m-d H:i:s',time()-(20 * 60))){
	    		unset($_SESSION["whois_cache"][$key]);
	    	}
	  	}
  	}

  	function script_format($string){
  		$string=str_replace("&nbsp;"," ",$string);
  		$string=str_replace("<br />","\n",$string);
  		$string=str_replace("&lt;","<",$string);
  		$string=str_replace("&gt;",">",$string);
  		$string=str_replace("&quot;","\"",$string);
  		$string=str_replace("&#039;","\'",$string);
  		return $string;
  	}

  	function formatStringForURL($input){
        $input = unhtmlentities($input);
        $input = html_entity_decode($input,ENT_QUOTES);
        $input = strtolower($input);
        $input = utf8_encode($input);
        $input = substr($input,0,50); // 50 cars maximum
        $patterns       = array("/é|è|ê|ë/","/à|á|â|ä/","/î|ï/","/ò|ó|ô|ö/","/ù|ú|û|ü/","/ý|ÿ/","/\s|\W|(_+)/","/(_+)/");
        $replacement = array("e"            ,"a"               ,"i"       ,"o"              ,"u"          ,"y"      ,"_"                   ,"_");
        $output = preg_replace($patterns ,$replacement , $input);
        $output = rtrim($output,"_");
        //$output = utf8_encode($output);
        return $output;
   }

   function getFakeURL($append,$str,$id){
        $str = formatStringForURL($str);
        $str.="_".$append.$id.".php";
        return $str;
   }

   function printToolBox($printable, $sendable, $help){
   		global $used_language;
   		$str="";
   		$str.="<div id='toolbox'>\n";
   		if($printable) $str.="<a id='print' href='?print' rel='external'>Print</a>";
   		if($sendable){
   			$str.="<a id='send' href=\"javascript:void(0);\" onclick=\"sendPage();\">Send</a>";
   			$_SESSION["current_url"] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
   		} else unset($_SESSION["current_url"]);
   		if($help) $str.="<a id='help' href=\"javascript:void(0);\" onclick=\"javascript:showHideHelpForm('".$used_language."')\">"._("Help")."</a>";
   		$str.="</div>\n";
   		$str.="";
   		return $str;
   }
?>
