<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idFaq"])){
	$idFaq=$_POST["idFaq"];
} elseif(isset($_GET["idFaq"])){
	$idFaq=$_GET["idFaq"] ;
} else $idFaq=0;
?>
<?
$title = _("Anix - Déplacement d'une question");
include("../html_header.php");
setTitleBar(_("Déplacement d'une question"));
$cancelLink="./list_faq.php?action=edit";
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_faq_faq.id,$TBL_faq_faq.id_category,$TBL_faq_faq.active,$TBL_faq_info_faq.question FROM $TBL_faq_faq,$TBL_faq_info_faq,$TBL_gen_languages where $TBL_faq_faq.id=$idFaq and $TBL_gen_languages.id='$used_language_id' and $TBL_faq_info_faq.id_faq=$TBL_faq_faq.id and $TBL_faq_info_faq.id_language=$TBL_gen_languages.id",$link);
$faq=mysql_fetch_object($request);
if($faq) $cancelLink.="&idCat=".$faq->id_category;;
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
  <TR>
    <TD colspan='2'>
    <?
    if(!$faq){
    	echo "<i><CENTER>"._("Erreur : Cette question n'existe pas")."</CENTER></I>";
    } else {
    ?>
      <CENTER>
        <B><?php echo _("Veuillez sélectionner la catégorie dans laquelle vous souhaitez déplacer la question suivante:");?></B>
      </CENTER>
      <BR>
      <BR>
      <TABLE width='60%' align='center'>
        <TR>
          <TD>
            <B><?php echo _("Question:");?></B>
          </TD>
          <TD>
            <?=$faq->question ?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("État:"); ?></B>
          </TD>
          <TD>
            <?
            if($faq->active=="Y") echo _("Active");
            elseif($faq->active=="N") echo _("Inactive");
            ?>
          </TD>
        </TR>
      </TABLE>
<?
$categories=request("select $TBL_faq_categories.id, $TBL_faq_categories.id_parent, $TBL_faq_categories.contain_items, $TBL_faq_categories.ordering, $TBL_faq_info_categories.name, $TBL_faq_info_categories.description from  $TBL_faq_categories,$TBL_gen_languages,$TBL_faq_info_categories where $TBL_faq_info_categories.id_faq_cat=$TBL_faq_categories.id and $TBL_faq_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_faq_categories.id_parent, $TBL_faq_categories.ordering", $link);
$tableCategories = getCatTable($categories);
echo showCategories($tableCategories,0,0,"./list_faq.php?action=move&idFaq=$idFaq&moveto=",false,true,$faq->id_category);
      ?>
<?
    } //else !$faq
      ?>
    </TD>
  </TR>
</TABLE>
<?
include ("../html_footer.php");
mysql_close($link);
?>
