<?php
/**
 * Anix Mailer: Sends emails from templates set in Anix
 * !!IMPORTANT!!: PHPMailer must be loaded before instanciating this class
 *
 */
class Mailer extends PHPMailer {
	var $id=0;
	var $idClient = 0;
	var $idOrder = 0;

	//An array with the field name as a key (ex.: fields["%%ID_ORDER%%"]=1234; )
	var $fields=array();
	//An array with the field name as a key (ex.: $fieldsAvailable["%%ID_ORDER%%"]=true; )
	var $fieldsAvailable = array();
	var $idLanguage = 0;
	var $enabled = true;

	function __construct($idEmail, $idLanguage=1, $fields=array(), $idClient=0, $idOrder=0){
		global $TBL_ecommerce_emails,$TBL_ecommerce_info_emails;
		$link = dbConnect();

		$this->id=$idEmail;
		//load the email and check it's existence
		$request = request("SELECT * FROM `$TBL_ecommerce_emails`,`$TBL_ecommerce_info_emails` WHERE `$TBL_ecommerce_emails`.`id`='$idEmail' AND `$TBL_ecommerce_info_emails`.`id_email`=`$TBL_ecommerce_emails`.`id` AND `$TBL_ecommerce_info_emails`.`id_language`='$idLanguage'",$link);
		if(!mysql_num_rows($request)){
			mysql_close($link);
			$this->id=0;
			return;
		}

		$this->idClient = $idClient;
		$this->idOrder = $idOrder;

		$this->idLanguage = $idLanguage;

		//load the email
		$email = mysql_fetch_object($request);

		$this->enabled = ($email->enabled=="Y");

		//get the available fields
		$this->fieldsAvailable = array();
		$tmp = explode(";",$email->fields);
		foreach ($tmp as $fieldAvailable){
			$this->fieldsAvailable[trim($fieldAvailable)]=true;
		}

		//get the used fields in the template and check if all of them are defined and allowed in fieldsAvailable
		$usedFields = array();
		//get all the strings like %%xxxx%% present in the content
		preg_match_all("|%%(.*)%%|U",$email->content,$usedFields,PREG_PATTERN_ORDER);
		foreach ($usedFields[0] as $field){
			if(!isset($fields[$field]) || !isset($this->fieldsAvailable[$field])){
				/*mysql_close($link);
				$this->id=0;
				$this->SetError("Le champs $field n'a pas été renseigné ou n'est pas autorisé.");
				return;*/
			} else $this->fields[$field]=$fields[$field];
		}
		$usedFields = array();
		//get all the strings like %%xxxx%% present in the content
		preg_match_all("|%%(.*)%%|U",$email->subject,$usedFields,PREG_PATTERN_ORDER);
		foreach ($usedFields[0] as $field){
			if(!isset($fields[$field]) || !isset($this->fieldsAvailable[$field])){
				/*mysql_close($link);
				$this->id=0;
				$this->SetError("Le champs $field n'a pas été renseigné ou n'est pas autorisé.");
				return;*/
			} else $this->fields[$field]=$fields[$field];
		}

		$this->FromName = stripslashes($email->sender_name);
		$this->From = stripslashes($email->sender_email);

		if($email->cc_email!=""){
			$cc_emails=explode(",",$email->cc_email);
			foreach ($cc_emails as $cc_email){
				$this->AddCC(trim($cc_email));
			}
		}

		if($email->bcc_email!=""){
			$bcc_emails=explode(",",$email->bcc_email);
			foreach ($bcc_emails as $bcc_email){
				$this->AddBCC(trim($bcc_email));
			}
		}

		//set the content and the subject of the email
		$body = stripslashes($email->content);
		$subject = stripslashes($email->subject);
		foreach($this->fields as $fieldName => $fieldValue){
			 $body = str_replace($fieldName,$fieldValue,$body);
			 $subject = str_replace($fieldName,$fieldValue,$subject);
		}
		$this->Body = $body;
		$this->Subject = $subject;
	}

	function Send(){
		global $TBL_ecommerce_emails_sent;
		if($this->id==0) return false;

		if(!$this->enabled){
			$this->SetError("L'envoi de cet email a été désactivé dans Anix.");
			return false;
		}

		$result = parent::Send();

		if($result && ($this->idClient!=0 || $this->idOrder!=0)){
			//SAVE THE SENT EMAIL
			$requestStr = "INSERT INTO `$TBL_ecommerce_emails_sent` (`id_email`,`id_client`,`id_order`,`subject`,`sent_to`,`sent_cc`,`sent_bcc`,`sent_timestamp`) VALUES (";
			$requestStr.= "'".$this->id."',";
			$requestStr.= "'".$this->idClient."',";
			$requestStr.= "'".$this->idOrder."',";
			$requestStr.= "'".addslashes($this->Subject)."',";
			$addresses = "";//get the To
			foreach($this->to as $tmp) $addresses.=$tmp[0]." ";
			$requestStr.= "'".addslashes($addresses)."',";
			$addresses = "";//get the CC addresses
			foreach($this->cc as $tmp) $addresses.=$tmp[0]." ";
			$requestStr.= "'".addslashes($addresses)."',";
			$addresses = "";//get the BC addresses
			foreach($this->bcc as $tmp) $addresses.=$tmp[0]." ";
			$requestStr.= "'".addslashes($addresses)."',";
			$requestStr.= "NOW() ";
			$requestStr.=")";
			$link=dbConnect();
			request($requestStr,$link);
		}

		return $result;
	}
}
?>