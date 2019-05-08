<?
ini_set("include_path",".:../");
ini_set("display_errors",1);
set_error_handler("customError");
require("custom/config.php");
require("dbConfig.php");
//Init the messages object
$ANIX_messages = new AnixMessenger();



if($ANIX_environment=="development"){
	#######################################################
	# DEVELOPMENT ENVIRONMENT SPECIFIC CONFIGURATIONS
	#######################################################
	// Anix main folder
	$web_path = "/anixv2/anix";
	//folder for anix languages files
	$folder_locales="/locales";
	//path of the WYSWYG editor
	$folder_editor = "/3rdparty/editor";
	$folder_calendar = "/3rdparty/calendar";
	//The relative location of the locales for the website
	$folder_webLocalesRoot = "../weblocalesroot/";
	//URI path to the managed website (usually / but may be something else...)
	$managedWebsite_URIPath="/anixv2";//NO TRAILING SLASH
} elseif($ANIX_environment=="test"){
	#######################################################
	# TEST ENVIRONMENT SPECIFIC CONFIGURATIONS
	#######################################################
	// Anix main folder
	$web_path = "/cibaxion_anixv2/anix";
	//folder for anix languages files
	$folder_locales="/locales";
	//path of the WYSWYG editor
	$folder_editor = "/3rdparty/editor";
	$folder_calendar = "/3rdparty/calendar";
	//The relative location of the locales for the website
	$folder_webLocalesRoot = "../weblocalesroot/";
	//URI path to the managed website (usually / but may be something else...)
	$managedWebsite_URIPath="/";//NO TRAILING SLASH
}else {
	#######################################################
	# PRODUCTION ENVIRONMENT SPECIFIC CONFIGURATIONS
	#######################################################
	// Anix main folder
	$web_path = "/anix";
	//folder for anix languages files
	$folder_locales="/locales";
	//path of the WYSWYG editor
	$folder_editor = "/3rdparty/editor";
	$folder_calendar = "/3rdparty/calendar";
	//The relative location of the locales for the website
	$folder_webLocalesRoot = "../weblocalesroot/";
	//URI path to the managed website (usually / but may be something else...)
	$managedWebsite_URIPath="/";//NO TRAILING SLASH
}


#######################################################
# INITIALISATIONS
#######################################################
ini_set('session.save_handler', 'user');
session_set_save_handler(array('AnixSession', 'open'),
array('AnixSession', 'close'),
array('AnixSession', 'read'),
array('AnixSession', 'write'),
array('AnixSession', 'destroy'),
array('AnixSession', 'gc')
);
if (session_id() == "") session_start();

$_SESSION["managedWebsite_URIPath"]=$managedWebsite_URIPath;

if(!isset($fromLogin) && !isset($_SESSION["userid"])) {
	Header("Location: ../login.php");
	exit();
}
$action="";
$errors=0;
$errMessage="";
$message="";
if(isset($_SESSION["used_language2"])) 
	$used_language=$_SESSION["used_language2"];
if(isset($_SESSION["anix_user"])) 
	$anix_username=$_SESSION["anix_user"];
if(isset($_SESSION["used_languageid2"])) 
	$used_language_id=$_SESSION["used_languageid2"];
else 
	$used_language_id=1;
putenv("LC_ALL=$used_language");
setlocale(LC_ALL, $used_language);
bindtextdomain("messages", "../locales");
textdomain("messages");
bind_textdomain_codeset("messages", 'UTF-8');

#######################################################
# GENERAL FUNCTIONS
#######################################################
//Function __autoload
//Loads the classes if not explicitly included
function __autoload($class_name){
	if(strpos($_SERVER['REQUEST_URI'],"login.php") || strpos($_SERVER['REQUEST_URI'],"new_window.php")) require_once("./class/$class_name.class.php");
	else require_once("../class/$class_name.class.php");
}

//Funtion htmlentities()
//Reformat a string back into html
function unhtmlentities ($string){
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
	$trans_tbl = array_flip ($trans_tbl);
	return utf8_encode(strtr (utf8_decode($string), $trans_tbl));
	//return strtr ($string, $trans_tbl);
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
	global $TBL_admin_login;
	session_start();
	$sessionId=session_id();
	// see if they're logged in
	$result = request("SELECT id_admin
    	FROM $TBL_admin_login
    	WHERE id_session = '$sessionId'",$link) or die(mysql_error());
	//if not, send them to the login page
	if (!mysql_num_rows($result)) {
		if($fromIndex) Header("Location: ./login.php");
		else Header("Location: ../login.php");
		mysql_close($link);
		exit;
	} else {
		$user = mysql_fetch_object($result);
		$_SESSION["userid"]=$user->id;
	}
}

function getWhenSessionExpires(){
	global $session_lifetime;
	return date('Y-m-d H:i:s' , time()+$session_lifetime);
}

function loginPassValid($text){
	return preg_match('/[a-zA-Z0-9]+/', $text); //
}
//Login the user by checking his username and password
//Returns an error code :
// 1 : Success
//-1 : Login invalid
//-2 : password invalid
//-3 : login/password mismatch
//-4 : Account locked
//-5 : language invalid
function login($username,$password,$language,$link){
	global $TBL_admin_admin,$TBL_admin_login,$TBL_gen_languages;
	if(!loginPassValid($username)) return -1;
	if(!loginPassValid($password)) return -2;
	$crypted=crypt($password,substr($username,0,2));

	$request=request("SELECT $TBL_admin_admin.`id`,$TBL_admin_admin.`login`,$TBL_admin_admin.`locked`,$TBL_gen_languages.`locales_folder`,$TBL_gen_languages.`used` language_used,$TBL_gen_languages.id id_language
                        FROM $TBL_admin_admin,$TBL_gen_languages
                        WHERE `login`='$username' AND `password`='$crypted'
                        AND $TBL_admin_admin.`id_language`=$TBL_gen_languages.`id`",$link);

	if(!mysql_num_rows($request)) return -3;
	$row=mysql_fetch_object($request);
	if($row->locked=="Y") return -4;
	//Time to start the session
	//session_start();
	$sessionId=session_id();
	$_SESSION["userid"]=$row->id;
	$_SESSION["anix_user"]=$row->login;
	if($language) {
		$request2=request("SELECT id,locales_folder FROM $TBL_gen_languages WHERE id='$language' and used='Y'",$link);
		if(!mysql_num_rows($request2)) return -5;
		$row2=mysql_fetch_object($request2);
		$_SESSION["used_language2"]=$row2->locales_folder;
		$_SESSION["used_languageid2"]=$row2->id;
	} else {
		if($row->language_used=="Y") {
			$_SESSION["used_language2"]=$row->locales_folder;
			$_SESSION["used_languageid2"]=$row->id_language;
		}
		else {
			$request2=request("SELECT `id`,`locales_folder` FROM $TBL_gen_languages WHERE `default`='Y'",$link);
			if(!mysql_num_rows($request2)) return -5;
			$row2=mysql_fetch_object($request2);
			$_SESSION["used_language2"]=$row2->locales_folder;
			$_SESSION["used_languageid2"]=$row2->id;
		}
	}
	request("DELETE FROM $TBL_admin_login WHERE `id_admin`='".$_SESSION["userid"]."'",$link);
	request("INSERT INTO $TBL_admin_login (`id_admin`,`session_expires`,`id_session`)
              VALUES ('".$_SESSION["userid"]."','".getWhenSessionExpires()."','".$sessionId."')",$link);
	return 1; //Success !!
}

function customError($errno, $errstr, $errfile, $errline, $errcontext){
	global $ANIX_messages,$ANIX_environment;
	if(!($errno & E_ALL & ~ E_STRICT)) return;

	//if($ANIX_environment=="development") $reported=false; //We do not report in development environment
	$reported = bugReport($errno,$errstr,$errfile,$errline,$errcontext);

	$userMessage = "";
	if($errno & E_NOTICE){
		if($ANIX_environment!="development") $userMessage.= _("Le système a émis un avertissement concernant un problème technique mineur.")."<br />"._("Ceci peut n'avoir aucune incidence sur le fonctionnement d'Anix.");
		else $userMessage.="<b>NOTICE:</b> $errstr <br /><b>File:</b> $errfile <br /><b>Line:</b> $errline";
	} else {
		if($ANIX_environment!="development") $userMessage.=_("Une erreur système s'est produite").":<br />[$errno] $errstr";
		else $userMessage.="<b>EROOR:</b> $errstr <br /><b>File:</b> $errfile <br /><b>Line:</b> $errline";
	}
	$userMessage.="<br /><br />";
	if($reported) $userMessage.=_("Un rapport vient d'être transmis à CIBAXION concernant cette erreur.");
	else $userMessage.=_("CIBAXION a déjà été informée de ce problème.");
	$ANIX_messages->addWarning($userMessage);
}

function emailValid($email){
	return ((preg_match('/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $email)) ||
	(preg_match('/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?)$/',$email)));
}

function bugReport($errno, $errstr, $errfile, $errline, $errcontext){
	global $TBL_gen_bugs_reporting,$AdministredSiteName,$ANIX_environment;

	$link=dbConnect();
	$request = request("SELECT `id` FROM `$TBL_gen_bugs_reporting` WHERE `err_num`='$errno' AND `err_file`='$errfile' AND `err_line`='$errline'",$link);
	if(mysql_num_rows($request)){
		return false;
	}
	//report the bug into DB
	request("INSERT INTO `$TBL_gen_bugs_reporting` (`err_num`,`err_str`,`err_file`,`err_line`,`err_context`,`report_date`) VALUES ('".mysql_escape_string($errno)."','".mysql_escape_string($errstr)."','".mysql_escape_string($errfile)."','".mysql_escape_string($errline)."','$errcontext',NOW())",$link);

	$idBug = mysql_insert_id($link);

	//send the bug by Email
	if($idBug && $ANIX_environment!="development"){
		$dest = "bugs_anix2@dev.cibaxion.com";
		$subject="BUG: [$idBug]$AdministredSiteName";
		$message="";
		$message.="[$errno] $errfile (Line:$errline)\n\n";
		$message.=$errstr."\n\n";
		$message.="ERROR CONTEXT\n";
		$message.="-------------------------------------\n";
		$message.=print_r($errcontext,true);

		$headers = "From: ANIX <anix@dev.cibaxion.com>\n";
		$headers .= "X-Sender: <anix@dev.cibaxion.com>\n";
		@mail(utf8_decode($dest),utf8_decode($subject),utf8_decode($message),utf8_decode($headers));
	}
	return true;
}


function setTitleBar($string){
	echo "<script type=\"text/javascript\">";
	echo "setTitleBar(\"".$string.":"."\");";
	echo "</script>\n";
}
/**
 * Print action buttons (ex.: validate, cancel, back, ...) in the buttons bar
 * The buttons parameter must be an array like buttons[0]["type"]="validate";buttons[0]["link"]='../';
 *
 * @param Array $buttons
 */
function printButtons($buttons){
	$str = "";
	foreach($buttons as $button){
		switch($button["type"]){
			case "validate":
				$str="<a class='button' href=\\\"".$button["link"]."\\\"><img src='../images/icon_validate.jpg' alt='"._("Valider")."' />"._("Valider")."</a>".$str;
				break;
			case "select":
				$tmp=$button["text"].":";
				$tmp.="<select id='".$button["id"]."' name='".$button["id"]."'>";
				foreach($button["choices"] as $value=>$text){
					$tmp.="<option value=\\\"$value\\\">$text</option>";
				}
				$tmp.="</select>";
				$tmp.="<input type='button' value=\\\"OK\\\" onclick=\\\"".$button["link"]."\\\" />";
				$str=$tmp.$str;
				break;
			case "cancel":
				$str="<a class='button' href='".$button["link"]."'><img src='../images/icon_cancel.jpg' alt='"._("Annuler")."' />"._("Annuler")."</a>".$str;
				break;
			case "back":
				$str="<a class='button' href='".$button["link"]."'><img src='../images/icon_back.jpg' alt='"._("Retour")."' />"._("Retour")."</a>".$str;
				break;
			case "additem":
				$str="<a class='button' href='".$button["link"]."'><img src='../images/icon_additem.jpg' alt='"._("Retour")."' />"._("Ajouter")."</a>".$str;
				break;
		}
	}
	$str="<script type=\"text/javascript\">\n document.getElementById('buttons_bar').innerHTML=\"".$str."\";</script>";
	echo $str;
}

/**
 * Print language buttons
 *
 * @param Array $languages
 */
function printLanguageToggles($link=0){
	global $TBL_gen_languages,$used_language_id;
	if($link) $insideLink=$link;
	else $insideLink=dbConnect();
	$languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used = 'Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default", $insideLink);
	$first=true;
	$str = "";
	while ($row_languages=mysql_fetch_object($languages)){
		if($first) {$first=false; $selected=true; $className="language_selector_on"; $flag="flag_on.jpg";}
		else { $selected=false; $className="language_selector"; $flag="flag_off.jpg";}
		$str = "<a id='selector_".$row_languages->id."' class='$className' href='javascript:void(0);' onClick=\\\"javascript:toggleLanguage('".$row_languages->id."');\\\"><img src='../locales/".$row_languages->locales_folder."/images/".$flag."' border='0'> ".$row_languages->name."</a>".$str;
	}
	$str="<script type=\"text/javascript\">\n document.getElementById('language_selector_bar').innerHTML=\"".$str."\";</script>";
	echo $str;
	if(!$link) mysql_close($insideLink);
}
?>
