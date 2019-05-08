<?
	include ("../config.php");
	include ("./module_config.php");
	$link = dbConnect();
	$delete=false;
	$errMessage="";
	$message="";
	$errors=0;
	if(isset($_POST["action"])){
	  $action=$_POST["action"];
	} elseif(isset($_GET["action"])){
	  $action=$_GET["action"];
	} else $action="";
	if(isset($_POST["idCat"])){
	  $idCat=$_POST["idCat"];
	} elseif(isset($_GET["idCat"])){
	  $idCat=$_GET["idCat"];
	} else $idCat=0;
?>
<?php
include("./list_articles.actions.php");
?>
<?
  $title = _("Anix - Liste des articles");
  include("../html_header.php");
  setTitleBar(_("Liste des articles"));
?>
<table border="0" align="center" CellPadding="0" CellSpacing="0" width="100%">
<tr bgcolor='#FFFFFF'>
<td colspan='2'>
<?
  $categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.id_parent, $TBL_articles_categories.contain_items, $TBL_articles_categories.ordering, $TBL_articles_info_categories.name, $TBL_articles_info_categories.description from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
  $nbCategories = mysql_num_rows($categories);
  echo _("Veuillez sélectionner la catégorie contenant l'article à modifier:");
  if($nbCategories) {
  	$tableCategories = getCatTable($categories);
  	echo showArticle($tableCategories,0,$idCat,0,"./list_articles.php?idCat=",true,$link);
  	echo "<SCRIPT LANGUAGE='JAVASCRIPT'>location.href='#$idCat'</SCRIPT>";
  } else {
  	echo "<table class='message' width='100%'>";
  	echo "<tr>";
  	echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie d'articles")."</b></td>";
  	echo "</tr>";
  	echo "</table>";
  }
?>
</td>
</tr>
</table>
</form>
<?
	include ("../html_footer.php");
	mysql_close($link);
?>
