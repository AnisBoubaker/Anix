<?php
	require_once("./config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title><?php echo _("Send page by email") ?></title>
<link rel="stylesheet" type="text/css" media="screen" href="./css/layout.css" />
</head>
<body style='background:#d1e6f2;padding:10px;text-align:left;'>
<script type="text/javascript">
	if(!window.opener || window.opener.closed) window.close();
</script>
<style type="text/css">
table td.title{
	font-weight:bold;
	text-align:right;
	vertical-align:top;
}
</style>
<?php
$errors=0;
$errMessage="";
if(isset($_POST["sender_name"]) && isset($_POST["sender_email"]) && isset($_POST["destination_name"]) && isset($_POST["destination_email"])){
	if(trim($_POST["sender_name"])=="") {$errors++; $errMessage.="- "._("Please enter your name")."<br />";}
	if(trim($_POST["sender_email"])=="") {$errors++; $errMessage.="- "._("Please enter your email address")."<br />";}
	if(trim($_POST["sender_email"])!="" && !emailValid($_POST["sender_email"])) {$errors++; $errMessage.="- "._("Your email address is not valid. Please check it.")."<br />";}
	if(trim($_POST["destination_name"])=="") {$errors++; $errMessage.="- "._("Please enter the name of your correspondant.")."<br />";}
	if(trim($_POST["destination_email"])=="") {$errors++; $errMessage.="- "._("Please enter the email address of your correspondant.")."<br />";}
	if(trim($_POST["destination_email"])!="" && !emailValid($_POST["destination_email"])) {$errors++; $errMessage.="- "._("Your correspondant's email address in not valid. Please check it.")."<br />";}
}
if(!$errors && isset($_POST["sender_name"]) && isset($_POST["sender_email"]) && isset($_POST["destination_name"]) && isset($_POST["destination_email"])){
	//construct the user message
	if($_POST["message"]!="") $userMessage = $_POST["message"];
	else $userMessage = _("Hello")." ".$_POST["destination_name"].",\n\n".$_POST["sender_name"]." "._("found this page interesting on the Canadian GeoExchange Coalition's website and he wished to share it with you.")."\n";
	//Add the link to the user message
	$userMessage.="\n\n"._("To access the page, please click on the link below:")."\n".$_SESSION["current_url"]."\n\n";
	if(!$errors){
		//construct the email
		$subject=_("An interesting page on the CGC's website.");
		$message=$userMessage;
		//add the disclaimer
		$message.="\n\n\n\n-----------\n";
		$message.=_("DISCLAIMER: This email has been sent to you at the request of the person mentionned above (author of the email) and the Canadian GeoExchange Coalition is not liable, in any case, for its content.")."\n\n";
		$message.=_("If you believe you have been victim of an abuse by receiving this email, please send an email to this address : abuse@geoexchange.ca . We ensure you that each complaint will be investigated.")."\n\n";
		$message.=_("Message sent from IP addresse:").$_SERVER['REMOTE_ADDR']." / ".date("Y-m-d H:i:s")."\n\n";
		$headers = "From: ".$_POST["sender_name"]." <".$_POST["sender_email"].">\n";
		$headers .= "X-Sender: <".$_POST["sender_email"].">\n";
		$emailok=true;
		if (mail(utf8_decode($_POST["destination_email"]),utf8_decode($subject),utf8_decode($message),utf8_decode($headers))){
			$emailok=true;
		} else {
			$emailok=false;
			$errors++;
			$errorMess.="- "._("A technical problem is preventing us from sending your request. Please try again later. We apologize for the inconvenience.");
		}
	}
	if(!$errors){
		echo "<div style='text-align:center;'><br /><br />";
			echo "<b>"._("The page has been sent.")."</b><br /><br />";
			echo _("Thank you for your interest in the Canadian GeoExchange Coalition.")."<br /><br />";
			echo "<button onclick='window.close()'>"._("Close this window")."</button>";
		echo "</div>";
	}


?>
<?php
} else {
?>
<form id='sendPage' method='post' action='./sendPage.php'>
<table style='width:450px;'>
<tr>
	<td style='text-align:center;font-size:12px;font-weight:bold;padding-bottom:15px;color:#242775' colspan="2"><?php echo _("Send page by email"); ?></td>
</tr>
	<?php if($errors){?>
	<tr>
		<td style='text-align:left;padding:0 0 15px 30px;color:#ff0000' colspan="2"><?php echo "<b>"._("The page couldn't be sent because of the following errors:")."</b><br />".$errMessage; ?></td>
	</tr>
	<?php } ?>
<tr>
	<td class='title' nowrap='nowrap'><?php echo _("Your name");?>:</td><td><input type='text' name='sender_name'<?php if(isset($_POST["sender_name"])) echo " value=\"".$_POST["sender_name"]."\"";?> /></td>
</tr>
<tr>
	<td class='title' nowrap='nowrap'><?php echo _("Your email address");?>:</td><td><input type='text' name='sender_email'<?php if(isset($_POST["sender_email"])) echo " value=\"".$_POST["sender_email"]."\"";?> /></td>
</tr>
<tr>
	<td class='title' nowrap='nowrap'><?php echo _("Your correspondant's name");?>:</td><td><input type='text' name='destination_name'<?php if(isset($_POST["destination_name"])) echo " value=\"".$_POST["destination_name"]."\"";?> /></td>
</tr>
<tr>
	<td class='title' nowrap='nowrap'><?php echo _("Your correspondant's email");?>:</td><td><input type='text' name='destination_email'<?php if(isset($_POST["destination_email"])) echo " value=\"".$_POST["destination_email"]."\"";?> /></td>
</tr>
<tr>
	<td class='title' nowrap='nowrap'><?php echo _("Subject");?>:</td><td><?php echo _("An interesting page on CGC's website"); ?></td>
</tr>
<tr>
	<td class='title' nowrap='nowrap'><?php echo _("Message (optional)");?>:</td><td><textarea name='message' style='width:200px;height:120px;'><?php
		if(isset($_POST["message"])) echo $_POST["message"];
		else echo _("Hello!\n\nI found this page interesting on the Canadian GeoExchange Coalition's website. Take a look!\n\nSee you soon.");
	?></textarea></td>
</tr>
<tr>
	<td class='title'><?php echo _("Address (URL)");?>:</td><td><i><?php echo _("A link to the page will be automatically added to your email message."); ?></i><br /><br /></td>
</tr>
<tr>
	<td>&nbsp;</td><td><input type='submit' class='button' value="<?php echo _("Send"); ?>" />&nbsp;<button onclick="window.close();"><?php echo _("Cancel")?></button></td>
</tr>
</table>
</form>
<?php
}
?>
</body>
</html>
