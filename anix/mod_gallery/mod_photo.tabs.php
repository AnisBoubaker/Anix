<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
	<font class='fieldTitle'><?php echo _("Affichage");?>:</font><br>
    <?
    if($action=="edit" || $action=="update"){
    }
    ?>
    <input type='radio' name='active' value='Y' <?
    if($action=="edit" && $edit->active=="Y") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="Y") echo " CHECKED";
    ?>> <?php echo _("Cette nouvelle est toujours affichée.");?><br>
    <input type='radio' name='active' value='DATE' <?
    if($action=="edit" && $edit->active=="DATE") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="DATE") echo " CHECKED";
    ?>> <?php echo _("Afficher cette nouvelle du");?> <input type='text' name='from_date' id='from_date' size='10'<?
    if($action=="edit" && $edit->active=="DATE") echo " value='".$edit->from_date."'";
    if($action=="insert" || $action=="update") echo " value='".$_POST["from_date"]."'";
    ?> READONLY><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('from_date'),this);" style='vertical-align:bottom;' />
     <?php echo _("au"); ?> <input type='text' name='to_date' id='to_date' size='10'<?
     if($action=="edit" && $edit->active=="DATE") echo " value='".$edit->to_date."'";
     if($action=="insert" || $action=="update") echo " value='".$_POST["to_date"]."'";
    ?> READONLY><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('to_date'),this);" style='vertical-align:bottom;' />
    <br />
    <input type='radio' name='active' value='ARCHIVE' <?
    if($action=="edit" && $edit->active=="ARCHIVE") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="ARCHIVE") echo " CHECKED";
    ?>> <?php echo _("Cette nouvelle est archivée (affichée avec les archives)."); ?><br>
    <input type='radio' name='active' value='N' <?
    if($action=="add") echo " CHECKED";
    if($action=="edit" && $edit->active=="N") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="N") echo " CHECKED";
    ?>> <?php echo _("Cette nouvelle est désactivée (non affichée)."); ?><br>
<?php
TABS_closeTab();
TABS_closeTabManager();
/**
 * END OF TABS
 */
//TABS_disableTab(5);
//TABS_enableTab(3);
?>