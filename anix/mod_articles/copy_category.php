<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"] ;
} else $idCat=0;
?>
<?
$title = _("Anix - Copie d'une categorie");include("../html_header.php");
setTitleBar(_("Copie d'une categorie"));
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_articles_categories.id,$TBL_articles_info_categories.name FROM $TBL_articles_categories,$TBL_articles_info_categories,$TBL_gen_languages where $TBL_articles_categories.id=$idCat and $TBL_gen_languages.id='$used_language_id' and $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id",$link);
$category=mysql_fetch_object($request);
$request = request("SELECT COUNT(*) as nb FROM $TBL_articles_categories where id_parent='$idCat' group by id_parent",$link);
$nbSubcats = mysql_fetch_object($request);
$request = request("SELECT COUNT(*) as nb FROM $TBL_articles_article where id_category='$idCat' group by id_category",$link);
$nbArticle = mysql_fetch_object($request);

$button=array();
$buttons[]=array("type"=>"cancel","link"=>'./list_categories.php?action=edit');
printButtons($buttons);
?>
<TR>
    <TD colspan='2'>
    <?
    if(!$category){
    	echo "<i><CENTER>"._("Erreur: Cette categorie n'existe pas.")."</CENTER></I>";
    } else {
    ?>
      <CENTER>
        <B><?php echo _("Veuillez sélectionner la catégorie dans laquelle vous souhaitez copier la catégorie suivante"); ?>:</B>
      </CENTER>
      <BR>
      <BR>
      <TABLE width='60%' align='center'>
        <TR>
          <TD>
            <B><?php echo _("Nom de la catégorie"); ?>:</B>
          </TD>
          <TD>
            <?=$category->name?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Nombre de sous catégories directes");?>:</B>
          </TD>
          <TD>
            <?php if(isset($nbSubcats->nb)) echo $nbSubcats->nb; else echo "0";?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Nombre d'articles directs"); ?>:</B>
          </TD>
          <TD>
            <?=$nbArticle->nb?>
          </TD>
        </TR>
      </TABLE>
<?
echo "<table class='message' width='100%'>";
echo "<tr>";
echo "<td align='left' valign='middle' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
echo "<td><a href='./list_categories.php?action=copy&idCat=$idCat&copyto=0'>"._("Premier niveau")."</a></td>";
echo "</tr>";
echo "</table>";
$categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering, $TBL_articles_info_categories.name, $TBL_articles_info_categories.description from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
$tableCategories = getCatTable($categories);
echo showCategoriesExcept($tableCategories,0,0,"./list_categories.php?action=copy&idCat=$idCat&copyto=",false,$idCat);
      ?>
<?
    } //else !$category
      ?>
    </TD>
  </TR>
</TABLE>
<?
include ("../html_footer.php");
mysql_close($link);
?>
