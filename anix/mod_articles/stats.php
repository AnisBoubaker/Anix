<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
?>
<?
$title = _("Anix - Statistiques des articles");
include("../html_header.php");
setTitleBar(_("Statistiques des articles"));
?>
<?
$categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering, $TBL_articles_info_categories.name, $TBL_articles_info_categories.description from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
$tableCategories = getCatTable($categories);
$tableCategories = getStats($tableCategories,$link);
$maxArticlePerCat = 0;
$totalArticle=0;
foreach($tableCategories as $cat){
	if($cat["nbTotalArticle"]>$maxArticlePerCat) $maxArticlePerCat=$cat["nbTotalArticle"];
	$totalArticle+=$cat["nbTotalArticle"];
}
?>
<TABLE width='100%'>
<TR>
  <TD width='50%' valign='top'>
    <TABLE border="0" align="center" CellPadding="0" CellSpacing="0" width="95%">
    <TR height='20'>
      <TD  background='../images/button_back.jpg' align='left' valign='middle'>
      </TD>
      <TD background='../images/button_back.jpg' align='right'>
        &nbsp;
      </TD>
    </TR>
    <TR bgcolor='#FFFFFF'>
      <TD colspan='2'>
        <B><U><?php echo _("Généralités"); ?>:</U></B><br>
        <table width='100%'>
          <tr>
            <td>
              <?php echo _("Nombre de catégories"); ?>:
            </td>
            <td>
              <?=count($tableCategories)?>
            </td>
          </tr>
          <tr>
            <td>
              <?php echo _("Nombre d'articles"); ?>:
            </td>
            <td>
              <?=$totalArticle?>
            </td>
          </tr>
          <tr>
            <td>
              <?php echo _("Répartition moyenne"); ?>:
            </td>
            <td>
              <?=number_format($totalArticle/count($tableCategories), 2)?>
            </td>
          </tr>
        </table>
        <br>
        <B><U><?php echo _("Répartition des articles par catégories"); ?>:</U></B><br>
        <?
        $units = 100/$maxArticlePerCat;
        echo showStats($tableCategories,0,0,$units);
        ?>
        <B><I><?php echo _("Légende"); ?>:</I></B><br>
        <img src='../images/bar_blue.jpg' height='10' width='10'> <?php echo _("En attente");?>&nbsp;&nbsp;&nbsp;
        <img src='../images/bar_green.jpg' height='10' width='10'> <?php echo _("Actifs")?>&nbsp;&nbsp;&nbsp;
        <img src='../images/bar_orange.jpg' height='10' width='10'> <?php echo _("Expirés"); ?>&nbsp;&nbsp;&nbsp;
        <img src='../images/bar_red.jpg' height='10' width='10'> <?php echo _("Inactifs"); ?>&nbsp;&nbsp;&nbsp;
        <img src='../images/bar_blue.jpg' height='10' width='10'> <?php echo _("Archivés"); ?>&nbsp;&nbsp;&nbsp;
      </TD>
    </TR>
    <TR height='20'>
      <TD  background='../images/button_back.jpg' align='left' valign='middle'>
        <FONT class='edittable_header'>&nbsp;</FONT>
      </TD>
      <TD background='../images/button_back.jpg' align='right'>
        &nbsp;
      </TD>
    </TR>
    </TABLE>
  </TD>
  <TD width='50%' valign='top'>
    <TABLE border="0" align="center" CellPadding="0" CellSpacing="0" width="95%">
    <TR height='20'>
      <TD  background='../images/button_back.jpg' align='left' valign='middle'>
        <FONT class='edittable_header'><?php echo _("À faire"); ?></FONT>
      </TD>
      <TD background='../images/button_back.jpg' align='right'>
        &nbsp;
      </TD>
    </TR>
    <TR bgcolor='#FFFFFF'>
      <TD colspan='2'>
        <center><I><?php echo _("Aucune action à effectuer sur les articles"); ?></I></center>
      </TD>
    </TR>
    <TR height='20'>
      <TD  background='../images/button_back.jpg' align='left' valign='middle'>
        <FONT class='edittable_header'>&nbsp;</FONT>
      </TD>
      <TD background='../images/button_back.jpg' align='right'>
        &nbsp;
      </TD>
    </TR>
    </TABLE>
  </TD>
</TR>
</TABLE>


<? include ("../html_footer.php"); ?>
