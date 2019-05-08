<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idItem"])){
	$idItem=$_POST["idItem"];
} elseif(isset($_GET["idItem"])){
	$idItem=$_GET["idItem"] ;
} else $idItem=0;
?>
<? $title = _("Anix - Copie d'un élément");include("../html_header.php"); ?>
<TABLE border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request = request("SELECT $TBL_lists_items.id,$TBL_lists_items.id_category,$TBL_lists_items.active,$TBL_lists_info_items.name FROM $TBL_lists_items,$TBL_lists_info_items,$TBL_gen_languages where $TBL_lists_items.id=$idItem and $TBL_gen_languages.id='$used_language_id' and $TBL_lists_info_items.id_item=$TBL_lists_items.id and $TBL_lists_info_items.id_language=$TBL_gen_languages.id",$link);
$item=mysql_fetch_object($request);
setTitleBar(_("Copie d'un élément"));
$cancelLink="./list_items.php?action=edit";
if($item) $cancelLink.="&idCat=".$item->id_category."#".$item->id_category;
$button=array();
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<TABLE border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<TR> <TD colspan='2'>
<?
if(!$item){
	echo "<i><CENTER>"._("Erreur : Cet élément n'existe pas.")."</CENTER></I>";
} else {
?>
<CENTER> <B><?php echo _("Veuillez sélectionner la catégorie dans laquelle vous souhaitez copier l'élément suivant"); ?>:</B></CENTER> <BR> <BR><TABLE width='60%' align='center'><TR> <TD> <B><?php echo _("Élément"); ?>:</B></TD> <TD><?=$item->name ?></TD></TR><TR> <TD> <B><?php echo _("État"); ?>:</B></TD> <TD>
<?
if($item->active=="Y") echo _("Actif");
elseif($item->active=="N") echo _("Inactif");
            ?>
</TD></TR></TABLE>
<?
$tableCategories = getCatTable();
echo showCategories($tableCategories,0,0,"./list_items.php?action=copy&idItem=$idItem&copyto=",false,false);
?>
<?
} //else !$item
?>
</TD></TR></TABLE>
<?
include ("../html_footer.php");
mysql_close($link);
?>
