<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
		<table width='100%'>
    	<tr>
          <td><B><?php echo _("Menu correspondant");?>:</B></td>
          <td align='left'>
          	<select name='id_menu'>
          	<option value='0'>-- <?php echo _("Page non liée à un menu"); ?>--</option>
          	<?php
          	if($action=="edit" || $action=="update"){
          		$tmp = $languages=request("SELECT `id_menu` FROM `$TBL_content_pages` WHERE `id` = '$idPage'", $link);
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
 * TAB2: LINKS
 */
TABS_addTab(2,_("Liens"));
?>
		<br /><br /><?php echo _("Désolé, cette fonction n'est pas encore disponible."); ?>
		<br /><br />
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