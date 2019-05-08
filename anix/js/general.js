function toggleLanguage(language){
	//unselect the selected language
	container = document.getElementById('language_selector_bar');

	selected=document.getElementsByClassName('language_selector_on',container);
	for(i=0;i<selected.length;i++){
		selected[i].className='language_selector';
		//strip the language ID from the selector ID (format: selector_languageID)
		id_selected = selected[i].id.substr(9,selected[i].id.length-9);
		toHide = document.getElementsByClassName('lang_'+id_selected,document.getElementById('main_table'));
		for(j=0;j<toHide.length;j++){
			toHide[j].style.display='none';
		}
	}
	newSelected = document.getElementById('selector_'+language);
	newSelected.className='language_selector_on';
	newSelected.blur();
	//show the body components
	toShow = document.getElementsByClassName('lang_'+language,document.getElementById('main_table'));
	for(j=0;j<toShow.length;j++){
		toShow[j].style.display='';
	}
}

function hideOtherLanguages(ids){
	for(i=0;i<ids.length;i++){
		setTimeout("doHideLanguage("+ids[i]+")",3000);
	}
}


function doHideLanguage(id){
	//for(i=0;i<ids.length;i++){
		hideArray = document.getElementsByClassName('lang_'+id,document.getElementById('main_table'));
		for(j=0;j<hideArray.length;j++){
			hideArray[j].style.display='none';
		}
	//}
}


document.getElementsByClassName = function(className, parentElement) {
	var children = parentElement.getElementsByTagName('*');
	var result = Array();
	for(var i=0; i < children.length; i++) {
		if(children[i].className==className) result.push(children[i]);
	}
	return result;
}

function hideMessage(){
	document.getElementById('message_error_box').style.display='none';
}

function setTitleBar(str){
	document.getElementById('title_bar').innerHTML=str;
}

function getViewPortSizes(){
	var viewportwidth;
	var viewportheight;
	 // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
	 if (typeof window.innerWidth != 'undefined'){
	      viewportwidth = window.innerWidth,
	      viewportheight = window.innerHeight
	 }
	// IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
	 else if (typeof document.documentElement != 'undefined'
	     && typeof document.documentElement.clientWidth !=
	     'undefined' && document.documentElement.clientWidth != 0){
		       viewportwidth = document.documentElement.clientWidth,
		       viewportheight = document.documentElement.clientHeight
	 }
	 // older versions of IE
	 else{
	       viewportwidth = document.getElementsByTagName('body')[0].clientWidth,
	       viewportheight = document.getElementsByTagName('body')[0].clientHeight
	 }
	 return viewportheight;

}

function setMainDivHeight(reduce){

	height=getViewPortSizes()-reduce;
	document.getElementById('main_container').style.height=height+'px';
}

function showHideAnixMessage(strDetails, strClose){
	if(document.getElementById('message_details').style.display=='none'){
		document.getElementById('message_details').style.display='';
		document.getElementById('message_button').innerHTML=strClose;
		//document.getElementById('message_button').className='opened';
		document.getElementById('message_button').style.backgroundImage = 'url(../images/arrow_details_up.jpg)';
	} else {
		document.getElementById('message_details').style.display='none';
		document.getElementById('message_button').innerHTML=strDetails;
		//document.getElementById('message_button').className='closed';
		document.getElementById('message_button').style.backgroundImage = 'url(../images/arrow_details_down.jpg)';
	}
}

function showSpinner(){
	document.getElementById('spinner').style.display='';
}

function hideSpinner(){
	document.getElementById('spinner').style.display='none';
}

function formSubmit(formElement){
	showSpinner();
	formElement.submit();
}

function anixPopup(page){
	void(window.open(page,'anix_popup','resizable=no,location=no,,menubar=no,scrollbars=yes,status=no,toolbar=no,fullscreen=no,dependent=no,directories=no,width=580,height=500,left=10,top=100'));
}

function logoutConfirm(message){
	if(confirm(message)){
		window.location='../logout.php';
	}
}

//MOD LINKS
function JS_links_add_link($linkFromModule,$linkFromId){
	//Retrieve the category id
	selected_category = document.getElementById('links_add_category').options[document.getElementById('links_add_category').selectedIndex].value;
	if(selected_category==0) return;
	anixPopup("../mod_links/index.php?action=addLink&linkCat="+selected_category+"&linkFrom="+$linkFromModule+"&id="+$linkFromId);
}

function showHideLinks($category){
	container = document.getElementById('links_'+$category);
	if(container.style.display=='none'){
		container.style.display='';
	} else {
		container.style.display='none';
	}
}

function updateLinks($category){
	str="";
	size = links_table[$category].length;
	for(i=0;i<size;i++){
		str=str+"<a href='javascript:void(0);' onclick='xajax_deleteLink("+links_table[$category][i][0]+")'><img src='../images/del.gif' /></a>";
		if(i!=0) str+="<a href='javascript:void(0);' onclick='xajax_moveLinkUp("+links_table[$category][i][0]+")'><img src='../images/order_up.gif' /></a>";
		else str+="<img src='../images/order_blank.gif' />";
		if(i<size-1) str+="<a href='javascript:void(0);' onclick='xajax_moveLinkDown("+links_table[$category][i][0]+")'><img src='../images/order_down.gif' /></a>";
		else str+="<img src='../images/order_blank.gif' />";
		str+="&nbsp;&nbsp;"+links_table[$category][i][1]+"<br />";
	}
	document.getElementById('links_'+$category).innerHTML=str;
	document.getElementById('links_nb_'+$category).innerHTML=links_table[$category].length;
}

function addLinksFromCart(){
	//Wierd...
	setTimeout('xajax_addLinks()', 500);
}