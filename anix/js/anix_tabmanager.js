/*
	ANIX TAB Manager v1.0.0
	Permission is hereby granted, free of charge, to any person obtaining a
	copy of this software and associated documentation files (the "Software"),
	to deal in the Software without restriction, including without limitation
	the rights to use, copy, modify, merge, publish, distribute, sublicense,
	and/or sell copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included
	in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
	OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	ITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
	DEALINGS IN THE SOFTWARE.
*/

var TABS_selected_tab=1;

function TABS_changeTab(idtab){
	if(idtab==TABS_selected_tab) return;
	document.getElementById('TABS_tab'+idtab).className='TABS_tab selected';
	document.getElementById('TABS_tab'+TABS_selected_tab).className='TABS_tab';
	document.getElementById('TABS_content'+TABS_selected_tab).style.display='none';
	document.getElementById('TABS_content'+idtab).style.display='';
	TABS_selected_tab=idtab;
}

function TABS_addTab(idTab,title,enabled){
	selected="";
	if(enabled){
		if(TABS_selected_tab==idTab) selected=" selected";
		document.getElementById('TABS_tab_container').innerHTML+="<div id='TABS_tab"+idTab+"' class='TABS_tab"+selected+"' onclick='TABS_changeTab("+idTab+");'>"+title+"</div>";
	} else {
		document.getElementById('TABS_tab_container').innerHTML+="<div id='TABS_tab"+idTab+"' class='TABS_tab_disabled'>"+title+"</div>";
	}
}

function TABS_disableTab(idTab){
	if(document.getElementById('TABS_tab'+idTab).className=='TABS_tab_disabled') return;
	if(TABS_selected_tab==idTab) TABS_changeTab(1); //select the first tab if the tab we want to disable is selected
	//change the tab class and onclick action
	document.getElementById('TABS_tab'+idTab).className='TABS_tab_disabled';
	document.getElementById('TABS_tab'+idTab).setAttribute('onclick', '');
	document.getElementById('TABS_tab'+idTab).onclick=""; //IE handling
}

function TABS_enableTab(idTab){
	if(document.getElementById('TABS_tab'+idTab).className!='TABS_tab_disabled') return;
	document.getElementById('TABS_tab'+idTab).className='TABS_tab';
	document.getElementById('TABS_tab'+idTab).setAttribute('onclick', "TABS_changeTab("+idTab+");");
	document.getElementById('TABS_tab'+idTab).onclick=function() {TABS_changeTab(idTab);} //IE handling
}