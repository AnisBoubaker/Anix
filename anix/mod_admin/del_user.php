<?
	include ("../config.php");
	include ("./module_config.php");
	$link = dbConnect();
	if(isset($_POST["idUser"])){
	  $idUser=$_POST["idUser"];
	} elseif(isset($_GET["idUser"])){
	  $idUser=$_GET["idUser"];
	}
?>
<? $title = _("Anix - Suppression d'un utilisateur");
   include("../html_header.php");
   setTitleBar(_("Suppression d'un utilisateur"));
?>
<?
  $result=request("select $TBL_admin_admin.id,$TBL_admin_admin.name from $TBL_admin_admin where $TBL_admin_admin.id='$idUser'", $link);
  $user=mysql_fetch_object($result);
  $cancelLink="./list_users.php";
?>

<form name='del' action='./list_users.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='delete'>
  <input type='hidden' name='idUser' value='<?=$idUser?>'>
<table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
  </td>
  <td background='../images/button_back.jpg' align='right'>
    <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
	<a href='<?=$cancelLink?>'><img src='../locales/<?=$used_language?>/images/button_cancel.jpg' border='0'></a>
  </td>
</tr>
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Etes vous sûr de vouloir supprimer l'utilisateur"); ?> "<?=$user->name?>" ? </font><br><br>
  <I><?php echo _("NB: Cette action est irrécupérable."); ?></I>
  </b></center>
  </td>
</tr>
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    &nbsp;
  </td>
  <td background='../images/button_back.jpg' align='right'>
    <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
	<a href='<?=$cancelLink?>'><img src='../locales/<?=$used_language?>/images/button_cancel.jpg' border='0'></a>
  </td>
</tr>
</table>
</form>
<?
	include ("../html_footer.php");
	mysql_close($link);
?>

