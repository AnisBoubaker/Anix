<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idArticle"])){
	$idArticle=$_POST["idArticle"];
} elseif(isset($_GET["idArticle"])){
	$idArticle=$_GET["idArticle"];
} else $idArticle=0;
?>
<?
$title = _("Anix - Suppression d'un article");include("../html_header.php");
setTitleBar(_("Suppression d'un article"));
?>
<form id='main_form' action='./list_articles.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='action' value='deletearticle'>
<input type='hidden' name='idArticle' value='<?=$idArticle?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request=request("SELECT $TBL_articles_article.id,$TBL_articles_article.id_category,$TBL_articles_article.active,$TBL_articles_article.from_date,$TBL_articles_article.to_date,$TBL_articles_info_article.title,$TBL_articles_info_article.short_desc from $TBL_articles_article,$TBL_articles_info_article,$TBL_gen_languages where $TBL_articles_article.id=$idArticle and $TBL_gen_languages.id='$used_language_id' and $TBL_articles_info_article.id_article=$TBL_articles_article.id and $TBL_articles_info_article.id_language=$TBL_gen_languages.id",$link);
$article = mysql_fetch_object($request);

$cancelLink="./list_articles.php?action=edit";
if($article) $cancelLink.="&idCat=".$article->id_category;
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<tr>
<td colspan='2'>
<?
if(!$article){
	echo "<i><center>"._("Erreur : Cet article n'existe pas")."</center></i>";
} else {
?>
<center><b><font color='red'><?php echo _("Êtes vous sûr de vouloir supprimer cet article ci-dessous ?"); ?></font></b></center><br><br>
<table align='center'>
<tr>
  <td colspan='2'><?php echo _("Si oui, veuillez cliquer sur \"OK\". Sinon, cliquez simplement sur le bouton \"Annuler\".");?><br><br>
  </td>
</tr>
<tr>
  <td><b><?php echo _("Titre"); ?>:</b></td>
  <td><?echo $article->title;?></td>
</tr>
<tr>
  <td><b><?php echo _("Résumé"); ?>:</b></td>
  <td><?php echo unhtmlentities($article->short_desc); ?></td>
</tr>
<tr>
  <td><b><?php echo _("Etat");?>:</b></td>
  <td>
  <?
  if($article->active=="Y") echo _("Actif");
  elseif($article->active=="N") echo _("Désactivé");
  elseif($article->active=="ARCHIVE") echo _("Archivé");
  elseif($article->active=="DATE"){
  	$currentDate=date("Y-m-d");
  	if($currentDate>=$article->from_date && $currentDate<=$article->to_date) echo _("Actif");
  	elseif($currentDate<$article->from_date) echo _("En attente");
  	else echo _("Expiré");
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
