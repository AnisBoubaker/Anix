<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="";
?>
<?
if($action=="moveup"){
	if(isset($_POST["idEmail"])){
		$idEmail=$_POST["idEmail"];
	} elseif(isset($_GET["idEmail"])){
		$idEmail=$_GET["idEmail"];
	} else {
		$errors++;
		$errMessage.=$sr6;
	}
	if(!$errors){
		$request=request("SELECT id,ordering,id_category from $TBL_ecommerce_emails where id='$idEmail'",$link);
		if(mysql_num_rows($request)){
			$email=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=$str7;
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_ecommerce_emails where id_category='".$email->id_category."' and ordering='".($email->ordering-1)."'",$link);
		if(mysql_num_rows($request)){
			$upEmail=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'email specifiee est deja au plus haut niveau.")."<br>";//$str8;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_ecommerce_emails set ordering='".($email->ordering-1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$email->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre de l'email.")."<br>";//$str9;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_ecommerce_emails set ordering='".$email->ordering."' where id='".$upEmail->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre de l'email.")."<br>";//$str9;
		}
	}
	if(!$errors){
		$message.=_("L'ordre de l'email a ete modifie avec succes.")."<br>";//$str10;
	}
	$action="edit";
}
?>
<?
if($action=="movedown"){
	if(isset($_POST["idEmail"])){
		$idEmail=$_POST["idEmail"];
	} elseif(isset($_GET["idEmail"])){
		$idEmail=$_GET["idEmail"];
	} else {
		$errors++;
		$errMessage.=_("L'email n'a pas ete specifiee.")."<br>";//$str6;
	}
	if(!$errors){
		$request=request("SELECT id,id_category,ordering from $TBL_ecommerce_emails where id='$idEmail'",$link);
		if(mysql_num_rows($request)){
			$email=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'email specifiee est invalide.")."<br>";//$str7;
		}
	}
	if(!$errors){
		$request=request("SELECT id,ordering from $TBL_ecommerce_emails where id_category='".$email->id_category."' and ordering='".($email->ordering+1)."'",$link);
		if(mysql_num_rows($request)){
			$downEmail=mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.=_("L'email specifiee est deja au plus bas niveau.")."<br>";//$str11;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_ecommerce_emails set ordering='".($email->ordering+1)."',modified_on='".getDBDate()."',modified_by='$anix_username' where id='".$email->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre de l'email.")."<br>";//$str9;
		}
	}
	if(!$errors){
		request("UPDATE $TBL_ecommerce_emails set ordering='".$email->ordering."' where id='".$downEmail->id."'",$link);
		if(mysql_errno()){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'ordre de l'email.")."<br>";//$str9;
		}
	}
	if(!$errors){
		$message.=_("L'ordre de l'email a ete modifie avec succes.")."<br>";//$str10;
	}
	$action="edit";
}
?>
<?
if($action=="delete"){
	$idEmail=$_POST["idEmail"];
	$request = request("SELECT `id` FROM `$TBL_ecommerce_emails` where `id`='$idEmail'",$link);
	if(mysql_num_rows($request)){
		$files = mysql_fetch_object($request);
	} else {
		$errors++;
		$errMessage.=_("L'email specifiee est invalide");//$str1;
	}
	if(!$errors){
		request("DELETE `$TBL_ecommerce_emails`,`$TBL_ecommerce_info_emails`
               FROM `$TBL_ecommerce_emails`,`$TBL_ecommerce_info_emails`
               WHERE `$TBL_ecommerce_emails`.`id`='$idEmail'
               AND `$TBL_ecommerce_info_emails`.`id_email`=`$TBL_ecommerce_emails`.`id`",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la suppression de l'email.");//$str2;
		}
	}
	if(!$errors){
		$message.=_("L'email type a ete supprimee correctement");//$str3;
	}
}
?>
<?
$title = _("Anix - Liste des courriels type");//$str4;
$menu_ouvert = 4;
$module_name="ecommerce";
include("../html_header.php");
setTitleBar(_("Liste des courriels types"));
?>
<table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'><?php echo _("Liste des emails type"); ?></font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  &nbsp;
  </td>
</tr>
<tr>
<td colspan='2'>
<?
echo showEmails($link);
?>
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    &nbsp;
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  &nbsp;
  </td>
</tr>
</table>
<?
include ("../html_footer.php");
mysql_close($link);
?>
