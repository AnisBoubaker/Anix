<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idArticle"])){
	$idArticle=$_POST["idArticle"];
} elseif(isset($_GET["idArticle"])){
	$idArticle=$_GET["idArticle"];
} else $idArticle="";
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory="";
if(isset($_POST["idAttachment"])){
	$idAttachment=$_POST["idAttachment"];
} elseif(isset($_GET["idAttachment"])){
	$idAttachment=$_GET["idAttachment"];
}
?>
<? 	$title = _("Anix - Suppression de fichier attaché");
include("../html_header.php");
setTitleBar(_("Suppression d'un fichier attaché"));
?>
<?
$result=request("select $TBL_articles_attachments.id,$TBL_articles_attachments.title,$TBL_gen_languages.name from $TBL_articles_attachments,$TBL_gen_languages where $TBL_articles_attachments.id='$idAttachment' and $TBL_gen_languages.id=$TBL_articles_attachments.id_language", $link);
$attachment=mysql_fetch_object($result);
if($idCategory) {
	$cancelLink="./mod_category.php?idCat=$idCategory&action=edit";
}elseif($idArticle) {
	$cancelLink="./mod_article.php?idArticle=$idArticle&action=edit";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<?php  ?>
<form id='main_form' name='del' action='./mod_article.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='delAttachment'>
  <input type='hidden' name='idArticle' value='<?=$idArticle?>'>
  <input type='hidden' name='idCat' value='<?=$idCategory?>'>
  <input type='hidden' name='idAttachment' value='<?=$idAttachment?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php echo _("Êtes vous sûr de vouloir supprimer le fichier attaché")." \"".$attachment->title."\""?> ? </font><br><br>
  <I><?php echo _("NB: Cette opération n'est pas récupérable!"); ?></I>
  </b></center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>

