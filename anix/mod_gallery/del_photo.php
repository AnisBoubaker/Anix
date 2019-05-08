<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idPhoto"])){
	$idPhoto=$_POST["idPhoto"];
} elseif(isset($_GET["idPhoto"])){
	$idPhoto=$_GET["idPhoto"];
} else $idPhoto=0;
?>
<?
$title = _("Anix - Suppression d'une nouvelle");
include("../html_header.php");
setTitleBar(_("Suppression d'une nouvelle"));
?>
<form id='main_form' action='./list_photos.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='action' value='deletephoto'>
<input type='hidden' name='idPhoto' value='<?=$idPhoto?>'>
<table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request=request("SELECT $TBL_gallery_photo.id,$TBL_gallery_photo.id_category,$TBL_gallery_photo.active,$TBL_gallery_photo.from_date,$TBL_gallery_photo.to_date,$TBL_gallery_info_photo.date,$TBL_gallery_info_photo.title from $TBL_gallery_photo,$TBL_gallery_info_photo,$TBL_gen_languages where $TBL_gallery_photo.id=$idPhoto and $TBL_gen_languages.id='$used_language_id' and $TBL_gallery_info_photo.id_photo=$TBL_gallery_photo.id and $TBL_gallery_info_photo.id_language=$TBL_gen_languages.id",$link);
$photo = mysql_fetch_object($request);
$cancelLink="./list_photos.php?action=edit";
if($photo) $cancelLink.="&idCat=".$photo->id_category;
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<tr>
<td colspan='2'>
<?
if(!$photo){
	echo "<i><center>"._("Erreur : Cette nouvelle n'existe pas")."</center></i>";
} else {
?>
<center><b><font color='red'><?php echo _("Êtes vous sûr de vouloir supprimer cette nouvelle ci-dessous?"); ?></font></b></center><br><br>
<table align='center'>
<tr>
  <td colspan='2'><?php echo _("Si oui, veuillez cliquer sur \"OK\". Sinon, cliquez simplement sur le bouton \"Annuler\"."); ?><br><br>
  </td>
</tr>
<tr>
  <td><b><?php echo _("Titre"); ?>:</b></td>
  <td><?=$photo->title ?></td>
</tr>
<tr>
  <td><b><?php echo _("Date"); ?>:</b></td>
  <td><?if($photo->date!="") echo $photo->date; else echo "<i>Pas de date spécifiée.</i>" ?></td>
</tr>
<tr>
  <td><b><?php echo _("État"); ?>:</b></td>
  <td>
  <?
  if($photo->active=="Y") echo _("Active");
  elseif($photo->active=="N") echo _("Désactivée");
  elseif($photo->active=="ARCHIVE") echo _("Archivée");
  elseif($photo->active=="DATE"){
  	$currentDate=date("Y-m-d");
  	if($currentDate>=$photo->from_date && $currentDate<=$photo->to_date) echo _("Active");
  	elseif($currentDate<$photo->from_date) echo _("En attente");
  	else echo _("Expirée");
  }
  ?>
  </td>
</tr>
</table>
<?
} //else !$category
?>
</td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
