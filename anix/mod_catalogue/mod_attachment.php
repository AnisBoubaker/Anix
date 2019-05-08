<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
//Defining default values
$idCat=0;
$requestString="";
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idProduct"])){
	$idProduct=$_POST["idProduct"];
} elseif(isset($_GET["idProduct"])){
	$idProduct=$_GET["idProduct"];
} else $idProduct=0;
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory=0;
if(isset($_POST["idAttachment"])){
	$idAttachment=$_POST["idAttachment"];
} elseif(isset($_GET["idAttachment"])){
	$idAttachment=$_GET["idAttachment"];
} else $idAttachment=0;


?>
<?php
$title = _("Anix - Fichiers Attachés - Edition");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'un fichier attaché"));break;
	case "edit":setTitleBar(_("Modification d'un fichier attaché"));break;
	default:setTitleBar(_("Modification d'un fichier attaché"));break;
}
?>
<?
if($idProduct){
	echo "<form id='main_form' action='./mod_product.php' method='POST' enctype='multipart/form-data'>";
} elseif($idCategory){
	echo "<form id='main_form' action='./mod_category.php' method='POST' enctype='multipart/form-data'>";
}
if($idProduct){
	$cancelLink="./mod_product.php?action=edit&idProduct=$idProduct";
} elseif($idCategory){
	$cancelLink="./mod_category.php?action=edit&idCat=$idCategory";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>
<table border="0" align="center" width="75%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2'>
<?
if($action=="add") {
	echo "<input type='hidden' name='action' value='addAttachment'>";
	echo "<input type='hidden' name='idProduct' value='$idProduct'>";
	echo "<input type='hidden' name='idCat' value='$idCategory'>";
	if($idProduct){
		$result=request("select $TBL_catalogue_info_products.name product from $TBL_catalogue_info_products,$TBL_gen_languages where $TBL_catalogue_info_products.id_product='$idProduct' and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id'",$link);
		$attachment=mysql_fetch_object($result);
	} elseif($idCategory){
		$result=request("select $TBL_catalogue_info_categories.name category from $TBL_catalogue_info_categories,$TBL_gen_languages where $TBL_catalogue_info_categories.id_catalogue_cat='$idCategory' and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id'",$link);
		$attachment=mysql_fetch_object($result);
	}

}
if($action=="edit") {
	echo "<input type='hidden' name='action' value='updateAttachment'>";
	echo "<input type='hidden' name='idProduct' value='$idProduct'>";
	echo "<input type='hidden' name='idCategory' value='$idCategory'>";
	echo "<input type='hidden' name='idAttachment' value='$idAttachment'>";
	if($idProduct){
		$result=request("select $TBL_catalogue_attachments.id,$TBL_catalogue_attachments.file_name,$TBL_catalogue_attachments.title,$TBL_catalogue_attachments.description,$TBL_catalogue_attachments.id_language,$TBL_catalogue_attachments.id_product,$TBL_catalogue_info_products.name product from $TBL_catalogue_attachments,$TBL_catalogue_info_products,$TBL_gen_languages where $TBL_catalogue_attachments.id='$idAttachment' and $TBL_catalogue_attachments.id_product=$TBL_catalogue_info_products.id_product and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id'",$link);
		$attachment=mysql_fetch_object($result);
	} elseif($idCategory){
		$result=request("select $TBL_catalogue_attachments.id,$TBL_catalogue_attachments.file_name,$TBL_catalogue_attachments.title,$TBL_catalogue_attachments.description,$TBL_catalogue_attachments.id_language,$TBL_catalogue_attachments.id_category,$TBL_catalogue_info_categories.name category from $TBL_catalogue_attachments,$TBL_catalogue_info_categories,$TBL_gen_languages where $TBL_catalogue_attachments.id='$idAttachment' and $TBL_catalogue_attachments.id_category=$TBL_catalogue_info_categories.id_catalogue_cat and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id'",$link);
		$attachment=mysql_fetch_object($result);
	}
}
?>

<table border="0" width='100%' align='center'>
<tr>
  <?
  if($idProduct){
  	echo "<td>"._("Nom du produit").": </td>";
  	echo "<td>".$attachment->product."</td>";
  } elseif($idCategory){
  	echo "<td>"._("Nom de la catégorie").":</td>";
  	echo "<td>".$attachment->category."</td>";
  }
  ?>
</tr>
<tr>
  <?
  if($action=="edit"){
  	echo "<td><B>"._("Fichier actuel").":</B></td>";
  	if($attachment->file_name!="") echo "<td><a href='../".$CATALOG_folder_attachments.$attachment->file_name."'>"._("Télécharger")."</a></td>";
  	else echo "<td><i>"._("Aucun ficher spécifié")."</i></td>";
  	echo "</tr>";
  	echo "<tr>";
  	echo "<td><B><i>"._("Nouveau fichier (Optionnel)")."</i>:</B></td><td><input type='file' name='attachment_file' size='30'></td>";
  	echo "</tr>";
  } else {
  	echo "<td><B>"._("Fichier").":</B></td><td><input type='file' name='attachment_file' size='30'></td>";
  }
  ?>
</tr>
<tr>
  <td><B><?php echo _("Langue"); ?>:</B></td>
  <td><SELECT name='id_language'>
  <?
  $request=request("SELECT id,name from $TBL_gen_languages ORDER BY name",$link);
  while($language=mysql_fetch_object($request)){
  	echo "<option value='".$language->id."'";
  	if($action=="edit" && $attachment->id_language==$language->id){
  		echo " SELECTED";
  	}
  	echo ">".$language->name."</option>";
  }
  ?>
  </SELECT>
  </td>
</tr>
<tr>
  <td><B><?php echo _("Titre du fichier"); ?>:</B></td><td><input type='text' name='title' size='30' value="<? if($action=="edit") echo $attachment->title; ?>"></td>
</tr>
</table><br>
<B><?php echo _("Déscription"); ?>: </B> <br>
<?php
echo "<textarea name='description' style='width:100%;height:600px;'>";
if($action=="add"){ echo $CATALOG_editor_default_value; }
if($action=="edit"){ echo unhtmlentities($attachment->description); }
echo "</textarea>";
?>

</td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
