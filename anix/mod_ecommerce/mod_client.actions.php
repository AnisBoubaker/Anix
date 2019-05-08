<?
if($action=="insert"){
	if($_POST["firstname"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez renseigner le prénom du client.")."<br>";
	}
	if($_POST["lastname"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez renseigner le nom du client.")."<br>";
	}
	if($_POST["phone"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez renseigner le numéro de téléphone du client.")."<br>";
	}
	if($_POST["language"]==0){
		$errors++;
		$errMessage.="- "._("Veuillez renseigner la langue de correspondance du client.")."<br>";
	}
	if(!loginPassValid($_POST["login"]) || ($_POST["password1"]!="" && !loginPassValid($_POST["password1"]))) {
		$errors++;
		$errMessage.="- "._("Votre login et/ou votre mot de passe comporte des caractères invalides. Seules les lettres et les chiffres sont acceptés.")."<br>";
	}
	if(strlen($_POST["login"])<$ECOMMERCE_min_login_legth || strlen($_POST["login"])>$ECOMMERCE_max_login_legth){
		$errors++;
		$errMessage.="- "._("Le login du client doit comporter entre $ECOMMERCE_min_login_legth et $ECOMMERCE_max_login_legth caractères.")."<br>";
	}
	/*if(strlen($_POST["password1"])<$ECOMMERCE_min_password_legth || strlen($_POST["password1"])>$ECOMMERCE_max_password_legth){
	$errors++;
	$errMessage.="- "._("Le mot de passe du client doit comporter entre $ECOMMERCE_min_password_legth et $ECOMMERCE_max_password_legth caractères.")."<br>";
	}*/
	if(strcmp($_POST["password1"],$_POST["password2"])){
		$errors++;
		$errMessage.="- "._("Veuillez entrer deux fois le même mot de passe.")."<br>";
	}
	if(!strcmp($_POST["password1"],$_POST["login"])){
		$errors++;
		$errMessage.="- "._("Le mot de passe et le login doivent être différents.")."<br>";
	}
	if(!$errors && $anix_demo_mode){
		$errors++;
		$errMessage.="- "._("Cette action n'est pas permise en mode de démo.")."<br>";
	}
	if(!$errors){
		$cryptedPass = crypt($_POST["password1"],substr($_POST["login"],0,2));
		//Insert the user in the database
		$requestString ="INSERT INTO `$TBL_ecommerce_customer` (`greating`,`firstname`,`lastname`,`company`,`phone`,`cell`,`fax`,`email`,`id_user_group`,`id_terms`,`credit_margin`,`id_tax_group`,`language`,`login`,`pass`,`state`)";
		$requestString.=" values ('".htmlentities($_POST["greating"],ENT_QUOTES,"UTF-8")."',
								'".htmlentities($_POST["firstname"],ENT_QUOTES,"UTF-8")."',
                                '".htmlentities($_POST["lastname"],ENT_QUOTES,"UTF-8")."',
                                '".htmlentities($_POST["company"],ENT_QUOTES,"UTF-8")."',
                                '".htmlentities($_POST["phone"],ENT_QUOTES,"UTF-8")."',
                                '".htmlentities($_POST["cell"],ENT_QUOTES,"UTF-8")."',
                                '".htmlentities($_POST["fax"],ENT_QUOTES,"UTF-8")."',
                                '".htmlentities($_POST["email"],ENT_QUOTES,"UTF-8")."',
                                '".$_POST["price_group"]."',
                                '".$_POST["terms"]."',
                                '".$_POST["credit_margin"]."',
                                '".$_POST["tax_group"]."',
                                '".$_POST["language"]."',
                                '".$_POST["login"]."',
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
	//Everything is OK => Insert the address...
	if(!$errors){
		$requestString ="INSERT INTO `$TBL_ecommerce_address` (`num`,`street1`,`street2`,`building`,`stairs`,`floor`,`code`,`city`,`province`,`country`,`zip`,`country_code`)";
		$requestString.=" values ('".htmlentities($_POST["mailing_num"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["mailing_street1"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["mailing_street2"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["mailing_building"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["mailing_stairs"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["mailing_floor"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["mailing_code"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["mailing_city"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["mailing_province"],ENT_QUOTES,"UTF-8")."',
                               '".$countryList[$_POST["mailing_country"]]."',
                               '".htmlentities($_POST["mailing_zip"],ENT_QUOTES,"UTF-8")."',
      						   '".$_POST["mailing_country"]."')";

		request($requestString,$link);
		//If insertion was OK, we rtrieve the id of the inserted category, else error...
		if(!mysql_errno($link)) {
			$idMailing=mysql_insert_id($link);
		} else {
			$errMessage.="- "._("Une erreur s'est produire lors de la sauvegarde de l'adresse de livraison.")."<br>";
			$errors++;
		}
	}
	if(!$errors && isset($_POST["same_address"])){$idBilling=$idMailing;}
	elseif(!$errors){
		$requestString ="INSERT INTO `$TBL_ecommerce_address` (`num`,`street1`,`street2`,`building`,`stairs`,`floor`,`code`,`city`,`province`,`country`,`zip`,`country_code`)";
		$requestString.=" values ('".htmlentities($_POST["billing_num"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["billing_street1"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["billing_street2"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["billing_building"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["billing_stairs"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["billing_floor"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["billing_code"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["billing_city"],ENT_QUOTES,"UTF-8")."',
                               '".htmlentities($_POST["billing_province"],ENT_QUOTES,"UTF-8")."',
                               '".$countryList[$_POST["billing_country"]]."',
                               '".htmlentities($_POST["billing_zip"],ENT_QUOTES,"UTF-8")."',
                               '".$_POST["billing_country"]."')";
		request($requestString,$link);
		//If insertion was OK, we rtrieve the id of the inserted category, else error...
		if(!mysql_errno($link)) {
			$idBilling=mysql_insert_id($link);
		} else {
			$errMessage.="- "._("Une erreur s'est produire lors de la sauvegarde de l'adresse de facturation.")."<br>";
			$errors++;
		}
	}
	//Update the customers table with the address Id's
	if(!$errors){
		$requestString ="UPDATE $TBL_ecommerce_customer set `id_address_mailing`='$idMailing', `id_address_billing`='$idBilling' WHERE `id`=$idClient";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errMessage.="- "._("Une erreur s'est produire lors de l'affectation des adresses au client.")."<br>";
			$errors++;
		}
	}
	if(!$errors && isset($_POST["send_login"])){
		$fields = array();
		$fields["%%GREETINGS%%"]=$_POST["greating"];
		$fields["%%FIRST_NAME%%"]=$_POST["firstname"];
		$fields["%%LAST_NAME%%"]=$_POST["lastname"];
		$fields["%%LOGIN%%"]=$_POST["login"];
		$fields["%%PASSWORD%%"]=$_POST["password1"];
		$mail = new AnixMailer($ECOMMERCE_email_ids["newaccount_credentials"],1,$fields, $idClient);
		$mail->AddAddress($_POST["email"],$_POST["firstname"]." ".$_POST["lastname"]);
		if(!$mail->Send()){
			$ANIX_messages->addWarning(_("Une erreur s'est produite lors de l'envoie de l'email avec les codes d'accès").": ".$mail->ErrorInfo);
		} else {
			$ANIX_messages->addMessage(_("Un courriel a été envoyé au client avec ses codes d'accès"));
		}
	}
	if(!$errors){
		$message = "- "._("Le client a été ajouté correctement.")."<br>";
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	$request=request("SELECT `id`,`id_address_mailing`,`id_address_billing` from $TBL_ecommerce_customer WHERE `id`='$idClient'",$link);
	if(!mysql_num_rows($request)){
		$errors++;
		$errMessage.="- "._("Le client à mettre à jour n'existe pas.")."<br>";
	} else {
		$client=mysql_fetch_object($request);
	}
	if($_POST["firstname"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez renseigner le prénom du client.")."<br>";
	}
	if($_POST["lastname"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez renseigner le nom du client.")."<br>";
	}
	if($_POST["phone"]==""){
		$errors++;
		$errMessage.="- "._("Veuillez renseigner le numéro de téléphone du client.")."<br>";
	}
	if($_POST["language"]==0){
		$errors++;
		$errMessage.="- "._("Veuillez renseigner la langue de correspondance du client.")."<br>";
	}
	if(!loginPassValid($_POST["login"])) {
		$errors++;
		$errMessage.="- "._("Votre login comporte des caractères invalides. Seules les lettres et les chiffres sont acceptés.")."<br>";
	}
	$passwordChanges = ($_POST["password1"]!="");
	if($passwordChanges && !loginPassValid($_POST["password1"])) {
		$errors++;
		$errMessage.="- "._("Votre mot de passe comporte des caractères invalides. Seules les lettres et les chiffres sont acceptés.")."<br>";
	}
	if(strlen($_POST["login"])<$ECOMMERCE_min_login_legth || strlen($_POST["login"])>$ECOMMERCE_max_login_legth){
		$errors++;
		$errMessage.="- "._("Le login du client doite comporter entre $ECOMMERCE_min_login_legth et $ECOMMERCE_max_login_legth caractères.")."<br>";
	}
	if($passwordChanges && (strlen($_POST["password1"])<$ECOMMERCE_min_password_legth || strlen($_POST["password1"])>$ECOMMERCE_max_password_legth)){
		$errors++;
		$errMessage.="- "._("Le mot de passe du client doit comporter entre $ECOMMERCE_min_password_legth et $ECOMMERCE_max_password_legth caractères.")."<br>";
	}
	if($passwordChanges && strcmp($_POST["password1"],$_POST["password2"])){
		$errors++;
		$errMessage.="- "._("Veuillez entrer deux fois le même mot de passe.")."<br>";
	}
	if($passwordChanges && !strcmp($_POST["password1"],$_POST["login"])){
		$errors++;
		$errMessage.="- "._("Le mot de passe et le login doivent être différents.")."<br>";
	}
	if(!$errors && $anix_demo_mode){
		$errors++;
		$errMessage.="- "._("Cette action n'est pas permise en mode de démo.")."<br>";
	}
	//Update the user
	if(!$errors){
		$requestString ="UPDATE `$TBL_ecommerce_customer` SET ";
		$requestString .="`greating`='".htmlentities($_POST["greating"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`firstname`='".htmlentities($_POST["firstname"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`lastname`='".htmlentities($_POST["lastname"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`company`='".htmlentities($_POST["company"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`phone`='".htmlentities($_POST["phone"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`cell`='".htmlentities($_POST["cell"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`fax`='".htmlentities($_POST["fax"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`email`='".htmlentities($_POST["email"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`id_user_group`='".$_POST["price_group"]."',";
		$requestString .="`id_terms`='".$_POST["terms"]."',";
		$requestString .="`credit_margin`='".$_POST["credit_margin"]."',";
		$requestString .="`id_tax_group`='".$_POST["tax_group"]."',";
		$requestString .="`language`='".htmlentities($_POST["language"],ENT_QUOTES,"UTF-8")."',";
		if($passwordChanges){
			$cryptedPass = crypt($_POST["password1"],substr($_POST["login"],0,2));
			$requestString .="`pass`='$cryptedPass',";
		}
		$requestString .="`login`='".htmlentities($_POST["login"],ENT_QUOTES,"UTF-8")."'";
		$requestString .=" WHERE `id`='".$client->id."'";
		//echo $requestString;
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour du client.")."<br>";
		}
	}
	//updates the mailing address
	if(!$errors){
		$requestString ="UPDATE `$TBL_ecommerce_address` SET ";
		$requestString .="`num`='".htmlentities($_POST["mailing_num"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`street1`='".htmlentities($_POST["mailing_street1"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`street2`='".htmlentities($_POST["mailing_street2"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`building`='".htmlentities($_POST["mailing_building"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`stairs`='".htmlentities($_POST["mailing_stairs"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`floor`='".htmlentities($_POST["mailing_floor"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`code`='".htmlentities($_POST["mailing_code"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`city`='".htmlentities($_POST["mailing_city"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`province`='".htmlentities($_POST["mailing_province"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`zip`='".htmlentities($_POST["mailing_zip"],ENT_QUOTES,"UTF-8")."',";
		$requestString .="`country`='".$countryList[$_POST["mailing_country"]]."',";
		$requestString .="`country_code`='".$_POST["mailing_country"]."'";
		$requestString .=" WHERE `id`='".$client->id_address_mailing."'";
		request($requestString,$link);
		if(mysql_errno($link)) {
			$errors++;
			$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour de l'adresse de livraison.")."<br>";
		}
	}
	//updates the billing address
	if(!$errors){
		$idBillingAddress = 0; //If the value changes (!=0), this means we have to update the client table and put the new billing address id.
		if(isset($_POST["same_address"])){
			if($client->id_address_mailing!=$client->id_address_billing){
				//The addresses were different and know we want them to be the same-->Delete the billing address from DB
				$requestString = "DELETE FROM `$TBL_ecommerce_address` WHERE id='".$client->id_address_billing."'";
				$idBillingAddress = $client->id_address_mailing;
				request($requestString,$link);
				if(mysql_errno($link)) {
					$errors++;
					$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour de l'adresse de facturation (suppression).")."<br>";
				}
			}
		} else { //The address are not the same
			if($client->id_address_mailing!=$client->id_address_billing){
				//The addresses were different and we want to keep them different-->Update the billing address
				$requestString ="UPDATE `$TBL_ecommerce_address` SET ";
				$requestString .="`num`='".htmlentities($_POST["billing_num"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`street1`='".htmlentities($_POST["billing_street1"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`street2`='".htmlentities($_POST["billing_street2"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`building`='".htmlentities($_POST["billing_building"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`stairs`='".htmlentities($_POST["billing_stairs"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`floor`='".htmlentities($_POST["billing_floor"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`code`='".htmlentities($_POST["billing_code"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`city`='".htmlentities($_POST["billing_city"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`province`='".htmlentities($_POST["billing_province"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`zip`='".htmlentities($_POST["billing_zip"],ENT_QUOTES,"UTF-8")."',";
				$requestString .="`country`='".$countryList[$_POST["billing_country"]]."',";
				$requestString .="`country_code`='".$_POST["billing_country"]."'";
				$requestString .=" WHERE `id`='".$client->id_address_billing."'";
				request($requestString,$link);
				if(mysql_errno($link)) {
					$errors++;
					$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour de l'adresse de facturation.")."<br>";
				}
			} else {
				//The addresses were the same and know we want them different --> Insert the billing address
				$requestString ="INSERT INTO `$TBL_ecommerce_address` (`num`,`street1`,`street2`,`building`,`stairs`,`floor`,`code`,`city`,`province`,`country`,`zip`,`country_code`)";
				$requestString.=" values ('".htmlentities($_POST["billing_num"],ENT_QUOTES,"UTF-8")."',
                                   '".htmlentities($_POST["billing_street1"],ENT_QUOTES,"UTF-8")."',
                                   '".htmlentities($_POST["billing_street2"],ENT_QUOTES,"UTF-8")."',
                                   '".htmlentities($_POST["billing_building"],ENT_QUOTES,"UTF-8")."',
		                           '".htmlentities($_POST["billing_stairs"],ENT_QUOTES,"UTF-8")."',
		                           '".htmlentities($_POST["billing_floor"],ENT_QUOTES,"UTF-8")."',
		                           '".htmlentities($_POST["billing_code"],ENT_QUOTES,"UTF-8")."',
                                   '".htmlentities($_POST["billing_city"],ENT_QUOTES,"UTF-8")."',
                                   '".htmlentities($_POST["billing_province"],ENT_QUOTES,"UTF-8")."',
                                   '".$countryList[$_POST["billing_country"]]."',
                                   '".htmlentities($_POST["billing_zip"],ENT_QUOTES,"UTF-8")."',
                                   '".htmlentities($_POST["billing_country"],ENT_QUOTES,"UTF-8")."')";
				request($requestString,$link);
				if(mysql_errno($link)) {
					$errors++;
					$errMessage.="- "._("Une erreur s'est produite lors de la mise à jour de l'adresse de facturation (insertion).")."<br>";
				} else {
					$idBillingAddress = mysql_insert_id($link);
				}
			}
		}
		if($idBillingAddress){
			$requestString = "UPDATE `$TBL_ecommerce_customer` SET `id_address_billing`='$idBillingAddress' WHERE id='".$client->id."'";
			request($requestString,$link);
			if(mysql_errno($link)) {
				$errors++;
				$errMessage.="- "._("Une erreur s'est produite lors de la spécification de l'adresse de facturation.")."<br>";
			}
		}
	}
	if(!$errors){
		$message = "- "._("Le client a été mis à jour correctement.")."<br>";
		$action="edit";
	}
}
?>