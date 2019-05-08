<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
	<font class='fieldTitle'><?PHP echo _("Affichage"); ?>:</font><br>
    <?
    if($action=="edit" || $action=="update"){
    }
    ?>
    <input type='radio' name='active' value='Y' <?
    if($action=="add") echo " CHECKED";
    if($action=="edit" && $edit->active=="Y") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="Y") echo " CHECKED";
    ?>> <?php echo _("Cette question est affichée sur le site.");?><br>
    <input type='radio' name='active' value='N' <?
    if($action=="edit" && $edit->active=="N") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="N") echo " CHECKED";
    ?>> <?php echo _("Cette question est désactivée (non affichée)."); ?><br>
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
?>