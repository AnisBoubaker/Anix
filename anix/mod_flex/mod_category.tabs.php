<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
	<input type='checkbox' name='contain_items'<?
	if($action=="add") echo " CHECKED";
	if($action=="edit" && $edit->contain_items=='Y') echo " CHECKED";
	if(($action=="insert" || $action=="update") && isset($_POST["contain_items"])) echo " CHECKED";
          ?>><?php echo _("Cette catégorie peut contenir des éléments"); ?>
    <br><br>
	<b><?php echo _("Menu correspondant");?>:</b>:<select name='id_menu'>
		<option value='0'>-- <?php echo _("Page non liée à un menu"); ?>--</option>
	  	<?php
	  	if($action=="edit" || $action=="update"){
	  		$tmp = $languages=request("SELECT `id_menu` FROM `$TBL_lists_categories` WHERE `id` = '$idCat'", $link);
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
	$extrasections=request("SELECT $TBL_lists_extracategorysection.id,$TBL_lists_extracategorysection.deletable,$TBL_lists_info_extracategorysection.name,$TBL_lists_extracategorysection.ordering from $TBL_lists_extracategorysection,$TBL_lists_info_extracategorysection,$TBL_gen_languages where $TBL_lists_extracategorysection.id_cat=$idCat and $TBL_gen_languages.id='$used_language_id' and $TBL_lists_extracategorysection.id=$TBL_lists_info_extracategorysection.id_extrasection and $TBL_lists_info_extracategorysection.id_language=$TBL_gen_languages.id order by $TBL_lists_extracategorysection.ordering",$link);
	if(mysql_num_rows($extrasections)){
		//Get the maximum order
		$tmp = request("SELECT MAX(ordering) as maximum from $TBL_lists_extracategorysection WHERE id_cat='$idCat' GROUP BY id_cat",$link);
		if(mysql_num_rows($tmp)) {
			$maxOrder= mysql_fetch_object($tmp);
			$maxOrderValue = $maxOrder->maximum;
		} else $maxOrderValue=1;
		echo "<table width='100%'>";
		while($extrasection=mysql_fetch_object($extrasections)){
			echo "<tr>";
			echo "<td>".$extrasection->name." (ID:".$extrasection->id.")</td>";
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
			}else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
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
//Item's extrafields
if($action=="edit" || $action=="update"){
	echo "<table class='message' width='100%'>";
	echo "<tr>";
	echo "<td>";
	echo "<font class='fieldTitle'>"._("Champs additionnels d'éléments").":</fONT><br>";
	$extrafields=request("SELECT $TBL_lists_extrafields.id, $TBL_lists_extrafields.id_cat, $TBL_lists_extrafields.params,$TBL_lists_extrafields.datatype,$TBL_lists_extrafields.deletable,$TBL_lists_info_extrafields.name,$TBL_lists_extrafields.ordering from $TBL_lists_extrafields,$TBL_lists_info_extrafields,$TBL_gen_languages where $TBL_lists_extrafields.id_cat='$idCat' and $TBL_gen_languages.id='$used_language_id' and $TBL_lists_extrafields.id=$TBL_lists_info_extrafields.id_extrafield and $TBL_lists_info_extrafields.id_language=$TBL_gen_languages.id order by $TBL_lists_extrafields.ordering",$link);
	//echo "SELECT $TBL_lists_extrafields.id, $TBL_lists_extrafields.id_cat, $TBL_lists_extrafields.params,$TBL_lists_extrafields.datatype,$TBL_lists_info_extrafields.name,$TBL_lists_extrafields.ordering from $TBL_lists_extrafields,$TBL_lists_info_extrafields,$TBL_gen_languages where $TBL_lists_extrafields.id_cat='$idCat' and $TBL_gen_languages.id='$used_language_id' and $TBL_lists_extrafields.id=$TBL_lists_info_extrafields.id_extrafield and $TBL_lists_info_extrafields.id_language=$TBL_gen_languages.id order by $TBL_lists_extrafields.ordering";
	if(mysql_num_rows($extrafields)){
		//Get the maximum order
		$tmp = request("SELECT MAX(ordering) as maximum from $TBL_lists_extrafields WHERE id_cat='$idCat' GROUP BY id_cat",$link);
		if(mysql_num_rows($tmp)) {
			$maxOrder= mysql_fetch_object($tmp);
			$maxOrderValue = $maxOrder->maximum;
		} else $maxOrderValue=1;
		echo "<table width='100%'>";
		while($extrafield=mysql_fetch_object($extrafields)){
			echo "<tr>";
			echo "<td><i>[".$extrafield->datatype."]</i> ".$extrafield->name." (ID:".$extrafield->id.")"."</td>";
			echo "<td align='right'>";
			if($extrafield->ordering>1){
				echo "<a href='./mod_category.php?idCat=$idCat&idExtrafield=".$extrafield->id."&action=moveEFup'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
			} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
			if($extrafield->ordering<$maxOrderValue){
				echo "<a href='./mod_category.php?idCat=$idCat&idExtrafield=".$extrafield->id."&action=moveEFdown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
			} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
			echo "<a href='./mod_extrafield.php?idCat=$idCat&idExtraField=".$extrafield->id."&action=edit'><img src='../images/edit.gif' border='0' alt=\""._("Modifier le champs")."\"></a>&nbsp;";
			if($extrafield->deletable=="Y") echo "<a href='./del_extrafield.php?idCat=$idCat&idExtraField=".$extrafield->id."'><img src='../images/del.gif' border='0' alt=\""._("Supprimer le champs")."\"></a>";
			else echo "<img src='../images/spacer.gif' style='width:18px;heigth20px;'";
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
  $attachments=request("SELECT $TBL_lists_attachments.id id, $TBL_lists_attachments.title attachment,$TBL_gen_languages.name language,$TBL_lists_attachments.ordering FROM `$TBL_lists_attachments`,`$TBL_gen_languages` WHERE $TBL_lists_attachments.id_category='$idCat' AND $TBL_lists_attachments.id_language=$TBL_gen_languages.id order by $TBL_lists_attachments.ordering",$link);
  if(mysql_num_rows($attachments)>0){
  	//Get the maximum order
  	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_lists_attachments` WHERE id_category='$idCat' GROUP BY id_category",$link);
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
 * TAB5: Config
 */
TABS_addTab(5,_("Configuration"));
?>
<b><?php echo _("Dimensions des images des éléments: (largeur x hauteur)"); ?>:</b><br />
	Icone:
		<input type='text' style='width:30px' name='itemimg_icon_width' value='<?php if($action=="edit") echo $edit->itemimg_icon_width; if($action=="insert" || $action=="update") echo $_POST["itemimg_icon_width"];if($action=="add") echo $CATALOG_image_prd_icon_max_width;?>' />
		x
		<input type='text' style='width:30px' name='itemimg_icon_height' value='<?php if($action=="edit") echo $edit->itemimg_icon_height; if($action=="insert" || $action=="update") echo $_POST["itemimg_icon_height"];if($action=="add") echo $CATALOG_image_prd_icon_max_height;?>' /> pixels
		<br />
	Petite:
		<input type='text' style='width:30px' name='itemimg_small_width' value='<?php if($action=="edit") echo $edit->itemimg_small_width; if($action=="insert" || $action=="update") echo $_POST["itemimg_small_width"];if($action=="add") echo $CATALOG_image_prd_small_max_width;?>' />
		x
		<input type='text' style='width:30px' name='itemimg_small_height' value='<?php if($action=="edit") echo $edit->itemimg_small_height; if($action=="insert" || $action=="update") echo $_POST["itemimg_small_height"];if($action=="add") echo $CATALOG_image_prd_small_max_height;?>' /> pixels
		<br />
	Grande:
		<input type='text' style='width:30px' name='itemimg_large_width' value='<?php if($action=="edit") echo $edit->itemimg_large_width; if($action=="insert" || $action=="update") echo $_POST["itemimg_large_width"];if($action=="add") echo $CATALOG_image_prd_large_max_width;?>' />
		x
		<input type='text' style='width:30px' name='itemimg_large_height' value='<?php if($action=="edit") echo $edit->itemimg_large_height; if($action=="insert" || $action=="update") echo $_POST["itemimg_large_height"];if($action=="add") echo $CATALOG_image_prd_large_max_height;?>' /> pixels<br /><br />

<b><?php echo _("Classement des éléments de la catégorie"); ?>:</b> <select id='items_ordering' name='items_ordering'><?php
echo "<option value='manual'";
	if(($action=="add") && $parent->items_ordering=="manual") echo " selected='selected'";
	if($action=="edit" && $edit->items_ordering=="manual") echo " selected='selected'";
	if(($action=="insert" || $action=="update") && $_POST["items_ordering"]=="manual") echo " selected='selected'";
	echo ">";
	echo _("Classement manuel");
echo "</option>";
echo "<option value='alpha'";
	if(($action=="add") && $parent->items_ordering=="alpha") echo " selected='selected'";
	if($action=="edit" && $edit->items_ordering=="alpha") echo " selected='selected'";
	if(($action=="insert" || $action=="update") && $_POST["items_ordering"]=="alpha") echo " selected='selected'";
	echo ">";
	echo _("Classement alphabétique");
echo "</option>";
?>
</select><br />

<b><?php echo _("Classement des sous-catégories"); ?>:</b> <select id='subcats_ordering' name='subcats_ordering'><?php
echo "<option value='manual'";
	if(($action=="add") && $parent->subcats_ordering=="manual") echo " selected='selected'";
	if($action=="edit" && $edit->subcats_ordering=="manual") echo " selected='selected'";
	if(($action=="insert" || $action=="update") && $_POST["subcats_ordering"]=="manual") echo " selected='selected'";
	echo ">";
	echo _("Classement manuel");
echo "</option>";
echo "<option value='alpha'";
	if(($action=="add") && $parent->subcats_ordering=="alpha") echo " selected='selected'";
	if($action=="edit" && $edit->subcats_ordering=="alpha") echo " selected='selected'";
	if(($action=="insert" || $action=="update") && $_POST["subcats_ordering"]=="alpha") echo " selected='selected'";
	echo ">";
	echo _("Classement alphabétique");
echo "</option>";
?>
</select><br />
<?
TABS_closeTabManager();
if($action=="add" || $action=="insert"){
	TABS_disableTab(2);
	TABS_disableTab(3);
	TABS_disableTab(4);
}
/**
 * END OF TABS
 */
//TABS_disableTab(5);
//TABS_enableTab(3);
?>