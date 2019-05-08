<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
		<?php
		if($action=="edit" || $action=="update"){
	  		$tmp = $languages=request("SELECT `contain_items`,`id_menu` FROM `$TBL_faq_categories` WHERE `id` = '$idCat'", $link);
	  		$edit = mysql_fetch_object($tmp);
	  	}
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
	  	$tmp = getMenusList(0,$link,$_SESSION["used_languageid2"]);
	  	foreach($tmp as $idmenu => $menus){
	  		echo "<option value='".$menus["id"]."'";
	  		if($action=="edit" && $menus["id"]==$edit->id_menu) echo " selected='selected'";
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
if($action=="add" || $action=="insert"){
	TABS_disableTab(2);
}
TABS_disableTab(2);
/**
 * END OF TABS
 */
//TABS_disableTab(5);
//TABS_enableTab(3);
?>