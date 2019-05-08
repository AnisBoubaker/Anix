<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
	<table width='100%'>
 	<tr valign='top'>
 	<td>
 		<?
	    if($action=="edit" || $action=="update"){
	    	echo "<center><a href='../".$CATALOG_folder_images.$edit->image_file_large."' target='_blank'><img class='item_image' src='../".$CATALOG_folder_images.$edit->image_file_small."' alt=\"Agrandir l'image\"></a></center>";
	    }
	    if($action=="add" || $action=="insert"){
	    	echo "<center><a href='../".$CATALOG_folder_images."imgcat_large_no_image.jpg' target='_blank'><img class='item_image' src='../".$CATALOG_folder_images."imgcat_small_no_image.jpg' alt=\""._("Agrandir l'image")."\"></a></center>";
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
		<input type='checkbox' name='contain_products'<?
		if($action=="add") echo " CHECKED";
		if($action=="edit" && $edit->contain_products=='Y') echo " CHECKED";
		if(($action=="insert" || $action=="update") && isset($_POST["contain_products"])) echo " CHECKED";
	          ?>><?php echo _("Cette catégorie peut contenir des produits"); ?>
	    <br><br>
	    <input type='checkbox' name='hide_products'<?
		if($action=="edit" && $edit->hide_products=='Y') echo " checked='checked'";
		if(($action=="insert" || $action=="update") && isset($_POST["contain_products"])) echo " checked='checked'";
	          ?>><?php echo _("Cacher les produits de cette catégorie des catégories supérieures"); ?>
	    <br><br>
	    <b><?php echo _("Modèle de référence de produits"); ?>:</b><br>
	    <?
	    echo "<input type='text' name='reference_pattern'";
	    if($action=="add"){
	    	if($idCat==0) echo " value='$CATALOG_default_products_ref'";
	    	else{
	    		$tmp1=request("SELECT reference_pattern FROM $TBL_catalogue_categories where id='$idCat'",$link);
	    		$tmp2=mysql_fetch_object($tmp1);
	    		echo " value='".$tmp2->reference_pattern."'";
	    	}
	    }
	    if($action=="edit") echo " value='".$edit->reference_pattern."'";
	    if($action=="insert" || $action=="update") echo " value='".$_POST["reference_pattern"]."'";
	    echo ">";
	    ?><br />
	    <b><?php echo _("Dimensions des images de produits: (largeur x hauteur)"); ?></b><br />
		Icone:
			<input type='text' style='width:30px' name='productimg_icon_width' value='<?php if($action=="edit") echo $edit->productimg_icon_width; if($action=="insert" || $action=="update") echo $_POST["productimg_icon_width"];if($action=="add") echo $CATALOG_image_prd_icon_max_width;?>' />
			x
			<input type='text' style='width:30px' name='productimg_icon_height' value='<?php if($action=="edit") echo $edit->productimg_icon_height; if($action=="insert" || $action=="update") echo $_POST["productimg_icon_height"];if($action=="add") echo $CATALOG_image_prd_icon_max_height;?>' /> pixels
			<br />
		Petite:
			<input type='text' style='width:30px' name='productimg_small_width' value='<?php if($action=="edit") echo $edit->productimg_small_width; if($action=="insert" || $action=="update") echo $_POST["productimg_small_width"];if($action=="add") echo $CATALOG_image_prd_small_max_width;?>' />
			x
			<input type='text' style='width:30px' name='productimg_small_height' value='<?php if($action=="edit") echo $edit->productimg_small_height; if($action=="insert" || $action=="update") echo $_POST["productimg_small_height"];if($action=="add") echo $CATALOG_image_prd_small_max_height;?>' /> pixels
			<br />
		Grande:
			<input type='text' style='width:30px' name='productimg_large_width' value='<?php if($action=="edit") echo $edit->productimg_large_width; if($action=="insert" || $action=="update") echo $_POST["productimg_large_width"];if($action=="add") echo $CATALOG_image_prd_large_max_width;?>' />
			x
			<input type='text' style='width:30px' name='productimg_large_height' value='<?php if($action=="edit") echo $edit->productimg_large_height; if($action=="insert" || $action=="update") echo $_POST["productimg_large_height"];if($action=="add") echo $CATALOG_image_prd_large_max_height;?>' /> pixels<br />
		<b><?php echo _("Menu correspondant");?>:</b>:<select name='id_menu'>
			<option value='0'>-- <?php echo _("Page non liée à un menu"); ?>--</option>
		  	<?php
		  	if($action=="edit" || $action=="update"){
		  		$tmp = $languages=request("SELECT `id_menu` FROM `$TBL_catalogue_categories` WHERE `id` = '$idCat'", $link);
		  		$edit_menu = mysql_fetch_object($tmp);
		  	}
		  	$tmp = getMenusList(0,$link,$_SESSION["used_languageid2"]);
		  	foreach($tmp as $idmenu => $menus){
		  		echo "<option value='".$menus["id"]."'";
		  		if($action=="edit" && $menus["id"]==$edit_menu->id_menu) echo " selected='selected'";
		  		if(($action=="insert" || $action=="update") && $menus["id"]==$_POST["id_menu"]) echo " selected='selected'";
		  		echo ">";
		  		echo $menus["title"];
		  		echo "</option>";
		  	}
		  	?>
		</select>
	</td>
	</tr>
	</table>
<?php
TABS_closeTab();
/**
 * TAB2: ADDITIONAL SECTIONS
 */
TABS_addTab(2,_("Sections additionnelles"));
//Category extrasections
if($action=="edit" || $action=="update"){
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td>";
	echo "<font class='fieldTitle'>"._("Sections additionnelles").":</fONT><br>";
	$extrasections=request("SELECT $TBL_catalogue_extracategorysection.id,$TBL_catalogue_extracategorysection.deletable,$TBL_catalogue_info_extracategorysection.name,$TBL_catalogue_extracategorysection.ordering from $TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection,$TBL_gen_languages where $TBL_catalogue_extracategorysection.id_cat=$idCat and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_extracategorysection.id=$TBL_catalogue_info_extracategorysection.id_extrasection and $TBL_catalogue_info_extracategorysection.id_language=$TBL_gen_languages.id order by $TBL_catalogue_extracategorysection.ordering",$link);
	if(mysql_num_rows($extrasections)){
		//Get the maximum order
		$tmp = request("SELECT MAX(ordering) as maximum from $TBL_catalogue_extracategorysection WHERE id_cat='$idCat' GROUP BY id_cat",$link);
		if(mysql_num_rows($tmp)) {
			$maxOrder= mysql_fetch_object($tmp);
			$maxOrderValue = $maxOrder->maximum;
		} else $maxOrderValue=1;
		echo "<table width='100%'>";
		while($extrasection=mysql_fetch_object($extrasections)){
			echo "<tr>";
			echo "<td>".$extrasection->name."</td>";
			echo "<td align='right'>";
			if($extrasection->ordering>1){
				echo "<a href='./mod_category.php?idCat=$idCat&idExtraSection=".$extrasection->id."&action=moveESup'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
			} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
			if($extrasection->ordering<$maxOrderValue){
				echo "<a href='./mod_category.php?idCat=$idCat&idExtraSection=".$extrasection->id."&action=moveESdown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
			} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
			echo "<a href='./mod_extrasection.php?idCat=$idCat&idExtraSection=".$extrasection->id."&action=edit'><img src='../images/edit.gif' border='0' alt=\""._("Modifier la section")."\"></a>&nbsp;";
			if($extrasection->deletable=="Y"){
				echo "<a href='./del_extrasection.php?idCat=$idCat&idExtraSection=".$extrasection->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer la section")."\"></a>";
			} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	} else {
		echo "<CENTER><i>"._("Aucune section additionnelle dans cette catégorie")."</i></CENTER>";
	}
	echo "</td>";
	echo "</tr>";
	echo "<tr><td align='right'><A href='./mod_extrasection.php?action=add&idCat=$idCat'>"._("Ajouter")."</A></td></tr>";
	echo "</table>";
}
TABS_closeTab();
/**
 * TAB3: EXTRA FIELDS
 */
TABS_addTab(3,_("Champs additionnels"));
//Product's extrafields
if($action=="edit" || $action=="update"){
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td>";
	echo "<font class='fieldTitle'>"._("Champs additionnels de produits").":</fONT><br>";
	$extrafields=request("SELECT $TBL_catalogue_extrafields.id, $TBL_catalogue_extrafields.deletable, $TBL_catalogue_extrafields.id_cat, $TBL_catalogue_extrafields.params,$TBL_catalogue_extrafields.datatype,$TBL_catalogue_info_extrafields.name,$TBL_catalogue_extrafields.ordering from $TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields,$TBL_gen_languages where $TBL_catalogue_extrafields.id_cat='$idCat' and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_extrafields.id=$TBL_catalogue_info_extrafields.id_extrafield and $TBL_catalogue_info_extrafields.id_language=$TBL_gen_languages.id order by $TBL_catalogue_extrafields.ordering",$link);
	//echo "SELECT $TBL_catalogue_extrafields.id, $TBL_catalogue_extrafields.id_cat, $TBL_catalogue_extrafields.params,$TBL_catalogue_extrafields.datatype,$TBL_catalogue_info_extrafields.name,$TBL_catalogue_extrafields.ordering from $TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields,$TBL_gen_languages where $TBL_catalogue_extrafields.id_cat='$idCat' and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_extrafields.id=$TBL_catalogue_info_extrafields.id_extrafield and $TBL_catalogue_info_extrafields.id_language=$TBL_gen_languages.id order by $TBL_catalogue_extrafields.ordering";
	if(mysql_num_rows($extrafields)){
		//Get the maximum order
		$tmp = request("SELECT MAX(ordering) as maximum from $TBL_catalogue_extrafields WHERE id_cat='$idCat' GROUP BY id_cat",$link);
		if(mysql_num_rows($tmp)) {
			$maxOrder= mysql_fetch_object($tmp);
			$maxOrderValue = $maxOrder->maximum;
		} else $maxOrderValue=1;
		echo "<table width='100%'>";
		while($extrafield=mysql_fetch_object($extrafields)){
			echo "<tr>";
			echo "<td><i>[".$extrafield->datatype."]</i> ".$extrafield->name."</td>";
			echo "<td align='right'>";
			if($extrafield->ordering>1){
				echo "<a href='./mod_category.php?idCat=$idCat&idExtrafield=".$extrafield->id."&action=moveEFup'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
			} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
			if($extrafield->ordering<$maxOrderValue){
				echo "<a href='./mod_category.php?idCat=$idCat&idExtrafield=".$extrafield->id."&action=moveEFdown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
			} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
			echo "<a href='./mod_extrafield.php?idCat=$idCat&idExtraField=".$extrafield->id."&action=edit'><img src='../images/edit.gif' border='0' alt=\""._("Modifier le champs")."\"></a>&nbsp;";
			if($extrafield->deletable=="Y"){
				echo "<a href='./del_extrafield.php?idCat=$idCat&idExtraField=".$extrafield->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer le champs")."\"></a>";
			} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
			echo "</td>";
			echo "</tr>";
		}
		echo "</table>";
	} else {
		echo "<CENTER><i>"._("Aucun champs additionnel dans cette catégorie")."</i></CENTER>";
	}
	echo "</td>";
	echo "</tr>";
	echo "<tr><td align='right'><A href='./mod_extrafield.php?action=add&idCat=$idCat'>"._("Ajouter")."</A></td></tr>";
	echo "</table>";
}
TABS_closeTab();
/**
 * TAB4: ADDITIONAL SECTIONS
 */
TABS_addTab(4,_("Fichiers attachés"));
?>
  <table class='message' width='100%'>
  <tr>
    <td colspan='2'><font class='fieldTitle'><?php echo _("Fichiers attachés");?>:</font></td>
  </tr>
  <?
  //category attachments
  $attachments=request("SELECT $TBL_catalogue_attachments.id id, $TBL_catalogue_attachments.title attachment,$TBL_gen_languages.name language,$TBL_catalogue_attachments.ordering FROM `$TBL_catalogue_attachments`,`$TBL_gen_languages` WHERE $TBL_catalogue_attachments.id_category='$idCat' AND $TBL_catalogue_attachments.id_language=$TBL_gen_languages.id order by $TBL_catalogue_attachments.ordering",$link);
  if(mysql_num_rows($attachments)>0){
  	//Get the maximum order
  	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_attachments` WHERE id_category='$idCat' GROUP BY id_category",$link);
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
  			echo "<a href='./mod_category.php?idCat=$idCat&idAttachment=".$attachment->id."&action=moveAttachmentUp'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
  		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
  		if($attachment->ordering<$maxOrderValue){
  			echo "<a href='./mod_category.php?idCat=$idCat&idAttachment=".$attachment->id."&action=moveAttachmentDown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
  		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
  		echo "<a href='./mod_attachment.php?action=edit&idCategory=$idCat&idAttachment=".$attachment->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a>";
  		echo "&nbsp;<a href='./del_attachment.php?idAttachment=".$attachment->id."&idCategory=$idCat'><img src='../images/del.gif' border='0' alt=\""._("Supprimer")."\"></a>";
  		echo "</td>";
  		echo "</tr>";
  	}
  } else {
  	echo "<tr><td colspan='2' align='center'><i>"._("Aucun fichier attaché à cette catégorie")."</i></td></tr>";
  }
  ?>
  <?
  if($action!="add" && $action!="insert"){
 ?>
  <tr>
    <td NOWRAP align='right' colspan='2'><A href='./mod_attachment.php?action=add&idCategory=<?=$idCat?>'><?php echo _("Ajouter"); ?></A></td>
  </tr>
  <?
  } //IF
  ?>
  </table>
<?php
TABS_closeTab();
/**
 * TAB5: Links
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
		$links = new LinkList(2,$idCat,$linkCategory->getId());
	?>
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
			} else {

			}
		} catch (Exception $e){
			$ANIX_messages->addError($e->getMessage());
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
	?></select> <input type='button' value="OK" onclick="javascript:JS_links_add_link(2,<?php echo $idCat; ?>)" />
<?php
}
TABS_closeTab();
/**
 * TAB5: Links
 */
TABS_addTab(6,_("Partenariats"));
?>
	<font class='fieldTitle'><?php echo _("Comparateurs de prix");?>:</font><br />
	<table>
	<?php
	$requestString = "SELECT `id`,`name`,`id_partner_category`
					  FROM `$TBL_catalogue_partner`
					  LEFT JOIN `$TBL_catalogue_anix_partner` ON (`id_partner`=`id` AND `id_catalogue_category`='$idCat')
					  ORDER BY `name`";
	$requestPartner = request($requestString,$link);
	while($partner = mysql_fetch_object($requestPartner)){
		echo "<tr>";
		echo "<td>".$partner->name.": </td>";
		echo "<td>";
		$requestString="SELECT `id`,`name` FROM `$TBL_catalogue_partner_category` WHERE `id_partner`='$partner->id'";
		$partnerCatRequest = request ($requestString,$link);
		echo "<select name='partner_".$partner->id."'>";
		echo "<option value='0'> -- "._("CHOISISSEZ")." -- </option>";
		while($partnerCat = mysql_fetch_object($partnerCatRequest)){
			echo "<option value='".$partnerCat->id."'";
			if($partner->id_partner_category==$partnerCat->id) echo " selected='selected'";
			echo ">".$partnerCat->name."</option>";
		}
		echo "</select>";
		echo "</td>";
		echo "</tr>";
	}
	?>
	</table>
<?php
TABS_closeTab();
TABS_closeTabManager();
if($action=="add" || $action=="insert"){
	TABS_disableTab(2);
	TABS_disableTab(3);
	TABS_disableTab(4);
	TABS_disableTab(5);
}
/**
 * END OF TABS
 */
//TABS_disableTab(5);
//TABS_enableTab(3);
?>