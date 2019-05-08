<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idPage"])){
	$idPage=$_POST["idPage"];
} elseif(isset($_GET["idPage"])){
	$idPage=$_GET["idPage"];
}
?>
<? $title = _("Anix - Suppression d'une page dynamique");
include("../html_header.php");
setTitleBar(_("Suppression d'une page dynamique"));
?>
<?
$result=request("SELECT $TBL_content_pages.type,
						$TBL_content_pages.id_category,
						$TBL_content_pages.link_module,
						$TBL_content_pages.link_id_item,
						$TBL_content_info_pages.title
				 FROM $TBL_content_pages,$TBL_content_info_pages,$TBL_gen_languages
				 WHERE $TBL_content_info_pages.id_page='$idPage'
				 AND $TBL_gen_languages.id='$used_language_id'
				 AND $TBL_content_pages.id=$TBL_content_info_pages.id_page
				 AND $TBL_content_info_pages.id_language=$TBL_gen_languages.id", $link);
$page=mysql_fetch_object($result);
$idCategory = $page->id_category;
$cancelLink="./list_pages.php?idCategory=$idCategory";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);

if($page->type=="page") $pageTitle = $page->title;
elseif($page->type=="link") $pageTitle = getLinkName($page->link_module,$page->link_id_item,$link);
?>

<form id='main_form' name='del' action='./list_pages.php' method='POST' enctype='multipart/form-data'>
  <input type='hidden' name='action' value='delete'>
  <input type='hidden' name='idPage' value='<?=$idPage?>'>
  <input type='hidden' name='idCategory' value='<?=$idCategory?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
  <td colspan='2'>
  <center><b><font color='red'>
  <?php
  switch($page->type){
  	case "page":echo _("Etes vous sûr de vouloir supprimer la page")." ".$pageTitle;break;
  	case "link":echo _("Etes vous sûr de vouloir supprimer la page liée vers").": ".$pageTitle;break;
  }
  ?></font><br><br>
  <i><?php echo _("NB: Cette action est irrecuperable.");//$str4?></i>
  </b></center>
  </td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>

