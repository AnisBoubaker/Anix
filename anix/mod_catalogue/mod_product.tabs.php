<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
	<table width='95%'>
	<tr>
	<td>
	<?
	if($action=="edit" || $action=="update"){
		echo "<a href='../".$CATALOG_folder_images.$edit->image_file_large."' target='_blank'><img class='item_image' src='../".$CATALOG_folder_images.$edit->image_file_small."' alt=\""._("Agrandir")."\"></a>";
	}
	if($action=="add" || $action=="insert"){
		echo "<a href='../$CATALOG_folder_images/imgprd_large_no_image.jpg' target='_blank' alt='Agrandir l\'image'><img class='item_image' src='../$CATALOG_folder_images/imgprd_small_no_image.jpg' alt=\""._("Agrandir")."\"></a>";
	}
      ?>
      <table>
	  	<tr>
	  		<td><input type='radio' name='image_action' value='keep' checked='checked' /></td>
	  		<td><?php echo _("Conserver cette image"); ?></td>
	  	</tr>
	  	<tr>
	  		<td style='vertical-align:top;'><input type='radio' name='image_action' value='change' /></td>
	  		<td><?php echo _("Modifier l'image:"); ?><br /><input type='file' name='image_file'>
	  		</td>
	  	</tr>
	  	<tr>
	  		<td><input type='radio' name='image_action' value='delete' /></td>
	  		<td><?php echo _("Supprimer l'image"); ?></td>
	  	</tr>
	  </table>
	</td>
	<td>
		<table class='message' width='95%'>
	      <tr>
	        <td>
	          <font class='fieldTitle'><?php echo _("Type");?>:</font>
	        </td>
	        <td>
	          <select name='product_type'>
	          <option value='good' <?
	          if($action=="add") echo " CHECKED";
	          if($action=="edit" && $edit->product_type=="good") echo " CHECKED";
	          if(($action=="insert" || $action=="update") && $_POST["product_type"]=="good") echo " CHECKED";
	          ?>><?php echo _("Bien"); ?></option>
	          <option value='service' <?
	          if($action=="edit" && $edit->product_type=="service") echo " SELECTED";
	          if(($action=="insert" || $action=="update") && $_POST["product_type"]=="service") echo " SELECTED";
	          ?>><?php echo _("Service");?></option>
	          </select>
	        </td>
	      </tr>
	      <tr>
	        <td>
	          <font class='fieldTitle'><?php echo _("Réf."); ?>:</font>
	        </td>
	        <td>
	          <input type='text' name='ref_store' size='10' <?
	          if($action=="add") {
	          	$tmp1=request("SELECT reference_pattern FROM $TBL_catalogue_categories where id='$idCat'",$link);
	          	$tmp2=mysql_fetch_object($tmp1);
	          	echo " value='".$tmp2->reference_pattern."'";
	          }
	          if($action=="edit") echo " value=\"".$edit->ref_store."\"";
	          if($action=="insert" || $action=="update")  echo " value=\"".$_POST["ref_store"]."\"";
	          ?>>
	        </td>
	      </tr>
	      <tr>
	        <td>
	          <font class='fieldTitle'><?php echo _("Marque"); ?>:</font>
	        </td>
	        <td>
	          <select name='brand'>
	            <option value='0'>-- <?php echo _("Choisissez"); ?> --</option>
	            <?
	            $brands=request("SELECT id,name from $TBL_catalogue_brands order by name",$link);
	            while($brand=mysql_fetch_object($brands)){
	            	echo "<option value=\"".$brand->id."\"";
	            	if($action=="edit" && $edit->brand==$brand->id) echo " SELECTED";
	            	if(($action=="insert" || $action=="update") && $_POST["brand"]==$brand->id) echo " SELECTED";
	            	echo ">".$brand->name."</option>";
	            }
	            ?>
	          </select>
	        </td>
	      </tr>
	      <tr>
	        <td>
	          <font class='fieldTitle'><?php echo _("Réf. Manufacturier"); ?>:</font>
	        </td>
	        <td>
	          <input type='text' name='ref_manufacturer' size='10' <?
	          if($action=="edit") echo " value=\"".$edit->ref_manufacturer."\"";
	          if($action=="insert" || $action=="update")  echo " value=\"".$_POST["ref_manufacturer"]."\"";
	          ?>>
	        </td>
	      </tr>
	      <tr>
	        <td>
	          <font class='fieldTitle'><?php echo _("URL. Manufacturier"); ?>:</font>
	        </td>
	        <td>
	          <input type='text' name='url_manufacturer' size='25' <?
	          if($action=="edit") echo " value=\"".$edit->url_manufacturer."\"";
	          if($action=="insert" || $action=="update")  echo " value=\"".$_POST["url_manufacturer"]."\"";
	          ?>>
	        </td>
	      </tr>
	      <tr>
	        <td>
	          <font class='fieldTitle'><?php echo _("Code UPC"); ?>:</font>
	        </td>
	        <td>
	          <input type='text' name='upc_code' size='10' <?
	          if($action=="edit") echo " value=\"".$edit->upc_code."\"";
	          if($action=="insert" || $action=="update")  echo " value=\"".$_POST["upc_code"]."\"";
	          ?>>
	        </td>
	      </tr>
	      <tr>
	        <td>
	          <font class='fieldTitle'><?php echo _("Affichage"); ?>:</font>
	        </td>
	        <td>
	          <select name='active'>
	          <option value='Y' <?
	          if($action=="add") echo " CHECKED";
	          if($action=="edit" && $edit->active=="Y") echo " CHECKED";
	          if(($action=="insert" || $action=="update") && $_POST["active"]=="Y") echo " CHECKED";
	          ?>><?php echo _("Affiché"); ?></option>
	          <option value='N' <?
	          if($action=="edit" && $edit->active=="N") echo " SELECTED";
	          if(($action=="insert" || $action=="update") && $_POST["active"]=="N") echo " SELECTED";
	          ?>><?php echo _("Déactivé"); ?></option>
	          </select>
	        </td>
	      </tr>
	      <tr>
	        <td>
	          <font class='fieldTitle'><?php echo _("État du produit");?>:</font>
	        </td>
	        <td>
	        <?php
	        $requestStr = "
	        	SELECT `catalogue_state`.`id`, `catalogue_state`.`name`, `catalogue_product_state`.`id_product`
	        	FROM `catalogue_state`
	        	LEFT JOIN `catalogue_product_state` ON ( `catalogue_state`.`id` = `catalogue_product_state`.`id_state`
	        											  AND `catalogue_product_state`.`id_product` ='$idProduct' )
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
	        		if($action=="edit" && $productState->id_product!=null) echo " CHECKED";
	        		if(($action=="insert" || $action=="update") && isset($_POST["state_".$productState->id])) echo " CHECKED";
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
	</table>
<?php
TABS_closeTab();
/**
 * TAB2: E-COMMERCE
 */
TABS_addTab(2,_("E-Commerce"));
?>
<table style='width:100%'>
<tr>
<td style="vertical-align:top;width:50%;"><!--ecommerce: col1-->
	<table class='message' width='95%'>
	<tr>
		<td colspan='2'><font class='fieldTitle'><?php echo _("Liste de prix"); ?>:</font></td>
	</tr>
	<tr>
		<td>
		  <?php echo _("Prix public"); ?>:
		</td>
		<td>
		  <input type='text'size='10' name='public_price'<?
		  if($action=="edit") echo " value=\"".$edit->public_price."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["public_price"]."\"";
		    ?>> <?=$currency_symbol?>
		</td>
	</tr>
	<?
	if($action=="edit"){
		$price_groups=request("select $TBL_catalogue_price_groups.id,$TBL_catalogue_info_price_groups.name,$TBL_catalogue_product_prices.price from $TBL_catalogue_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_info_price_groups where $TBL_catalogue_product_prices.id_product='$idProduct' and $TBL_catalogue_info_price_groups.id_language='$used_language_id' and $TBL_catalogue_info_price_groups.id_price_group=$TBL_catalogue_price_groups.id and $TBL_catalogue_price_groups.id=$TBL_catalogue_product_prices.id_price_group",$link);
	}
	if($action=="add" || $action=="insert" || $action=="update"){
		$price_groups=request("select $TBL_catalogue_price_groups.id,$TBL_catalogue_info_price_groups.name from $TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups WHERE $TBL_catalogue_info_price_groups.id_language='$used_language_id' AND $TBL_catalogue_info_price_groups.id_price_group=$TBL_catalogue_price_groups.id",$link);
	}
	while($price_group=mysql_fetch_object($price_groups)){
		echo "<tr>";
		echo "<td>";
		echo $price_group->name.":";
		echo "</td>";
		echo "<td>";
		echo "<input name='price_".$price_group->id."' type='text' size='10'";
		if($action=="edit"){
			echo " value=\"".$price_group->price."\"";
		}
		if($action=="insert" || $action=="update"){
			echo " value=\"".$_POST["price_".$price_group->id]."\"";
		}
		echo ">&nbsp;$currency_symbol<br>";
	}
	?>
	</table><br />
	<table class='message' width='95%'>
	<tr>
		<td colspan='2'><font class='fieldTitle'><?php echo _("Spéciaux");?>: </font> <input type='checkbox' name='is_in_special'<?php
		if($action=="edit" && $edit->is_in_special=='Y') echo " CHECKED";
		if(($action=="insert" || $action=="update") && isset($_POST["is_in_special"]))  echo " CHECKED";
		  ?>> <i>(<?php echo _("Activé");?>)</i>
		</td>
	</tr>
	<tr>
		<td>
		  <input type='checkbox' name='public_special'<?
		  if($action=="edit" && $edit->public_special=='Y') echo " CHECKED";
		  if(($action=="insert" || $action=="update") && isset($_POST["public_special"]))  echo " CHECKED";
		  ?>><?php echo _("Prix public"); ?>:
		</td>
		<td>
		  <input type='text'size='10' name='special_price'<?
		  if($action=="edit") echo " value=\"".$edit->special_price."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["special_price"]."\"";
		    ?>> <?=$currency_symbol?>
		</td>
	</tr>
	<?
	if($action=="edit"){
		$price_groups=request("select $TBL_catalogue_price_groups.id,$TBL_catalogue_info_price_groups.name,$TBL_catalogue_product_prices.price,$TBL_catalogue_product_prices.is_in_special,$TBL_catalogue_product_prices.special_price from $TBL_catalogue_price_groups,$TBL_catalogue_product_prices,$TBL_catalogue_info_price_groups where $TBL_catalogue_product_prices.id_product='$idProduct' and $TBL_catalogue_info_price_groups.id_language='$used_language_id' and $TBL_catalogue_info_price_groups.id_price_group=$TBL_catalogue_price_groups.id and $TBL_catalogue_price_groups.id=$TBL_catalogue_product_prices.id_price_group",$link);
	}
	if($action=="add" || $action=="insert" || $action=="update"){
		$price_groups=request("select $TBL_catalogue_price_groups.id,$TBL_catalogue_info_price_groups.name from $TBL_catalogue_price_groups,$TBL_catalogue_info_price_groups WHERE $TBL_catalogue_info_price_groups.id_language='$used_language_id' AND $TBL_catalogue_info_price_groups.id_price_group=$TBL_catalogue_price_groups.id",$link);
	}
	while($price_group=mysql_fetch_object($price_groups)){
		echo "<tr>";
		echo "<td>";
		echo "<input type='checkbox' name='is_in_special_".$price_group->id."'";
		if($action=="edit" && $price_group->is_in_special=='Y') echo " CHECKED";
		if(($action=="insert" || $action=="update") && isset($_POST["is_in_special_".$price_group->id]))  echo " CHECKED";
		echo ">";
		echo $price_group->name.":";
		echo "</td>";
		echo "<td>";
		echo "<input name='special_price_".$price_group->id."' type='text' size='10'";
		if($action=="edit"){
			echo " value=\"".$price_group->special_price."\"";
		}
		if($action=="insert" || $action=="update"){
			echo " value=\"".$_POST["price_".$price_group->id]."\"";
		}
		echo ">&nbsp;$currency_symbol<br>";
	}
	?>
	</table><br />
	<?php
	/**
	 * QTY PRICES
	 */
	if(isset($CATALOG_enable_qty_prices) && $CATALOG_enable_qty_prices){
	?>
	<table class='message' width='95%'>
	<tr>
		<td colspan='2'><font class='fieldTitle'><?php echo _("Prix à la quantité"); ?>:</font></td>
	</tr>
	<?
	//Get the qty prices already set
	if($action=="edit"){
		$request = request("SELECT * FROM `$TBL_catalogue_product_qty_price` WHERE `id_product`='$idProduct' ORDER BY `qty`",$link);
		$qtyLevels = array();
		$counter=0;
		while($level = mysql_fetch_object($request)){
			$qtyLevels[$counter]=array();
			$qtyLevels[$counter]["qty"]=$level->qty;
			$qtyLevels[$counter]["price"]=$level->price;
			$counter++;
		}
	}
	for($i=0;$i<$CATALOG_qty_price_levels;$i++){
		echo "<tr>";
		echo "<td>";
		echo _("Qté").": ";
		echo "<input type='text' name='qtyprice_qty$i' style='width:40px;' ";
		if($action=="edit" && isset($qtyLevels[$i])) echo "value='".$qtyLevels[$i]["qty"]."' ";
		if(($action=="insert" || $action=="update") && isset($_POST["qtyprice_qty$i"])) echo  "value='".$_POST["qtyprice_qty$i"]."' ";
		echo " />";
		echo "</td>";
		echo "<td>";
		echo _("Prix").": ";
		echo "<input type='text' name='qtyprice_price$i' ";
		if($action=="edit" && isset($qtyLevels[$i])) echo "value='".$qtyLevels[$i]["price"]."' ";
		if(($action=="insert" || $action=="update") && isset($_POST["qtyprice_price$i"])) echo  "value='".$_POST["qtyprice_price$i"]."' ";
		echo " />";
		echo "&nbsp;$currency_symbol<br>";
	}
	?>
	</table><br />
	<?php
	} //if $CATALOG_enable_qty_prices
	?>
</td><!--ecommerce: col1-->
<td style="vertical-align:top;width:50%;"><!--ecommerce: col2-->
	<!-- TAXES -->
	<table class='message' width='95%'>
	<tr>
		<td colspan='2'><font class='fieldTitle'><?php echo _("Taxes spéciales"); ?>:</font></td>
	</tr>
	<tr>
		<td>
		  <?php echo _("Écotaxe"); ?>:
		</td>
		<td>
		  <input type='text'size='10' name='ecotaxe'<?
		  if($action=="edit") echo " value=\"".$edit->ecotaxe."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["ecotaxe"]."\"";
		    ?>> <?=$currency_symbol?>
		</td>
	</tr>
	</table><br />
	<!-- STOCK -->
	<table class='message' width='95%'>
	<tr>
		<td>
		  <font class='fieldTitle'><?php echo _("Stock"); ?>: </font>
		</td>
		<td>
		  <input type='text' size='10' name='stock'<?
		  if($action=="edit") echo " value=\"".$edit->stock."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["stock"]."\"";
		  ?>>
		</td>
	</tr>
	<tr>
		<td>
		  <font class='fieldTitle'><?php echo _("En commande"); ?>: </font>
		</td>
		<td>
		  <?php
		  if($action=="edit" || $action=="update") echo $edit->on_order_qty;
		  else echo "0.00";
		  ?>
		</td>
	</tr>
	<tr>
		<td>
		  <font class='fieldTitle'><?php echo _("Délai d'approvisionnement"); ?>: </font>
		</td>
		<td>
		  <select name='restocking_delay'>
		   <?
		    $delays=request("
		    	SELECT `$TBL_catalogue_restocking_delay`.`id`,`$TBL_catalogue_info_restocking_delay`.`name`
		    	FROM `$TBL_catalogue_restocking_delay`,`$TBL_catalogue_info_restocking_delay`
		    	WHERE `$TBL_catalogue_info_restocking_delay`.`id_language`='$used_language_id'
		    	AND `$TBL_catalogue_info_restocking_delay`.`id_delay`=`$TBL_catalogue_restocking_delay`.`id`
		    	ORDER BY `$TBL_catalogue_restocking_delay`.`delay_days`",$link);
		    while($delay=mysql_fetch_object($delays)){
		    	echo "<option value=\"".$delay->id."\"";
		    	if($action=="edit" && $edit->restocking_delay==$delay->id) echo " SELECTED";
		    	if(($action=="insert" || $action=="update") && $_POST["restocking_delay"]==$delay->id) echo " SELECTED";
		    	echo ">".$delay->name."</option>";
		    }
		    ?>
		  </select>
		</td>
	</tr>
	<tr>
		<td>
		  <font class='fieldTitle'><?php echo _("Alerter si le stock est inférieur à"); ?>: </font>
		</td>
		<td>
		  <input type='text' size='10' name='stock_alert'<?
		  if($action=="edit") echo " value=\"".$edit->stock_alert."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["stock_alert"]."\"";
		  ?>>
		</td>
	</tr>
	<tr>
	<td>
	  <font class='fieldTitle'><?php echo _("Dimensions"); ?>: </font>
	</td>
		<td>
		  <input type='text' size='5' name='dim_W'<?
		  if($action=="edit") echo " value=\"".$edit->dim_W."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["dim_W"]."\"";
		  ?>>x<input type='text' size='5' name='dim_H'<?
		  if($action=="edit") echo " value=\"".$edit->dim_H."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["dim_H"]."\"";
		  ?>>x<input type='text' size='5' name='dim_L'<?
		  if($action=="edit") echo " value=\"".$edit->dim_L."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["dim_L"]."\"";
		  ?>> <?php echo $measure_symbol; ?>
		</td>
	</tr>
	<tr>
		<td>
		  <font class='fieldTitle'><?php echo _("Poids"); ?>:</font>
		</td>
		<td>
		  <input type='text' size='5' name='weight'<?
		  if($action=="edit") echo " value=\"".$edit->weight."\"";
		  if($action=="insert" || $action=="update")  echo " value=\"".$_POST["weight"]."\"";
		  ?>> <?php echo $weight_symbol; ?>
		</td>
	</tr>
	</table><br />
	<!-- SUPPLIERS -->
	<table class='message' width='95%'>
		<tr>
        <td colspan="2">
          <font class='fieldTitle'><?php echo _("Coûts fournisseurs"); ?>: </font>
        </td>
        </tr>
	      <?php
	      $suppliers = new EcommerceSuppliersList();
	      ?>
	      <?php
	      for($i=1;$i<=4;$i++){
	      ?>
	      <tr>
	      	<td>
	      		<?php
	      		$fieldName = "id_supplier".$i;
	      		?>
	      		<select name='<?php echo $fieldName;?>'>
	      			<option value='0'>-- Non défini --</option>
	      			<?php
	      			foreach($suppliers as $supplier){
	      				$supplierId = $supplier->getId();
	      				$supplierName = $supplier->getName();
	      				echo "<option value='$supplierId'";
	      				if($action=="edit" && $edit->$fieldName==$supplierId) echo " selected='selected'";
	      				if(($action=="insert" || $action=="update") && $_POST[$fieldName]==$supplierId) echo " selected='selected'";
	      				echo ">$supplierName</option>";
	      			}
	      			?>
	      		</select>
	      	</td>
	      	<td>
	      		<?php
	      		echo "<b>"._("Réf.").":</b>";

	      		$fieldName = "ref_supplier".$i;
	      		echo "<input name='$fieldName' type='text' size='15'";
	      		if($action=="edit"){
	      			echo " value=\"".$edit->$fieldName."\"";
	      		}
	      		if($action=="insert" || $action=="update"){
	      			echo " value=\"".$_POST[$fieldName]."\"";
	      		}
	      		echo ">";
          		?>
	      	</td>
	      	<td>
	      		<?php
	      		echo "<b>"._("Prix").":</b>";

	      		$fieldName = "cost_supplier".$i;
	      		echo "<input name='$fieldName' type='text' size='10'";
	      		if($action=="edit"){
	      			echo " value=\"".$edit->$fieldName."\"";
	      		}
	      		if($action=="insert" || $action=="update"){
	      			echo " value=\"".$_POST[$fieldName]."\"";
	      		}
	      		echo ">".$currency_symbol;
          		?>
	      	</td>
	      </tr>
	      <?php
	      } //for($i=1;$i<=4;$i++)
	      ?>
      </table>
</td><!--ecommerce: col2-->
</tr>
</table>
<?php
TABS_closeTab();
/**
 * TAB3: PRODUCT OPTIONS
 */
TABS_addTab(3,_("Options"));
?>
	  <table class='message' width='95%'>
      <tr>
        <td><font class='fieldTitle'><?php echo _("Options du produit"); ?>:</font></td>
      </tr>
      <?
      //Product options
      $options=request("SELECT $TBL_catalogue_product_options.id, $TBL_catalogue_info_options.name,$TBL_catalogue_product_options.ordering FROM `$TBL_catalogue_product_options`,`$TBL_catalogue_info_options`,`$TBL_gen_languages` WHERE $TBL_catalogue_product_options.id_product='$idProduct' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_options.id_option=$TBL_catalogue_product_options.id AND $TBL_catalogue_info_options.id_language=$TBL_gen_languages.id order by $TBL_catalogue_product_options.ordering",$link);
      if(mysql_num_rows($options)>0){
      	//Get the maximum order
      	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_product_options` WHERE id_product='$idProduct' GROUP BY id_product",$link);
      	if(mysql_num_rows($tmp)) {
      		$maxOrder= mysql_fetch_object($tmp);
      		$maxOrderValue = $maxOrder->maximum;
      	} else $maxOrderValue=1;
      	while($option = mysql_fetch_object($options)){
      		echo "<tr>";
      		echo "<td>";
      		echo $option->name;
      		echo "</td>";
      		echo "<td align='right'>";
      		if($option->ordering>1){
      			echo "<a href='./mod_product.php?idProduct=$idProduct&idOption=".$option->id."&action=moveOptionUp'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
      		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
      		if($option->ordering<$maxOrderValue){
      			echo "<a href='./mod_product.php?idProduct=$idProduct&idOption=".$option->id."&action=moveOptionDown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
      		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
      		echo "<a href='./mod_option.php?action=edit&idProduct=$idProduct&idOption=".$option->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a>";
      		echo "&nbsp;<a href='./del_option.php?idOption=".$option->id."&idProduct=$idProduct'><img src='../images/del.gif' border='0' alt=\""._("Supprimer")."\"></a>";
      		echo "</td>";
      		echo "</tr>";
      	}
      } else {
      	echo "<tr><td colspan='2' align='center'><i>"._("Aucune option pour ce produit")."</i></td></tr>";
      }
      ?>
        <?
        if($action!="add" && $action!="insert"){
       ?>
        <tr>
          <td NOWRAP align='right' colspan='2'><A href='./mod_option.php?action=add&idProduct=<?=$idProduct?>'><?php echo _("Ajouter"); ?></A></td>
        </tr>
        <?
        } //IF
        ?>
      </table>
<?php
TABS_closeTab();
/**
 * TAB4: ATTACHMENTS
 */
TABS_addTab(4,_("Fichiers attachés"));
?>
	  <table class='message' width='95%'>
      <tr>
        <td colspan='2'><font class='fieldTitle'><?php echo _("Fichiers attaché"); ?>:</font></td>
      </tr>
      <?
      //product attachments
      $attachments=request("SELECT $TBL_catalogue_attachments.id id, $TBL_catalogue_attachments.title attachment,$TBL_gen_languages.name language,$TBL_catalogue_attachments.ordering FROM `$TBL_catalogue_attachments`,`$TBL_gen_languages` WHERE $TBL_catalogue_attachments.id_product='$idProduct' AND $TBL_catalogue_attachments.id_language=$TBL_gen_languages.id order by $TBL_catalogue_attachments.ordering",$link);
      if(mysql_num_rows($attachments)>0){
      	//Get the maximum order
      	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_attachments` WHERE id_product='$idProduct' GROUP BY id_product",$link);
      	if(mysql_num_rows($tmp)) {
      		$maxOrder= mysql_fetch_object($tmp);
      		$maxOrderValue = $maxOrder->maximum;
      	} else $maxOrderValue=1;
      	while($attachment = mysql_fetch_object($attachments)){
      		echo "<tr>";
      		echo "<td>";
      		echo $attachment->attachment."(".$attachment->language.")";
      		echo "</td>";
      		echo "<td align='right'>";
      		if($attachment->ordering>1){
      			echo "<a href='./mod_product.php?idProduct=$idProduct&idAttachment=".$attachment->id."&action=moveAttachmentUp'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
      		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
      		if($attachment->ordering<$maxOrderValue){
      			echo "<a href='./mod_product.php?idProduct=$idProduct&idAttachment=".$attachment->id."&action=moveAttachmentDown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
      		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
      		echo "<a href='./mod_attachment.php?action=edit&idProduct=$idProduct&idAttachment=".$attachment->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a>";
      		echo "&nbsp;<a href='./del_attachment.php?idAttachment=".$attachment->id."&idProduct=$idProduct'><img src='../images/del.gif' border='0' alt=\""._("Supprimer")."\"></a>";
      		echo "</td>";
      		echo "</tr>";
      	}
      } else {
      	echo "<tr><td colspan='2' align='center'><i>"._("Aucun fichier attaché à ce produit")."</i></td></tr>";
      }
      ?>
      <?
      if($action!="add" && $action!="insert"){
     ?>
      <tr>
        <td NOWRAP align='right' colspan='2'><A href='./mod_attachment.php?action=add&idProduct=<?=$idProduct?>'><?php echo _("Ajouter"); ?></A></td>
      </tr>
      <?
      } //IF
      ?>
      </table>
<?php
TABS_closeTab();
/**
 * TAB5: LINKS
 */
TABS_addTab(5,_("Liens"));
//get the link categories
if($action=="edit" || $action=="update"){
	$linkCategories = new LinkCategoriesList($used_language_id);
?>
	<?php
	$JS_links = "links_table=Array();\n";
	foreach ($linkCategories as $linkCategory){
		$JS_links.= "links_table[".$linkCategory->getId()."]=Array();\n";
		$links = new LinkList(1,$idProduct,$linkCategory->getId());
	?>
		<!--<table id='links_<?php echo $linkCategory->getId()?>' class='message' width='95%'>
		<th>
			<td colspan="2"><font class='fieldTitle'><?php echo $linkCategory->getName(); ?>:</font></td>
		</tr>-->
		<a href='javascript:void(0);' onclick='showHideLinks(<?php echo $linkCategory->getId()?>)'><img src='../images/show.jpg' /> <b><?php echo $linkCategory->getName(); ?> - <span id='links_nb_<?php echo $linkCategory->getId()?>'></span> <?php echo _("Lien(s)")?></b></a><br />
		<div id='links_<?php echo $linkCategory->getId()?>' style="display:none;">
		<?php

		$JS_links_counter = 0;
		try {
			$links->setIteratorCategory($linkCategory->getId());
			if($links->categoryHasLinks($linkCategory->getId()))
			foreach ($links as $itemLink){
				$JS_links.="links_table[".$linkCategory->getId()."][$JS_links_counter]=Array(".$itemLink->getId().",\"".$itemLink->getToInfos()."\")\n";
				$JS_links_counter++;
			}
		} catch (Exception $e){
			$ANIX_messages->addError($e->getMessage()." ".$e->getFile()." ".$e->getLine());
		}
		$JS_links.="updateLinks(".$linkCategory->getId().")\n";
		?>
		<br />
		</div>
	<?php
	} //for each link categories
	?><br />
	<script type="text/javascript">
	<?php echo $JS_links; ?>
	</script>
	<?php echo _("Ajouter un lien de type").":"; ?> <select id='links_add_category' name='links_add_category'><?php
	echo "<option value='0'> --- "._("CHOISISSEZ")." --- </option>";
	foreach($linkCategories as $linkCategory){
		echo "<option value='".$linkCategory->getId()."'>".$linkCategory->getName()."</option>";
	}
	?></select> <input type='button' value="OK" onclick="javascript:JS_links_add_link(1,<?php echo $idProduct; ?>)" />
<?php
}
TABS_closeTab();
/**
 * TAB5: LINKS
 */
TABS_addTab(6,_("Avis clients"));
//get the link categories
if($action=="edit" || $action=="update"){
	$reviews = new CatalogueReviewsList($idProduct);
	if($tmp=$reviews->getNbUnmoderatedReviews()) $ANIX_messages->addMessage(_("Des avis doivent être approuvés pour ce produit").": ".$tmp." "._("avis non approuvé(s)."));
?>
	<?php
	$JS_reviews = "reviews_table=Array();\n";
	$JS_reviews_counter = 0;
	foreach($reviews as $review){
		$JS_reviews.="reviews_table[$JS_reviews_counter]=Array(".$review->getId().",\""._("Par:")." ".$review->getCustomerName()."\",\""._("Le:")." ".$review->getReviewDate()."\",".($review->isModerated()?"true":"false").",\"".$review->getReview()."\")\n";
		$JS_reviews_counter++;
	}
	$JS_reviews.="updateReviews()\n";
	?>
	<div id='reviews_list'>
	</div>
	<script type="text/javascript">
	<?php echo $JS_reviews; ?>
	</script>
<?php
}
TABS_closeTab();
TABS_closeTabManager();
if($action=="add" || $action=="insert"){
	TABS_disableTab(3);
	TABS_disableTab(4);
	TABS_disableTab(5);
}
?>