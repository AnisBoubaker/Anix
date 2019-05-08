<?
if($action=="insert"){
	try{
		$supplierObj = new EcommerceSupplier();
		$supplierObj->setName(htmlentities($_POST["name"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setContact(htmlentities($_POST["contact"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setContactEmail(htmlentities($_POST["contact_email"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setPhoneSales(htmlentities($_POST["tel_sales"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setPhoneSupport(htmlentities($_POST["tel_support"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setWebsiteURL(htmlentities($_POST["url"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setDeliveryDelay(htmlentities($_POST["delivery_delay"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setAcceptEmailOrders(isset($_POST["accept_email_orders"]));
		$supplierObj->setOrdersEmail(htmlentities($_POST["orders_email"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setOrdersSenderName(htmlentities($_POST["orders_sender"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setOrdersSenderEmail(htmlentities($_POST["orders_sender_email"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setEmailTemplate(htmlentities($_POST["email_template"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setEmailResendHeader(htmlentities($_POST["email_resend_header"],ENT_QUOTES,"UTF-8"));
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	try{
		$supplierObj->save();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("Le fournisseur a été ajouté correctement."));
		$idSupplier = $supplierObj->getId();
		$action="edit";
	}
}
?>
<?
if($action=="update"){
	try{
		$supplierObj = new EcommerceSupplier($idSupplier);
		$supplierObj->setName(htmlentities($_POST["name"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setContact(htmlentities($_POST["contact"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setContactEmail(htmlentities($_POST["contact_email"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setPhoneSales(htmlentities($_POST["tel_sales"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setPhoneSupport(htmlentities($_POST["tel_support"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setWebsiteURL(htmlentities($_POST["url"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setDeliveryDelay(htmlentities($_POST["delivery_delay"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setAcceptEmailOrders(isset($_POST["accept_email_orders"]));
		$supplierObj->setOrdersEmail(htmlentities($_POST["orders_email"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setOrdersSenderName(htmlentities($_POST["orders_sender"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setOrdersSenderEmail(htmlentities($_POST["orders_sender_email"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setEmailTemplate(htmlentities($_POST["email_template"],ENT_QUOTES,"UTF-8"));
		$supplierObj->setEmailResendHeader(htmlentities($_POST["email_resend_header"],ENT_QUOTES,"UTF-8"));
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	try{
		$supplierObj->save();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("Le fournisseur a été mis à jour correctement."));
		$action="edit";
	}
}
?>