<?php
require_once("./config.php");
if(isset($_SESSION["current_module"])){
	Header("Location: ./".$modules[$_SESSION["current_module"]]["folder"]."/");
	exit();
}
foreach($modules as $name => $module){
	if(!isset($first)) $first=$name;
	if(isset($module["default"]) && $module["default"]."/"){
		Header("Location: ./".$module["folder"]);
		exit();
	}
}
?>