<?php
class EcommerceSupplier{

	private $id=0;
	private $name="";
	private $contact="";
	private $contactEmail="";
	private $phoneSales="";
	private $phoneSupport="";
	private $websiteURL="";
	private $deliveryDelay = 0;
	private $acceptEmailOrders = false;
	private $ordersEmail = "";
	private $ordersSenderName = "";
	private $ordersSenderEmail = "";
	private $emailTemplate = "";
	private $emailResendHeader = "";


	private $TBL_ecommerce_supplier;

	public function __construct($id=0){
		global $TBL_ecommerce_supplier;
		$this->id = $id;
		$this->TBL_ecommerce_supplier = $TBL_ecommerce_supplier;
		if($this->id) $this->load();
	}

	private function load(){
		if(!$this->id) throw new ExceptionAnixEngineError(_("L'identifiant du fournisseur est invalide."));
		$link = dbConnect();
		$request = request("SELECT * FROM `$this->TBL_ecommerce_supplier` WHERE `id`='$this->id'",$link);
		if(!mysql_num_rows($request)){
			$this->id=0;
			mysql_close($link);
			throw new ExceptionAnixError(_("Le fournisseur identifié n'existe pas."));
		}
		$supplier = mysql_fetch_object($request);
		$this->name = $supplier->name;
		$this->contact = $supplier->contact;
		$this->contactEmail = $supplier->contact_email;
		$this->phoneSales = $supplier->tel_sales;
		$this->phoneSupport = $supplier->tel_support;
		$this->websiteURL = $supplier->url;
		$this->deliveryDelay = $supplier->delivery_delay;
		$this->acceptEmailOrders = ($supplier->send_orders_email=="Y");
		$this->ordersEmail = $supplier->orders_email;
		$this->ordersSenderName = $supplier->orders_sender;
		$this->ordersSenderEmail = $supplier->orders_sender_email;
		$this->emailTemplate = $supplier->email_template;
		$this->emailResendHeader = $supplier->email_header;
	}

	private function isConsistent(){
		if($this->acceptEmailOrders){
			if(!emailValid($this->ordersEmail)) throw new ExceptionAnixError(_("Vous n'avez pas spécifié une adresse de courriel valide pour l'envoi des commandes."));
			if($this->ordersSenderName=="") throw new ExceptionAnixError(_("Veuillez entrer le nom de l'expéditeur des commandes."));
			if(!emailValid($this->ordersSenderEmail)) throw new ExceptionAnixError(_("Vous n'avez pas spécifié une adresse de courriel valide pour l'expéditeur des commandes."));

			// Check the template
			$templateCheck = $this->checkTemplate();
			if(count($templateCheck["unknown"])){
				$unknown = "";
				foreach($templateCheck["unknown"] as $str) $unknown.= $str." ";
				throw new ExceptionAnixError(_("Les champs suivants ne sont pas autorisés dans le modèle de courriel pour les commandes").": ".$unknown);
			}
			if(count($templateCheck["unused"])){
				$unused = "";
				foreach($templateCheck["unused"] as $str) $unused.= $str." ";
				throw new ExceptionAnixError(_("Les champs suivants sont obligatoires et ne figurent pas dans le modèle de courriel pour les commandes").": ".$unused);
			}
		}
		return true;
	}

	public function save(){
		$this->isConsistent();
		$link= dbConnect();
		$requestStr="";
		if($this->id){
			//UPDATE THE SUPPLIER
			$requestStr ="UPDATE `$this->TBL_ecommerce_supplier` SET ";
			$requestStr .="`name`='$this->name',";
			$requestStr .="`contact`='$this->contact',";
			$requestStr .="`contact_email`='$this->contactEmail',";
			$requestStr .="`tel_sales`='$this->phoneSales',";
			$requestStr .="`tel_support`='$this->phoneSupport',";
			$requestStr .="`url`='$this->websiteURL',";
			$requestStr .="`delivery_delay`='$this->deliveryDelay',";
			$requestStr .="`send_orders_email`='".($this->acceptEmailOrders?"Y":"N")."',";
			$requestStr .="`orders_email`='$this->ordersEmail',";
			$requestStr .="`orders_sender`='$this->ordersSenderName',";
			$requestStr .="`orders_sender_email`='$this->ordersSenderEmail',";
			$requestStr .="`email_template`='$this->emailTemplate',";
			$requestStr .="`email_header`='$this->emailResendHeader' ";
			$requestStr .="WHERE `id`='$this->id'";
			request($requestStr,$link);
			if(mysql_errno($link)) {
				$errMessage.=_("Une erreur s'est produite lors de la mise à jour du fournisseur.")."<br />";
			}
		} else {
			//INSERT THE NEW SUPPLIER
			$requestStr.="INSERT INTO `$this->TBL_ecommerce_supplier` (`name`,`contact`,`contact_email`,`tel_sales`, `tel_support`,`url`,`delivery_delay`,`send_orders_email`,`orders_email`,`orders_sender`,`orders_sender_email`,`email_template`,`email_header`) ";
			$requestStr.="VALUES ('$this->name','$this->contact','$this->contactEmail','$this->phoneSales','$this->phoneSupport','$this->websiteURL','$this->deliveryDelay','".($this->acceptEmailOrders?"Y":"N")."','$this->ordersEmail','$this->ordersSenderName','$this->ordersSenderEmail','$this->emailTemplate','$this->emailResendHeader')";
			request($requestStr,$link);
		    //If insertion was OK, we rtrieve the id of the inserted category, else error...
		  	if(!mysql_errno($link)) {
		  	  $this->id=mysql_insert_id($link);
		  	} else {
		  	  throw new ExceptionAnixEngineError(_("Une erreur s'est produite lors de l'insertion du nouveau fournisseur."));
		  	}
		}
	}

	public function delete(){
		global $TBL_catalogue_products;
		if(!$this->id) throw new ExceptionAnixEngineError(_("L'identifiant du fournisseur à supprimer n'est pas valide."));

		$link=dbConnect();
		//Remove the supplier from catalogue products
		request("UPDATE $TBL_catalogue_products SET `id_supplier1`='0', `cost_supplier1`='0' WHERE `id_supplier1`='$this->id'",$link);
		request("UPDATE $TBL_catalogue_products SET `id_supplier2`='0', `cost_supplier2`='0' WHERE `id_supplier2`='$this->id'",$link);
		request("UPDATE $TBL_catalogue_products SET `id_supplier3`='0', `cost_supplier3`='0' WHERE `id_supplier3`='$this->id'",$link);
		request("UPDATE $TBL_catalogue_products SET `id_supplier4`='0', `cost_supplier4`='0' WHERE `id_supplier4`='$this->id'",$link);

		//Delete the non received orders
		$orders = new EcommercePOrdersList($this->id);
		foreach($orders as $order){
			if($order->getStatus()=="created" || $order->getStatus()=="ordered") $order->delete();
		}

		//Delete supplier from database
		request("DELETE FROM $this->TBL_ecommerce_supplier WHERE id='$this->id'",$link);

		$this->id = 0;

		mysql_close($link);
	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function getContact(){
		return $this->contact;
	}

	public function getContactEmail(){
		return $this->contactEmail;
	}

	public function getPhoneSales(){
		return $this->phoneSales;
	}

	public function getPhoneSupport(){
		return $this->phoneSupport;
	}

	public function getWebsiteURL(){
		return $this->websiteURL;
	}

	public function getDeliveryDelay(){
		return $this->deliveryDelay;
	}

	public function isAcceptEmailOrders(){
		return $this->acceptEmailOrders;
	}

	public function getOrdersEmail(){
		return $this->ordersEmail;
	}

	public function getOrdersSenderName(){
		return $this->ordersSenderName;
	}

	public function getOrdersSenderEmail(){
		return $this->ordersSenderEmail;
	}

	public function getEmailTemplate(){
		return $this->emailTemplate;
	}

	public function getEmailResendHeader(){
		return $this->emailResendHeader;
	}


	public function setName($name){
		if(trim($name)) $this->name= $name;
		else throw new ExceptionAnixError(_("Vous n'avez pas entré le nom du fournisseur."));
	}

	public function setContact($contact){
		$this->contact = $contact;
	}

	public function setContactEmail($email){
		if(emailValid($email)) $this->contactEmail=$email;
		elseif(trim($email)=="") $this->contactEmail="";
		else throw new ExceptionAnixError(_("L'adresse de courriel du représentant n'est pas une adresse de courriel valide."));
	}

	public function setPhoneSales($phone){
		$this->phoneSales= $phone;
	}

	public function setPhoneSupport($phone){
		$this->phoneSupport= $phone;
	}

	public function setWebsiteURL($url){
		$this->websiteURL= $url;
	}

	public function setDeliveryDelay($delay){
		if($delay<0) $delay=0;
		$this->deliveryDelay= $delay;
	}

	public function setAcceptEmailOrders($send){
		$this->acceptEmailOrders=$send;
	}

	public function setOrdersEmail($email){
		if(emailValid($email)) $this->ordersEmail=$email;
		elseif(trim($email)=="") $this->ordersEmail="";
		else throw new ExceptionAnixError(_("L'adresse de courriel pour l'envoi des commandes n'est pas une adresse de courriel valide."));
	}

	public function setOrdersSenderName($name){
		$this->ordersSenderName = $name;
	}

	public function setOrdersSenderEmail($email){
		if(emailValid($email)) $this->ordersSenderEmail=$email;
		elseif(trim($email)=="") $this->ordersSenderEmail="";
		else throw new ExceptionAnixError(_("L'adresse de courriel de l'auteur des commandes envoyées n'est pas valide."));
	}

	public function setEmailTemplate($template){
		$this->emailTemplate=$template;
	}

	/**
	 * Check the email template: verify that each mandatory field has been used and that
	 * there are not unknown fields used. The email template is valid if both of the returned arrays
	 * are empty.
	 *
	 * @return array containing two arrays: at index "unused" the unused mandatory fields; and at index "unknown" the unknow used fields.
	 */
	private function checkTemplate(){
		$fields = EcommerceSupplier::getAvailableOrderFields();
		$unknown = array();
		$unused = array();

		$usedFields = array();
		//get all the strings like %%xxxx%% present in the content
		preg_match_all("|%%(.*)%%|U",$this->emailTemplate,$usedFields,PREG_PATTERN_ORDER);
		foreach ($usedFields[0] as $field){
			if(isset($fields[$field])) $fields[$field][2]=true;
			else $unknown[]=$field;
		}
		//check the mandatory fields that has not been used.
		foreach ($fields as $str=>$field){
			if($field[1] && !isset($field[2])) $unused[]=$str;
		}
		return array("unused"=>$unused,"unknown"=>$unknown);
	}

	public function setEmailResendHeader($template){
		$this->emailResendHeader=$template;
	}

	public static function getAvailableOrderFields(){
		$templateFields = array();
		$templateFields["%%PORDER_NUM%%"]=array(_("Numéro du bon de commande"),true);
		$templateFields["%%PORDER_DATE%%"]=array(_("Date de la commande"),true);
		$templateFields["%%SUPP_ORDER_NUM%%"]=array(_("Numéro de commande chez le fournisseur"),false);
		$templateFields["%%REPRESENTATIVE_NAME%%"]=array(_("Nom du représentant"),false);
		$templateFields["%%ITEMS_LIST%%"]=array(_("Liste des produits commandés"),true);
		$templateFields["%%ORDER_TOTAL_AMOUNT%%"]=array(_("Montant total de la commande"),true);

		return $templateFields;
	}
}
?>