<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idItem"])){
	$idItem=$_POST["idItem"];
} elseif(isset($_GET["idItem"])){
	$idItem=$_GET["idItem"];
} else $idItem=0;
?>
<?
$title = _("Anix - Suppression d'un élément");
include("../html_header.php");
setTitleBar(_("Suppression d'un élément"));
?>
<form id='main_form' action='./list_items.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='action' value='deleteitem'>
<input type='hidden' name='idItem' value='<?=$idItem?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request=request("SELECT $TBL_lists_items.id,$TBL_lists_items.active,$TBL_lists_items.id_category,$TBL_lists_info_items.name from $TBL_lists_items,$TBL_lists_info_items,$TBL_gen_languages where $TBL_lists_items.id=$idItem and $TBL_gen_languages.id='$used_language_id' and $TBL_lists_info_items.id_item=$TBL_lists_items.id and $TBL_lists_info_items.id_language=$TBL_gen_languages.id",$link);
$item = mysql_fetch_object($request);
$cancelLink="./list_items.php?action=edit";
if($item) $cancelLink.="&idCat=".$item->id_category;
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<tr>
<td colspan='2'>
<?
if(!$item){
	echo "<i><center>"._("Erreur : Cet élément n'existe pas!")."</center></i>";
} else {
?>
<center><b><font color='red'><?php echo _("Êtes vous sûr de vouloir supprimer l'élément ci-dessous ?"); ?></font></b></center><br>
<table align='center'>
<tr>
  <td colspan='2'><?php echo _("Si oui, veuillez cliquer sur \"OK\". Sinon, cliquez simplement sur le bouton \"Annuler\"."); ?><br><br>
  </td>
</tr>
<tr>
  <td><b><?php echo _("Nom")?>: </b></td>
  <td><?=$item->name ?></td>
</tr>
<tr>
  <td><b><?php echo _("État"); ?>: </b></td>
  <td>
  <?
  if($item->active=="Y") echo _("Actif");
  elseif($item->active=="N") echo _("Inactif");
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
