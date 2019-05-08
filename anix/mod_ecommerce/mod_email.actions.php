<?
if($action=="insert"){
	//check the fields
	if($idCategory==-1 || !isset($ECOMMERCE_email_categories[$idCategory])){
		$errMessage.=_("La categorie specifiee n'existe pas.")."<br>";//$str15;
		$errors++;
	}
	if(!isset($_POST["cc_email"]) || $_POST["cc_email"]!=""){
		$emails = explode(",",$_POST["cc_email"]);
		foreach($emails as $email) if(!emailValid(trim($email))){
			$errors++;
			$errMessage.=_("- Une ou les adresse(s) email CC est/sont invalide(s). Veuillez entrer des adresses valides séparées par des ;")."<br /><br />";
		}
	}
	if(!isset($_POST["bcc_email"]) || $_POST["bcc_email"]!=""){
		$emails = explode(",",$_POST["bcc_email"]);
		foreach($emails as $email) if(!emailValid(trim($email))){
			$errors++;
			$errMessage.=_("- Une ou les adresse(s) email BCC est/sont invalide(s). Veuillez entrer des adresses valides séparées par des ;")."<br /><br />";
		}
	}
	if(!$errors){
		//Insert the category in the database
		$request = request("SELECT MAX(ordering) as maximum from `$TBL_ecommerce_emails` WHERE id_category='$idCategory' GROUP BY id_category",$link);
		$tmp = mysql_fetch_object($request);
		$ordering = 1;
		if(mysql_num_rows($request)) $ordering = ($tmp->maximum+1);
		$requestString="INSERT INTO $TBL_ecommerce_emails (`id_category`,`cc_email`,`bcc_email`,`enabled`,`ordering`,`created_on`,`created_by`,`modified_on`,`modified_by`) values ('$idCategory','".addslashes($_POST["cc_email"])."','".addslashes($_POST["bcc_email"])."','".(isset($_POST["enabled"])?"Y":"N")."','$ordering','".getDBDate()."','$anix_username','".getDBDate()."','$anix_username')";
		//echo $requestString;
		request($requestString,$link);
		//echo mysql_error();
		//If insertion was OK, we rtrieve the id of the inserted category, else error...
		if(!mysql_errno($link)) {
			$idEmail=mysql_insert_id($link);
		} else {
			$errMessage.=_("Une erreur s'est produite lors de l'insertion de l'email type.")."<br>";//$str1;
			$errors++;
		}
	}
	if(!$errors){
		$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
		while($language = mysql_fetch_object($request)){
			request("INSERT INTO `$TBL_ecommerce_info_emails` (`id_email`,`id_language`,`title`,`sender_name`,`sender_email`,`subject`,`content`)
                VALUES ('$idEmail','".$language->id."','".addslashes($_POST["title_".$language->id])."','".addslashes($_POST["sendername_".$language->id])."','".addslashes($_POST["senderemail_".$language->id])."','".addslashes($_POST["subject_".$language->id])."','".addslashes($_POST["content_".$language->id])."')",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de l'insertions des informations de l'email type.")."<br>";//$str2;
			}
		}
	}
	if(!$errors){
		$message = _("L'email a ete insere correctement.")."<br>";//$str3;
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	//check the email existence
	$request = request("SELECT * FROM `$TBL_ecommerce_emails` WHERE `$TBL_ecommerce_emails`.`id`='$idEmail'",$link);
	if(mysql_num_rows($request)) $email = mysql_fetch_object($request);
	else {
		$errors++;
		$errMessage.=_("- L'email spécifié n'existe pas.")."<br /><br />";
	}
	//check the fields
	if(!isset($_POST["cc_email"]) || $_POST["cc_email"]!=""){
		$email_addresses = explode(",",$_POST["cc_email"]);
		foreach($email_addresses as $email_address) if(!emailValid(trim($email_address))){
			$errors++;
			$errMessage.=_("- Une ou les adresse(s) email CC est/sont invalide(s). Veuillez entrer des adresses valides séparées par des ;")."<br /><br />";
		}
	}
	if(!isset($_POST["bcc_email"]) || $_POST["bcc_email"]!=""){
		$email_addresses = explode(",",$_POST["bcc_email"]);
		foreach($email_addresses as $email_address) if(!emailValid(trim($email_address))){
			$errors++;
			$errMessage.=_("- Une ou les adresse(s) email BCC est/sont invalide(s). Veuillez entrer des adresses valides séparées par des ;")."<br /><br />";
		}
	}

	$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
	while(!$errors && $language = mysql_fetch_object($request)){
		if(!isset($_POST["title_".$language->id]) || $_POST["title_".$language->id]==""){
			$errors++;
			$errMessage.=_("- Vous n'avez pas entré un titre à cet email dans toutes les langues:").$language->name."<br /><br />";
		}
		if(!isset($_POST["sendername_".$language->id]) || $_POST["sendername_".$language->id]==""){
			$errors++;
			$errMessage.=_("Vous n'avez pas entré le nom de l'auteur dans toutes les langues:").$language->name."<br /><br />";
		}
		if(!isset($_POST["senderemail_".$language->id]) || !emailValid($_POST["senderemail_".$language->id])){
			$errors++;
			$errMessage.=_("Vous n'avez pas entré un email de l'auteur dans toutes les langues (ou email invalide):").$language->name."<br /><br />";
		}
		if(!isset($_POST["subject_".$language->id]) || $_POST["subject_".$language->id]==""){
			$errors++;
			$errMessage.=_("- Vous n'avez pas spécifié le sujet de cet email dans toutes les langues:").$language->name."<br /><br />";
		}
		if(!isset($_POST["content_".$language->id]) || $_POST["content_".$language->id]==""){
			$errors++;
			$errMessage.=_("- Vous n'avez pas spécifié le corps de cet email dans toutes les langues:").$language->name."<br /><br />";
		}
		//check the fields used in the content
		if(!$errors){
			$fieldsAvailable = array();
			$tmp = explode(";",$email->fields);
			foreach ($tmp as $fieldAvailable){
				$fieldsAvailable[trim($fieldAvailable)]=true;
			}
			$fieldsFound = array();
			//get all the strings like %%xxxx%% present in the content
			preg_match_all("|%%(.*)%%|U",$_POST["content_".$language->id],$fieldsFound,PREG_PATTERN_ORDER);
			foreach ($fieldsFound[0] as $field){
				if(!isset($fieldsAvailable[$field])){
					$errors++;
					$errMessage.=_("- Ce champs utilisé dans le corps de l'email n'est pas permis").": $field ($language->name)<br /><br />";
				}
			}
			$fieldsFound = array();
			//get all the strings like %%xxxx%% present in the subject
			preg_match_all("|%%(.*)%%|U",$_POST["subject_".$language->id],$fieldsFound,PREG_PATTERN_ORDER);
			foreach ($fieldsFound[0] as $field){
				if(!isset($fieldsAvailable[$field])){
					$errors++;
					$errMessage.=_("- Ce champs utilisé dans le corps de l'email n'est pas permis").": $field ($language->name)<br /><br />";
				}
			}
		}

	}
	//Update the category
	if(!$errors){
		$request=request("SELECT id,name from $TBL_gen_languages where used='Y'",$link);
		while($language = mysql_fetch_object($request)){
			request("UPDATE $TBL_ecommerce_info_emails
                SET `title`='".addslashes($_POST["title_".$language->id])."',
                `sender_name`='".addslashes($_POST["sendername_".$language->id])."',
                `sender_email`='".addslashes($_POST["senderemail_".$language->id])."',
                `subject`='".addslashes($_POST["subject_".$language->id])."',
                `content`='".addslashes($_POST["content_".$language->id])."'
                WHERE id_email='$idEmail'
                AND id_language='".$language->id."'",$link);
			if(mysql_errno($link)){
				$errors++;
				$errMessage.=_("Une erreur s'est produite lors de la mise a jour des informations de l'email.")."<br>";//$str4;
			}
		}
	}
	if(!$errors){
		request("UPDATE $TBL_ecommerce_emails
               SET `cc_email`='".addslashes($_POST["cc_email"])."',
               `bcc_email`='".addslashes($_POST["bcc_email"])."',
               `enabled`='".(isset($_POST["enabled"])?"Y":"N")."',
               `modified_on`='".getDBDate()."',
               modified_by='$anix_username'
               WHERE `id`='$idEmail'",$link);
		if(mysql_errno($link)!=0){
			$errors++;
			$errMessage.=_("Une erreur s'est produite lors de la mise a jour de l'email.")."<br>";//$str4;
		}
	}
	if(!$errors){
		$message = _("L'email a ete mis a jour correctement.")."<br>";//$str5;
		$action="edit";
	}
}
?>