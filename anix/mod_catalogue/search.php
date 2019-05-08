<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$action="";
$nb=0;
if(isset($_REQUEST["action"])){
	$action=$_REQUEST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
?>
<?php
include("./search.actions.php");
?>
<?
$title = _("Anix - Catalogue");
include("../html_header.php");
setTitleBar(_("Module: Catalogue - Recherche"));
?>
<table width='95%' align='center'>
<tr valign='top'>
<td>
  <form action='./search.php' method='GET'>
  <input type='hidden' name='action' value='search_product'>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Rechercher un produit"); ?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><?php echo _("Nom"); ?>:</td>
          <td><input type='text' name='name' size='30' <?
          if($action=="search_product") echo "value='".$_REQUEST["name"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Réf."); ?>:</td>
          <td><input type='text' name='ref' size='20' <?
          if($action=="search_product") echo "value='".$_REQUEST["ref"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("UPC"); ?>:</td>
          <td><input type='text' name='upc_code' size='20' <?
          if($action=="search_product") echo "value='".$_REQUEST["upc_code"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Catégorie"); ?>:</td>
          <td><SELECT name='category'>
        	  <option value='0'>-- <?php echo _("Toutes")?> --</option>
        	  <?
        	  $categories=request("select $TBL_catalogue_categories.id, $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering,$TBL_catalogue_categories.contain_products,$TBL_catalogue_info_categories.name, $TBL_catalogue_info_categories.description from  $TBL_catalogue_categories,$TBL_gen_languages,$TBL_catalogue_info_categories where $TBL_catalogue_info_categories.id_catalogue_cat=$TBL_catalogue_categories.id and $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_categories.id_parent, $TBL_catalogue_categories.ordering", $link);
        	  $tableCategories = getCatTable($categories);
        	  $listCategories = getCategoriesList($tableCategories,0 , 0);
        	  foreach($listCategories as $row){
        	  	echo "<option value='".$row["id"]."'";
        	  	if($action=="search_product" && $_REQUEST["category"]==$row["id"]) echo " SELECTED";
        	  	echo ">".$row["name"]."</option>";
        	  }
        	  ?>
        	</SELECT></td>
      	</tr>
        <tr>
          <td><?php echo _("Marque"); ?>:</td>
          <td><select name='brand'>
          <option value='0'>-- <?php echo _("Toutes"); ?> --</option>
          <?
          $tmp=request("SELECT id,name from $TBL_catalogue_brands order by name",$link);
          while($brand=mysql_fetch_object($tmp)){
          	echo "<option value='".$brand->id."'";
          	if($action=="search_product" && $_REQUEST["brand"]==$brand->id) echo " SELECTED";
          	echo ">".$brand->name."</option>";
          }
          ?></select></td>
        </tr>
        <tr>
          <td><?php echo _("Type de produit"); ?>:</td>
          <td><SELECT name='product_type'>
        	  <option value='0'<?
        	  if($action=="search_product" && $_REQUEST["product_type"]==0) echo " SELECTED";
            ?>>-- <?php echo _("Tous"); ?> --</option>
        	  <option value='good'<?
        	  if($action=="search_product" && $_REQUEST["product_type"]=="good") echo " SELECTED";
            ?>><?php echo _("Biens"); ?></option>
        	  <option value='service'<?
        	  if($action=="search_product" && $_REQUEST["product_type"]=="service") echo " SELECTED";
            ?>><?php echo _("Services"); ?></option>
        	</SELECT></td>
      	</tr>
        <tr>
          <td><?php echo _("Status"); ?>:</td>
          <td><SELECT name='active'>
        	  <option value='0'<?
        	  if($action=="search_product" && $_REQUEST["active"]=="0") echo " SELECTED";
            ?>>-- <?php echo _("Tous"); ?> --</option>
        	  <option value='Y'<?
        	  if($action=="search_product" && $_REQUEST["active"]=="Y") echo " SELECTED";
            ?>><?php echo _("Activés seulement"); ?></option>
        	  <option value='N'<?
        	  if($action=="search_product" && $_REQUEST["active"]=="N") echo " SELECTED";
            ?>><?php echo _("Désactivés seulement"); ?></option>
        	</SELECT></td>
      	</tr>
      	<tr>
          <td><?php echo _("État du stock"); ?>:</td>
          <td>
          <?php echo _("MIN");?>: <input type='text' name='stock_min' size='10' <?
          if($action=="search_product") echo "value='".$_REQUEST["stock_min"]."'";
          ?>>&nbsp;
          <?php echo _("MAX");?>: <input type='text' name='stock_max' size='10' <?
          if($action=="search_product") echo "value='".$_REQUEST["stock_max"]."'";
          ?>>
          </td>
        </tr>
        <tr>
			<td><?php echo _("Délai d'approvisionnement"); ?>:
			<td>
			  <select name='restocking_delay'>
			   <option value='0'>-- <?php echo _("Tous"); ?> --</option>
			   <?
			    $delays=request("
			    	SELECT `$TBL_catalogue_restocking_delay`.`id`,`$TBL_catalogue_info_restocking_delay`.`name`
			    	FROM `$TBL_catalogue_restocking_delay`,`$TBL_catalogue_info_restocking_delay`
			    	WHERE `$TBL_catalogue_info_restocking_delay`.`id_language`='$used_language_id'
			    	AND `$TBL_catalogue_info_restocking_delay`.`id_delay`=`$TBL_catalogue_restocking_delay`.`id`
			    	ORDER BY `$TBL_catalogue_restocking_delay`.`delay_days`",$link);
			    while($delay=mysql_fetch_object($delays)){
			    	echo "<option value=\"".$delay->id."\"";
			    	if($action=="search_product" && $_REQUEST["restocking_delay"]==$delay->id) echo " SELECTED";
			    	echo ">".$delay->name."</option>";
			    }
			    ?>
			  </select>
			</td>
		</tr>
		<tr>
			<td><?php echo _("Fournisseur"); ?>:
			<td>
			  <select name='id_supplier'>
			   <option value='0'>-- <?php echo _("Tous"); ?> --</option>
			   <?
			    $suppliers=request("SELECT `id`,`name` FROM `$TBL_ecommerce_supplier` ORDER BY `name`",$link);
			    while($supplier=mysql_fetch_object($suppliers)){
			    	echo "<option value=\"".$supplier->id."\"";
			    	if($action=="search_product" && $_REQUEST["id_supplier"]==$supplier->id) echo " SELECTED";
			    	echo ">".$supplier->name."</option>";
			    }
			    ?>
			  </select>
			</td>
		</tr>
        <tr>
          <td><?php echo _("Mots clés"); ?>:</td>
          <td><input type='text' name='keywords' size='30' <?
          if($action=="search_product") echo "value='".$_REQUEST["keywords"]."'";
          ?>></td>
        </tr>
        <tr>
          <td valign='top'><?php echo _("Filtres"); ?>:</td>
          <td>
          <?php
          $requestStr = "
	        	SELECT `catalogue_state`.`id`, `catalogue_state`.`name`
	        	FROM `catalogue_state`
				ORDER BY `ordering`";
          $tmpRequest = request($requestStr,$link);
	        ?>
	        <table>
	        	<?php
	        	$newLine = true;
	        	while($productState = mysql_fetch_object($tmpRequest)){
	        		if($newLine){
	        			$newLine=false;
	        			echo "<tr>";
	        		} else $newLine=true;
	        		echo "<td>";
	        		echo "<input type='checkbox' name='state_".$productState->id."'";
	        		if($action=="search_product" && isset($_REQUEST["state_".$productState->id])) echo " CHECKED";
	        		echo ">";
	        		echo "</td>";
	        		echo "<td>".$productState->name."</td>";
	        		if($newLine){
	        			echo "</tr>";
	        		}
	        	}
	        	?>
	        </table>
          </td>
        </tr>
        </table>
      </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
      </td>
    </tr>
  </table>
  </form>
</td>
<td>
  <form action='./search.php' method='POST'>
  <input type='hidden' name='action' value='search_category'>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Rechercher une catégorie"); ?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><?php echo _("Catégorie"); ?>:</td>
          <td><input type='text' name='name' size='30' <?
          if($action=="search_category") echo "value='".$_REQUEST["name"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Mots clés"); ?>:</td>
          <td><input type='text' name='keywords' size='30' <?
          if($action=="search_category") echo "value='".$_REQUEST["keywords"]."'";
          ?>></td>
        </tr>
        </table>
      </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
      </td>
    </tr>
  </table>
  </form>
</td>
</tr>
</table>

<?
if(($action=="search_product" || $action=="search_category") && $nb){
?>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      <font class='edittable_header'><?php echo _("Résultats de la recherche"); ?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
      &nbsp;
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <center><?php echo _("Votre recherche a retourné")." ".$nbResults." "._("résultat(s)"); ?></center><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width='100%' class='message'>
  <?
  if($nbResults){
  	while($result=mysql_fetch_object($request)){
  		if($action=="search_product"){
  			echo "<tr>";
  			echo "<td valign='middle' width='42' bgcolor='#e7eff2' align='right'><a href='./mod_product.php?action=edit&idProduct=".$result->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a></td>";
  			echo "<td>";
  			echo $result->name." (Ref.:".$result->ref_store.")";
  			if($result->image_file_orig=="") echo " <img src='../images/no_picture.jpg' alt=\""._("Pas d'image pour ce produit.")."\" style='vertical-align:middle;' />";
  			echo "</td>";
  			echo "</tr>";
  			//echo "<tr><td valign='middle' width='42' bgcolor='#e7eff2' align='right'>&nbsp;</td><td><hr></td></tr>";
  		}
  		if($action=="search_category"){
  			echo "<tr>";
  			echo "<td valign='middle' width='42' bgcolor='#e7eff2' align='right'><a href='./mod_category.php?action=edit&idCat=".$result->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a></td>";
  			echo "<td>".$result->name."</td>";
  			echo "</tr>";
  		}
  	}
  }
  ?>
    </table>
    </td>
  </tr>
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      &nbsp;
    </td>
    <td background='../images/button_back.jpg' align='right'>
      &nbsp;
    </td>
  </tr>
  </table>
<?
} //if search
?>
<?
include ("../html_footer.php");
mysql_close($link);
?>
