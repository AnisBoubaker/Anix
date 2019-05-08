<?
require_once("./custom/config.php");
require_once("./dbConfig.php");
require_once("./class/AnixSession.class.php");
ini_set('session.save_handler', 'user');
session_set_save_handler(array('AnixSession', 'open'),
                         array('AnixSession', 'close'),
                         array('AnixSession', 'read'),
                         array('AnixSession', 'write'),
                         array('AnixSession', 'destroy'),
                         array('AnixSession', 'gc')
                         );
if (session_id() == "") session_start();
if(!isset($_SESSION["userid"])) {
	Header("Location: ./login.php");
	exit();
}
$openerName = crypt(session_id());
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title><?php echo _("Anix - Authentifcation"); ?></title>
  <link rel="stylesheet" href="./css/anix.css"></link>
  <script language="JavaScript" src="./js/general.js"></script>
  <script language="javascript">
  <!--
  var sorry="<?php echo _("Désolé, cette action n'est pas permise."); ?>";
  function click(e)
  {
  	if (document.all)
  	{
  		if (event.button == 2)
  		{
  			//alert(sorry);
  			return false;
  		}
  	}
  	if (document.layers)
  	{
  		if (e.which == 3)
  		{
  			alert(sorry);
  			return false;
  		}
  	}
  }
  if (document.layers)
  {
  	document.captureEvents(Event.MOUSEDOWN);
  }
  document.onmousedown=click;
  window.name="<?=session_id()?>";
  openWins = new Array();
  function newWindow(fromChild){
  	if(fromChild) openWins[openWins.length] = window.open("../new_window.php", "", "height="+(window.screen.availHeight-10)+", width="+(window.screen.availWidth-10)+", top=0, left=0, toolbar=no, status=no, scrollbars=yes, location=no, menubar=no, directories=no, resizable=yes");
  	else openWins[openWins.length] = window.open("./new_window.php", "", "height="+(window.screen.availHeight-10)+", width="+(window.screen.availWidth-10)+", top=0, left=0, toolbar=no, status=no, scrollbars=yes, location=no, menubar=no, directories=no, resizable=yes");
  }
  function closeAll(){
  	for(i=0; i<openWins.length; i++) {
		if (openWins[i] && !openWins[i].closed) {
			openWins[i].close();
		}
	}
	window.focus();
	window.location='./login.php';
  }
  //-->
  </script>
</head>
<body style='background:url(./images/bgd_login.jpg);background-repeat:repeat-x;background-color:#696a6c;padding-top:150px;text-align:center;'>
  <div style='width:250px;height:200px;background:url(./images/logged_in.jpg);background-repeat:no-repeat;background-color:#ffffff;padding:10px 5px 10px 195px;margin:auto;border-bottom:3px solid #000000;border-right:3px solid #000000;'>
		<div style='float:right;padding:0 15px 20px 0;'><img src='./images/logo.jpg' alt='Anix by Cibaxion' /></div>
		<div style='clear:both;font:12px Arial;padding:0 5px 0 0;width:250px;'>
			<p style='text-align:center; color:#2e71a5;'><b><?php echo _("Merci, vous êtes authentifié(e)"); ?></b><br /><br />
	       <?php echo _("Cliquez sur le bouton ci-dessous pour ouvrir votre interface d'administration."); ?><br /><br />
	       <input type='button' style='background:#2e71a5;color:#ffffff;width:130px;' value='Ouvrir Anix' onclick='javascript:newWindow(false);' />
	       </p>
       </div>
  </div>
  <script language="javascript">
  	//alert(window.name);
    //-->
  </SCRIPT>
  </script>
</body>
</html>
