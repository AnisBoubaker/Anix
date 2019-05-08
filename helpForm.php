<?php
	require_once("./config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title><?php _("Question sent") ?></title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/layout.css" />
</head>
<body style='background:#d1e6f2;padding:10px;text-align:left;'>
<script type="text/javascript">
	if(!window.opener || window.opener.closed) window.close();
</script>
<?php
if(isset($_POST["helpFormName"])){
	$name = $_POST["helpFormName"];
	$email = $_POST["helpFormEmail"];
	$phone = $_POST["helpFormPhone"];
	$messageForm = $_POST["helpFormMessage"];
	$page = $_POST["helpFormPage"];

	$subject="WWW: Help requested about a page on CGC's website";
	$message="";
	$message.=_("Visitor information").":\n";
	$message.="-----------------------\n";
	$message.=_("Name").": ".$name."\n";
	$message.=_("Phone").": ".$phone."\n";
	$message.=_("Email").": ".$email."\n\n";
	$message.="-------------------------------------\n";
	$message.=$messageForm;
	$message.="\n\n-------------------------------------\n";
	$message.=_("Sent from page").": ".$page;
	$headers = "From: ".$name." <".$email.">\n";
	$headers .= "X-Sender: <".$email.">\n";
	$emailok=true;
	if (mail(utf8_decode("anis@dev.cibaxion.com"),utf8_decode($subject),utf8_decode($message),utf8_decode($headers))){
		$emailok=true;
	} else {
		$emailok=false;
		$errorMess.="- "._("A technical problem is preventing us from sending your request. Please try again later. We apologize for the inconvenience.");
	}
	if($emailok){
		echo "<p style='text-align:center'><b>"._("We received your request!")."</b></p><br /><br />";
		echo _("A representative will contact you as soon as possible.")."<br /><br /><br />";
		echo "<b>"._("Thank you for your interest in the Canadian GeoExchange Coalition.")."</b>";
	} else {
		echo "<b>$errorMess</b>";
	}
	echo "<br /><br /><button onclick='window.close()'>Close this Window</button>";
	if($emailok){
		echo "<script type='text/javascript'>window.opener.document.getElementById('divHelpForm').innerHTML='';window.opener.document.getElementById('divHelpForm').style.display='none';</script>";
	}
?>
<?php
} else {
?>
<form id='helpForm' method='post' action='./helpForm.php'>
<input type='hidden' name='helpFormName' id='helpFormName' />
<input type='hidden' name='helpFormEmail' id='helpFormEmail' />
<input type='hidden' name='helpFormPhone' id='helpFormPhone' />
<input type='hidden' name='helpFormMessage' id='helpFormMessage' />
<input type='hidden' name='helpFormPage' id='helpFormPage' />
</form>
<script type='text/javascript'>
if(window.opener && !window.opener.closed){
	var parentDocument = window.opener.document;
	document.getElementById('helpFormName').value=parentDocument.getElementById('helpFormName').value;
	document.getElementById('helpFormEmail').value=parentDocument.getElementById('helpFormEmail').value;
	document.getElementById('helpFormPhone').value=parentDocument.getElementById('helpFormPhone').value;
	document.getElementById('helpFormMessage').value=parentDocument.getElementById('helpFormMessage').value;
	document.getElementById('helpFormPage').value=parentDocument.location.href;
	document.getElementById('helpForm').submit();
}
</script>
<?php
}
?>
</body>
</html>
