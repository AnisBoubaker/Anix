<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idPhoto"])){
	$idPhoto=$_POST["idPhoto"];
} elseif(isset($_GET["idPhoto"])){
	$idPhoto=$_GET["idPhoto"] ;
} else $idPhoto=0;
?>
<?
$title = _("Anix - Copie d'une nouvelle");
include("../html_header.php");
setTitleBar( _("Copie d'une nouvelle"));
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_gallery_photo.id,$TBL_gallery_photo.id_category,$TBL_gallery_photo.active,$TBL_gallery_photo.from_date,$TBL_gallery_photo.to_date,$TBL_gallery_info_photo.date,$TBL_gallery_info_photo.title FROM $TBL_gallery_photo,$TBL_gallery_info_photo,$TBL_gen_languages where $TBL_gallery_photo.id=$idPhoto and $TBL_gen_languages.id='$used_language_id' and $TBL_gallery_info_photo.id_photo=$TBL_gallery_photo.id and $TBL_gallery_info_photo.id_language=$TBL_gen_languages.id",$link);
$photo=mysql_fetch_object($request);
$cancelLink="./list_photos.php?action=edit";
if($photo) $cancelLink.="&idCat=".$photo->id_category;
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
  <TR>
    <TD colspan='2'>
    <?
    if(!$photo){
    	echo "<i><CENTER>"._("Erreur : Cette nouvelle n'existe pas")."</CENTER></I>";
    } else {
    ?>
      <CENTER>
        <B><?php echo _("Veuillez sélectionner la catégorie dans laquelle vous souhaitez copier la nouvelle suivante"); ?>:</B>
      </CENTER>
      <BR>
      <BR>
      <TABLE width='60%' align='center'>
        <TR>
          <TD>
            <B><?php echo _("Date"); ?>:</B>
          </TD>
          <TD>
            <?=$photo->date ?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Nouvelle"); ?>:</B>
          </TD>
          <TD>
            <?=$photo->title?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("État"); ?>:</B>
          </TD>
          <TD>
            <?
            if($photo->active=="Y") echo _("Active");
            elseif($photo->active=="N") echo _("Désactivée");
            elseif($photo->active=="DATE"){
            	$currentDate=date("Y-m-d");
            	if($currentDate>=$photo->from_date && $currentDate<=$photo->to_date) echo _("Active");
            	elseif($currentDate<$photo->from_date) echo _("En attente");
            	else echo _("Expirée");
            }elseif($photo->active=="ARCHIVE") echo _("Archivée");
            ?>
          </TD>
        </TR>
      </TABLE>
<?
$categories=request("select $TBL_gallery_categories.id, $TBL_gallery_categories.id_parent, $TBL_gallery_categories.contain_items, $TBL_gallery_categories.ordering, $TBL_gallery_info_categories.name, $TBL_gallery_info_categories.description from  $TBL_gallery_categories,$TBL_gen_languages,$TBL_gallery_info_categories where $TBL_gallery_info_categories.id_gallery_cat=$TBL_gallery_categories.id and $TBL_gallery_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_gallery_categories.id_parent, $TBL_gallery_categories.ordering", $link);
$tableCategories = getCatTable($categories);
echo showCategories($tableCategories,0,0,"./list_photos.php?action=copy&idPhoto=$idPhoto&copyto=",false,true);
      ?>
<?
    } //else !$photo
      ?>
    </TD>
  </TR>
</TABLE>
<?
include ("../html_footer.php");
mysql_close($link);
?>
