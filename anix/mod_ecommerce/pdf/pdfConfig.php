<?php
require_once("../../dbConfig.php");
require_once("../../custom/config.php");
require_once("../../custom/pdfConfig.php");
require_once("../../class/Core/Session.class.php");
require_once('../../3rdparty/ezpdf/class.ezpdf.php');

ini_set('session.save_handler', 'user');
session_set_save_handler(array('AnixSession', 'open'),
                         array('AnixSession', 'close'),
                         array('AnixSession', 'read'),
                         array('AnixSession', 'write'),
                         array('AnixSession', 'destroy'),
                         array('AnixSession', 'gc')
                         );
if (session_id() == "") session_start();

if(!isset($fromLogin) && !isset($_SESSION["userid"])) {
    Header("Location: ../../login.php");
    exit();
}
$action="";
$errors=0;
$errMessage="";
$message="";
if(isset($_SESSION["used_language2"])) $used_language=$_SESSION["used_language2"];
if(isset($_SESSION["anix_user"])) $anix_username=$_SESSION["anix_user"];
if(isset($_SESSION["used_languageid2"])) $used_language_id=$_SESSION["used_languageid2"];
else $used_language_id=1;
putenv("LC_ALL=$used_language");


function unhtmlentities($string){
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    $trans_tbl = array_flip ($trans_tbl);
    return strtr ($string, $trans_tbl);
}

function id_format($id){
    $return="$id";
    $count = strlen($return);
    if($count<5) $padding = 5-$count;
    for($i=0;$i<$padding;$i++) $return ="0".$return;
    return $return;
}

//Function to count the number of lines in a string. $str is the string, $limit is the maximum length of a line.
function countLines($str,$limit){
    $str = preg_replace('[<.*?.>]', "", $str); //remove html tags
    $lines=explode("\n",$str);
    //$nbLines = count($lines);
    $nbLines=0;
    foreach($lines as $line){
        $length=strlen($line);
        $nbLines+=intval($length/$limit)+1;
        if($length%$limit==0) $nbLines--;
    }
    return $nbLines;
}
?>
