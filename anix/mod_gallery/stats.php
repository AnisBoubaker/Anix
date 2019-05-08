<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
?>
<?php
$title = _("Anix - Statistiques des nouvelles");
include("../html_header.php");
setTitleBar(_("Statistiques des nouvelles"));
?>
<?
$categories=request("select $TBL_gallery_categories.id, $TBL_gallery_categories.id_parent, $TBL_gallery_categories.ordering, $TBL_gallery_info_categories.name, $TBL_gallery_info_categories.description from  $TBL_gallery_categories,$TBL_gen_languages,$TBL_gallery_info_categories where $TBL_gallery_info_categories.id_gallery_cat=$TBL_gallery_categories.id and $TBL_gallery_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_gallery_categories.id_parent, $TBL_gallery_categories.ordering", $link);
$tableCategories = getCatTable($categories);
$tableCategories = getStats($tableCategories,$link);
$maxPhotoPerCat = 0;
$totalPhoto=0;
foreach($tableCategories as $cat){
	if($cat["nbTotalPhoto"]>$maxPhotoPerCat) $maxPhotoPerCat=$cat["nbTotalPhoto"];
	$totalPhoto+=$cat["nbTotalPhoto"];
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
        <B><u><?php echo _("Généralités"); ?>:</u></B><br>
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
              <?php echo _("Nombre de nouvelles"); ?>:
            </td>
            <td>
              <?=$totalPhoto?>
            </td>
          </tr>
          <tr>
            <td>
              <?php echo _("Répartition moyenne"); ?>:
            </td>
            <td>
              <?=number_format($totalPhoto/count($tableCategories), 2)?>
            </td>
          </tr>
        </table>
        <br>
        <B><U><?php echo _("Répartition des nouvelles par catégories"); ?>:</U></B><br>
        <?
        $units = 100/$maxPhotoPerCat;
        echo showStats($tableCategories,0,0,$units);
        ?>
        <B><I><?php echo _("Légende"); ?>:</I></B><br>
        <img src='../images/bar_blue.jpg' height='10' width='10'> <?php echo _("En attente"); ?>&nbsp;&nbsp;&nbsp;
        <img src='../images/bar_green.jpg' height='10' width='10'> <?php echo _("Actives"); ?>&nbsp;&nbsp;&nbsp;
        <img src='../images/bar_orange.jpg' height='10' width='10'> <?php echo _("Expirées"); ?>&nbsp;&nbsp;&nbsp;
        <img src='../images/bar_red.jpg' height='10' width='10'> <?php echo _("Inactives"); ?>&nbsp;&nbsp;&nbsp;
        <img src='../images/bar_blue.jpg' height='10' width='10'> <?php echo _("Archivées"); ?>&nbsp;&nbsp;&nbsp;
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
        <center><I><?php echo _("Aucune action à effectuer sur les nouvelles"); ?></I></center>
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
