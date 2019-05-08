<?php
if($action=="issue"){
	if(!$errors){
		$request = request("SELECT * FROM `$TBL_ecommerce_invoice` WHERE `id`='$idInvoice'",$link);
		if(mysql_num_rows($request)){
			$invoice = mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.="- "._("Le numéro de facture n'a pas été spécifié ou est erroné")."<br>";
		}
	}
	if(!$errors){
		if($invoice->status!="created"){
			$errors++;
			$errMessage.="- "._("Cette facture a déjà été émise.")."<br>";
		}
	}
	$transaction_started=false;
	if(!$errors){
		//We start a database transaction
		request("START TRANSACTION",$link);
		$transaction_started = true;
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'initialisation de la transaction. Merci de contacter le support technique")." <a href='mailto:support@cibaxion.com'>CIBAXION</a>."."<br>";
		}
	}
	//update the invoice status
	if(!$errors){
		if(abs($invoice->grandtotal-$invoice->payed_amount)>0.1) $status = 'issued'; else $status='payed';
		request("UPDATE `$TBL_ecommerce_invoice` SET `status`='$status' WHERE id='$idInvoice'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'émission de la facture.")."<br>";
		}
	}
	//update the customer balance
	if(!$errors){
		//Calculate the balance to add to customer's balance
		$balance = number_format($invoice->grandtotal,2,".","");
		request("UPDATE `$TBL_ecommerce_customer` SET `balance`=`balance`+'$balance' WHERE id='$invoice->id_client'",$link);
		//echo "UPDATE `$TBL_ecommerce_customer` SET `balance`=`balance`+'$balance' WHERE id='$invoice->id_client'";
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour du solde du client.")."<br>";
		}
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
		$message = "- "._("La facture a ete émise correctement.")."<br>";
	} else {
		$action="issue_confirm";
	}
}
?>
<?php
if($action=="un_issue"){
	if(!$errors){
		$request = request("SELECT *,`grandtotal`-`payed_amount` AS to_pay_amount FROM `$TBL_ecommerce_invoice` WHERE `id`='$idInvoice'",$link);
		if(mysql_num_rows($request)){
			$invoice = mysql_fetch_object($request);
		} else {
			$errors++;
			$errMessage.="- "._("Le numéro de facture n'a pas été spécifié ou est erroné")."<br>";
		}
	}
	if(!$errors){
		if($invoice->status=="created"){
			$errors++;
			$errMessage.="- "._("Cette facture n'a pas été émise.")."<br>";
		}
	}
	$transaction_started=false;
	if(!$errors){
		//We start a database transaction
		request("START TRANSACTION",$link);
		$transaction_started = true;
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'initialisation de la transaction. Merci de contacter le support technique")." <a href='mailto:support@cibaxion.com'>CIBAXION</a>."."<br>";
		}
	}
	//update the order status if the invoice was voided
	/*if(!$errors && $invoice->status=="voided"){
	$request = request("SELECT `id_invoice`,`status` FROM `$TBL_ecommerce_order` WHERE `id`='$invoice->id_order'",$link);
	$order = mysql_fetch_object($request);
	//Error if the order was re-invoiced
	if($order->id_invoice != $idInvoice){
	$errors++;
	$errMessage.="- "._("La commande correspondante à cette facture a été refacturée sous la facture No #").$order->id_invoice.". "._("Cette facture est annulée et n'est plus ré-éditable.")."<br>";
	}
	if(!$errors){
	request("UPDATE `$TBL_ecommerce_order` SET `status`='invoiced' WHERE id='$invoice->id_order'",$link);
	if(mysql_errno($link)){
	$errors++;
	$errMessage.="- "._("Une erreur s'est produite lors du changement de l'état de la commande.")."<br>";
	}
	}
	}*/
	//Delete payment allocations which may have been allocated to invoice only
	if(!$errors){
		$unallocated_amount=0;
		$request = request("SELECT `id_payment`,`amount` FROM `$TBL_ecommerce_payment_allocation` WHERE `id_invoice`='$idInvoice' AND `id_order`='0'",$link);
		while(!$errors && $allocation=mysql_fetch_object($request)){
			request("UPDATE `$TBL_ecommerce_payment` SET `allocated_amount`=`allocated_amount`-'$allocation->amount', `to_allocate_amount`=`to_allocate_amount`+'$allocation->amount' WHERE `id`='$allocation->id_payment'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour des paiements de la facture.")."<br>";
			} else{
				$unallocated_amount+= $allocation->amount;
			}
		}
		//Delete the allocations
		if(!$errors){
			request("DELETE FROM `$TBL_ecommerce_payment_allocation` WHERE `id_invoice`='$idInvoice' AND `id_order`='0'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la suppression des allocations de paiements à la facture.")."<br>";
			}
		}
		//Update the allocations which have been also allocated to the order
		if(!$errors){
			request("UPDATE `$TBL_ecommerce_payment_allocation` SET `id_invoice`='0' WHERE `id_invoice`='$idInvoice' AND `id_order`<>'0'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour des allocations de paiements relatifs à cette facture.")."<br>";
			}
		}
	}
	//update the invoice status
	if(!$errors){
		request("UPDATE `$TBL_ecommerce_invoice` SET `status`='created', `payed_amount`=`payed_amount`-'".number_format($unallocated_amount,2,".","")."' WHERE id='$idInvoice'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors du changement de l'état de la facture.")."<br>";
		}
	}
	//update the customer balance
	if(!$errors){
		request("UPDATE `$TBL_ecommerce_customer` SET `balance`=`balance`-'$invoice->to_pay_amount'-'".number_format($unallocated_amount,2,".","")."' WHERE id='$invoice->id_client'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour du solde du client.")."<br>";
		}
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
		$message = "- "._("La facture a ete modifiée correctement.")."<br>";
	} else {
		$action="un_issue_confirm";
	}
}
?>