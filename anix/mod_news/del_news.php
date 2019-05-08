<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idNews"])){
	$idNews=$_POST["idNews"];
} elseif(isset($_GET["idNews"])){
	$idNews=$_GET["idNews"];
} else $idNews=0;
?>
<?
$title = _("Anix - Suppression d'une nouvelle");
include("../html_header.php");
setTitleBar(_("Suppression d'une nouvelle"));
?>
<form id='main_form' action='./list_news.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='action' value='deletenews'>
<input type='hidden' name='idNews' value='<?=$idNews?>'>
<table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request=request("SELECT $TBL_news_news.id,$TBL_news_news.id_category,$TBL_news_news.active,$TBL_news_news.from_date,$TBL_news_news.to_date,$TBL_news_info_news.date,$TBL_news_info_news.title from $TBL_news_news,$TBL_news_info_news,$TBL_gen_languages where $TBL_news_news.id=$idNews and $TBL_gen_languages.id='$used_language_id' and $TBL_news_info_news.id_news=$TBL_news_news.id and $TBL_news_info_news.id_language=$TBL_gen_languages.id",$link);
$news = mysql_fetch_object($request);
$cancelLink="./list_news.php?action=edit";
if($news) $cancelLink.="&idCat=".$news->id_category;
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<tr>
<td colspan='2'>
<?
if(!$news){
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
  <td><?=$news->title ?></td>
</tr>
<tr>
  <td><b><?php echo _("Date"); ?>:</b></td>
  <td><?if($news->date!="") echo $news->date; else echo "<i>Pas de date spécifiée.</i>" ?></td>
</tr>
<tr>
  <td><b><?php echo _("État"); ?>:</b></td>
  <td>
  <?
  if($news->active=="Y") echo _("Active");
  elseif($news->active=="N") echo _("Désactivée");
  elseif($news->active=="ARCHIVE") echo _("Archivée");
  elseif($news->active=="DATE"){
  	$currentDate=date("Y-m-d");
  	if($currentDate>=$news->from_date && $currentDate<=$news->to_date) echo _("Active");
  	elseif($currentDate<$news->from_date) echo _("En attente");
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
