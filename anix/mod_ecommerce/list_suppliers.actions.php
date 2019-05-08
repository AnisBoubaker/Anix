<?php
if($action=="deleteSupplier"){
	if(isset($_REQUEST["idSupplier"])) $idSupplier=$_REQUEST["idSupplier"];
	else $ANIX_messages->addError(_("Le fournisseur à supprimer n'a pas été spécifié."));
	try{
		$supplierObj = new EcommerceSupplier($idSupplier);
		$supplierObj->delete();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("Le fournisseur a bien été supprimé."));
	}
}
?>