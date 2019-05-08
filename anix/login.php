<?
  $fromLogin=true; //Used by config !!
  include("./config.php");
  $link=dbConnect();
  if(isset($_POST["action"])){
	  $action=$_POST["action"];
	} else $action="";
?>
<?
  if($action=="login"){
    if(!isset($_POST["log1"])){
      $errors++;
      $errMessage.="Login et/ou mot de passe invalide(s)";
    }
    if(!$errors && !isset($_POST["pass1"])){
      $errors++;
      $errMessage.="Login et/ou mot de passe invalide(s)";
    }
    if(!$errors){
      $res=login($_POST["log1"],$_POST["pass1"],$_POST["language"],$link);
      if($res<0){
        $errors++;
        $errMessage.="Login et/ou mot de passe invalide(s)";
      }
    }
    if(!$errors){
      Header("Location: ./");
      mysql_close($link);
      exit();
    }
  }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Anix - Authentifcation</title>
  <link rel="stylesheet" href="./css/anix.css"></link>
</head>
<body style='background:url(./images/bgd_login.jpg);background-repeat:repeat-x;background-color:#696a6c;padding-top:150px;text-align:center;'>
  <form action='./login.php' method='post' enctype='multipart/form-data'>
  <div style='width:290px;height:200px;background:url(./images/login.jpg);background-repeat:no-repeat;background-color:#ffffff;padding:10px 5px 10px 155px;margin:auto;border-bottom:3px solid #000000;border-right:3px solid #000000;'>
		<div style='float:right;padding:0 15px 20px 0;'><img src='./images/logo.jpg' alt='Anix by Cibaxion' /></div>
		<table style='clear:both;'>
		<tr>
			<td style='color:#2e71a5;text-align:right;' nowrap='nowrap'><b>Login / Utilisateur</b>&nbsp;</td>
			<td><input name='log1' type='text'  style='width:130px;'<?php
	          if($action=="login") echo " value='".$_POST["log1"]."'"
	        ?> /></td>
		</tr>
		<tr>
	    	<td style='color:#2e71a5;text-align:right;' nowrap='nowrap'><b>Password / Mot de passe</b>&nbsp;</td>
	        <td><input name='pass1' type='password' style='width:130px;' /></td>
	    </tr>
	    <tr>
	    	<td style='color:#2e71a5;text-align:right;' nowrap='nowrap'><b>Language / Langue</b>&nbsp;</td>
	        <td><select name='language' style='width:130px;'>
	          <option value='0'>Default / Défaut</option>
	          <?
	            $request=request("SELECT id,name FROM $TBL_gen_languages WHERE used='Y' ORDER BY name",$link);
	            while($language=mysql_fetch_object($request)){
	              echo "<option value='".$language->id."'";
	              if($action=="login" && $_POST["language"]==$language->id) echo " selected='selected'";
	              echo ">".$language->name."</option>";
	            }
	          ?>
	        </select></td>
	    </tr>
	    <tr>
	        <td>&nbsp;</td>
	        <td><br />
	          <input style='background:#2e71a5;color:#ffffff;width:130px;' type='submit' value='Connect / Connexion'>
	        </td>
	    </tr>
		</table>
  </div>
  <input type='hidden' name='action' value='login'>
  </form>
</body>
</html>
<?
  mysql_close($link);
?>
