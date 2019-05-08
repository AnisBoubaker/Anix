/**
* Tabs Menu manager
* Copyright: Cibaxion Inc. All rights reserved
* Version: 1.0
* Author: Anis Boubaker
*
*/

/*****************************
 * Configure the script here *
 *****************************/
var TABS_auto_id_generation = false; //whether the tab ID's have to be created automatically or not? If not, it shouldn't be any ID on the tab container because it'll be overwritten!
var TABS_tab_ids_prefix='tab_'; //The string prefixed before the numerical identifier of the tab (unique)
var TABS_subtabs_ids_prefix = 'subtab_';
var TABS_tab_container='tabs_box'; //ID of the block element containing the tabs
var TABS_tab_classes=['tab_off','tab_on']; //Name of the classes used to display the tabs: [0] contains the OFF state and [1] contains the ON state
var TABS_tab_marker_id = 'tabsMarker'; //div positioned at the max left position, used as a marker to determine submenus positions
var TABS_initial_right_offset = 2;
var TABS_submenus_positioning_gap = 3; //gap added to the style.right property
var TABS_submenu_hide_delay = 100;


/*********************************
 ** Do not edit below this line **
 *********************************/
var TABS_selected_tab=-1;//contains the selected tab id; -1 means no tab selected
var TABS_hide_timeouts=Array();


function TABS_getSubmenuX(tabObj){
	var curleft = curtop = 0;
	if (tabObj.offsetParent) {
		curleft = tabObj.offsetLeft
		//curtop = tabObj.offsetTop
		while (tabObj = tabObj.offsetParent) {
			curleft += tabObj.offsetLeft
			//curtop += tabObj.offsetTop
		}
	}
	return curleft;
}

function TABS_displaySubmenu(){
	var id=this.id.substr(TABS_tab_ids_prefix.length,this.id.length-TABS_tab_ids_prefix.length);
	var submenu = document.getElementById(TABS_subtabs_ids_prefix+id);
	//if(submenu) alert("Oui submenu");
	if(submenu){
		//TABS_doHideSubmenu(TABS_displayed_menu);
		clearTimeout(TABS_hide_timeouts[id]);
		//TABS_displayed_menu = id;
		submenu.style.display='none';
		submenu.style.zIndex=1000;
		submenu.style.display='';
	}
}

function TABS_hideSubmenu(){
	var id=this.id.substr(TABS_tab_ids_prefix.length,this.id.length-TABS_tab_ids_prefix.length);
	var submenu = document.getElementById(TABS_subtabs_ids_prefix+id);
	if(submenu){
		//submenu.style.display='none';
		TABS_hide_timeouts[id]=setTimeout("TABS_doHideSubmenu("+id+")",TABS_submenu_hide_delay);
		//alert("TABS_doHideSubmenu("+this.dbId+")");
	}
}

function TABS_doHideSubmenu(idSubmenu){
	var submenu = document.getElementById(TABS_subtabs_ids_prefix+idSubmenu);
	if(submenu){
		//clearTimeout(TABS_hide_timeouts[idSubmenu]);
		submenu.style.display='none';
		submenu.style.zIndex=900+idSubmenu;
	}
}

/**
* Functions to handle submenus onmouseover & onmouseout events
*/
function TABS_submenuPersist(){
	id=this.id.substr(TABS_subtabs_ids_prefix.length,this.id.length-TABS_subtabs_ids_prefix.length);
	clearTimeout(TABS_hide_timeouts[id]);
	//TABS_tabs_class_backup[id]=document.getElementById('tab_'+id).className;
	if(id!=TABS_selected_tab) document.getElementById('tab_'+id).className=TABS_tab_classes[1];
	//alert('timeout cleared: '+id);
}

function TABS_submenuHideSubmenu(){
	id=this.id.substr(TABS_subtabs_ids_prefix.length,this.id.length-TABS_subtabs_ids_prefix.length);
	TABS_hide_timeouts[id]=setTimeout("TABS_doHideSubmenu("+id+")",TABS_submenu_hide_delay);
	if(id!=TABS_selected_tab) document.getElementById('tab_'+id).className=TABS_tab_classes[0];
}

/**
* Initialise the tabs
*/
function TABS_init() {
	var isJS = /javascript/i;
    //get all the input fields on the page
    //menuItems = Array();
    /*for(i=0;i<TABS_tab_classes.length;i++) {
	    menuItems = menuItems.concat(document.getElementsByClassName(TABS_tab_classes[i],document.getElementById('tabs_box')));
	}*/
    menuItems = document.getElementById('tabs_box').getElementsByTagName('a');//Contain the menu objects - Must be anchors
	maxLeftCoordinate = TABS_getSubmenuX(document.getElementById(TABS_tab_marker_id));
	prefix_length=TABS_tab_ids_prefix.length;
    //cycle trough the menuItems fields
    lastId=0;
    for(var i=0; i < menuItems.length; i++) {
    	if(TABS_auto_id_generation){ //we generate an ID automatically
    		menuItems[i].id=TABS_tab_ids_prefix+i;
    		id=i;
    	} else { //we use the id defined inside de markup
    		id=menuItems[i].id.substr(prefix_length,menuItems[i].id.length-prefix_length);
    	}
    	// Is it the selected tab?
    	if(menuItems[i].className==TABS_tab_classes[1]) TABS_selected_tab=id;
    	// Set the submenu position - The submenu must have the same numerical ID
    	submenu = document.getElementById(TABS_subtabs_ids_prefix+id)
    	//initialise the timeouts array
    	TABS_hide_timeouts[id]=0;
    	if(submenu){ //if the submenu exists, we proceed with the positionning
    		//set the z-index
    		submenu.style.zIndex=900+id;
			if(i==0) rightOffset = TABS_initial_right_offset;
			else rightOffset=maxLeftCoordinate-TABS_getSubmenuX(document.getElementById('tab_'+(lastId)))+TABS_submenus_positioning_gap;
			document.getElementById(TABS_subtabs_ids_prefix+id).style.right=rightOffset+'px';
	    	//Add the onmouseover & onmouseout events to the tab and the submenu
	    	menuItems[i].onmouseover=TABS_displaySubmenu;
	    	menuItems[i].onmouseout=TABS_hideSubmenu;
	    	submenu.onmouseover=TABS_submenuPersist
	    	submenu.onmouseout=TABS_submenuHideSubmenu;
    	}
    	lastId=id;
    }
}