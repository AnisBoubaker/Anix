//this function runs when the page is loaded
function init() {
    //updateMenu();
    updateExternalLinks();
    MENU_init();
    TABS_init();
}

function updateExternalLinks() {
 if (!document.getElementsByTagName) return;
 var anchors = document.getElementsByTagName("a");
 for (var i=0; i<anchors.length; i++) {
   var anchor = anchors[i];
   if (anchor.getAttribute("href") &&
       anchor.getAttribute("rel") == "external")
     anchor.target = "_blank";
 }
}

document.getElementsByClassName = function(classNames, parentElement) {
	var children = parentElement.getElementsByTagName('*');
	var result = Array();
	for(var i=0; i < children.length; i++) {
		if(children[i].className==classNames[0] || children[i].className==classNames[1]) result.push(children[i]);
	}
	return result;
}

function showHideHelpForm($language){
	var htmlCode = "";
	if($language=="fr_CA"){
		htmlCode+="<b>Obtenez de l'aide à propos de cette page:</b><br /><br />";
	} else {
		htmlCode+="<b>Get help about this page:</b><br /><br />";
	}
	htmlCode+="<div style='float:left;width:37%;'>";
	if($language=="fr_CA"){
		htmlCode+="Vous ne comprenez pas quelque chose sur cette page ou vous avez besoin de plus de précisions?<br /><br />";
		htmlCode+="Remplissez simplement ce formulaire et un de nos représentant se fera un plaisir de vous apporter les éclaircissements nécessaires.<br /><br />";
		htmlCode+="Nous saurons exactement sur quelle page vous vous trouviez, il est donc inutile de le préciser.";
	} else {
		//default to english
		htmlCode+="You don't understand something on this page or you need more information?<br /><br />";
		htmlCode+="Simply fill this form and a representative will be glad to give you the appropriate clarifications.<br /><br />";
		htmlCode+="We will know exactly from which page you are sending us this request, so there is no need to specify it.";
	}
	htmlCode+="</div>";
	htmlCode+="<table id='tableHelpForm'>";
	if($language=="fr_CA"){
		htmlCode+="<tr><td>&nbsp;</td><td class='note'>(*): Champs obligatoires</td></tr>";
		htmlCode+="<tr><td class='title'>Nom*:</td><td><input type='text' id='helpFormName' /></td></tr>";
		htmlCode+="<tr><td class='title'>Courriel*:</td><td><input type='text' id='helpFormEmail' /></td></tr>";
		htmlCode+="<tr><td class='title'>Téléphone:</td><td><input type='text' id='helpFormPhone' /></td></tr>";
		htmlCode+="<tr><td class='title'>Question*:</td><td><textarea id='helpFormMessage'></textarea></td></tr>";
		htmlCode+="<tr><td>&nbsp;</td><td><button onclick=\"submitHelpForm('"+$language+"')\">Envoyer</button>&nbsp;<button onclick='cancelHelpForm()'>Annuler</button></td></tr>";
	} else {
		htmlCode+="<tr><td>&nbsp;</td><td class='note'>(*): Mandatory fields</td></tr>";
		htmlCode+="<tr><td class='title'>Name*:</td><td><input type='text' id='helpFormName' /></td></tr>";
		htmlCode+="<tr><td class='title'>Email*:</td><td><input type='text' id='helpFormEmail' /></td></tr>";
		htmlCode+="<tr><td class='title'>Phone:</td><td><input type='text' id='helpFormPhone' /></td></tr>";
		htmlCode+="<tr><td class='title'>Question*:</td><td><textarea id='helpFormMessage'></textarea></td></tr>";
		htmlCode+="<tr><td>&nbsp;</td><td><button onclick=\"submitHelpForm('"+$language+"')\">Send</button>&nbsp;<button onclick='cancelHelpForm()'>Cancel</button></td></tr>";
	}
	htmlCode+="</table>";

	//We fill and empty the div each time we click the button (instead of just hiding it)
	//because of a crazy IE bug!!
	if(document.getElementById('divHelpForm').style.display=='none'){
		document.getElementById('divHelpForm').innerHTML=htmlCode;
		document.getElementById('divHelpForm').style.display='';
	} else {
		document.getElementById('divHelpForm').innerHTML='';
		document.getElementById('divHelpForm').style.display='none';
	}
}

function cancelHelpForm(){
	document.getElementById('divHelpForm').innerHTML=''; //Still the IE crazy bug!
	document.getElementById('divHelpForm').style.display='none';
}

function submitHelpForm($language){
	var emailFilter = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.([a-zA-Z]{2,6})$/

	var emptyNameMessage, emptyMailMessage ,emptyQuestionMessage;
	if($language=="fr_CA"){
		emptyNameMessage="Merci de saisir votre nom.";
		emptyMailMessage="Merci de saisir votre adresse de courriel.";
		emptyQuestionMessage="Merci de saisir votre question.";
		badEmailMessage="L'adresse de courriel saisie est invalide. Merci de la vérifier.";
	} else {
		emptyNameMessage="Please enter your name.";
		emptyMailMessage="Please enter your email address.";
		emptyQuestionMessage="Please enter your question.";
		badEmailMessage="The entered email address is invalid. Please check it.";
	}
	//check if all required fields are filled
	if(document.getElementById('helpFormName').value==""){ alert(emptyNameMessage); return;	}
	if(document.getElementById('helpFormEmail').value==""){ alert(emptyMailMessage); return; }
	if(document.getElementById('helpFormMessage').value==""){ alert(emptyQuestionMessage); return; }
	//check email address (syntax check only)
	if(!emailFilter.test(document.getElementById('helpFormEmail').value)){
		alert(badEmailMessage); return;
	}
	window.open('./helpForm.php','helpForm','resizable=no,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,fullscreen=no,dependent=no,width=300,height=200,left=200,top=150')
}

function sendPage(){
	window.open('./sendPage.php','sendPage','resizable=no,location=no,menubar=no,scrollbars=no,status=no,toolbar=no,fullscreen=no,dependent=no,width=450,height=450,left=50,top=50');
}


window.onload = init;