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
          ?>><?php echo _("Cette catégorie peut contenir des nouvelles"); ?>
	    <br><br>
		<b><?php echo _("Dimensions des images des éléments: (largeur x hauteur)"); ?></b><br />
		Icone:
			<input type='text' style='width:30px' name='newsimg_icon_width' value='<?php if($action=="edit") echo $edit->newsimg_icon_width; if($action=="insert" || $action=="update") echo $_POST["newsimg_icon_width"];if($action=="add") echo $NEWS_image_news_icon_max_width;?>' />
			x
			<input type='text' style='width:30px' name='newsimg_icon_height' value='<?php if($action=="edit") echo $edit->newsimg_icon_height; if($action=="insert" || $action=="update") echo $_POST["newsimg_icon_height"];if($action=="add") echo $NEWS_image_news_icon_max_height;?>' /> pixels
			<br />
		Petite:
			<input type='text' style='width:30px' name='newsimg_small_width' value='<?php if($action=="edit") echo $edit->newsimg_small_width; if($action=="insert" || $action=="update") echo $_POST["newsimg_small_width"];if($action=="add") echo $NEWS_image_news_small_max_width;?>' />
			x
			<input type='text' style='width:30px' name='newsimg_small_height' value='<?php if($action=="edit") echo $edit->newsimg_small_height; if($action=="insert" || $action=="update") echo $_POST["newsimg_small_height"];if($action=="add") echo $NEWS_image_news_small_max_height;?>' /> pixels
			<br />
		Grande:
			<input type='text' style='width:30px' name='newsimg_large_width' value='<?php if($action=="edit") echo $edit->newsimg_large_width; if($action=="insert" || $action=="update") echo $_POST["newsimg_large_width"];if($action=="add") echo $NEWS_image_news_large_max_width;?>' />
			x
			<input type='text' style='width:30px' name='newsimg_large_height' value='<?php if($action=="edit") echo $edit->newsimg_large_height; if($action=="insert" || $action=="update") echo $_POST["newsimg_large_height"];if($action=="add") echo $NEWS_image_news_large_max_height;?>' /> pixels<br />
		<b><?php echo _("Menu correspondant");?>:</b>:<select name='id_menu'>
		<option value='0'>-- <?php echo _("Page non liée à un menu"); ?>--</option>
	  	<?php
	  	if($action=="edit" || $action=="update"){
	  		$tmp = $languages=request("SELECT `id_menu` FROM `$TBL_news_categories` WHERE `id` = '$idCat'", $link);
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
 * TAB2: LINKS
 */
TABS_addTab(2,_("Liens"));
?>
<?php
TABS_closeTab();
TABS_closeTabManager();
TABS_disableTab(2);
/**
 * END OF TABS
 */
//TABS_disableTab(5);
//TABS_enableTab(3);
?>