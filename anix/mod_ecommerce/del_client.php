<?
include ("../config.php");
$link = dbConnect();
if(isset($_POST["idClient"])){
	$idClient=$_POST["idClient"];
} elseif(isset($_GET["idClient"])){
	$idClient=$_GET["idClient"];
}
?>
<? $title = _("Suppression d'un client");
$menu_ouvert = 1;
$module_name="ecommerce";
include("../html_header.php");
?>
<?
$result=request("select id,firstname,lastname,company from $TBL_ecommerce_customer where id='$idClient'", $link);
$client=mysql_fetch_object($result);
$cancelLink="./list_clients.php";
?>

<form name='del' action='./list_clients.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='delete'>
  <input type='hidden' name='idClient' value='<?=$idClient?>'>
<table border="0" align="center" width="50%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'>
      Suppression d'un client
	  </font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
    <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
	<a href='<?=$cancelLink?>'><img src='../locales/<?=$used_language?>/images/button_cancel.jpg' border='0'></a>
  </td>
</tr>
<tr>
  <td colspan='2'>
  <br><br>
  <center><b><font color='red'>
  <?php echo _("Etes vous sur de vouloir supprimer le client")." $client->firstname $client->lastname  ($client->company) ?"?></font><br><br>
  <I>N.B.: Cette operation est irreversible.</I>
  </b></center>
  <br><br>
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

