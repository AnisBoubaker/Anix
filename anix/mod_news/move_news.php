<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idNews"])){
	$idNews=$_POST["idNews"];
} elseif(isset($_GET["idNews"])){
	$idNews=$_GET["idNews"] ;
} else $idNews=0;
?>
<?
$title = _("Anix - Déplacement d'une nouvelle");
include("../html_header.php");
setTitleBar( _("Déplacement d'une nouvelle"));
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_news_news.id,$TBL_news_news.id_category,$TBL_news_news.active,$TBL_news_news.from_date,$TBL_news_news.to_date,$TBL_news_info_news.date,$TBL_news_info_news.title FROM $TBL_news_news,$TBL_news_info_news,$TBL_gen_languages where $TBL_news_news.id=$idNews and $TBL_gen_languages.id='$used_language_id' and $TBL_news_info_news.id_news=$TBL_news_news.id and $TBL_news_info_news.id_language=$TBL_gen_languages.id",$link);
$news=mysql_fetch_object($request);
$cancelLink="./list_news.php?action=edit";
if($news) $cancelLink.="&idCat=".$news->id_category;
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
  <TR>
    <TD colspan='2'>
    <?
    if(!$news){
    	echo "<i><CENTER>"._("Erreur : Cette nouvelle n'existe pas")."</CENTER></I>";
    } else {
    ?>
      <CENTER>
        <B><?php echo _("Veuillez sélectionner la catégorie dans laquelle vous souhaitez déplacer la nouvelle suivante"); ?>:</B>
      </CENTER>
      <BR>
      <BR>
      <TABLE width='60%' align='center'>
        <TR>
          <TD>
            <B><?php echo _("Date"); ?>:</B>
          </TD>
          <TD>
            <?=$news->date ?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("Nouvelle"); ?>:</B>
          </TD>
          <TD>
            <?=$news->title?>
          </TD>
        </TR>
        <TR>
          <TD>
            <B><?php echo _("État"); ?>:</B>
          </TD>
          <TD>
            <?
            if($news->active=="Y") echo _("Active");
            elseif($news->active=="N") echo _("Désactivée");
            elseif($news->active=="DATE"){
            	$currentDate=date("Y-m-d");
            	if($currentDate>=$news->from_date && $currentDate<=$news->to_date) echo _("Active");
            	elseif($currentDate<$news->from_date) echo _("En attente");
            	else echo _("Expirée");
            }elseif($news->active=="ARCHIVE") echo _("Archivée");
            ?>
          </TD>
        </TR>
      </TABLE>
		<?
		$categories=request("select $TBL_news_categories.id, $TBL_news_categories.id_parent, $TBL_news_categories.contain_items, $TBL_news_categories.ordering, $TBL_news_info_categories.name, $TBL_news_info_categories.description from  $TBL_news_categories,$TBL_gen_languages,$TBL_news_info_categories where $TBL_news_info_categories.id_news_cat=$TBL_news_categories.id and $TBL_news_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_news_categories.id_parent, $TBL_news_categories.ordering", $link);
		$tableCategories = getCatTable($categories);
		echo showCategories($tableCategories,0,0,"./list_news.php?action=move&idNews=$idNews&moveto=",false,true,$news->id_category);
      	?>
<?
    } //else !$news
      ?>
    </TD>
  </TR>
</TABLE>
<?
include ("../html_footer.php");
mysql_close($link);
?>
