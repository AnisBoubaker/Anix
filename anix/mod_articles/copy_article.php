<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idArticle"])){
	$idArticle=$_POST["idArticle"];
} elseif(isset($_GET["idArticle"])){
	$idArticle=$_GET["idArticle"] ;
} else $idArticle=0;
?>
<?
$title = _("Anix - Copie d'un article");include("../html_header.php");
setTitleBar(_("Copie d'un article"));
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_articles_article.id,$TBL_articles_article.id_category,$TBL_articles_article.active,$TBL_articles_article.from_date,$TBL_articles_article.to_date,$TBL_articles_info_article.title,$TBL_articles_info_article.short_desc FROM $TBL_articles_article,$TBL_articles_info_article,$TBL_gen_languages where $TBL_articles_article.id=$idArticle and $TBL_gen_languages.id='$used_language_id' and $TBL_articles_info_article.id_article=$TBL_articles_article.id and $TBL_articles_info_article.id_language=$TBL_gen_languages.id",$link);
$article=mysql_fetch_object($request);
$cancelLink="./list_articles.php?action=edit";
if($article) $cancelLink.="&idCat=".$article->id_category;
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
  <TR>
    <TD colspan='2'>
    <?
    if(!$article){
    	echo "<i><CENTER>"._("Erreur : Cet article n'existe pas")."</CENTER></I>";
    } else {
    ?>
      <CENTER>
        <B><?php echo _("Veuillez sélectionner la catégorie dans laquelle vous souhaitez copier l'article suivant"); ?>:</B>
      </CENTER>
      <BR>
      <BR>
      <TABLE width='60%' align='center'>
        <TR>
          <TD>
            <B><?php echo _("Titre");?>:</B>
          </TD>
          <TD>
            <?=$article->title ?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Résumé"); ?>:</B>
          </TD>
          <TD>
            <?echo unhtmlentities($article->short_desc); ?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Etat"); ?>:</B>
          </TD>
          <TD>
            <?
            if($article->active=="Y") echo _("Actif");
            elseif($article->active=="N") echo _("Désactivé");
            elseif($article->active=="DATE"){
            	$currentDate=date("Y-m-d");
            	if($currentDate>=$article->from_date && $currentDate<=$article->to_date) echo _("Actif");
            	elseif($currentDate<$article->from_date) echo _("En attente");
            	else echo _("Expiré");
            }elseif($article->active=="ARCHIVE") echo _("Archivé");
            ?>
          </TD>
        </TR>
      </TABLE>
<?
$categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.id_parent, $TBL_articles_categories.contain_items, $TBL_articles_categories.ordering, $TBL_articles_info_categories.name, $TBL_articles_info_categories.description from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
$tableCategories = getCatTable($categories);
echo showCategories($tableCategories,0,0,"./list_articles.php?action=copy&idArticle=$idArticle&copyto=",false,true);
      ?>
<?
    } //else !$article
      ?>
    </TD>
  </TR>
</TABLE>
<?
include ("../html_footer.php");
mysql_close($link);
?>
