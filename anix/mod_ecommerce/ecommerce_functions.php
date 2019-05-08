<?php
$MAX_SEARCH_RESULTS = 1000;

function getCountriesList(){
	global $used_language_id, $TBL_ecommerce_countries,$TBL_ecommerce_info_countries;
	$link=dbConnect();
	$countryList = array();
	$lastOrdering=-1;
	$request = request("SELECT `id`,`code2`,`name`,`ordering`,`default_country`,`provinces` FROM `$TBL_ecommerce_countries`,`$TBL_ecommerce_info_countries` WHERE `authorized`='Y' AND `id_country`=`id` AND `id_language`='$used_language_id' ORDER BY `ordering`,`name`",$link);
	while($country=mysql_fetch_object($request)){
		if($lastOrdering==-1) $lastOrdering = $country->ordering;
		if($lastOrdering!=$country->ordering) {
			$countryList["separator_".$country->ordering]="separator";
			$lastOrdering = $country->ordering;
		}
		$countryList[$country->code2]=$country->name;
	}
	return $countryList;
}

function showClients($criteria,$order,$link){
	global $TBL_ecommerce_customer;
	global $sort;
	$returnStr="";
	$request = request("SELECT * from $TBL_ecommerce_customer $criteria ORDER BY $order",$link);
	//echo "SELECT * from $TBL_ecommerce_customer $criteria ORDER BY $order";

	$returnStr.="<table class='edittable_text' width='100%'>";
	$returnStr.="<tr>";
	$returnStr.="<td>&nbsp;</td>";
	$returnStr.="<td><a href='./list_clients.php?sort=$sort&order=1'><b>"._("Prenom")."</b></a></td>";
	$returnStr.="<td><a href='./list_clients.php?sort=$sort&order=2'><b>"._("Nom")."</b></a></td>";
	$returnStr.="<td><a href='./list_clients.php?sort=$sort&order=3'><b>"._("Compagnie")."</b></a></td>";
	$returnStr.="<td><b>"._("Telephone")."</b></td>";
	$returnStr.="<td><b>"._("Courriel")."</b></td>";
	$returnStr.="</tr>";
	if(!mysql_num_rows($request)){
		$returnStr.="<tr><td colspan='6'><center><i>"._("Aucun client ne correspondant a vos criteres n'a ete trouve")."</i></center></td></tr>";
	}
	while($client=mysql_fetch_object($request)){
		$returnStr.="<tr>";
		$returnStr.="<td align='right' valign='middle' width='63' bgcolor='#e7eff2'>";
		$returnStr.="<a href='./del_client.php?idClient=".$client->id."'><img src='../images/del.gif' border='0' alt='"._("Supprimer")."'></a>";
		$returnStr.="<a href='./mod_client.php?action=edit&idClient=".$client->id."'><img src='../images/edit.gif' border='0' alt='"._("Modifier")."'></a>";
		$returnStr.="<a href='./view_client.php?idClient=".$client->id."'><img src='../images/view.gif' border='0' alt='"._("Voir")."'></a>";
		$returnStr.="</td>";
		$returnStr.="<td >";
		$returnStr.=$client->firstname;
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.=$client->lastname;
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.=$client->company;
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.=$client->phone;
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<a href='mailto:$client->email'>".$client->email."</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
	}
	$returnStr.="</table>";

	return $returnStr;
}

function deleteClient($idClient,$link){
	global $TBL_ecommerce_customer;
	global $TBL_ecommerce_address;
	$return = array();
	$return["errors"]=0;
	$return["errMessage"]="";
	$return["message"]="";
	$result = request("SELECT id,firstname,lastname,id_address_mailing,id_address_billing FROM `$TBL_ecommerce_customer` WHERE `id`='$idClient'",$link);
	if(!mysql_num_rows($result)){
		$return["errors"]++;
		$return["errMessage"].="- "._("Le client specifie n'existe pas.")."<br>";
	}
	/**
    * Check here if there are invoices ou orders ==> Cannot delete client
    */
	if(!$return["errors"]){
		//Get the client object
		$client=mysql_fetch_object($result);
		//Delete the address
		request("DELETE FROM `$TBL_ecommerce_address`
               WHERE `id`='".$client->id_address_mailing."' OR `id`='".$client->id_address_billing."'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].="- "._("Une erreur s'est produite lors de la suppression des adresses du client.")."<br>";
		}
	}
	if(!$return["errors"]){
		//Delete the customer
		request("DELETE FROM `$TBL_ecommerce_customer` WHERE `id`='".$client->id."'",$link);
		if(mysql_errno($link)){
			$return["errors"]++;
			$return["errMessage"].="- "._("Une erreur s'est produite lors de la suppression du client.")."<br>";
		}
	}
	if(!$return["errors"]){
		$return["message"]="- "._("Le client a ete supprime correctement.")."<br>";
	}
	return $return;
}

function getPaymentTypes($link){
	global $TBL_ecommerce_payment_type,$TBL_ecommerce_info_payment_type;
	global $used_language_id;
	$requestStr ="SELECT `$TBL_ecommerce_payment_type`.`id`,
                                `$TBL_ecommerce_payment_type`.`image_file`,
                                `$TBL_ecommerce_info_payment_type`.`name`,
                                `$TBL_ecommerce_info_payment_type`.`fields`
                    FROM   `$TBL_ecommerce_payment_type`,`$TBL_ecommerce_info_payment_type`
                    WHERE `$TBL_ecommerce_info_payment_type`.`id_payment_type` = `$TBL_ecommerce_payment_type`.`id`
                    AND `$TBL_ecommerce_info_payment_type`.`id_language`='$used_language_id'
                    ORDER BY `$TBL_ecommerce_payment_type`.`ordering`";
	$request = request($requestStr,$link);
	$return = array();
	while($type = mysql_fetch_object($request)){
		if($type->fields !="") $fields = split(";",$type->fields); else $fields=array();
		$return[$type->id]=array("id"=>$type->id,"name"=>$type->name,"fields"=>$fields,"image_file"=>$type->image_file);
	}
	return $return;
}

function getCurrentInvoices($idClient, $link){
	global $TBL_ecommerce_invoice;
	$request = request("SELECT `id`,`grandtotal`,`payed_amount`,`due_date`,`id_order` FROM `$TBL_ecommerce_invoice` WHERE id_client='$idClient' AND `status`='issued' AND `payed_amount`!=`grandtotal` ORDER BY `due_date`",$link);
	$return = array();
	while($invoice = mysql_fetch_object($request)){
		$to_pay = number_format($invoice->grandtotal - $invoice->payed_amount,2,".","");
		$return[]=array("id"=>$invoice->id,"amount"=>$invoice->grandtotal,"payed_amount"=>$invoice->payed_amount,"to_pay"=>$to_pay,"due_date"=>$invoice->due_date,"id_order"=>$invoice->id_order);
	}
	return $return;
}

function getCurrentOrders($idClient, $link){
	global $TBL_ecommerce_order;
	$request = request("SELECT `id`,`deposit_amount`,`payed_amount`,`order_date`,`id_invoice` FROM `$TBL_ecommerce_order` WHERE id_client='$idClient' AND (`payed_amount`-`deposit_amount`)<0 ORDER BY `order_date`",$link);
	$return = array();
	while($order = mysql_fetch_object($request)){
		$to_pay = number_format($order->deposit_amount - $order->payed_amount,2,".","");
		$return[]=array("id"=>$order->id,"deposit_amount"=>$order->deposit_amount,"payed_amount"=>$order->payed_amount,"to_pay"=>$to_pay,"order_date"=>$order->order_date,"id_invoice"=>$order->id_invoice);
	}
	return $return;
}

function id_format($id){
	$return="$id";
	$count = strlen($return);
	if($count<5) $padding = 5-$count;
	for($i=0;$i<$padding;$i++) $return ="0".$return;
	return $return;
}

function refundInvoice($idInvoice,$link){
	global $TBL_ecommerce_invoice,$TBL_ecommerce_order,$TBL_ecommerce_invoice_item;
	$errors=0;
	$request = request("SELECT * from `$TBL_ecommerce_invoice` WHERE `id`='$idInvoice'",$link);
	if(!mysql_num_rows($request)) {
		return 0;
	}
	request("START TRANSACTION",$link);
	//insert the new refund invoice
	$oldInvoice = mysql_fetch_object($request);
	//Create the refund order
	$request = request("SELECT * FROM `$TBL_ecommerce_order` WHERE `id`='$oldInvoice->id_order'",$link);
	$oldOrder = mysql_fetch_object($request);

	$requestStr = "INSERT INTO `$TBL_ecommerce_order`  ( `id_client` , `mailing_address` , `billing_address` , `xml_address` , `order_date` , `delivery_date` , `reception_date` , `subtotal` , `status`)
			 VALUES('$oldOrder->id_client','".addslashes($oldOrder->mailing_address)."','".addslashes($oldOrder->billing_address)."','".addslashes($oldOrder->xml_address)."',NOW(),NOW(),NOW(),'-$oldOrder->subtotal','invoiced')";
	request($requestStr,$link);
	$idRefundOrder = mysql_insert_id($link);

	request("INSERT INTO `$TBL_ecommerce_invoice` (`id_order`,`id_client`,`billing_address`,`invoice_date`,`due_date`,`id_terms`,`subtotal`,`grandtotal`,`payed_amount`,`status`,`refund`,`id_refunded`)
              VALUES ('$idRefundOrder','$oldInvoice->id_client','".addslashes($oldInvoice->billing_address)."',NOW(),NOW(),$oldInvoice->id_terms,-$oldInvoice->subtotal,-$oldInvoice->grandtotal,0,'created','Y','$oldInvoice->id')",$link);
	if(mysql_errno()){
		$errors++;
	} else {
		$idRefund = mysql_insert_id($link);
	}

	//Update the new order with the invoice number
	request("UPDATE `$TBL_ecommerce_order` SET `id_invoice`='$idRefund' WHERE `id`='$idRefundOrder'",$link);

	if(!$errors){
		//insert the old invoice items refunded
		$request = request("SELECT * FROM `$TBL_ecommerce_invoice_item` WHERE `id_invoice`='$idInvoice'",$link);
		while(!$errors && $item = mysql_fetch_object($request)){
			request("INSERT INTO `$TBL_ecommerce_invoice_item` (`id_invoice`,`id_order`,`reference`,`description`,`details`,`qty`,`uprice`,`id_product`)
					 VALUES('$idRefund','$idRefundOrder','".addslashes($item->reference)."','".htmlentities(addslashes($item->description),ENT_QUOTES,"UTF-8")."','".htmlentities(addslashes($item->details),ENT_QUOTES,"UTF-8")."','-$item->qty','$item->uprice','$item->id_product')",$link);
			if(mysql_errno()) $errors++;
		}
	}
	//Update the original invoice to change its status to refunded and set the refund id
	if(!$errors){
		request("UPDATE `$TBL_ecommerce_invoice` SET `status`='refunded', `id_refund`='$idRefund' WHERE `id`='$idInvoice'",$link);
		if(mysql_errno($link)){
			$errors++;
		}
	}
	if(!$errors) request("COMMIT",$link);
	else{
		request("ROLLBACK",$link);
		$idRefund=0;
	}
	return $idRefund;
}


/**
* Classes
**/

Class Cart {
	var $cartType; //Order or Invoice

	var $LineItems; //Array of items. Each item is a LineItem instance.
	var $linesCount; //Number of items in the cart

	var $clientId;
	var $clientInfos; //Custumer informations (phone, fax, email)

	var $orderNumber; //Use for invoices

	var $GST;
	var $QST;

	//Constructor
	function Cart($cartType){
		$this->cartType = $cartType;
		$this->LineItems = array();
		$this->linesCount = 0;
	}
	//Add an item to cart and returns its Index in the cart table
	function addItem($qty,$code,$description,$details,$price){
		$this->LineItems[$this->linesCount]=new LineItem($qty,$code,$description,$details,$price);
		$this->linesCount++;

		return $this->linesCount-1;
	}

	function delItem($index){
		if(isset($index) && $index<$this->linesCount){
			for($i=$index;$i<$this->linesCount-1;$i++){
				$this->LineItems[$i]=$this->LineItems[$i+1];
			}
			unset($this->LineItems[$this->linesCount-1]);
			$this->linesCount--;
		}
	}
}

Class LineItem{
	var $qty;
	var $code;
	var $description;
	var $details;
	var $price;

	var $total;

	function LineItem($qty,$code,$description,$details,$price){
		$this->qty = $qty;
		$this->code = $code;
		$this->description = $description;
		$this->details = $details;
		$this->price = $price;
		$this->total = $this->price * $this->qty;
	}
}

function showEmails($link){
	global $TBL_ecommerce_emails;
	global $TBL_ecommerce_info_emails;
	global $TBL_gen_languages,$used_language_id;
	global $ECOMMERCE_email_categories;
	$returnStr="";
	foreach($ECOMMERCE_email_categories as $category){
		$request = request(
		"SELECT `$TBL_ecommerce_emails`.`id`,`$TBL_ecommerce_emails`.`id_category`,`$TBL_ecommerce_emails`.`ordering`,`$TBL_ecommerce_emails`.`enabled`,`$TBL_ecommerce_emails`.`deletable`,`$TBL_ecommerce_info_emails`.`title`
        FROM `$TBL_ecommerce_emails`,`$TBL_ecommerce_info_emails`,`$TBL_gen_languages`
        WHERE `$TBL_ecommerce_emails`.`id_category`=".$category["id"]."
        AND `$TBL_gen_languages`.`id`='$used_language_id'
        AND `$TBL_ecommerce_info_emails`.`id_email`=`$TBL_ecommerce_emails`.`id`
        AND `$TBL_ecommerce_info_emails`.`id_language`=`$TBL_gen_languages`.`id`
        ORDER BY `$TBL_ecommerce_emails`.`ordering`",$link);
		$returnStr.="<table class='edittable_text' width='100%'>";
		$returnStr.="<tr>";
		$returnStr.="<td align='right' valign='middle' width='82' bgcolor='#e7eff2'>";
		if($category["nbAllowed"]==-1 || mysql_num_rows($request)<$category["nbAllowed"]){
			$returnStr.="<a href='./mod_email.php?action=add&idCategory=".$category["id"]."'><img src='../images/add.gif' border='0' alt='"._("Ajouter un email type à cette categorie")."'></a>";
		}
		$returnStr.="</td>";
		$returnStr.="<td>";
		$returnStr.="<img src='../images/folder.gif' border='0' alt='"._("Categorie")."'><b>".$category["name"]."</b>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		$returnStr.="</table>";
		$nbResults=mysql_num_rows($request);
		if(!$nbResults){
			$returnStr.="<center><i>"._("Aucun email n'a ete trouve en base de donnees")."</i></center>";
		}
		while($email=mysql_fetch_object($request)){
			$returnStr.="<table class='edittable_text' width='100%'>";
			$returnStr.="<tr>";
			$returnStr.="<td align='left' valign='middle' width='82' bgcolor='#e7eff2'>";
			$returnStr.="<a href='./mod_email.php?action=edit&idEmail=".$email->id."'><img src='../images/edit.gif' border='0' alt='"._("Éditer l'Email Type")."'></a>";
			if($email->deletable=='Y') $returnStr.="&nbsp;<a href='./del_email.php?idEmail=".$email->id."'><img src='../images/del.gif' border='0' alt='"._("Supprimer l'email type")."'></a>";
			if($email->ordering!=1) $returnStr.="<a href='./list_emails.php?action=moveup&idEmail=".$email->id."'><img src='../images/order_up.gif' border='0' alt='"._("Monter")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			if($email->ordering!=$nbResults) $returnStr.="<a href='./list_emails.php?action=movedown&idEmail=".$email->id."'><img src='../images/order_down.gif' border='0' alt='"._("Descendre")."'></a>";
			else $returnStr.="<img src='../images/order_blank.gif' border='0'>";
			$returnStr.="</td>";
			$returnStr.="<td>";
			$returnStr.="&nbsp;&nbsp;&nbsp;&nbsp;";
			$returnStr.=stripslashes($email->title)." <i>(id:".$email->id;
			if($email->enabled=="N") $returnStr.=" <font color='#ff0000'><B>!"._("Désactivé")."!</B></font>";
			$returnStr.=")</i>";
			$returnStr.="</td>";
			$returnStr.="</tr>";
			$returnStr.="</table>";
		}
	}
	return $returnStr;
}

function createRandomPassword() {
    $chars = "abcdefghijkmnpqrstuvwxyz23456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i <= 8) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}

function getCatalogueCatTable($result){
	$catalogueCat=array();
	$updateSubcatsPool = array();
	$lastParent=0;$lastInserted=0;$first=true;
	while($row=mysql_fetch_object($result)){
		if(!$first && $lastParent==$row->id_parent) $catalogueCat[$lastInserted]["last"]=false;
		$catalogueCat[$row->id]=array();
		$catalogueCat[$row->id]["id"]=$row->id;
		$catalogueCat[$row->id]["subcats"]=array();
		if(isset($row->deletable)) $catalogueCat[$row->id]["deletable"]=$row->deletable;
		$catalogueCat[$row->id]["ordering"]=$row->ordering;
		$catalogueCat[$row->id]["name"]=$row->name;
		$catalogueCat[$row->id]["description"]=$row->description;
		$catalogueCat[$row->id]["id_parent"]=$row->id_parent;
		$catalogueCat[$row->id]["contain_products"]=$row->contain_products;
		$catalogueCat[$row->id]["last"]=true;
		if($first || $lastParent!=$row->id_parent) $catalogueCat[$row->id]["first"]=true;
		else $catalogueCat[$row->id]["first"]=false;
		if($row->id_parent!=0) {
			//Declare the child to his parent
			if(isset($catalogueCat[$row->id_parent])) $catalogueCat[$row->id_parent]["subcats"][$row->id]=$row->id;
			else {
				//if the parent have not been processed yet, put the declaration in updaye pool; declaration will be made later on
				$updateSubcatsPool[$row->id]=array();
				$updateSubcatsPool[$row->id]["child"]=$row->id;
				$updateSubcatsPool[$row->id]["parent"]=$row->id_parent;
			}
		}
		$lastInserted=$row->id;
		$lastParent=$row->id_parent;
		$first=false;
		//Stats fields
		$catalogueCat[$row->id]["nbActiveProducts"]=0;
		$catalogueCat[$row->id]["nbInactiveProducts"]=0;
		$catalogueCat[$row->id]["nbTotalProducts"]=0;
	}
	foreach($updateSubcatsPool as $toUpdate){
		$catalogueCat[$toUpdate["parent"]]["subcats"][$toUpdate["child"]]=$toUpdate["child"];
	}
	return $catalogueCat;
}

function isCatalogueChild($table, $idCategory, $idParent){
	if($idParent==$idCategory) return true;
	if(!isset($table[$idParent]["subcats"]) || !count($table[$idParent]["subcats"])) return false;
	foreach($table[$idParent]["subcats"] as $currentCat){
		if(isCatalogueChild($table,$idCategory,$currentCat)) return true;
	}
	return false;
}

function showCatalogueProducts($table,$idParent,$showCategory,$level,$reloadURL,$prohibited,$link,$warnStock=false){
	global $TBL_catalogue_categories;
	global $TBL_catalogue_info_categories;
	global $TBL_catalogue_info_products;
	global $TBL_catalogue_products,$TBL_catalogue_brands;
	global $TBL_gen_languages,$used_language_id;
	global $CATALOG_folder_images;
	$returnStr="";
	foreach($table as $row)
	if($row["id_parent"]==$idParent){
		$returnStr.="<a name='".$row["id"]."'>";
		$returnStr.="<table class='edittable_text' width='100%' rowspan='0' colspan='0'";
		//Decide if we display the category
		//If it's a top level category or the category to display is a child of the current category
		if(!$level || isCatalogueChild($table,$showCategory,$row["id"])) $displayTable = true;
		//If it's a category at at the same level
		elseif(isset($table[$row["id_parent"]]) && isset($table[$row["id_parent"]]["subcats"]) && isset($table[$row["id_parent"]]["subcats"][$showCategory])) $displayTable=true;
		elseif(isset($table[$showCategory]["subcats"]) && isset($table[$showCategory]["subcats"][$row["id"]])) $displayTable=true;
		else $displayTable=false;
		if(!$displayTable) $returnStr.=" style='display:none;'";
		$returnStr.=">";
		$returnStr.="<tr>";
		//$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
		$padding=$level*10;
		$returnStr.="<td style='padding-left:".$padding."px'>";
		if($showCategory==$row["id"]) $returnStr.="<img src='../images/folder_open.gif' border='0' alt=\""._("Catégorie en cours")."\">";
		else $returnStr.="<img src='../images/folder.gif' border='0' alt=\""._("Catégorie")."\">";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_products"]=="Y") $returnStr.="<a href='".$reloadURL.$row["id"]."'>";
		if($showCategory==$row["id"]) $returnStr.="<b>";
		$returnStr.=$row["name"];
		if($showCategory==$row["id"]) $returnStr.="</b>";
		if($reloadURL!="" && $showCategory!=$row["id"] && $row["contain_products"]=="Y") $returnStr.="</a>";
		$returnStr.="</td>";
		$returnStr.="</tr>";
		//DISPLAY THE SUB-CATEGORIES
		if(count($row["subcats"])) {
			$returnStr.="<tr>";
			$returnStr.="<td colspan='2'>";
			$returnStr.=showCatalogueProducts($table,$row["id"],$showCategory,$level+1,$reloadURL,$prohibited,$link);
			$returnStr.="</td>";
			$returnStr.="</tr>";
		}
		//DISPLAY THE PRODUCTS
		if($showCategory==$row["id"]){
			$request = request("SELECT $TBL_catalogue_products.id,$TBL_catalogue_products.ordering,$TBL_catalogue_products.active,$TBL_catalogue_products.ref_store,$TBL_catalogue_products.stock,$TBL_catalogue_products.image_file_icon,$TBL_catalogue_info_products.name,$TBL_catalogue_brands.name brand_name FROM ($TBL_catalogue_products,$TBL_catalogue_info_products,$TBL_gen_languages) LEFT JOIN $TBL_catalogue_brands on ($TBL_catalogue_products.brand=$TBL_catalogue_brands.id) WHERE $TBL_catalogue_products.id_category='$showCategory' and $TBL_catalogue_products.id=$TBL_catalogue_info_products.id_product and $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_catalogue_products.ordering",$link);
			if(!mysql_num_rows($request)){
				$returnStr.="<tr>";
				//$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$returnStr.="<td align='center'>";
				$returnStr.="<i>"._("Aucun produit dans cette catégorie.")."</i>";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
			while($products=mysql_fetch_object($request)){
				$returnStr.="<tr>";
				//$returnStr.="<td align='left' width='102' bgcolor='#e7eff2'>&nbsp;</td>";
				$padding=($level+1)*10;
				$returnStr.="<td style='padding-left:".$padding."px'>";

				if(!$warnStock || $products->stock>0) $JS_action = "javascript:addProduct($products->id);";
				else $JS_action = "javascript:stockAlert($products->id);";

				$returnStr.="- ";
				if($products->id!=$prohibited) $returnStr.="<a href='javascript:void(0);' onclick='$JS_action'>";
				$returnStr.="<img src='../".$CATALOG_folder_images.$products->image_file_icon."' alt='' />";
				$showDesc=$products->name;
				if($products->name=="") $showDesc="<i>"._("Sans Nom")."</i>";
				//Get the activation status of the news
				if($products->active=="N") $returnStr.="<i><font color='red'><b>!"._("Désactivé")."!</b></font></i> ";
				if($products->ref_store!="") $returnStr.="(".$products->ref_store.") ";
				if($products->brand_name!=NULL) $returnStr.="<I><b>".$products->brand_name."</b> </I>- ";
				else $returnStr.="<I><b><font color='#505050'>"._("Sans Marque")."</font></b> </I>- ";
				$returnStr.=$showDesc;
				if($products->id!=$prohibited) $returnStr.="</a>";
				$returnStr.=" (".$products->stock.")";
				$returnStr.="</td>";
				$returnStr.="</tr>";
			}
		}
		$returnStr.="</table>";
	}
	return $returnStr;
}

function updateOrderStatus($idOrder,$link){
	global $TBL_ecommerce_order,$TBL_ecommerce_invoice;
	$request = request("SELECT `deposit_amount`,`payed_amount`,`id_invoice` FROM `$TBL_ecommerce_order` WHERE `id`='$idOrder'",$link);
	$row = mysql_fetch_object($request);
	if($row->deposit_amount>$row->payed_amount){
		request("UPDATE `$TBL_ecommerce_order` SET `status`='stand by' WHERE `id`='$idOrder'",$link);
		if($row->id_invoice!=0){//if the order invoiced, must unpublish the invoice
			request("UPDATE `$TBL_ecommerce_invoice` SET `status`='created' WHERE `id`='$row->id_invoice'",$link);
		}
	} elseif($row->id_invoice!=0) {//check if the order has been invoiced
		request("UPDATE `$TBL_ecommerce_order` SET `status`='invoiced' WHERE `id`='$idOrder'",$link);
	} else {
		request("UPDATE `$TBL_ecommerce_order` SET `status`='ordered' WHERE `id`='$idOrder'",$link);
	}
}

function updateOrderTotal($idOrder,$link){
	global $TBL_ecommerce_order,$TBL_ecommerce_invoice_item;
	//compute the subtotal
	$requestStr="SELECT SUM( `uprice` * `qty` ) as order_subtotal
				 FROM `$TBL_ecommerce_invoice_item`
				 WHERE `id_order` ='$idOrder'
				 GROUP BY id_order";
	$request = request($requestStr,$link);

	if(mysql_num_rows($request)){
		$tmp=mysql_fetch_object($request);
		$subtotal=$tmp->order_subtotal;
	} else $subtotal=0;

	//Update the subtotal field
	request("UPDATE `$TBL_ecommerce_order` SET `subtotal`='".number_format($subtotal,2,".","")."',`deposit_amount`=(`subtotal`*`deposit_requested`/100) WHERE `id`='$idOrder'",$link);
	if(mysql_errno($link)){
		$errors++;
		$errMessage.="- "._("Une erreur s'est produite lors de l'inscription du total de la commande.")."<br>";
	}
}

function updateInvoiceTotal($idInvoice,$idClient,$link){
	global $TBL_ecommerce_invoice,$TBL_ecommerce_invoice_item;
	global $TBL_ecommerce_tax_group,$TBL_ecommerce_tax_authority,$TBL_ecommerce_tax_group_authority,$TBL_ecommerce_customer,$TBL_ecommerce_tax_item;
	global $ECOMMERCE_product_prices_inclue_VAT;
	$errors=0;

	//compute the subtotal
	$requestStr="SELECT SUM( `uprice` * `qty` ) as invoice_subtotal
				 FROM `$TBL_ecommerce_invoice_item`
				 WHERE `id_invoice` ='$idInvoice'
				 GROUP BY id_invoice";
	$request = request($requestStr,$link);

	if(mysql_num_rows($request)){
		$tmp=mysql_fetch_object($request);
		$subtotal=$tmp->invoice_subtotal;
	} else $subtotal=0;

	if(isset($ECOMMERCE_product_prices_inclue_VAT) && $ECOMMERCE_product_prices_inclue_VAT){
		$totalPercentage = getTotalTaxesPercentage($idClient,$link);
		$subtotal = $subtotal / (1+$totalPercentage);
	}

	$grandtotal = $subtotal;
	$subtotalTaxes = $subtotal;

	//APPLY CUSTOMER TAXES
	$request = request("SELECT `$TBL_ecommerce_tax_group`.`method` as groupmethod,
                                                    `$TBL_ecommerce_tax_authority`.*
                        FROM `$TBL_ecommerce_tax_group`,`$TBL_ecommerce_tax_authority`,`$TBL_ecommerce_tax_group_authority`,`$TBL_ecommerce_customer`
                        WHERE `$TBL_ecommerce_customer`.id = '$idClient'
                        AND `$TBL_ecommerce_tax_group`.`id` = `$TBL_ecommerce_customer`.`id_tax_group`
                        AND `$TBL_ecommerce_tax_group_authority`.`id_tax_group`=`$TBL_ecommerce_tax_group`.`id`
                        AND `$TBL_ecommerce_tax_group_authority`.`id_tax_authority`=`$TBL_ecommerce_tax_authority`.`id`
                        ORDER BY $TBL_ecommerce_tax_authority.`ordering`",$link);

	$amount = 0;
	$requestString="";
	request("DELETE FROM `$TBL_ecommerce_tax_item` WHERE `id_invoice`='$idInvoice'",$link);
	if(mysql_errno($link)){
		$errors++;
		$errMessage.="-"._("Une erreur s'est produite lors de la suppression des anciennes taxes.");
	}
	while(!$errors && $taxes = mysql_fetch_object($request)){
		switch($taxes->method){
			case "percentage": $amount = number_format(($subtotalTaxes * $taxes->value)/100,2,".","");break;
			case "fixed": $amount = number_format($taxes->value,2,".","");break;
		}
		if($taxes->groupmethod="cumulate") $subtotalTaxes+=$amount;
		$requestString="INSERT INTO `$TBL_ecommerce_tax_item` (id_invoice,id_tax_authority,amount) VALUES ('$idInvoice','$taxes->id','$amount'); ";
		$grandtotal+=$amount;
		request($requestString,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="-"._("Une erreur s'est produite lors de l'ajout des taxes.");
		}
	}

	//Update the subtotal field
	request("UPDATE `$TBL_ecommerce_invoice` SET `subtotal`='".number_format($subtotal,2,".","")."',`grandtotal`='".number_format($grandtotal,2,".","")."' WHERE `id`='$idInvoice'",$link);
	if(mysql_errno($link)){
		$errors++;
		$errMessage.="- "._("Une erreur s'est produite lors de l'inscription du total de la facture.")."<br>";
	}
}

function updateInvoiceStatus($idInvoice, $link){
	global $TBL_ecommerce_invoice;
	$request = request("SELECT `status`,`grandtotal`,`payed_amount` FROM `$TBL_ecommerce_invoice` WHERE `id`='$idInvoice'",$link);
	$invoice = mysql_fetch_object($request);
	if($invoice->status=="issued" || $invoice->status=="payed"){
		$diff = $invoice->payed_amount-$invoice->grandtotal;
		if(abs($diff)<0.1){
			request("UPDATE `$TBL_ecommerce_invoice` SET `status`='payed' WHERE `id`='$idInvoice'",$link);
		} else {
			request("UPDATE `$TBL_ecommerce_invoice` SET `status`='issued' WHERE `id`='$idInvoice'",$link);
		}
	}
}

function getTotalTaxesPercentage($idClient,$link){
	global $TBL_ecommerce_tax_group,$TBL_ecommerce_tax_authority,$TBL_ecommerce_tax_group_authority,$TBL_ecommerce_customer;

	$request = request("SELECT `$TBL_ecommerce_tax_group`.`method` as groupmethod,`$TBL_ecommerce_tax_authority`.*
	                    FROM `$TBL_ecommerce_tax_group`,`$TBL_ecommerce_tax_authority`,`$TBL_ecommerce_tax_group_authority`,`$TBL_ecommerce_customer`
	                    WHERE `$TBL_ecommerce_customer`.id = '$idClient'
	                    AND `$TBL_ecommerce_tax_group`.`id` = `$TBL_ecommerce_customer`.`id_tax_group`
	                    AND `$TBL_ecommerce_tax_group_authority`.`id_tax_group`=`$TBL_ecommerce_tax_group`.`id`
	                    AND `$TBL_ecommerce_tax_group_authority`.`id_tax_authority`=`$TBL_ecommerce_tax_authority`.`id`
	                    ORDER BY $TBL_ecommerce_tax_authority.`ordering`",$link);
	$amount = 0;
	$total_percentage = 0;
	$percentage=0;
	while($taxes = mysql_fetch_object($request)){
		if($taxes->method=="percentage"){
			$percentage= $taxes->value/100;
		}
		if($taxes->groupmethod="cumulate") $total_percentage+= $percentage + ($percentage * $total_percentage);
		else $total_percentage+=$percentage;
	}

	return $total_percentage;
}
?>
