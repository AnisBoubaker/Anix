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
<?php
$title = _("Anix - Copie d'une catégorie");
include("../html_header.php");
setTitleBar(_("Copie d'une catégorie"));
$button=array();
$buttons[]=array("type"=>"cancel","link"=>'./list_categories.php?action=edit');
printButtons($buttons);
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_faq_categories.id,$TBL_faq_info_categories.name FROM $TBL_faq_categories,$TBL_faq_info_categories,$TBL_gen_languages where $TBL_faq_categories.id=$idCat and $TBL_gen_languages.id='$used_language_id' and $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id",$link);
$category=mysql_fetch_object($request);
$request = request("SELECT COUNT(*) as nb FROM $TBL_faq_categories where id_parent='$idCat' group by id_parent",$link);
$nbSubcats = mysql_fetch_object($request);
$request = request("SELECT COUNT(*) as nb FROM $TBL_faq_faq where id_category='$idCat' group by id_category",$link);
$nbFaq = mysql_fetch_object($request);
?>
  <TR>
    <TD colspan='2'>
    <?
    if(!$category){
    	echo "<i><CENTER>"._("Erreur : Cette catégorie n'existe pas")."</CENTER></I>";
    } else {
    ?>
      <CENTER>
        <B><?php echo _("Veuillez sélectionner la catégorie dans laquelle vous souhaitez copier la catégorie suivante:");?></B>
      </CENTER>
      <BR>
      <BR>
      <TABLE width='60%' align='center'>
        <TR>
          <TD>
            <B><?php echo _("Nom de la catégorie:");?></B>
          </TD>
          <TD>
            <?php echo $category->name;?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Nombre de sous catégories directes:");?></B>
          </TD>
          <TD>
            <?if(isset($nbSubcats->nb)) echo $nbSubcats->nb; else echo "0"; ?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Nombre de questions directes:"); ?></B>
          </TD>
          <TD>
            <?=$nbFaq->nb?>
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
$categories=request("select $TBL_faq_categories.id, $TBL_faq_categories.id_parent, $TBL_faq_categories.ordering, $TBL_faq_info_categories.name, $TBL_faq_info_categories.description from  $TBL_faq_categories,$TBL_gen_languages,$TBL_faq_info_categories where $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_faq_categories.id_parent, $TBL_faq_categories.ordering", $link);
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