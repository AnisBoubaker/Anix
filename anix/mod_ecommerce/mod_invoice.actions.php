<?
if($action=="insert"){
	$transaction_started=false;
	$subtotal=0;
	if(!$idOrder){
		$errors++;
		$errMessage.="- "._("La commande n'a pas ete specifiee.")."<br>";
	}
	if(!$errors){
		$request = request("SELECT * from `$TBL_ecommerce_order` WHERE `id`='$idOrder' AND `status`='ordered'",$link);
		if(mysql_num_rows($request)) $order=mysql_fetch_object($request);
		else {
			$errors++;
			$errMessage.="- "._("La commande spécifiée est invalide.")."<br>";
		}
	}
	if($_POST["billing_address"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez specifier l'adresse de facturation.")."<br>";
	}
	if($_POST["invoice_date"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez specifier la date de facturation.")."<br>";
	}
	if($_POST["nb_new_lines"]!=0){
		$newLines = $_POST["nb_new_lines"];
		for($i=0;$i<$newLines;$i++) if(!$errors && isset($_POST["newrow".$i."_qty"])){
			if($_POST["newrow".$i."_qty"]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier la quantitee.")."<br>";
			}
			if($_POST["newrow".$i."_reference"]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier le code du produit.")."<br>";
			}
			if($_POST["newrow".$i."_description"]==""){
				$errors++;
				$errMessage.="- "._("Le produit doit avoir une description")."<br>";
			}
			if($_POST["newrow".$i."_uprice"]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier le prix du produit.")."<br>";
			}
		}
	}
	if(!$errors){
		//We start a database transaction
		request("START TRANSACTION",$link);
		$transaction_started = true;
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'initialisation de la transaction. Merci de contacter le support technique")." <a href='mailto:support@cibaxion.com'>CIBAXION</a>."."<br>";
		}
	}
	if(!$errors){
		$idClient = $order->id_client;
		$request=request("SELECT `$TBL_ecommerce_terms`.`name`,`$TBL_ecommerce_terms`.`delay`
                                      FROM `$TBL_ecommerce_terms`
                                      WHERE `$TBL_ecommerce_terms`.`id`='".$_POST["id_terms"]."'",$link);
		$terms = mysql_fetch_object($request);
		//Insert the invoice
		request("INSERT INTO `$TBL_ecommerce_invoice` (`id_order`,`id_client`,`billing_address`,`invoice_date`,`due_date`,`id_terms`,`payed_amount`)
              VALUES ('$order->id','$order->id_client','".$_POST["billing_address"]."','".$_POST["invoice_date"]."',ADDDATE('".$_POST["invoice_date"]."', $terms->delay),'".$_POST["id_terms"]."','$order->payed_amount')",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'insertion de la facture.")."<br>";
		} else {
			//Insertion was OK => get the inserted ID
			$idInvoice = mysql_insert_id($link);
		}
	}
	if(!$errors){
		//Insert the invoice items
		$request = request("SELECT id FROM `$TBL_ecommerce_invoice_item` WHERE `id_order`='$order->id' ORDER BY `id`",$link);
		while(($item=mysql_fetch_object($request)) && !$errors){
			$requestStr = "UPDATE `$TBL_ecommerce_invoice_item` SET ";
			$requestStr.= "`id_invoice`='$idInvoice', ";
			$requestStr.= "`id_order`='$order->id', ";
			$requestStr.= "`reference`='".addslashes($_POST["reference_".$item->id])."', ";
			$requestStr.= "`description`='".htmlentities(addslashes($_POST["description_".$item->id]),ENT_QUOTES,"UTF-8")."', ";
			$requestStr.= "`details`='".htmlentities(addslashes($_POST["details_".$item->id]),ENT_QUOTES,"UTF-8")."', ";
			$requestStr.= "`qty`='".addslashes($_POST["qty_".$item->id])."', ";
			$requestStr.= "`uprice`='".addslashes($_POST["uprice_".$item->id])."', ";
			$requestStr.= "`id_product`='".addslashes($_POST["product_".$item->id])."' ";
			$requestStr.= "WHERE `id`='$item->id'";
			request($requestStr,$link);

			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la copie des lignes de la commande.")."<br>";
			} else {
				$subtotal+= $_POST["qty_".$item->id] * $_POST["uprice_".$item->id];
			}
		}
	}
	if(!$errors && $_POST["nb_new_lines"]!=0){
		$newLines = $_POST["nb_new_lines"];
		for($i=0;$i<$newLines;$i++) if(!$errors && isset($_POST["newrow".$i."_qty"])){
			request("INSERT INTO `$TBL_ecommerce_invoice_item` (`id_invoice`,`id_order`,`reference`,`description`,`details`,`qty`,`uprice`,`id_product`)
	              VALUES ('$idInvoice',
	              		  '$order->id',
	                      '".addslashes($_POST["newrow".$i."_reference"])."',
	                      '".htmlentities($_POST["newrow".$i."_description"],ENT_QUOTES,"UTF-8")."',
	                      '".htmlentities($_POST["newrow".$i."_details"],ENT_QUOTES,"UTF-8")."',
	                      '".addslashes($_POST["newrow".$i."_qty"])."',
	                      '".addslashes($_POST["newrow".$i."_uprice"])."',
	                      '".addslashes($_POST["newrow".$i."_product"])."')"
			,$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de l'insertion des nouvelles lignes de facture.")."<br>";
			} else {
				$subtotal+=$_POST["newrow".$i."_qty"]*$_POST["newrow".$i."_uprice"];
			}
		}
	}
	if(!$errors){
		request("UPDATE `$TBL_ecommerce_order` SET `id_invoice`='$idInvoice' WHERE `id`='$order->id'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour de la commande.")."<br>";
		}
	}
	if(!$errors){
		//update payment allocations
		request("UPDATE `$TBL_ecommerce_payment_allocation` SET `id_invoice`='$idInvoice' WHERE `id_order`='$order->id'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la récupération des paeiements faits à la commande.")."<br>";
		}
	}
	if(!$errors){
		updateOrderTotal($idOrder,$link);

		updateOrderStatus($idOrder,$link);

		updateInvoiceTotal($idInvoice,$idClient,$link);

		updateInvoiceStatus($idInvoice,$link);
	}

	if(!$errors){
		//Finished, commit the changes
		request("COMMIT",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la validation des donnees inserees.")."<br>";
		}
	}
	if($errors && $transaction_started){
		//Finished with errors => rollback
		request("ROLLBACK",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'annulation des donnees inserees.")."<br>";
		}
	}
	if(!$errors){
		$message = "- "._("La facture a ete creee correctement.")."<br>";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$transaction_started=false;
	$subtotal = 0;
	if(!$idInvoice){
		$errors++;
		$errMessage.="- "._("La facture n'a pas ete specifiee.")."<br>";
	}
	if(!$errors){
		//Check the order id
		$request = request("SELECT * from `$TBL_ecommerce_invoice` WHERE `id`='$idInvoice'",$link);
		if(!mysql_num_rows($request)){
			$errors++;
			$errMessage.="- "._("La facture specifiee est invalide.")."<br>";
		} else {
			$invoice=mysql_fetch_object($request);
			$idClient = $invoice->id_client;
			$old_balance = $invoice->grandtotal - $invoice->payed_amount;
		}
	}
	if($_POST["invoice_date"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez specifier la date de livraison.")."<br>";
	}
	if(isset($_POST["add_item"])){
		//If we add a new line too...
		if($_POST["qty_new"]==""){
			$errors++;
			$errMessage.="- "._("Merci de specifier la quantitee")."<br>";
		}
		if($_POST["reference_new"]==""){
			$errors++;
			$errMessage.="- "._("Merci de specifier le code du produit.")."<br>";
		}
		if($_POST["description_new"]==""){
			$errors++;
			$errMessage.="- "._("Le produit doit avoir une description.")."<br>";
		}
		if($_POST["uprice_new"]==""){
			$errors++;
			$errMessage.="- "._("Merci de specifier le prix du produit.")."<br>";
		}
	}
	if($_POST["nb_new_lines"]!=0){
		$newLines = $_POST["nb_new_lines"];
		for($i=0;$i<$newLines;$i++) if(!$errors && isset($_POST["newrow".$i."_qty"])){
			if($_POST["newrow".$i."_qty"]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier la quantitee.")."<br>";
			}
			if($_POST["newrow".$i."_reference"]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier le code du produit.")."<br>";
			}
			if($_POST["newrow".$i."_description"]==""){
				$errors++;
				$errMessage.="- "._("Le produit doit avoir une description")."<br>";
			}
			if($_POST["newrow".$i."_uprice"]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier le prix du produit.")."<br>";
			}
		}
	}
	if(!$errors){
		//Check for each item
		$items = request("SELECT `id` FROM `$TBL_ecommerce_invoice_item` WHERE `id_invoice`='$idInvoice' ORDER BY `id`",$link);
		while(!$errors && $item = mysql_fetch_object($items)){
			if($_POST["qty_".$item->id]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier la quantitee")."<br>";
			}
			if($_POST["reference_".$item->id]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier le code du produit.")."<br>";
			}
			if($_POST["description_".$item->id]==""){
				$errors++;
				$errMessage.="- "._("Le produit doit avoir une description.")."<br>";
			}
			if($_POST["uprice_".$item->id]==""){
				$errors++;
				$errMessage.="- "._("Merci de specifier le prix du produit.")."<br>";
			}
		}
	}
	if(!$errors){
		//We start a database transaction
		request("START TRANSACTION",$link);
		$transaction_started = true;
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'initialisation de la transaction. Merci de contacter le support technique")." <a href='mailto:support@cibaxion.com'>CIBAXION</a>."."<br>";
		}
	}
	if(!$errors){
		//Get the client terms
		$request=request("SELECT `$TBL_ecommerce_terms`.`name`,`$TBL_ecommerce_terms`.`delay`
                                      FROM `$TBL_ecommerce_terms`
                                      WHERE `$TBL_ecommerce_terms`.`id`='".$_POST["id_terms"]."'",$link);
		$terms = mysql_fetch_object($request);
		//Update the order entry first
		$requestString ="UPDATE `$TBL_ecommerce_invoice` SET ";
		$requestString.="`billing_address`='".addslashes($_POST["billing_address"])."',";
		$requestString.="`invoice_date`='".addslashes($_POST["invoice_date"])."',";
		$requestString.="`due_date`=ADDDATE('".addslashes($_POST["invoice_date"])."',$terms->delay), ";
		$requestString.="`id_terms`='".addslashes($_POST["id_terms"])."' ";
		$requestString.="WHERE `id`='$idInvoice'";
		//echo $requestString;
		request($requestString,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise a jour de la facture.")."<br>";
		}
	}
	if(!$errors && isset($_POST["delete_line"]) && $_POST["delete_line"]!=0){
		$request = request("SELECT id,id_product,unstocked_qty from `$TBL_ecommerce_invoice_item` WHERE `id`='".$_POST["delete_line"]."' AND `id_invoice`='$idInvoice'",$link);
		$line_item = mysql_fetch_object($request);

		request("DELETE FROM `$TBL_ecommerce_invoice_item` WHERE `id`='".$_POST["delete_line"]."' AND id_invoice='$idInvoice'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la suppression de la ligne.")."<br>";
		}

		if(!$errors && $line_item->id_product!=0 && $line_item->unstocked_qty!=0){
			request("UPDATE `$TBL_catalogue_products` SET `stock`=`stock`+'$line_item->unstocked_qty' WHERE `id`='$line_item->id_product'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour du stock.")."<br>";
			} else {
				$ANIX_messages->addMessage(_("Le stock du produit supprimé de la facture a été mis à jour."));
			}
		}

	}
	if(!$errors){
		//Update the actual items
		$items = request("SELECT `id` FROM `$TBL_ecommerce_invoice_item` WHERE `id_invoice`='$idInvoice' ORDER BY `id`",$link);
		while(!$errors && $item = mysql_fetch_object($items)){
			$requestString ="UPDATE `$TBL_ecommerce_invoice_item` SET ";
			$requestString.="`reference`='".addslashes($_POST["reference_".$item->id])."',";
			$requestString.="`description`='".htmlentities(addslashes($_POST["description_".$item->id]),ENT_QUOTES,"UTF-8")."',";
			$requestString.="`details`='".htmlentities(addslashes($_POST["details_".$item->id]),ENT_QUOTES,"UTF-8")."',";
			$requestString.="`qty`='".addslashes($_POST["qty_".$item->id])."',";
			$requestString.="`uprice`='".addslashes($_POST["uprice_".$item->id])."',";
			$requestString.="`id_product`='".addslashes($_POST["product_".$item->id])."' ";
			$requestString.="WHERE `id`='".$item->id."'";
			$subtotal+=$_POST["qty_".$item->id]*$_POST["uprice_".$item->id];
			request($requestString,$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la mise a jour des produits.")."<br>";
			}
		}
	}

	if(!$errors && $_POST["nb_new_lines"]!=0){
		$newLines = $_POST["nb_new_lines"];
		for($i=0;$i<$newLines;$i++) if(!$errors && isset($_POST["newrow".$i."_qty"])){
			request("INSERT INTO `$TBL_ecommerce_invoice_item` (`id_invoice`,`id_order`,`reference`,`description`,`details`,`qty`,`uprice`,`id_product`)
	              VALUES ('$idInvoice',
	              		  '$invoice->id_order',
	                      '".addslashes($_POST["newrow".$i."_reference"])."',
	                      '".htmlentities($_POST["newrow".$i."_description"],ENT_QUOTES,"UTF-8")."',
	                      '".htmlentities($_POST["newrow".$i."_details"],ENT_QUOTES,"UTF-8")."',
	                      '".addslashes($_POST["newrow".$i."_qty"])."',
	                      '".addslashes($_POST["newrow".$i."_uprice"])."',
	                      '".addslashes($_POST["newrow".$i."_product"])."')"
			,$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de l'insertion de la nouvelle entree.")."<br>";
			} else {
				$subtotal+=$_POST["newrow".$i."_qty"] * $_POST["newrow".$i."_uprice"];
			}
		}
	}
	if(!$errors){
		updateOrderTotal($invoice->id_order,$link);

		updateOrderStatus($invoice->id_order,$link);

		updateInvoiceTotal($idInvoice,$idClient,$link);

		updateInvoiceStatus($idInvoice,$link);
	}

	/*
	if(!$errors){
	//update customer balance
	$request = request("SELECT `grandtotal`,`payed_amount` FROM `$TBL_ecommerce_invoice` WHERE `id`='$idInvoice'",$link);
	$tmp_grand_total = mysql_fetch_object($request);
	$new_balance= $tmp_grand_total->grandtotal - $tmp_grand_total->payed_amount;
	$balance = number_format($new_balance - $old_balance,2,".","");
	request("UPDATE `$TBL_ecommerce_customer` SET `balance`=`balance`+'$balance' WHERE `id`='$order->id_client'",$link);
	if(mysql_errno($link)){
	$errors++;
	$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour de la balance du client.")."<br>";
	}
	}
	*/
	if(!$errors){
		//Finished, commit the changes
		request("COMMIT",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la validation des donnees mises a jour.<br>")."<br>";
		}
	}
	if($errors && $transaction_started){
		//Finished with errors => rollback
		request("ROLLBACK",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'annulation des donnees mises a jour.<br>")."<br>";
		}
	}
	if(!$errors){
		$message = "- "._("La facture a ete mise a jour correctement.<br>")."<br>";
		$action="edit";
	}
}
?>
<?php
if($action=="refund"){
	$transaction_started=false;
	$subtotal = 0;
	if(!$idInvoice){
		$errors++;
		$errMessage.="- "._("La facture n'a pas ete specifiée.")."<br/>";
	}
	if(!$errors){
		//Check the order id
		$request = request("SELECT * from `$TBL_ecommerce_invoice` WHERE `id`='$idInvoice'",$link);
		if(!mysql_num_rows($request)){
			$errors++;
			$errMessage.="- "._("La facture specifiee est invalide.")."<br/>";
		} else {
			$invoice=mysql_fetch_object($request);
			$idClient = $invoice->id_client;
		}
		$idRefund = refundInvoice($idInvoice,$link);
		if($idRefund==0){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la création de la facture de remboursement.")."<br />";
		}
		$idInvoice = $idRefund;
		$action="edit";
	}
}
?>
<?
if($action=="deleteLine"){
	$transaction_started=false;
	if(isset($_POST["idItem"])){
		$idItem=$_POST["idItem"];
	} else $idItem=0;
	if(!$idInvoice){
		$errors++;
		$errMessage.="- "._("La facture n'a pas ete specifiee.")."<br>";
	}
	if(!$errors){
		$request=request("SELECT `id_client` FROM `$TBL_ecommerce_invoice` where `id`='$idInvoice'",$link);
		if(mysql_num_rows($request)){
			$result = mysql_fetch_object($request);
			$idClient = $result->id_client;
		} else {
			$errors++;
			$errMessage.="-"._("La facture specifiee est invalide.");
		}
	}
	if(!$idItem){
		$errors++;
		$errMessage.="- "._("La ligne a supprimer n'a pas ete specifiee.")."<br>";
	}
	if(!$errors){
		//We start a database transaction
		request("START TRANSACTION",$link);
		$transaction_started = true;
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'initialisation de la transaction. Merci de contacter le support technique")." <a href='mailto:support@cibaxion.com'>CIBAXION</a>."."<br>";
		}
	}
	if(!$errors){
		request("DELETE FROM `$TBL_ecommerce_invoice_item` WHERE `id`='$idItem' AND id_invoice='$idInvoice'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la suppression de la ligne.")."<br>";
		}
	}
	//Subtotal update
	if(!$errors){
		$result = request("SELECT `qty`,`uprice` FROM `$TBL_ecommerce_invoice_item` WHERE `id_invoice`='$idInvoice' ORDER BY `id`",$link);
		$tmpTotal = 0;
		while($line= mysql_fetch_object($result)){
			$tmpTotal+=$line->qty * $line->uprice;
		}
		request("UPDATE `$TBL_ecommerce_invoice` SET subtotal='".number_format($tmpTotal,2,".","")."' WHERE id='$idInvoice'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'inscription du total de la facture.")."<br>";
		}
	}
	if(!$errors){
		//Get the customer taxes
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
				case "percentage": $amount = number_format(($tmpTotal * $taxes->value)/100,2);break;
				case "fixed": $amount = number_format($taxes->value,2);break;
			}
			if($taxes->groupmethod="cumulate") $tmpTotal+=$amount;
			$requestString="INSERT INTO `$TBL_ecommerce_tax_item` (id_invoice,id_tax_authority,amount) VALUES ('$idInvoice','$taxes->id','$amount'); ";
			request($requestString,$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="-"._("Une erreur s'est produite lors de l'ajout des taxes.");
			}
		}
	}
	if(!$errors){
		//Finished, commit the changes
		request("COMMIT",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la validation des donnees mises a jour.")."<br>";
		}
	}
	if($errors && $transaction_started){
		//Finished with errors => rollback
		request("ROLLBACK",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'annulation des donnees mises a jour.")."<br>";
		}
	}
	if(!$errors){
		$message = "- "._("La commande a ete mise a jour correctement.")."<br>";
		$action="edit";
	}
	if(!$errors){
		$message = "- "._("La ligne a ete supprimee correctement.")."<br>";
		$action="edit";
	}
	$action="edit";
}
?>