<?
if($action=="insert"){
	$transaction_started=false;
	$subtotal=0;
	if(!$idClient){ //NEW CLIENT
		$countryList = getCountriesList();
		//check the form
		if($_POST["newclient_lastname"]==""){
			$errors++;
			$errMessage.="- "._("Veuillez entrer le nom du client.")."<br>";
		}
		if($_POST["newclient_firstname"]==""){
			$errors++;
			$errMessage.="- "._("Veuillez entrer le prénom du client.")."<br>";
		}
		if($_POST["newclient_email"]==""){
			$errors++;
			$errMessage.="- "._("Veuillez entrer le courriel du client.")."<br>";
		}
		if($_POST["newclient_login"]==""){
			$errors++;
			$errMessage.="- "._("Veuillez entrer le login du client.")."<br>";
		}
		if($_POST["newclient_num"]==""){
			$errors++;
			$errMessage.="- "._("Veuillez entrer le numéro de la rue du client.")."<br>";
		}
		if($_POST["newclient_street1"]==""){
			$errors++;
			$errMessage.="- "._("Veuillez entrer le nom de la rue du client.")."<br>";
		}
		if($_POST["newclient_city"]==""){
			$errors++;
			$errMessage.="- "._("Veuillez entrer la ville du client.")."<br>";
		}
		if($_POST["newclient_zip"]==""){
			$errors++;
			$errMessage.="- "._("Veuillez entrer le code postal du client.")."<br>";
		}
		if(!loginPassValid($_POST["newclient_login"])) {
			$errors++;
			$errMessage.="- "._("Le login du client comporte des caractères invalides. Seules les lettres et les chiffres sont acceptés.")."<br>";
		}
		if(strlen($_POST["newclient_login"])<$ECOMMERCE_min_login_legth || strlen($_POST["newclient_login"])>$ECOMMERCE_max_login_legth){
			$errors++;
			$errMessage.="- "._("Le login du client doit comporter entre $ECOMMERCE_min_login_legth et $ECOMMERCE_max_login_legth caractères.")."<br>";
		}
		if(!$errors){
			//Check if the username exists
			$request=request("SELECT `id` FROM `$TBL_ecommerce_customer` WHERE `login`='".$_POST["newclient_login"]."'",$link);
			if(mysql_num_rows($request)){
				$errors++;
				$errMessage.="- "._("Le login choisi existe déjà.")."<br>";
			}
			//Check if the email address exists
			$request=request("SELECT `id` FROM `$TBL_ecommerce_customer` WHERE `email`='".$_POST["newclient_email"]."'",$link);
			if(mysql_num_rows($request)){
				$errors++;
				$errMessage.="- "._("Un compte avec la même adresse de courriel existe déjà.")."<br>";
			}
		}
		//Insert the client
		if(!$errors){
			$newPassword = createRandomPassword();
			$cryptedPass = crypt($newPassword,substr($_POST["newclient_login"],0,2));
			//Insert the user in the database
			$requestString ="INSERT INTO `$TBL_ecommerce_customer` (`greating`,`firstname`,`lastname`,`company`,`phone`,`email`,`id_terms`,`id_tax_group`,`language`,`login`,`pass`,`state`)";
			$requestString.=" values ('".htmlentities($_POST["newclient_greating"],ENT_QUOTES,"UTF-8")."',
									'".htmlentities($_POST["newclient_firstname"],ENT_QUOTES,"UTF-8")."',
	                                '".htmlentities($_POST["newclient_lastname"],ENT_QUOTES,"UTF-8")."',
	                                '".htmlentities($_POST["newclient_company"],ENT_QUOTES,"UTF-8")."',
	                                '".htmlentities($_POST["newclient_phone"],ENT_QUOTES,"UTF-8")."',
	                                '".htmlentities($_POST["newclient_email"],ENT_QUOTES,"UTF-8")."',
	                                '".$_POST["newclient_terms"]."',
	                                '".$_POST["newclient_tax_group"]."',
	                                '".$_POST["newclient_language"]."',
	                                '".$_POST["newclient_login"]."',
	                                '$cryptedPass',
	                                'activated')";
			request($requestString,$link);
			//If insertion was OK, we rtrieve the id of the inserted category, else error...
			if(!mysql_errno($link)) {
				$idClient=mysql_insert_id($link);
			} else {
				$errMessage.="- "._("Une erreur s'est produire lors de l'ajout du nouveau client. Vérifiez que le login n'existe pas déjà")."<br>";

				$errors++;
			}
		}
		//Insert the address
		if(!$errors){
			$requestString ="INSERT INTO `$TBL_ecommerce_address` (`num`,`street1`,`street2`,`building`,`stairs`,`floor`,`code`,`city`,`province`,`country`,`zip`,`country_code`)";
			$requestString.=" value ('".htmlentities($_POST["newclient_num"],ENT_QUOTES,"UTF-8")."',
	                               '".htmlentities($_POST["newclient_street1"],ENT_QUOTES,"UTF-8")."',
	                               '".htmlentities($_POST["newclient_street2"],ENT_QUOTES,"UTF-8")."',
	                               '".htmlentities($_POST["newclient_building"],ENT_QUOTES,"UTF-8")."',
	                               '".htmlentities($_POST["newclient_stairs"],ENT_QUOTES,"UTF-8")."',
	                               '".htmlentities($_POST["newclient_floor"],ENT_QUOTES,"UTF-8")."',
	                               '".htmlentities($_POST["newclient_code"],ENT_QUOTES,"UTF-8")."',
	                               '".htmlentities($_POST["newclient_city"],ENT_QUOTES,"UTF-8")."',
	                               '".htmlentities($_POST["newclient_province"],ENT_QUOTES,"UTF-8")."',
	                               '".$countryList[$_POST["newclient_country"]]."',
	                               '".htmlentities($_POST["newclient_zip"],ENT_QUOTES,"UTF-8")."',
	      						   '".$_POST["newclient_country"]."')";
			request($requestString,$link);
			//If insertion was OK, we rtrieve the id of the inserted category, else error...
			if(!mysql_errno($link)) {
				$idAddress=mysql_insert_id($link);
			} else {
				$errMessage.="- "._("Une erreur s'est produire lors de la sauvegarde de l'adresse.")."<br>";
				$errors++;
			}
		}
		//Update the addess in the customer table
		if(!$errors){
			$requestString ="UPDATE $TBL_ecommerce_customer set `id_address_mailing`='$idAddress', `id_address_billing`='$idAddress' WHERE `id`='$idClient'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errMessage.="- "._("Une erreur s'est produire lors de l'affectation de l'adresses au client.")."<br>";
				$errors++;
			}
		}
		if(!$errors){
			$ANIX_messages->addMessage(_("Le nouveau client a bien été ajouté sous la référence:").$idClient);
			$addressStr="";
			if($_POST["newclient_company"]!="") $addressStr.= $_POST["newclient_company"]."\n";
			$addressStr.= $_POST["newclient_firstname"]." ".$_POST["newclient_lastname"]."\n";
			$addressStr.= $_POST["newclient_num"]." ".$_POST["newclient_street1"]."\n";
	    	if($_POST["newclient_street2"]!="") $addressStr.= $_POST["newclient_street2"]."\n";
	    	$extraAddress="";
	    	if($_POST["newclient_building"]!="") $extraAddress.="Bat.:".$_POST["newclient_building"]." ";
	    	if($_POST["newclient_stairs"]!="") $extraAddress.="Esc.:".$_POST["newclient_stairs"]." ";
	    	if($_POST["newclient_floor"]!="") $extraAddress.="Étage:".$_POST["newclient_floor"]." ";
	    	if($_POST["newclient_code"]!="") $extraAddress.="Code:".$_POST["newclient_code"]." ";
	    	if($extraAddress!="") echo $extraAddress."\n";
	    	$addressStr.=$_POST["newclient_city"]." ".$_POST["newclient_province"]."\n";
	    	$addressStr.=$_POST["newclient_zip"]." ".$countryList[$_POST["newclient_country"]];
	    	$_POST["mailing_address"]=$addressStr;
	    	$_POST["billing_address"]=$addressStr;

	    	//Send a confirmation email to the client
	    	if(isset($_POST["newclient_send_login"])){
				$fields = array();
				$fields["%%GREETINGS%%"]=$_POST["newclient_greating"];
				$fields["%%FIRST_NAME%%"]=$_POST["newclient_firstname"];
				$fields["%%LAST_NAME%%"]=$_POST["newclient_lastname"];
				$fields["%%LOGIN%%"]=$_POST["newclient_login"];
				$fields["%%PASSWORD%%"]=$newPassword;
				$mail = new AnixMailer($ECOMMERCE_email_ids["newaccount_credentials"],1,$fields, $idClient);
				$mail->AddAddress($_POST["newclient_email"],$_POST["newclient_firstname"]." ".$_POST["newclient_lastname"]);
				if(!$mail->Send()){
					$ANIX_messages->addWarning(_("Une erreur s'est produite lors de l'envoie de l'email avec les codes d'accès").": ".$mail->ErrorInfo);
				} else {
					$ANIX_messages->addMessage(_("Un courriel a été envoyé au client avec ses codes d'accès"));
				}
			}
		}
	}
	if(!$errors && $_POST["mailing_address"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez l'adresse de livraison.")."<br>";
	}
	if($_POST["order_date"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez specifier la date de la commande.")."<br>";
	}
	if($_POST["delivery_date"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez specifier la date de livraison.")."<br>";
	}
	if($_POST["delivery_date"]<$_POST["order_date"]){
		$errors++;
		$errMessage.="- "._("La date de livraison ne peut etre anterieure a la date de commande.")."<br>";
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
		//Insert the order
		request("INSERT INTO `$TBL_ecommerce_order` (`id_client`,`mailing_address`,`billing_address`,`order_date`,`delivery_date`,`deposit_requested`,`status`)
              VALUES ('$idClient','".$_POST["mailing_address"]."','".$_POST["billing_address"]."','".$_POST["order_date"]."','".$_POST["delivery_date"]."','".$_POST["deposit_requested"]."','ordered')",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de l'insertion de la commande.")."<br>";
		} else {
			//Insertion was OK => get the inserted ID
			$idOrder = mysql_insert_id($link);
		}
	}
	$subtotal =0;
	if(!$errors && $_POST["nb_new_lines"]!=0){
		$newLines = $_POST["nb_new_lines"];
		for($i=0;$i<$newLines;$i++) if(!$errors && isset($_POST["newrow".$i."_qty"])){
			request("INSERT INTO `$TBL_ecommerce_invoice_item` (`id_order`,`reference`,`description`,`details`,`qty`,`uprice`,`id_product`)
	              VALUES ('$idOrder',
	                      '".addslashes($_POST["newrow".$i."_reference"])."',
	                      '".htmlentities($_POST["newrow".$i."_description"],ENT_QUOTES,"UTF-8")."',
	                      '".htmlentities($_POST["newrow".$i."_details"],ENT_QUOTES,"UTF-8")."',
	                      '".addslashes($_POST["newrow".$i."_qty"])."',
	                      '".addslashes($_POST["newrow".$i."_uprice"])."',
	                      '".addslashes($_POST["newrow".$i."_product"])."')"
			,$link);
			$subtotal+=$_POST["newrow".$i."_uprice"]*$_POST["newrow".$i."_product"];
			if(mysql_errno($link)){
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de l'insertion de la nouvelle entree.")."<br>";
			}
		}
	}
	if(!$errors){ //UPDATE THE ORDER WITH THE NEW SUBTOTAL
		//request("UPDATE `$TBL_ecommerce_order` SET `subtotal`='".number_format($subtotal,2,".","")."' WHERE `id`='$idOrder'",$link);
		updateOrderTotal($idOrder,$link);
		updateOrderStatus($idOrder,$link);
	}
	//No change to customer balance on order
	/*if(!$errors && isset($_POST["add_item"]) && $_POST["deposit_requested"]!=0){
	$request = request("SELECT `deposit_amount` FROM `$TBL_ecommerce_order` WHERE `id`='$idOrder'",$link);
	$deposit = mysql_fetch_object($request);
	request("UPDATE `$TBL_ecommerce_customer` SET `balance`=`balance`+'$deposit->deposit_amount'",$link);
	if(mysql_errno($link)){
	$errors++;
	$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour du solde du client.")."<br>";
	}
	}*/
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
		$message = "- "._("La commande a ete creee correctement.")."<br>";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$transaction_started=false;
	$subtotal = 0;
	if(!$idOrder){
		$errors++;
		$errMessage.="- "._("La commande n'a pas ete specifiee.")."<br>";
	}
	if(!$errors){
		//Check the order id
		$request = request("SELECT * from `$TBL_ecommerce_order` WHERE `id`='$idOrder'",$link);
		if(!mysql_num_rows($request)){
			$errors++;
			$errMessage.="- "._("La commande specifiee est invalide.")."<br>";
		} else {
			$order=mysql_fetch_object($request);
			$idClient = $order->id_client;
			$old_deposit_amount = $order->deposit_amount;
		}
	}
	if($_POST["mailing_address"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez l'adresse de livraison.")."<br>";
	}
	if($_POST["order_date"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez specifier la date de la commande.")."<br>";
	}
	if($_POST["delivery_date"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez specifier la date de livraison.")."<br>";
	}
	if($_POST["delivery_date"]<$_POST["order_date"]){
		$errors++;
		$errMessage.="- "._("La date de livraison ne peut etre anterieure a la date de commande.")."<br>";
	}
	if($_POST["shipping_date"]!="" && $_POST["shipping_date"]<$_POST["order_date"]){
		$errors++;
		$errMessage.="- "._("La date d'expédition ne peut être antérieure à la date de commande.")."<br>";
	}
	if($_POST["tracking"]!="" && $_POST["shipping_date"]==""){
		$errors++;
		$errMessage.="- "._("Vous n'avez pas précisé la date d'expédition.")."<br>";
	}
	if($_POST["shipping_date"]!="" && $_POST["id_transporter"]==0){
		$errors++;
		$errMessage.="- "._("Veuillez préciser le transporteur.")."<br>";
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
		$items = request("SELECT `id` FROM `$TBL_ecommerce_invoice_item` WHERE `id_order`='$idOrder'",$link);
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
		//Update the order entry first
		$requestString ="UPDATE `$TBL_ecommerce_order` SET ";
		$requestString.="`mailing_address`='".addslashes($_POST["mailing_address"])."',";
		$requestString.="`billing_address`='".addslashes($_POST["billing_address"])."',";
		$requestString.="`order_date`='".addslashes($_POST["order_date"])."',";
		$requestString.="`delivery_date`='".addslashes($_POST["delivery_date"])."',";
		$requestString.="`deposit_requested`='".addslashes($_POST["deposit_requested"])."',";
		$requestString.="`id_transporter`='".addslashes($_POST["id_transporter"])."',";
		if($_POST["tracking"]!="" && $_POST["shipping_date"]=="")	$requestString.="`shipping_date`=NOW(),";
		else $requestString.="`shipping_date`='".$_POST["shipping_date"]."',";
		$requestString.="`tracking`='".addslashes($_POST["tracking"])."' ";
		$requestString.="WHERE `id`='$idOrder'";
		request($requestString,$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise a jour de la commande.")."<br>";
		}
	}
	//DELETE LINE
	if(!$errors && isset($_POST["delete_line"]) && $_POST["delete_line"]!=0){
		$request = request("SELECT id,id_product,unstocked_qty from `$TBL_ecommerce_invoice_item` WHERE `id`='".$_POST["delete_line"]."' AND `id_order`='$idOrder'",$link);
		$line_item = mysql_fetch_object($request);

		request("DELETE FROM `$TBL_ecommerce_invoice_item` WHERE `id`='".$_POST["delete_line"]."' AND id_order='$idOrder'",$link);
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
				$ANIX_messages->addMessage(_("Le stock du produit supprimé de la commande a été mis à jour."));
			}
		}
	}
	if(!$errors){
		//Update the actual items
		$items = request("SELECT `id` FROM `$TBL_ecommerce_invoice_item` WHERE `id_order`='$idOrder'",$link);
		while(!$errors && $item = mysql_fetch_object($items)){
			$requestString ="UPDATE `$TBL_ecommerce_invoice_item` SET ";
			$requestString.="`reference`='".addslashes($_POST["reference_".$item->id])."',";
			$requestString.="`description`='".htmlentities($_POST["description_".$item->id],ENT_QUOTES,"UTF-8")."',";
			$requestString.="`details`='".htmlentities($_POST["details_".$item->id],ENT_QUOTES,"UTF-8")."',";
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
			request("INSERT INTO `$TBL_ecommerce_invoice_item` (`id_order`,`id_invoice`,`reference`,`description`,`details`,`qty`,`uprice`,`id_product`)
	              VALUES ('$idOrder',
	              		  '$order->id_invoice',
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
		//Update the subtotal field
		updateOrderTotal($idOrder,$link);

		//Define order status
		updateOrderStatus($idOrder,$link);

		//Update the invoice total if set
		if($order->id_invoice!=0) updateInvoiceTotal($order->id_invoice,$idClient,$link);

		//Update the invoice status
		if($order->id_invoice!=0) updateInvoiceStatus($order->id_invoice,$link);
	}

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
	if(!$errors){//send email notifications if needed
		$request = request("SELECT * FROM `$TBL_ecommerce_customer` WHERE `id`='$order->id_client'",$link);
		if(mysql_num_rows($request)){
			$client = mysql_fetch_object($request);
			if($_POST["delivery_date"]<$order->delivery_date && isset($_POST["notify_delivery_change"])){
				$fields = array();
				$fields["%%GREETINGS%%"]=unhtmlentities($client->greating);
				$fields["%%FIRST_NAME%%"]=unhtmlentities($client->firstname);
				$fields["%%LAST_NAME%%"]=unhtmlentities($client->lastname);
				$fields["%%ID_ORDER%%"]=$order->id;
				$fields["%%ORDER_OLD_DELIVERY_DATE%%"]=$order->delivery_date;
				$fields["%%ORDER_DELIVERY_DATE%%"]=$_POST["delivery_date"];
				$mail = new AnixMailer($ECOMMERCE_email_ids["early_delivery"],1,$fields, $client->id, $order->id);
				$mail->AddAddress($client->email,unhtmlentities($client->firstname)." ".unhtmlentities($client->lastname));
				if(!$mail->Send()){
					$errMessage.="- "._("Une erreur s'est produite lors de l'envoie de l'email").": ".$mail->ErrorInfo."<br />";
				} else {
					$message.= "- "._("Email de notification envoyé")."<br />";
				}
			}
			if($_POST["delivery_date"]>$order->delivery_date && isset($_POST["notify_delivery_change"])){
				$fields = array();
				$fields["%%GREETINGS%%"]=unhtmlentities($client->greating);
				$fields["%%FIRST_NAME%%"]=unhtmlentities($client->firstname);
				$fields["%%LAST_NAME%%"]=unhtmlentities($client->lastname);
				$fields["%%ID_ORDER%%"]=$order->id;
				$fields["%%ORDER_OLD_DELIVERY_DATE%%"]=$order->delivery_date;
				$fields["%%ORDER_DELIVERY_DATE%%"]=$_POST["delivery_date"];
				$mail = new AnixMailer($ECOMMERCE_email_ids["late_delivery"],1,$fields, $client->id, $order->id);
				$mail->AddAddress($client->email,unhtmlentities($client->firstname)." ".unhtmlentities($client->lastname));
				if(!$mail->Send()){
					$errMessage.="- "._("Une erreur s'est produite lors de l'envoie de l'email").": ".$mail->ErrorInfo."<br />";
				} else {
					$message.= "- "._("Email de notification envoyé")."<br />";
				}
			}
			if(isset($_POST["notify_comment_request"])){
				$fields = array();
				$fields["%%GREETINGS%%"]=unhtmlentities($client->greating);
				$fields["%%FIRST_NAME%%"]=unhtmlentities($client->firstname);
				$fields["%%LAST_NAME%%"]=unhtmlentities($client->lastname);
				$fields["%%ID_ORDER%%"]=$order->id;
				$mail = new AnixMailer($ECOMMERCE_email_ids["comment_request"],1,$fields, $client->id, $order->id);
				$mail->AddAddress($client->email,unhtmlentities($client->firstname)." ".unhtmlentities($client->lastname));
				if(!$mail->Send()){
					$errMessage.="- "._("Une erreur s'est produite lors de l'envoie de l'email de demande de commentaire").": ".$mail->ErrorInfo."<br />";
				} else {
					$message.= "- "._("Email de demande de coommentaire envoyé")."<br />";
				}
			}
			if($order->shipping_date=="0000-00-00" && $order->shipping_date!=$_POST["shipping_date"] && isset($_POST["notify_shipping_change"])){
				//get the shipping method & tracking URL
				//get the transporter
				//$result = request("SELECT * FROM `$TBL_ecommerce_shipping_transporters` WHERE `id`='".addslashes($_POST["id_transporter"])."'",$link);
				$result = request("SELECT * FROM `$TBL_ecommerce_shipping_transporters`,`$TBL_ecommerce_info_transporter` WHERE `$TBL_ecommerce_shipping_transporters`.`id`='".addslashes($_POST["id_transporter"])."' AND `$TBL_ecommerce_info_transporter`.`id_transporter`=`$TBL_ecommerce_shipping_transporters`.`id` AND `$TBL_ecommerce_info_transporter`.`id_language`='$used_language_id'",$link);
				$transporter_name = "N/A";
				$transporter_tracking = "N/A";
				if(mysql_num_rows($result)){
					$transporter = mysql_fetch_object($result);
					$transporter_name = $transporter->name;
					if($transporter->tracking_url!="" && addslashes($_POST["tracking"])!="") {
						$transporter_tracking = str_replace("%%TRACKINGID%%",addslashes($_POST["tracking"]),$transporter->tracking_url);
					}
				}
				$fields = array();
				$fields["%%GREETINGS%%"]=unhtmlentities($client->greating);
				$fields["%%FIRST_NAME%%"]=unhtmlentities($client->firstname);
				$fields["%%LAST_NAME%%"]=unhtmlentities($client->lastname);
				$fields["%%ID_ORDER%%"]=$order->id;
				$fields["%%SHIPPING_DATE%%"]=$_POST["shipping_date"];
				$fields["%%TRANSPORTER%%"]=$transporter_name;
				$fields["%%TRACKING_URL%%"]=$transporter_tracking;
				$mail = new AnixMailer($ECOMMERCE_email_ids["order_shipped"],1,$fields, $client->id, $order->id);
				$mail->AddAddress($client->email,unhtmlentities($client->firstname)." ".unhtmlentities($client->lastname));
				if(!$mail->Send()){
					$errMessage.="- "._("Une erreur s'est produite lors de l'envoie de l'email").": ".$mail->ErrorInfo."<br />";
				} else {
					$message.= "- "._("Email de notification envoyé")."<br />";
				}
			}
		}
	}
	if(!$errors){
		$message.= "- "._("La commande a ete mise a jour correctement.<br>")."<br>";
		$action="edit";
	}
}
?>
<?
if($action=="delete"){
	$transaction_started=false;

	echo "Je passe!";

	if(isset($_POST["idItem"])){
		$idItem=$_POST["idItem"];
	} else $idItem=0;
	if(!$idOrder){
		$errors++;
		$errMessage.="- "._("La commande n'a pas ete specifiee.")."<br>";
	}
	if(!$idItem){
		$errors++;
		$errMessage.="- "._("La ligne a supprimer n'a pas ete specifiee.")."<br>";
	}
	if(!$errors){
		//Check the order id
		$request = request("SELECT * from `$TBL_ecommerce_order` WHERE `id`='$idOrder'",$link);
		if(!mysql_num_rows($request)){
			$errors++;
			$errMessage.="- "._("La commande specifiee est invalide.")."<br>";
		} else {
			$order=mysql_fetch_object($request);
			$idClient = $order->id_client;
			$old_deposit_amount = $order->deposit_amount;
		}
	}
	if(!$errors){
		//Check the line item
		$request = request("SELECT id,id_product,unstocked_qty from `$TBL_ecommerce_invoice_item` WHERE `id`='$idItem' AND `id_order`='$idOrder'",$link);
		if(!mysql_num_rows($request)){
			$errors++;
			$errMessage.="- "._("La ligne à supprimer qui a été specifiée est invalide.")."<br>";
		} else {
			$line_item=mysql_fetch_object($request);
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
		request("DELETE FROM `$TBL_ecommerce_invoice_item` WHERE `id`='$idItem' AND `id_order`='$idOrder'",$link);
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la suppression de la ligne.")."<br>";
		}
	}
	//Update the stock
	echo "$line_item->id_product!=0 && $line_item->unstocked_qty!=0 resultat.:".($line_item->id_product!=0 && $line_item->unstocked_qty!=0);

	if(!$errors && $line_item->id_product!=0 && $line_item->unstocked_qty!=0){
		request("UPDATE `$TBL_catalogue_products` SET `stock`=`stock`+'$line_item->unstocked_qty' WHERE `id`='$line_item->id_product'");
		if(mysql_errno($link)){
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour du stock.")."<br>";
		} else {
			$ANIX_messages->addMessage(_("Le stock du produit supprimé a été mis à jour."));
		}
	}
	//Update the deposit & account information
	if(!$errors){

		updateOrderTotal($order->id,$link);

		updateOrderStatus($order->id,$link);

		//Update the invoice total if set
		if($order->id_invoice!=0) updateInvoiceTotal($order->id_invoice,$order->id_client,$link);

		//Update the invoice status
		if($order->id_invoice!=0) updateInvoiceStatus($order->id_invoice,$link);
	}
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
		$message = "- "._("La ligne a ete supprimee correctement.")."<br>";
		$action="edit";
	}
	$action="edit";
}
?>