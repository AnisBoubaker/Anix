<?php
if($action=="deletePOrder"){
	if(isset($_REQUEST["idPOrder"])) $idPOrder = $_REQUEST["idPOrder"];
	else $ANIX_messages->addError(_("La commande d'achat à supprimer n'a pas été spécifiée"));

	if(!$ANIX_messages->nbErrors) try {
		$porder = new EcommercePOrder($idPOrder);
	} catch (Exception $e){
		echo "Je passe!";
		$ANIX_messages->addError($e->getMessage());
	}

	if(!$ANIX_messages->nbErrors && $porder->getSupplierId()!=$idSupplier){
		$ANIX_messages->addError(_("Cette commande n'appartient pas à ce fournisseur."));
	}

	if(!$ANIX_messages->nbErrors) try {
		$porder->delete();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}

	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La commande d'achat a bien été supprimée."));
	}
}
?>