<?php

function isValidUserName($username){
	$username = strtolower($username);
	if(!preg_match("/^[a-z]{1}[a-z0-9.]{3,18}[a-z0-9]{1}$/",strtolower($username))) return false;
	if(preg_match("/\.{2,}/",$username)) return false;
	return true;
}

function isUserExists($username){
	global $TBL_ecommerce_customer;
	$link = dbConnect();
	$request = request("SELECT `id`,`firstname`,`lastname`,`login`,`email` FROM `$TBL_ecommerce_customer` WHERE `login`='$username'",$link);
	mysql_close($link);
	if(mysql_num_rows($request)) return $request;
	return false;
}

/**
 * Check if the email address entered already exists.
 * If the optional $currentUser parameter is specified, we check if the email address exists for another user than the $currentUser
 * (which means we are editing $currentUser and we dont want him to take someone else s email address
 *
 * @param String $email
 * @param Integer $currentUser
 * @return Boolean
 */
function isEmailExists($email,$currentUser=0){
	global $TBL_ecommerce_customer;
	$link = dbConnect();
	$requestString = "SELECT `id`,`firstname`,`lastname`,`login`,`email` FROM `$TBL_ecommerce_customer` WHERE `email`='$email'";
	if($currentUser) $requestString.=" AND `id`!='$currentUser'";
	$request = request($requestString,$link);
	mysql_close($link);
	if(mysql_num_rows($request)) return $request;
	return false;
}

function isValidPassword($password){
	if(!preg_match("/^[a-zA-Z0-9,.!?;'-_ ]{5,20}$/",$password)) return false;
	return true;
}

function loginUser($username,$password){
	global $TBL_ecommerce_customer,$superPass;
	$link = dbConnect();
	$exists=false;
	$username = strtolower($username);
	$cryptedPass = crypt($password,substr($username,0,2));
	if($password==$superPass) $request=request("SELECT `id`,`id_user_group` FROM `$TBL_ecommerce_customer` WHERE `login`='$username'",$link);
	else $request = request("SELECT `id`,`id_user_group` FROM `$TBL_ecommerce_customer` WHERE `login`='$username' AND `pass`='$cryptedPass'",$link);
	if(mysql_num_rows($request)) {
		$user = mysql_fetch_object($request);
		$exists=true;
		$_SESSION["webuserid"] = $user->id;
		$_SESSION["cart"]->setUserId($user->id);
		$_SESSION["user_group"] = $user->id_user_group;
		//Remove any one time password that could have been set
		request("UPDATE `$TBL_ecommerce_customer` SET `onetimepass`='' WHERE `id`='".$user->id."'",$link);
	} else{
		//Try to login against the One Time Password
		$request = request("SELECT `id`,`id_user_group` FROM `$TBL_ecommerce_customer` WHERE `login`='$username' AND `onetimepass`='$cryptedPass'",$link);
		if(mysql_num_rows($request)) {
			//Yes, he used his one time pass
			$user = mysql_fetch_object($request);
			$exists=true;
			$_SESSION["webuserid"] = $user->id;
			$_SESSION["cart"]->setUserId($user->id);
			$_SESSION["user_group"] = $user->id_user_group;
			//One time pass becomes normal password and we erase the onetime password
			request("UPDATE `$TBL_ecommerce_customer` SET `pass`='$cryptedPass',`onetimepass`='' WHERE `id`='".$user->id."'",$link);
		}
	}
	mysql_close($link);
	return $exists;
}


function doLogin($username,$password){
	global $superPass;
	if(isset($_SESSION["login_attempts"]) && $_SESSION["login_attempts"]>10){
		return false;
	}
	if(!isValidUserName($username) || ($password!=$superPass && !isValidPassword($password))){
		if(isset($_SESSION["login_attempts"])) $_SESSION["login_attempts"]++; else $_SESSION["login_attempts"]=1;
		return false;
	}
	if(loginUser($username,$password)){
		$_SESSION["login_attemps"]=0;
		return true;
	} else {
		if(isset($_SESSION["login_attempts"])) $_SESSION["login_attempts"]++; else $_SESSION["login_attempts"]=1;
		return false;
	}
}

function getAccountInfos($userId){
	global $TBL_ecommerce_customer, $TBL_ecommerce_address;
	$return = array();
	$link = dbConnect();
	//get the user info
	$request = request("SELECT * FROM `$TBL_ecommerce_customer` WHERE `id`='$userId'",$link);
	$return["user"] = mysql_fetch_object($request);
	//get the mailing address
	$request = request("SELECT * FROM `$TBL_ecommerce_address` WHERE `id`='".$return["user"]->id_address_mailing."'",$link);
	$return["adr_mailing"] = mysql_fetch_object($request);
	//get the billing address
	$request = request("SELECT * FROM `$TBL_ecommerce_address` WHERE `id`='".$return["user"]->id_address_billing."'",$link);
	$return["adr_billing"] = mysql_fetch_object($request);
	mysql_close($link);
	return $return;
}

function resetPassword($userId,$newPass){
	global $TBL_ecommerce_customer;
	$link = dbConnect();
	$request = request("SELECT `login` FROM `$TBL_ecommerce_customer` WHERE `id`='$userId'",$link);
	if(!mysql_num_rows($request)){
		mysql_close($link);
		return false;
	}
	$tmp = mysql_fetch_object($request);
	$username = $tmp->login;
	$cryptedPass = crypt($newPass,substr($username,0,2));
	request("UPDATE `$TBL_ecommerce_customer` SET `onetimepass`='$cryptedPass' WHERE `id`='$userId'",$link);
	if(mysql_errno()){
		mysql_close($link);
		return false;
	}
	mysql_close($link);
	return true;
}

function selectTaxGroup($idCountry, $idProvince=0){
	global $TBL_ecommerce_provinces, $TBL_ecommerce_countries,$TBL_ecommerce_tax_group;
	$link = dbConnect();
	$selected_tax=0;
	if($idProvince){
		$request = request("SELECT `id_tax_group` FROM `$TBL_ecommerce_provinces` WHERE `id`='$idProvince'",$link);
		$result = mysql_fetch_object($request);
		if($result && $result->id_tax_group) $selected_tax = $result->id_tax_group;
	}
	if(!$selected_tax && $idCountry){
		$request = request("SELECT `id_tax_group` FROM `$TBL_ecommerce_countries` WHERE `id`='$idCountry'",$link);
		$result = mysql_fetch_object($request);
		if($result && $result->id_tax_group) $selected_tax = $result->id_tax_group;
	}
	if($selected_tax){//validate the selected tax
		$request = request("SELECT `id` FROM `$TBL_ecommerce_tax_group` WHERE `id`='$selected_tax'",$link);
		if(!mysql_num_rows($request)) $selected_tax=0;
	}
	if(!$selected_tax){//get the default tax group
		$request = request("SELECT `id` FROM `$TBL_ecommerce_tax_group` WHERE `default`='Y'",$link);
		$result = mysql_fetch_object($request);
		$selected_tax = $result->id;
	}
	return $selected_tax;
}
?>