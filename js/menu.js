/**
* Vertical Menu manager
* Copyright: Cibaxion Inc. All rights reserved
* Version: 1.0
* Author: Anis Boubaker
*
* Required:
* - document.getElementsByClassName(className, parentObj)
*/

/*****************************
 * Configure the script here *
 *****************************/
MENU_menu_prefix = 'menu_';
MENU_submenu_prefix = 'submenu_';
MENU_menus_container='left_menu';
MENU_menus_class = 'menul2e';
MENU_menus_class_on = 'menul2e_on';
MENU_initial_top_offset=0; //top offset of the first submenu in px
MENU_submenus_positioning_gap=-5; //gap from the top of the menu
MENU_submenu_hide_delay=100; //in ms


/*********************************
 ** Do not edit below this line **
 *********************************/
var MENU_selected_menu=-1;//contains the selected menu id; -1 means no menu selected
var MENU_hide_timeouts=Array();

function MENU_getSubmenuY(tabObj){
	var curleft = curtop = 0;
	if (tabObj.offsetParent) {
		//curleft = tabObj.offsetLeft
		curtop = tabObj.offsetTop
		while (tabObj = tabObj.offsetParent) {
			//curleft += tabObj.offsetLeft
			curtop += tabObj.offsetTop
		}
	}
	return curtop;
}

/**
* Initialise the left menus
*/
function MENU_init() {
	var isJS = /javascript/i;


    prefix_length=MENU_menu_prefix.length;

    //First, check if the menu is actually displayed...
    if(!document.getElementById(MENU_menus_container)) return;

    //get all the input fields on the page
    //Note: in order to use the JS 'in' function, the class names must be an array with the class name as a key and a blank value.
	menuItems = document.getElementsByClassName(Array(MENU_menus_class,MENU_menus_class_on),document.getElementById(MENU_menus_container));
    //cycle trough the menuItems fields
    lastId=0;
    for(var i=0; i < menuItems.length; i++){
    	id=menuItems[i].id.substr(prefix_length,menuItems[i].id.length-prefix_length);
    	// Is it the selected tab?
    	if(menuItems[i].className==MENU_menus_class_on) MENU_selected_menu=id;
    	// Set the submenu position - The submenu must have the same numerical ID
    	submenu = document.getElementById(MENU_submenu_prefix+id)

    	if(submenu){ //if the submenu exists, we proceed with the positionning
    		//set the z-index
    		submenu.style.zIndex=500+id;
			/*if(i==0) topOffset = MENU_initial_top_offset;
			else topOffset=MENU_getSubmenuY(document.getElementById(MENU_menu_prefix+(lastId)))+MENU_submenus_positioning_gap;*/
			topOffset=MENU_getSubmenuY(document.getElementById(MENU_menu_prefix+id))+MENU_submenus_positioning_gap;
			document.getElementById(MENU_submenu_prefix+id).style.top=topOffset+'px';
			//document.getElementById(MENU_submenu_prefix+id).style.top='466px';
			document.getElementById(MENU_submenu_prefix+id).style.left='200px';
	    	//Add the onmouseover & onmouseout events to the tab and the submenu
	    	menuItems[i].onmouseover=MENU_displaySubmenu;
	    	menuItems[i].onmouseout=MENU_hideSubmenu;
	    	submenu.onmouseover=MENU_submenuPersist;
	    	submenu.onmouseout=MENU_submenuHideSubmenu;
    	}
    	lastId=id;
    }
}

function MENU_displaySubmenu(){
	var id=this.id.substr(MENU_menu_prefix.length,this.id.length-MENU_menu_prefix.length);
	var submenu = document.getElementById(MENU_submenu_prefix+id);
	//if(submenu) alert("Oui submenu");
	if(submenu){
		clearTimeout(MENU_hide_timeouts[id]);
		submenu.style.zIndex=1000;
		submenu.style.display='';
		//alert(MENU_getSubmenuY(this));
	}
}

function MENU_hideSubmenu(){
	var id=this.id.substr(MENU_menu_prefix.length,this.id.length-MENU_menu_prefix.length);
	var submenu = document.getElementById(MENU_submenu_prefix+id);
	if(submenu){
		MENU_hide_timeouts[id]=setTimeout("MENU_doHideSubmenu("+id+")",MENU_submenu_hide_delay);
		//submenu.style.display='none';
		//submenu.style.zIndex=500+id;
	}
}

function MENU_doHideSubmenu(idSubmenu){
	var submenu = document.getElementById(MENU_submenu_prefix+idSubmenu);
	if(submenu){
		//clearTimeout(TABS_hide_timeouts[idSubmenu]);
		submenu.style.display='none';
		submenu.style.zIndex=500+idSubmenu;
	}
}

/**
* Functions to handle submenus onmouseover & onmouseout events
*/
function MENU_submenuPersist(){
	id=this.id.substr(MENU_submenu_prefix.length,this.id.length-MENU_submenu_prefix.length);
	clearTimeout(MENU_hide_timeouts[id]);
	if(id!=MENU_selected_menu) document.getElementById(MENU_menu_prefix+id).className=MENU_menus_class_on;
}


function MENU_submenuHideSubmenu(){
	id=this.id.substr(MENU_submenu_prefix.length,this.id.length-MENU_submenu_prefix.length);
	MENU_hide_timeouts[id]=setTimeout("MENU_doHideSubmenu("+id+")",MENU_submenu_hide_delay);
	if(id!=MENU_selected_menu) document.getElementById(MENU_menu_prefix+id).className=MENU_menus_class;
}

/*function MENU_submenuHideSubmenu(){
	id=this.id.substr(MENU_submenu_prefix.length,this.id.length-MENU_submenu_prefix.length);
	MENU_hideSubmenu2(id);
	if(id!=MENU_selected_menu) document.getElementById(MENU_menu_prefix+id).className=MENU_menus_class;
}

function MENU_hideSubmenu2(id){
	var submenu = document.getElementById(MENU_submenu_prefix+id);
	if(submenu){
		submenu.style.display='none';
		submenu.style.zIndex=500+idSubmenu;
	}
}*/
