<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idFaq"])){
	$idFaq=$_POST["idFaq"];
} elseif(isset($_GET["idFaq"])){
	$idFaq=$_GET["idFaq"];
} else $idFaq=0;
?>
<?
$title=_("Anix - Suppression d'une question");
include("../html_header.php");
setTitleBar(("Suppression d'une question"));
?>
<form id='main_form' action='./list_faq.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='action' value='deletefaq'>
<input type='hidden' name='idFaq' value='<?=$idFaq?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request=request("SELECT $TBL_faq_faq.id,$TBL_faq_faq.id_category,$TBL_faq_faq.active,$TBL_faq_info_faq.question from $TBL_faq_faq,$TBL_faq_info_faq,$TBL_gen_languages where $TBL_faq_faq.id=$idFaq and $TBL_gen_languages.id='$used_language_id' and $TBL_faq_info_faq.id_faq=$TBL_faq_faq.id and $TBL_faq_info_faq.id_language=$TBL_gen_languages.id",$link);
$faq = mysql_fetch_object($request);
$cancelLink="./list_faq.php?action=edit";
if($faq) $cancelLink.="&idCat=".$faq->id_category;
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<tr>
<td colspan='2'>
<?
if(!$faq){
	echo "<i><center>"._("Erreur : Cette question n'existe pas")."</center></i>";
} else {
?>
<center><b><font color='red'><?php echo _("Êtes vous sûr de vouloir supprimer la question ci-dessous ?");?></font></b></center><br><br>
<table align='center'>
<tr>
  <td colspan='2'><?php echo _("Si oui, veuillez cliquer sur \"OK\". Sinon, cliquez simplement sur le bouton \"Annuler\"."); ?><br><br>
  </td>
</tr>
<tr>
  <td><b><?php echo _("Question:"); ?></b></td>
  <td><?=$faq->question ?></td>
</tr>

<tr>
  <td><b><?php echo _("État:"); ?></b></td>
  <td>
  <?
  if($faq->active=="Y") echo _("Active");
  elseif($faq->active=="N") echo _("Inactive");
  ?>
  </td>
</tr>
</table>
<?
} //else !$category
?>
</td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
