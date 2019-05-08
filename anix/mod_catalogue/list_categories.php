<?
	include ("../config.php");
	include ("./module_config.php");
	$link = dbConnect();
	$delete=false;
	$errMessage="";
	$message="";
	$errors=0;
	if(isset($_POST["action"])){
	  $action=$_POST["action"];
	} elseif(isset($_GET["action"])){
	  $action=$_GET["action"];
	} else $action="";
?>
<?php
include("./list_categories.actions.php");
?>
<?
  $title = _("Anix - Liste des catégories");
  include("../html_header.php");
  switch($action){
  	case "add":setTitleBar(_("Ajout d'une catégorie de produits"));break;
  	case "edit":setTitleBar(_("Modification des catégories de produits"));break;
  	case "addproduct":setTitleBar(_("Ajout d'un produit"));break;
  	case "updateproducts":setTitleBar(_("Mise à jour de produits"));break;
  	case "modproduct":setTitleBar(_("Modification des produits"));break;
  }
?>
<table border="0" align="center" width="100%" CellPadding="0" CellSpacing="0" bgcolor="#FFFFFF">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
  </td>
  <td background='../images/button_back.jpg' align='right'>
    &nbsp;
  </td>
</tr>
<tr>
<td colspan='2'>
<?
  $categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.deletable, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products, $TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
  $nbCategories = mysql_num_rows($categories);
  if($action=="add"){
    echo _("Veuillez cliquer sur le nom de la catégorie parente sous laquelle vous souhaitez ajouter la nouvelle catégorie.")."<br><br>";
  	echo "<table class='message' width='100%'>";
  	echo "<tr>";
  	echo "<td><a href='./mod_category.php?action=add&idCat=0'>"._("Premier niveau")."</a></td>";
  	echo "</tr>";
  	echo "</table>";
  	$tableCategories = getCatTable($categories);
  	echo showCategories($tableCategories,0,0,"./mod_category.php?action=add&idCat=%%ID_CAT%%",false,true);
  }
  if($action=="edit"){
    if($nbCategories) {
	  $tableCategories = getCatTable($categories);
    //var_dump($tableCategories);
	  echo showCategories($tableCategories,0,0,"",true,true);
	} else {
	  echo "<table class='message' width='100%'>";
	  echo "<tr>";
	  echo "<td align='center'><b>"._("La base de données ne contient aucune catégorie de produits")."</b></td>";
	  echo "</tr>";
	  echo "</table>";
	}
  }
  if($action=="addproduct"){
    echo _("Veuillez cliquer sur le nom de la catégorie sous laquelle vous souhaitez ajouter le produit.")."<br><br>";
	  $tableCategories = getCatTable($categories);
	  echo showCategories($tableCategories,0,0,"./mod_product.php?action=add&idCat=%%ID_CAT%%",false,false);
  }
  if($action=="modproduct"){
    echo _("Veuillez cliquer sur le nom de la catégorie où se trouve le produit à modifier.")."<br><br>";
  	$tableCategories = getCatTable($categories);
  	echo showCategories($tableCategories,0,0,"./list_products.php?idCat=%%ID_CAT%%",false,false);
  }
  if($action=="updateproducts"){
    echo _("Veuillez cliquer sur le nom de la catégorie où se trouve les produits à mettre à jour.")."<br><br>";
  	$tableCategories = getCatTable($categories);
  	echo showCategories($tableCategories,0,0,"./update_products.php?idCat=%%ID_CAT%%",false,false);
  }
?>
</td></tr>
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'>&nbsp;</font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
    &nbsp;
  </td>
</tr>
</table>
</form>
<?
	include ("../html_footer.php");
	mysql_close($link);
?>
