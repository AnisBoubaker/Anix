<?php
if($action=="insert"){
	try{
		$porder = new EcommercePOrder(0,$idSupplier);
		$porder->setOrderDate($_POST["porder_date"]);
		$porder->setExpectedReceptionDate($_POST["porder_expected_date"]);
		$porder->setOrderSent(isset($_POST["order_sent"]));
		if($porder->isOrderSent()) $porder->setSentDate($_POST["porder_sent_date"]);
		$porder->loadItems();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	if(!$ANIX_messages->nbErrors) try{
		$items = $porder->getItems();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	//INSERT ADDED LINE ITEMS
	if(!$ANIX_messages->nbErrors && $_POST["nb_new_lines"]!=0){
		$newLines = $_POST["nb_new_lines"];
		for($i=0;$i<$newLines;$i++) if(isset($_POST["newrow".$i."_qty"])){
			try{
				$item = new EcommercePOrderItem(0,0);
				$item->setIdProduct($_POST["newrow".$i."_product"]);
				$item->setQty($_POST["newrow".$i."_qty"]);
				$item->setStoreRef($_POST["newrow".$i."_refStore"]);
				$item->setSupplierRef($_POST["newrow".$i."_refSupplier"]);
				$item->setDescription($_POST["newrow".$i."_description"]);
				$item->setUprice($_POST["newrow".$i."_uprice"]);
			} catch (Exception $e){
				$ANIX_messages->addError($e->getMessage());
			}

			if(!$ANIX_messages->nbErrors) try {
				$items->addItem($item);
			} catch (Exception $e){
				$ANIX_messages->addError($e->getMessage());
			}
		}
	}
	if(!$ANIX_messages->nbErrors) try{
		$porder->save();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La commande d'achat a été mise à insérée avec succès."));
		$idPOrder = $porder->getId();
		$action="edit";
	}
}
if($action=="update"){
	try{
		$porder = new EcommercePOrder($idPOrder);
		$porder->setOrderDate($_POST["porder_date"]);
		$porder->setExpectedReceptionDate($_POST["porder_expected_date"]);
		$porder->setOrderSent(isset($_POST["order_sent"]));
		$porder->setSentDate($_POST["porder_sent_date"]);
		$porder->loadItems();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	if(!$ANIX_messages->nbErrors) try{
		$items = $porder->getItems();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	//DELETE LINE IF REQUESTED
	if(!$ANIX_messages->nbErrors && isset($_POST["delete_line"]) && $_POST["delete_line"]!=0){
		try{
			$items->deleteItem($_POST["delete_line"]);
		} catch (Exception $e){
			$ANIX_messages->addError($e->getMessage());
		}
	}
	//UPDATE EXISTING LINE ITEMS
	if(!$ANIX_messages->nbErrors) foreach ($items as $item){
		try{
			$item->setQty($_POST["qty_".$item->getId()]);
			if($porder->isOrderSent() && isset($_POST["receivedqty_".$item->getId()])) $item->setReceivedQty($_POST["receivedqty_".$item->getId()]);
			$item->setStoreRef($_POST["refStore_".$item->getId()]);
			$item->setSupplierRef($_POST["refSupplier_".$item->getId()]);
			$item->setIdProduct($_POST["product_".$item->getId()]);
			$item->setDescription($_POST["description_".$item->getId()]);
			$item->setUprice($_POST["uprice_".$item->getId()]);
		} catch (Exception $e){
			$ANIX_messages->addError($e->getMessage());
		}
	}
	//INSERT ADDED LINE ITEMS
	if(!$ANIX_messages->nbErrors && $_POST["nb_new_lines"]!=0){
		$newLines = $_POST["nb_new_lines"];
		for($i=0;$i<$newLines;$i++) if(isset($_POST["newrow".$i."_qty"])){
			try{
				$item = new EcommercePOrderItem(0,$idPOrder);
				$item->setIdProduct($_POST["newrow".$i."_product"]);
				$item->setQty($_POST["newrow".$i."_qty"]);
				$item->setStoreRef($_POST["newrow".$i."_refStore"]);
				$item->setSupplierRef($_POST["newrow".$i."_refSupplier"]);
				$item->setDescription($_POST["newrow".$i."_description"]);
				$item->setUprice($_POST["newrow".$i."_uprice"]);
			} catch (Exception $e){
				$ANIX_messages->addError($e->getMessage());
			}

			if(!$ANIX_messages->nbErrors) try {
				$items->addItem($item);
			} catch (Exception $e){
				$ANIX_messages->addError($e->getMessage());
			}
		}
	}
	if(!$ANIX_messages->nbErrors) try{
		$porder->save();
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
	if(!$ANIX_messages->nbErrors){
		$ANIX_messages->addMessage(_("La commande d'achat a été mise à jour."));
		$action="edit";
	}
}
?>