<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idProduct"])){
	$idProduct=$_POST["idProduct"];
} elseif(isset($_GET["idProduct"])){
	$idProduct=$_GET["idProduct"];
} else $idProduct=0;
if(isset($_POST["idOption"])){
	$idOption=$_POST["idOption"];
} elseif(isset($_GET["idOption"])){
	$idOption=$_GET["idOption"];
}
if(isset($_POST["idChoice"])){
	$idChoice=$_POST["idChoice"];
} elseif(isset($_GET["idChoice"])){
	$idChoice=$_GET["idChoice"];
}
?>
<?
$title = _("Anix - Suppression d'un choix");
include("../html_header.php");
?>

<?
$result=request("select $TBL_catalogue_info_options.name,$TBL_catalogue_info_choices.value from `$TBL_catalogue_info_options`,`$TBL_catalogue_info_choices`,`$TBL_gen_languages` where $TBL_catalogue_info_options.id_option='$idOption' AND $TBL_catalogue_info_choices.id_choice='$idChoice' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_options.id_language=$TBL_gen_languages.id AND $TBL_catalogue_info_choices.id_language=$TBL_gen_languages.id",$link);
$choice=mysql_fetch_object($result);
$cancelLink = "./mod_option.php?action=edit&idProduct=$idProduct&idOption=$idOption";
setTitleBar(_("Suppression d'un choix pour l'option")." ".$choice->name);

$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>

  <form id='main_form' name='del' action='./mod_option.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='idProduct' value='<?=$idProduct?>'>
  <input type='hidden' name='idOption' value='<?=$idOption?>'>
  <input type='hidden' name='idChoice' value='<?=$idChoice?>'>
  <input type='hidden' name='action' value='delChoice'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Êtes vous sûr de vouloir supprimer le choix"); ?>: </font></b><br><br>
  <?
  echo "<b>"._("Option").": </b> ".$choice->name."<br><br>"."<b>"._("Choix").": </b> ".$choice->value;
  ?>
  </center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
