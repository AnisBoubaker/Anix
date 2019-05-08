<?php
function dbConnect(){
	return mysql_connect("localhost","root","",true);
}
function request($query, $link, $old){
	if($old) $dbName="numeridog_old";
	else $dbName="anixv2";
	mysql_db_query($dbName,"SET NAMES 'utf8'",$link);
	return mysql_db_query($dbName,$query,$link);
}

$dbLink = dbConnect();

include("./import_categories.php");
?>