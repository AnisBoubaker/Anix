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
$title = _("Anix - Déplacement d'une catégorie");
include("../html_header.php");
setTitleBar(_("Déplacement d'une catégorie"));
?>
<TABLE border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_catalogue_categories.id,$TBL_catalogue_info_categories.name,$TBL_catalogue_categories.id_parent FROM $TBL_catalogue_categories,$TBL_catalogue_info_categories,$TBL_gen_languages where $TBL_catalogue_categories.id=$idCat and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id",$link);
$category=mysql_fetch_object($request);
$request = request("SELECT COUNT(*) as nb FROM $TBL_catalogue_categories where id_parent='$idCat' group by id_parent",$link);
$nbSubcats = mysql_fetch_object($request);
$request = request("SELECT COUNT(*) as nb FROM $TBL_catalogue_products where id_category='$idCat' group by id_category",$link);
$nbProducts = mysql_fetch_object($request);

$button=array();
$buttons[]=array("type"=>"cancel","link"=>'./list_categories.php?action=edit');
printButtons($buttons);
?>
  <TR>
    <TD colspan='2'>
    <?
    if(!$category){
    	echo "<i><CENTER>"._("Erreur : Cette catégorie n'existe pas")."</CENTER></I>";
    } else {
    ?>
      <CENTER>
        <B><?php echo _("Veuillez sélectionner la catégorie dans laquelle vous souhaitez déplacer la catégorie suivante"); ?>:</B>
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
            <B><?php echo _("Nombre de sous catégories directes"); ?>:</B>
          </TD>
          <TD>
            <?php if(isset($nbSubcats->nb)) echo $nbSubcats->nb; else echo "0";?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Nombre de produits directes"); ?>:</B>
          </TD>
          <TD>
            <?=$nbProducts->nb?>
          </TD>
        </TR>
      </TABLE>

<?
echo "<table class='message' width='100%'>";
echo "<tr>";
echo "<td>";
if($category->id_parent!=0){
	echo "<a href='./list_categories.php?action=move&idCat=$idCat&moveto=0'>";
}
echo _("Premier niveau");
if($category->id_parent!=0){
	echo "</a>";
}
echo "</td>";
echo "</tr>";
echo "</table>";
$categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
$tableCategories = getCatTable($categories);
echo showCategoriesExceptTree($tableCategories,0,0,"./list_categories.php?action=move&idCat=$idCat&moveto=",false,$category->id_parent,$idCat);
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
