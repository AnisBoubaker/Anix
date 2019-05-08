<?php
/**
 * class that manages a purchase order's line item.
 *
 * @version 1.0
 * @author Anis Boubaker
 * @copyright CIBAXION Inc.
 *
 */
class EcommercePOrderItem{

	private $id=0;
	private $idPOrder=0;
	private $idProduct=0;
	private $storeRef="";
	private $supplierRef="";
	private $description="";
	private $uprice=0;
	private $qty=0;
	private $total=0;
	private $receivedQty=0;
	/**
	 * Qty reported in the inventory as on order
	 *
	 * @var int
	 */
	private $inventoryOnOrderQty=0;
	/**
	 * Qty reported in the inventory as in stock
	 *
	 * @var unknown_type
	 */
	private $stockedQty=0;
	private $updated=false;

	private $TBL_ecommerce_porder_item;
	private $TBL_catalogue_products;

	public function __construct($id=0, $idPOrder=0){
		global $TBL_ecommerce_porder_item, $TBL_catalogue_products;
		$this->TBL_ecommerce_porder_item = $TBL_ecommerce_porder_item;
		$this->TBL_catalogue_products = $TBL_catalogue_products;

		$this->id = $id;
		$this->idPOrder = $idPOrder;

		if($this->id) $this->load();
		else $this->updated=true;
	}


	private function load(){
		if(!$this->id) throw new ExceptionAnixEngineError(_("L'identifiant de la ligne n'est pas valide."));
		$link = dbConnect();
		$requestStr = "SELECT * ";
		$requestStr.= "FROM (`$this->TBL_ecommerce_porder_item`) ";
		$requestStr.= "WHERE 1 ";
		$requestStr.= "AND `id`='$this->id' ";
		$request=request($requestStr,$link);
		if(!mysql_num_rows($request)) throw new ExceptionAnixEngineError(_("La ligne spécifiée n'existe pas.")."(".$this->id.")");
		while($item = mysql_fetch_object($request)){
			$this->idPOrder = $item->id_porder;
			$this->idProduct = $item->id_product;
			$this->storeRef = $item->ref_store;
			$this->supplierRef = $item->ref_supplier;
			$this->description = $item->description;
			$this->uprice = $item->uprice;
			$this->qty = $item->qty;
			$this->total = $this->uprice * $this->qty;
			$this->receivedQty = $item->received_qty;
			$this->inventoryOnOrderQty = $item->inventory_on_order_qty;
			$this->stockedQty = $item->stocked_qty;
			$this->updated = false;
		}
		mysql_close($link);
	}

	/**
	 * Saves the item into database
	 *
	 * @throws ExceptionAnixEngineError
	 *
	 */
	public function save(){
		if(!$this->idPOrder) throw new ExceptionAnixEngineError(_("L'identifiant de la commande d'achat n'est pas valide."));
		$link = dbConnect();
		if($this->updated){ //Updates or inserts only the items that needs to be.
			if($this->id){
				//UPDATE THE ITEM
				$requestStr = "";
				$requestStr.= "UPDATE `$this->TBL_ecommerce_porder_item` SET ";
				$requestStr.= "`id_product`='".$this->idProduct."', ";
				$requestStr.= "`ref_store`='".$this->storeRef."', ";
				$requestStr.= "`ref_supplier`='".$this->supplierRef."', ";
				$requestStr.= "`description`='".$this->description."', ";
				$requestStr.= "`uprice`='".$this->uprice."', ";
				$requestStr.= "`qty`='".$this->qty."', ";
				$requestStr.= "`received_qty`='".$this->receivedQty."', ";
				$requestStr.= "`inventory_on_order_qty`='".$this->inventoryOnOrderQty."', ";
				$requestStr.= "`stocked_qty`='".$this->stockedQty."' ";
				$requestStr.= "WHERE `id`='".$this->id."'";
				request($requestStr,$link);
			} else {
				//INSERT THE NEW ITEM
				$requestStr = "";
				$requestStr.= "INSERT INTO `$this->TBL_ecommerce_porder_item` (`id_porder`,`id_product`,`ref_store`,`ref_supplier`,`description`,`uprice`,`qty`,`received_qty`,`inventory_on_order_qty`,`stocked_qty`) VALUES ";
				$requestStr.= "(";
				$requestStr.= "'".$this->idPOrder."',";
				$requestStr.= "'".$this->idProduct."',";
				$requestStr.= "'".$this->storeRef."',";
				$requestStr.= "'".$this->supplierRef."',";
				$requestStr.= "'".$this->description."',";
				$requestStr.= "'".$this->uprice."',";
				$requestStr.= "'".$this->qty."',";
				$requestStr.= "'".$this->receivedQty."',";
				$requestStr.= "'".$this->inventoryOnOrderQty."',";
				$requestStr.= "'".$this->stockedQty."'";
				$requestStr.= ")";
				request($requestStr,$link);
				$this->id = mysql_insert_id($link);
			}
		}
		mysql_close($link);
	}

	public function delete(){
		if(!$this->id) throw new ExceptionAnixEngineError(_("Cette ligne ne peut être supprimée car elle n'a pas été insérée en base de données."));
		$this->qty=0;
		$this->receivedQty=0;
		$this->updateStock();

		$link = dbConnect();
		$requestStr = "DELETE FROM `$this->TBL_ecommerce_porder_item` WHERE `id`='$this->id'";
		request($requestStr,$link);
		$this->id=0;
		mysql_close($link);
	}

	public function getId(){
		return $this->id;
	}

	public function getIdProduct(){
		return $this->idProduct;
	}

	public function getStoreRef(){
		return $this->storeRef;
	}

	public function getSupplierRef(){
		return $this->supplierRef;
	}

	public function getDescription(){
		return $this->description;
	}

	public function getUprice(){
		return $this->uprice;
	}

	public function getQty(){
		return $this->qty;
	}

	public function getTotal(){
		return number_format($this->total,2,".","");
	}

	public function getReceivedQty(){
		return $this->receivedQty;
	}

	public function isUpdated(){
		return $this->updated;
	}

	/**
	 * Determines whether the inventory stock needs to be updated
	 *
	 * @return boolean
	 */
	public function isStockNeedsUpdate(){
		if(!$this->idProduct) return false;
		if($this->inventoryOnOrderQty!=$this->qty) return true;
		if($this->stockedQty!=$this->receivedQty) return true;
		return false;
	}

	public function isReceived(){
		return $this->receivedQty >= $this->qty;
	}

	public function setIdPOrder($id){
		if($this->idPOrder) throw new ExceptionAnixEngineError(_("L'identifiant de la commande d'achats a déjà été spécifié."));
		$this->idPOrder = $id;
	}

	public function setIdProduct($id){
		if($id!=$this->idProduct){
			$this->idProduct = $id;
			$this->updated=true;
		}
	}

	public function setStoreRef($ref){
		if(trim($ref)=="") throw new ExceptionAnixError(_("Veuillez entrer la référence du produit."));
		if($ref!=$this->storeRef){
			$this->storeRef = $ref;
			$this->updated=true;
		}
	}

	public function setSupplierRef($ref){
		if(trim($ref)=="") throw new ExceptionAnixError(_("Veuillez entrer la référence fournisseur."));
		if($ref!=$this->supplierRef){
			$this->supplierRef = trim($ref);
			$this->updated=true;
		}
	}

	public function setDescription($desc){
		if(trim($desc)=="") throw new ExceptionAnixError(_("Veuillez entrer la description du produit."));
		if($desc!=$this->description){
			$this->description = trim($desc);
			$this->updated=true;
		}
	}

	public function setUprice($uprice){
		if($uprice<0) throw new ExceptionAnixError(_("Le prix du produit ne peut être négatif."));
		if($uprice!=$this->uprice){
			$this->uprice = $uprice;
			$this->total = $this->uprice * $this->qty;
			$this->updated=true;
		}
	}

	public function setQty($qty){
		if($qty<0) throw new ExceptionAnixError(_("La quantité ne peut être inférieure à 0."));
		if($qty!=$this->qty){
			$this->qty = $qty;
			$this->total = $this->uprice * $this->qty;
			$this->updated=true;
		}
	}

	public function setReceivedQty($qty){
		if($qty<0) throw new ExceptionAnixError(_("La quantité reçue ne peut être inférieure à 0."));
		if($qty!=$this->receivedQty){
			$this->receivedQty = $qty;
			$this->updated=true;
		}
	}

	public function updateStock(){
		if(!$this->isStockNeedsUpdate()) return;
		$stockDiff = $this->receivedQty - $this->stockedQty;
		$onOrderDiff = $this->qty - $this->inventoryOnOrderQty;

		$link = dbConnect();
		$requestStr = "";
		$requestStr.= "UPDATE `$this->TBL_catalogue_products` SET ";
		$requestStr.="`stock`=`stock`+'$stockDiff',";
		$requestStr.="`on_order_qty`=`stock`+'$onOrderDiff' ";
		$requestStr.="WHERE `id`='$this->idProduct'";

		request($requestStr,$link);
		mysql_close($link);

		$this->stockedQty+=$stockDiff;
		$this->inventoryOnOrderQty+=$onOrderDiff;
		$this->updated=true;
		$this->save();


	}

}

?>