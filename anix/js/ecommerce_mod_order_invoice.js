/* GLOBAL VARIABLES */
$JS_COUNT_NEW_LINES = 0;

function newItemActivation(){
	box_state = document.main_form.add_item.checked;
	document.main_form.qty_new.disabled=!box_state;
	document.main_form.reference_new.disabled=!box_state;
	document.main_form.description_new.disabled=!box_state;
	document.main_form.details_new.disabled=!box_state;
	document.main_form.uprice_new.disabled=!box_state;
}
function delItem(id,confirmText,domId){
	if(id>0){
		if(confirm(confirmText)){
			document.getElementById('delete_line').value=id;
			//document.delForm.idItem.value=id;
			//document.delForm.submit();
			document.main_form.submit();
		}
	} else if(domId!=-1){
		var tblBody = document.getElementById('invoice_items').tBodies[0];
		tblBody.removeChild(document.getElementById('newrow'+domId));
	}
}
function doFraudCheck(idOrder){
	if(document.getElementById('fraud_check_method').value==0){
		alert("Veuillez choisir une méthode d'évaluation");
	} else {
		url='./fraud_check.php?idOrder='+idOrder+'&method=';
		url=url+document.getElementById('fraud_check_method').value;
		//window.open(url);
		void(window.open(url,'fraud_check','resizable=no,location=no,menubar=no,scrollbars=yes,status=no,toolbar=no,fullscreen=no,dependent=no,width=580,height=500,left=200,top=150'));
	}
}
function hideFraud(){
	document.getElementById('fraud_details').style.display='none';
	document.getElementById('confirm_refresh').style.display='';
}

function pageRefresh(){
	if(confirm("ATTENTION: Afin de refléter des changements, cette page doit être rechargée et vos modifications risquent d'être perdues. Voulez-vous autoriser le rechargement de la page?\n\nSi vous avez apporté des modifications à ce bon de commande, cliquez sur NON et validez vosu modifications d'abord. Si vous n'avez effectué aucune modification, cliquez sur OUI.")){
		location.reload(true);
	}
}

function addLineItem(qty,ref,description,uprice,id_product){
	var tblBody = document.getElementById('invoice_items').tBodies[0];
	var newRow = tblBody.insertRow(0); //append new row to the top of the table
	newRow.id='newrow'+$JS_COUNT_NEW_LINES;
	//qty
	var newCell = newRow.insertCell(0);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '50px';
	newInput.id = newRow.id+'_qty';
	newInput.name = newRow.id+'_qty';
	newInput.className = 'price';
	if(qty!='') newInput.value=qty;
	newCell.appendChild(newInput);
	//code
	var newCell = newRow.insertCell(1);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '80px';
	newInput.id = newRow.id+'_reference';
	newInput.name = newRow.id+'_reference';
	if(ref!='') newInput.value=ref;
	if(id_product!='') newInput.setAttribute("readonly", 'readonly');
	newCell.appendChild(newInput);
	var newInput = document.createElement('input');
	newInput.type = 'hidden';
	newInput.id = newRow.id+'_product';
	newInput.name = newRow.id+'_product';
	if(id_product!='') newInput.value=id_product;
	newCell.appendChild(newInput);
	//desc
	var newCell = newRow.insertCell(2);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '400px';
	newInput.id = newRow.id+'_description';
	newInput.name = newRow.id+'_description';
	if(description!='') newInput.value=description;
	newCell.appendChild(newInput);
	newLine = document.createElement('span');
	newLine.innerHTML="<br />";
	newCell.appendChild(newLine);
	var newInput = document.createElement('textarea');
	newInput.className='mceNoEditor';
	newInput.style.width = '400px';
	newInput.id = newRow.id+'_details';
	newInput.name = newRow.id+'_details';
	newCell.appendChild(newInput);
	//price
	var newCell = newRow.insertCell(3);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '80px';
	newInput.id = newRow.id+'_uprice';
	newInput.name = newRow.id+'_uprice';
	newInput.className = 'price';
	if(uprice!='') newInput.value=uprice;
	newCell.appendChild(newInput);
	//total
	var newCell = newRow.insertCell(4);
	newCell.appendChild(document.createTextNode('--'));
	//actions
	var newCell = newRow.insertCell(5);
	newCell.style.verticalAlign='middle';
	actions = document.createElement('span');
	actions.innerHTML="<img src=\"../images/del.gif\" onclick=\"javascript:delItem(0,0,"+$JS_COUNT_NEW_LINES+")\" />";
	newCell.appendChild(actions);
	$JS_COUNT_NEW_LINES++;
	document.getElementById('nb_new_lines').value=$JS_COUNT_NEW_LINES;
}

function addPOrderLineItem(qty,refStore,refSupplier,description,uprice,id_product){
	var tblBody = document.getElementById('invoice_items').tBodies[0];
	var newRow = tblBody.insertRow(0); //append new row to the top of the table
	newRow.id='newrow'+$JS_COUNT_NEW_LINES;
	//qty
	var newCell = newRow.insertCell(0);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '50px';
	newInput.id = newRow.id+'_qty';
	newInput.name = newRow.id+'_qty';
	newInput.className = 'price';
	if(qty!='') newInput.value=qty;
	newCell.appendChild(newInput);
	//received qty
	var newCell = newRow.insertCell(1);
	var newSpan = document.createElement('span');
	newSpan.innerHTML="--";
	newCell.appendChild(newSpan);
	//code
	var newCell = newRow.insertCell(2);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '80px';
	newInput.id = newRow.id+'_refStore';
	newInput.name = newRow.id+'_refStore';
	if(refStore!='') newInput.value=refStore;
	if(id_product!='') newInput.setAttribute("readonly", 'readonly');
	newCell.appendChild(newInput);
	var newInput = document.createElement('input');
	newInput.type = 'hidden';
	newInput.id = newRow.id+'_product';
	newInput.name = newRow.id+'_product';
	if(id_product!='') newInput.value=id_product;
	newCell.appendChild(newInput);
	//ref supplier
	var newCell = newRow.insertCell(3);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '80px';
	newInput.id = newRow.id+'_refSupplier';
	newInput.name = newRow.id+'_refSupplier';
	if(refSupplier!='') newInput.value=refSupplier;
	newCell.appendChild(newInput);
	//desc
	var newCell = newRow.insertCell(4);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '400px';
	newInput.id = newRow.id+'_description';
	newInput.name = newRow.id+'_description';
	if(description!='') newInput.value=description;
	newCell.appendChild(newInput);
	//price
	var newCell = newRow.insertCell(5);
	var newInput = document.createElement('input');
	newInput.type = 'text';
	newInput.style.width = '80px';
	newInput.id = newRow.id+'_uprice';
	newInput.name = newRow.id+'_uprice';
	newInput.className = 'price';
	if(uprice!='') newInput.value=uprice;
	newCell.appendChild(newInput);
	//total
	var newCell = newRow.insertCell(6);
	newCell.appendChild(document.createTextNode('--'));
	//actions
	var newCell = newRow.insertCell(7);
	newCell.style.verticalAlign='middle';
	actions = document.createElement('span');
	actions.innerHTML="<img src=\"../images/del.gif\" onclick=\"javascript:delItem(0,0,"+$JS_COUNT_NEW_LINES+")\" />";
	newCell.appendChild(actions);
	$JS_COUNT_NEW_LINES++;
	document.getElementById('nb_new_lines').value=$JS_COUNT_NEW_LINES;
}


function confirmStockUpdate($msg,$idOrder,$idInvoice){
	if(confirm($msg)){
		xajax_updateStock($idOrder,$idInvoice);
	}
}