<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$action="";
if(isset($_POST["action"])){
	$action=$_POST["action"];
}
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
$category=mysql_fetch_object(request("SELECT $TBL_catalogue_info_categories.name from $TBL_catalogue_info_categories,$TBL_gen_languages where $TBL_catalogue_info_categories.id_catalogue_cat='$idCat' and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id",$link));
$catName=$category->name;
if($action=="update"){
	$products=request("select $TBL_catalogue_products.id from $TBL_catalogue_products where $TBL_catalogue_products.id_category='$idCat'",$link);
	while($product=mysql_fetch_object($products)){
		//updates general information
		$requestString = "UPDATE $TBL_catalogue_products set ";
		$requestString.= "`public_price`='".$_POST["public_price_".$product->id]."',";
		$requestString.= "`is_in_special`='".(isset($_POST["is_in_special_".$product->id])?"Y":"N")."',";
		$requestString.= "`special_price`='".$_POST["public_price_special_".$product->id]."',";
		$requestString.= "`stock`='".$_POST["stock_".$product->id]."',";
		$requestString.= "`restocking_delay`='".$_POST["restocking_delay_".$product->id]."',";
		$requestString.= "`public_special`='".(isset($_POST["public_special_".$product->id])?"Y":"N")."' ";
		$requestString.= "where id='".$product->id."'";
		//echo $requestString;
		request($requestString,$link);
		if(mysql_errno($link)){
			$errMessage.=_("Une erreur s'est produite lors de la mise à jour des produits.")."<br />";
			$errors++;
		}
		//updates special prices
		$spec_prices=request("SELECT $TBL_catalogue_price_groups.id from $TBL_catalogue_price_groups",$link);
		while($spec_price=mysql_fetch_object($spec_prices)){
			$requestString = "UPDATE $TBL_catalogue_product_prices set ";
			$requestString.= "`price`='".$_POST["price_spec_".$spec_price->id."_".$product->id]."',";
			$requestString.= "`is_in_special`='".(isset($_POST["price_spec_is_in_special_".$spec_price->id."_".$product->id])?"Y":"N")."',";
			$requestString.= "`special_price`='".$_POST["price_spec_special_".$spec_price->id."_".$product->id]."' ";
			$requestString.= "where id_product='".$product->id."' and id_price_group='".$spec_price->id."'";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour des prix des produit.")."<br />";
				$errors++;
			}
		}
	}
	if(!$errors){
		$message = _("Les produits ont été mis à jour correctement.")."<br />";
	}
}
?>
<?
$title = _("Mise à jour des produits de la catégorie")." ".$catName;
include("../html_header.php");
setTitleBar(_("Mise à jour des produits de la catégorie"));
$cancelLink = "./list_categories.php?action=updateproducts";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>
<form id='main_form' action='./update_products.php' method='POST'>
<input type='hidden' name='idCat' value='<?=$idCat?>'>
<input type='hidden' name='action' value='update'>
<?
//get the restocking delays
$request=request("
		    	SELECT `$TBL_catalogue_restocking_delay`.`id`,`$TBL_catalogue_info_restocking_delay`.`name`
		    	FROM `$TBL_catalogue_restocking_delay`,`$TBL_catalogue_info_restocking_delay`
		    	WHERE `$TBL_catalogue_info_restocking_delay`.`id_language`='$used_language_id'
		    	AND `$TBL_catalogue_info_restocking_delay`.`id_delay`=`$TBL_catalogue_restocking_delay`.`id`
		    	ORDER BY `$TBL_catalogue_restocking_delay`.`delay_days`",$link);
$delays=array();
while($delay=mysql_fetch_object($request)){
	$delays[]=array("id"=>$delay->id, "name"=>$delay->name);
}
//Get the products
$products=request("select $TBL_catalogue_products.id,$TBL_catalogue_products.public_price,$TBL_catalogue_products.is_in_special,$TBL_catalogue_products.public_special,$TBL_catalogue_products.special_price,$TBL_catalogue_info_products.name,$TBL_catalogue_products.stock,$TBL_catalogue_products.restocking_delay,$TBL_catalogue_products.ref_store from $TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages where $TBL_catalogue_products.id_category='$idCat' and $TBL_catalogue_info_products.id_product=$TBL_catalogue_products.id and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_products.ordering",$link);
if(mysql_num_rows($products)){
?>
  <table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr>
  <td colspan='2'>
  <table width='100%'>
  <?
  $viewed=false;
  while($product=mysql_fetch_object($products)){
  	if($viewed){
  		echo "<tr><td colspan='5'><hr></td></tr>";
  	}
  	$viewed = true;
  	echo "<tr valign='top'>";
  	echo "<td><b>".$product->name."</b><br><u>"._("Réf.").":</u>".$product->ref_store."</td>";
  	echo "<td nowrap='nowrap'><br>";
  	echo "&nbsp;&nbsp;&nbsp;<u>"._("Prix public").":</u> ";
  	echo "<input type='text' name='public_price_".$product->id."' size='10' value='".$product->public_price."'>";
  	//Get special prices
  	$spec_prices=request("SELECT $TBL_catalogue_price_groups.id,$TBL_catalogue_product_prices.price,$TBL_catalogue_info_price_groups.name from $TBL_catalogue_product_prices,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups where $TBL_catalogue_product_prices.id_product='".$product->id."' and $TBL_catalogue_info_price_groups.id_language='$used_language_id' and $TBL_catalogue_product_prices.id_price_group=$TBL_catalogue_price_groups.id and $TBL_catalogue_info_price_groups.id_price_group=$TBL_catalogue_price_groups.id order by $TBL_catalogue_info_price_groups.name",$link);
  	while($spec_price=mysql_fetch_object($spec_prices)){
  		echo "<br>";
  		echo "&nbsp;&nbsp;&nbsp;<u>".$spec_price->name.":</u> ";
  		echo "<input type='text' name='price_spec_".$spec_price->id."_".$product->id."' size='10' value='".$spec_price->price."'>";
  	}
  	echo "</td>";
  	echo "<td align='left' nowrap='nowrap'>";
  	echo "&nbsp;&nbsp;&nbsp;<u><input type='checkbox' name='is_in_special_".$product->id."'".($product->is_in_special=="Y"?" CHECKED":"").">"._("Spéciaux activés").":</u><br>";
  	echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='public_special_".$product->id."'".($product->public_special=="Y"?" CHECKED":"").">";
  	echo "<u>"._("Prix public")."</u> <input type='text' name='public_price_special_".$product->id."' size='10' value='".$product->special_price."'>";
  	//Get special prices
  	$spec_prices=request("SELECT $TBL_catalogue_price_groups.id,$TBL_catalogue_product_prices.is_in_special,$TBL_catalogue_product_prices.special_price,$TBL_catalogue_info_price_groups.name from $TBL_catalogue_product_prices,$TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups WHERE $TBL_catalogue_product_prices.id_product='".$product->id."' and $TBL_catalogue_info_price_groups.id_language='$used_language_id' and  $TBL_catalogue_product_prices.id_price_group=$TBL_catalogue_price_groups.id and $TBL_catalogue_info_price_groups.id_price_group=$TBL_catalogue_price_groups.id order by $TBL_catalogue_info_price_groups.name",$link);
  	while($spec_price=mysql_fetch_object($spec_prices)){
  		echo "<br>";
  		echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='price_spec_is_in_special_".$spec_price->id."_".$product->id."'".($spec_price->is_in_special=="Y"?" CHECKED":"")."><u>".$spec_price->name.":</u> ";
  		echo "<input type='text' name='price_spec_special_".$spec_price->id."_".$product->id."' size='10' value='".$spec_price->special_price."'>";
  	}
  	echo "</td>";
  	echo "<td align='left' nowrap='nowrap'>";
  	echo "<u>"._("Stock").":</u> <input type='text' name='stock_".$product->id."' size='10' value='".$product->stock."'><br>";
  	echo "<u>"._("Aprovisionnement").":</u> <select name='restocking_delay_".$product->id."'>";
  	foreach($delays as $delay){
  		echo "<option value='".$delay["id"]."'";
  		if($product->restocking_delay==$delay["id"]) echo " selected='selected'";
  		echo ">".$delay["name"]."</option>";
  	}
  	echo "</select>";
  	echo "</td>";
  	echo "</tr>";
  }
  ?>
  </table>
  </td>
  </tr>
  </table>
</form>
<?
} // products
?>
<?
include ("../html_footer.php");
mysql_close($link);
?>
