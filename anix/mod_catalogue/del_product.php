<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["idProduct"])){
	$idProduct=$_POST["idProduct"];
} elseif(isset($_GET["idProduct"])){
	$idProduct=$_GET["idProduct"];
} else $idProduct=0;
?>
<?
$title = _("Anix - Suppression d'un produit");include("../html_header.php");
setTitleBar(_("Suppression d'un produit"));
?>
<form id='main_form' action='./list_products.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='action' value='deleteproduct'>
<input type='hidden' name='idProduct' value='<?=$idProduct?>'>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<?
$request=request("SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.active,$TBL_catalogue_products.id_category,$TBL_catalogue_info_products.name from $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages where $TBL_catalogue_products.id=$idProduct and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id",$link);
$product = mysql_fetch_object($request);
$cancelLink="./list_products.php?action=edit";
if($product) $cancelLink.="&idCat=".$product->id_category;
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>$cancelLink);
printButtons($buttons);
?>
<tr>
<td colspan='2'>
<?
if(!$product){
	echo "<i><center>"._("Erreur : Ce produit n'existe pas!")."</center></i>";
} else {
?>
<center><b><font color='red'><?php echo _("Êtes vous sûr de vouloir supprimer le produit ci-dessous ?"); ?></font></b></center><br>
<table align='center'>
<tr>
  <td colspan='2'><?php echo _("Si oui, veuillez cliquer sur \"OK\". Sinon, cliquez simplement sur le bouton \"Annuler\"."); ?><br><br>
  </td>
</tr>
<tr>
  <td><b><?php echo _("Nom")?>: </b></td>
  <td><?=$product->name ?></td>
</tr>
<tr>
  <td><b><?php echo _("État"); ?>: </b></td>
  <td>
  <?
  if($product->active=="Y") echo _("Actif");
  elseif($product->active=="N") echo _("Inactif");
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
