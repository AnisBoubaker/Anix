<?php
function TABS_startTabManager($width){
	echo "<div id='TABS_tabcontrol' class='TABS_tabManager' style='width:$width'>\n";
	echo "<div id='TABS_tab_container'></div>\n";
}

function TABS_addTab($id,$title,$enabled=true){
	echo "<script type=\"text/javascript\">TABS_addTab($id,\"$title\",".($enabled?"true":"false").")</script>\n";
	if($id!=1) $style = "display:none;"; else $style="";
	echo "<div id='TABS_content$id' class='TABS_content' style='$style'>\n";
}

function TABS_closeTab(){
	echo "</div>\n";
}

function TABS_closeTabManager(){
	echo "</div>\n";
}

function TABS_disableTab($id){
	echo "<script type=\"text/javascript\">TABS_disableTab($id)</script>\n";
}

function TABS_enableTab($id){
	echo "<script type=\"text/javascript\">TABS_enableTab($id)</script>\n";
}

function TABS_changeTab($id){
	echo "<script type=\"text/javascript\">TABS_changeTab($id)</script>\n";
}

?>