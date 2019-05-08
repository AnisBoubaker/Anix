<?
if($action=="insert"){
	$transaction_started=false;
	if(!$idClient){
		$errors++;
		$errMessage.="- "._("Le client n'a pas ete specifie.")."<br>";
	}
	if(!$errors){
		$request = request("SELECT * from `$TBL_ecommerce_customer` WHERE `id`='$idClient'",$link);
		if(mysql_num_rows($request)) $client=mysql_fetch_object($request);
		else {
			$errors++;
			$errMessage.="- "._("Le client spécifiée est invalide.")."<br>";
		}
	}
	if(!$errors && !isset($_POST["payment_type"])){
		$errors++;
		$errMessage.="- "._("Vous n'avez pas spécifié le type de paiement.")."<br>";
	}
	if(!$errors){
		$payment_types=getPaymentTypes($link);
		$found = false;
		if($_POST["payment_type"]==-1) $found=true; //coupon
		foreach($payment_types as $payment_type){
			if($payment_type["id"]==$_POST["payment_type"]) $found=true;
		}
		if(!$found){
			$errors++;
			$errMessage.="- "._("Le type de paiement specifie est invalide.")."<br>";
		}
	}
	if(!$errors){
		if(!isset($_POST["amount"]) || $_POST["amount"]==""){
			$errors++;
			$errMessage="- "._("Merci de specifier le montant du paiement.")."<br>";
		}
	}
	if(!$errors){
		if(!isset($_POST["reception_date"]) || $_POST["reception_date"]==""){
			$errors++;
			$errMessage="- "._("Merci de specifier la date de réception du paiement.")."<br>";
		}
	}
	//coupon specific check
	if(!$errors && $_POST["payment_type"]==-1 && $_POST["amount"]>=0){
		if($_POST["coupon_code"]===0){
			$errors++;
			$errMessage="- "._("Veuillez choisir le coupon à utiliser pour ce paiement.")."<br>";
		} else {
			//coupn chosen. Verify if the coupon is enough
			$coupon = new EcommerceCoupon($_POST["coupon_code"]);
			if($coupon->id==0 || !$coupon->valid){
				$errors++;
				$errMessage="- "._("Le coupon transmis est invalide, déjà utilisé ou expiré.")."<br>";
			} elseif($coupon->getValue()<$_POST["amount"]) {
				$errors++;
				$errMessage="- "._("Le coupon est insuffisant pour effectuer ce paiement.")."<br>";
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
	//Insert payment in database
	if(!$errors){
		request("
            INSERT INTO `$TBL_ecommerce_payment` (`id_client`,`id_payment_type`,`reception_date`,`amount`,`allocated_amount`,`to_allocate_amount`,`field1`,`field2`,`field3`,`field4`)
            VALUES (
                '$client->id',
                '".$_POST["payment_type"]."',
                '".$_POST["reception_date"]."',
                '".$_POST["amount"]."',
                '0.00',
                '".$_POST["amount"]."',
                '".htmlentities($_POST["field1"],ENT_QUOTES,"UTF-8")."',
                '".htmlentities($_POST["field2"],ENT_QUOTES,"UTF-8")."',
                '".htmlentities($_POST["field3"],ENT_QUOTES,"UTF-8")."',
                '".htmlentities($_POST["field4"],ENT_QUOTES,"UTF-8")."')",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'ajout du paiement.")."<br>";
		} else {
			$idPayment = mysql_insert_id($link);
		}
	}
	if(!$errors && $_POST["payment_type"]==-1 && $_POST["amount"]>=0){ //payed using a coupon
		if($coupon->useCoupon($client->id,$_POST["amount"],0,$idPayment,0,0)===FALSE){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'utilisation du coupon.")."<br>";
		} else {
			//put the coupon code into field1
			request("UPDATE `$TBL_ecommerce_payment` SET `field1`='$coupon->code' WHERE `id`='$idPayment'",$link);
		}
	}
	if(!$errors && $_POST["payment_type"]==-1 && $_POST["amount"]<0){ //create a coupon
		$coupon = new EcommerceCoupon();
		$coupon->setIdClient($client->id);
		$coupon->setType('fixed');
		$coupon->setValue(-$_POST["amount"]);
		$coupon->setUsage('unlimited');
		$noError=$coupon->create();
		//$newCoupon=createCoupon($client->id,'fixed',-$_POST["amount"],0,'','unlimited',0,$link);

		if(!$noError){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la création du nouveau coupon.")."<br>";
		} else {
			$message.= "- "._("Le coupon Num.")." ".$coupon->code." "."a été créé automatiquement pour ce client."."<br/>";
			request("UPDATE `$TBL_ecommerce_payment` SET `field1`='$coupon->code' WHERE `id`='$idPayment'",$link);
		}
	}
	//Update customer balance
	if(!$errors){
		request("UPDATE `$TBL_ecommerce_customer` SET `balance`=(`balance`-".number_format($_POST["amount"],2,".","").") WHERE `id`='$client->id'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour du solde du compte client.")."<br>";
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
		$message = "- "._("Le paiement a ete ajoute correctement.")."<br>";
		if(isset($_POST["allocate"])) $action="allocate_payment";
		else $action="edit";
	}
}
?>
<?
if($action=="allocate_payment"){
	$transaction_started=false;
	$request = request("SELECT * from `$TBL_ecommerce_payment` WHERE id='$idPayment'",$link);
	if(!mysql_num_rows($request)){
		$errors++;
		$errMessage.="- "._("Le paiement spécifié est invalide.")."<br>";
	} else {
		$payment = mysql_fetch_object($request);
		$idClient = $payment->id_client;
	}
	if(!$errors){
		$invoices = getCurrentInvoices($idClient,$link);
		$orders = getCurrentOrders($idClient,$link);
		if((count($invoices) + count($orders))==0){
			$errors++;
			$errMessage.="- "._("Aucune facture ou commande en attente de paiement.")."<br>";
		}
	}
	$allocated_amount = 0;
	if(!$errors){
		//We start a database transaction
		request("START TRANSACTION",$link);
		$transaction_started = true;
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'initialisation de la transaction. Merci de contacter le support technique")." <a href='mailto:support@cibaxion.com'>CIBAXION</a>."."<br>";
		}
	}
	//Invoices processing
	if(!$errors){
		foreach($invoices as $invoice) if(!$errors && isset($_POST["inv_".$invoice["id"]]) && ($_POST["inv_".$invoice["id"]]!="max"  ||  $_POST["inv_".$invoice["id"]]!="0.00")){
			//Check how much we want to allocate (to handle the max value)
			if($_POST["inv_".$invoice["id"]]=="max") $amount_to_allocate = $payment->to_allocate_amount;
			else $amount_to_allocate=$_POST["inv_".$invoice["id"]];
			//see if we already have an allocation from this payment to the current invoice
			$request = request("SELECT * FROM `$TBL_ecommerce_payment_allocation` WHERE `id_payment`='$idPayment' AND `id_invoice`='".$invoice["id"]."'",$link);
			if(mysql_num_rows($request)){
				//we update by adding
				request("UPDATE `$TBL_ecommerce_payment_allocation` set `amount`=`amount`+'".number_format($amount_to_allocate,2,".","")."' WHERE `id_payment`='$idPayment' AND `id_invoice`='".$invoice["id"]."'",$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.="- "._("Une erreur s'est produite lors de l'ajout de l'allocation de paiement pour la facture")." ".$invoice["id"]."<br>";
				}
			} else {
				//we insert new allocation
				request("INSERT INTO `$TBL_ecommerce_payment_allocation` (`id_payment`,`id_invoice`,`id_order`,`amount`) VALUES ('$idPayment', '".$invoice["id"]."', '".$invoice["id_order"]."','".number_format($amount_to_allocate,2,".","")."')",$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.="- "._("Une erreur s'est produite lors de l'ajout de l'allocation de paiement pour la facture")." ".$invoice["id"]."<br>";
				}
			}
			//update the invoice
			$requestString ="UPDATE `$TBL_ecommerce_invoice` SET `payed_amount`=(`payed_amount`+'".number_format($amount_to_allocate,2,".","")."')";
			$requestString.=" WHERE `id`='".$invoice["id"]."'";
			request($requestString,$link);

			$requestString ="UPDATE `$TBL_ecommerce_order` SET `payed_amount`=(`payed_amount`+'".number_format($amount_to_allocate,2,".","")."')";
			$requestString.=" WHERE `id_invoice`='".$invoice["id"]."'";
			request($requestString,$link);

			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour de la facture")." ".$invoice["id"]."<br>";
			}
			updateInvoiceStatus($invoice["id"],$link);
			//Count the allocated amount
			$allocated_amount+=$amount_to_allocate;
		}
	}
	//Orders processing
	if(!$errors){
		foreach($orders as $order) if(!$errors && isset($_POST["ord_".$order["id"]]) && ($_POST["ord_".$order["id"]]!="max" || $_POST["ord_".$order["id"]]!="0.00")){
			//Check how much we want to allocate (to handle the max value)
			if($_POST["ord_".$order["id"]]=="max") $amount_to_allocate = $payment->to_allocate_amount;
			else $amount_to_allocate=$_POST["ord_".$order["id"]];

			//see if we already have an allocation from this payment to the current order
			$request = request("SELECT * FROM `$TBL_ecommerce_payment_allocation` WHERE `id_payment`='$idPayment' AND `id_order`='".$order["id"]."' AND `id_invoice`='0'",$link);
			if(mysql_num_rows($request)){
				//we update by adding
				request("UPDATE `$TBL_ecommerce_payment_allocation` set `amount`=`amount`+'".number_format($amount_to_allocate,2,".","")."' WHERE `id_payment`='$idPayment' AND `id_order`='".$order["id"]."' AND `id_invoice`='0'",$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.="- "._("Une erreur s'est produite lors de l'ajout de l'allocation de paiement pour la commande")." ".$order["id"]."<br>";
				}
			} elseif($amount_to_allocate!=0) {
				//we insert new allocation
				request("INSERT INTO `$TBL_ecommerce_payment_allocation` (`id_payment`,`id_invoice`,`id_order`,`amount`) VALUES ('$idPayment', '".$order["id_invoice"]."', '".$order["id"]."','".number_format($amount_to_allocate,2,".","")."')",$link);
				if(mysql_errno($link)){
					$errors++;
					$errMessage.="- "._("Une erreur s'est produite lors de l'ajout de l'allocation de paiement pour la commande")." ".$order["id"]."<br>";
				}
			}
			//update the order
			//if($order["to_pay"]>$amount_to_allocate) $status="stand by"; else $status="ordered";
			request("UPDATE `$TBL_ecommerce_order` SET `payed_amount`=(`payed_amount`+'".number_format($amount_to_allocate,2,".","")."') WHERE `id`='".$order["id"]."'",$link);

			request("UPDATE `$TBL_ecommerce_invoice` SET `payed_amount`=(`payed_amount`+'".number_format($amount_to_allocate,2,".","")."') WHERE `id_order`='".$order["id"]."'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour de la commande")." ".$order["id"]."<br>";
			}

			updateOrderStatus($order["id"],$link);
			//Count the allocated amount
			$allocated_amount+=$amount_to_allocate;
		}
	}
	//update the payment
	if(!$errors){
		request("UPDATE `$TBL_ecommerce_payment` set  `allocated_amount`=(`allocated_amount`+'".number_format($allocated_amount,2,".","")."'), `to_allocate_amount`=(`amount`-`allocated_amount`) WHERE `id`='$idPayment'",$link);
	}
	//finish
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
		$message = "- "._("Le paiement a été alloué correctement.")."<br>";
		$action="edit";
	}
}
?>