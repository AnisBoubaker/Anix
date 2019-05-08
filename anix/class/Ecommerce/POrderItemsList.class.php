<?php
/**
 * Iterator containing the line items of a purchase order.
 *
 * @version 1.0
 * @author Anis Boubaker
 * @copyright CIBAXION Inc.
 *
 */
class EcommercePOrderItemsList implements Iterator {

	/**
	 * Array containing the list of items
	 *
	 * @var array
	 */
	private $items;
	/**
	 * Purchase order ID
	 *
	 * @var int
	 */
	private $idPOrder;
	/**
	 * Holds the position of the iterator inside the $items array
	 *
	 * @var int
	 */
	private $pointer;

	//DB TABLES
	private $TBL_ecommerce_porder_item;

	function __construct($idPOrder=0){
		global $TBL_ecommerce_porder_item;
		$this->TBL_ecommerce_porder_item = $TBL_ecommerce_porder_item;
		$this->idPOrder = $idPOrder;
		$this->pointer=0;
		$this->items=array();
		if($this->idPOrder) $this->load();
	}

	/**
	 * Loads the items from database based on the purchase order ID (idPOrder)
	 *
	 * @throws ExceptionAnixEngineError
	 */
	private function load(){
		if(!$this->idPOrder) throw new ExceptionAnixEngineError(_("L'identifiant de la commande d'achat n'est pas valide."));
		$link = dbConnect();
		$requestStr = "SELECT `id` ";
		$requestStr.= "FROM (`$this->TBL_ecommerce_porder_item`) ";
		$requestStr.= "WHERE 1 ";
		$requestStr.= "AND `id_porder`='$this->idPOrder' ";
		$request=request($requestStr,$link);

		while($item = mysql_fetch_object($request)){
			$this->items[] = new EcommercePOrderItem($item->id);
		}
		mysql_close($link);
	}

	/**
	 * Saves the items into database
	 *
	 * @throws ExceptionAnixEngineError
	 *
	 */
	public function save(){
		if(!$this->idPOrder) throw new ExceptionAnixEngineError(_("L'identifiant de la commande d'achat n'est pas valide."));
		$link = dbConnect();
		foreach($this->items as $item){
			$item->save();
		}
		mysql_close($link);
	}

	/**
	 * Add a new line item to the items list
	 *
	 * @param EcommercePOrderItem $item
	 */
	public function addItem($item){
		if(!is_a($item,"EcommercePOrderItem")) throw new ExceptionAnixEngineError(_("La nouvelle ligne à ajouter n'est pas valide."));
		$this->items[]=$item;
	}

	public function rewind(){
		$this->pointer=0;
	}
	/**
	 * Return the current item inside the iterator
	 *
	 * @return EcommercePOrderItem
	 */
	public function current(){
		return $this->items[$this->pointer];
	}
	/**
	 * Returns the current position insite the Iterator
	 *
	 * @return int
	 */
	public function key(){
		return $this->pointer;
	}

	/**
	 * Moves the Iterator pointer to the next position
	 *
	 */
	public function next(){
		$this->pointer++;
	}

	/**
	 * Moves the Iterator pointer to the previous position
	 *
	 */
	public function previous(){
		if($this->pointer>0) $this->pointer--;
	}

	/**
	 * Checks is the current Iterator pointer position relates to a valid element
	 *
	 * @return boolean
	 */
	public function valid(){
		return ($this->pointer>=0 && $this->pointer<count($this->items));
	}

	/**
	 * Compute the total amount from all the items
	 *
	 * @return int
	 */
	public function getTotalAmount(){
		$total = 0;
		foreach($this->items as $item){
			$total+=$item->getTotal();
		}

		return $total;
	}

	/**
	 * Checks if all the items has been received.
	 *
	 * @return boolean
	 */
	public function isAllItemsReceived(){
		foreach ($this->items as $item){
			if(!$item->isReceived()) return false;
		}
		return true;
	}

	/**
	 * Determines whether the inventory stock needs to be updated regarding the items in this list.
	 *
	 * @return boolean
	 */
	public function isStockNeedsUpdate(){
		foreach($this->items as $item){
			if($item->isStockNeedsUpdate()) return true;
		}
		return false;
	}

	public function updateStock(){
		foreach ($this->items as $item) $item->updateStock();
	}

	/**
	 * Sets the
	 *
	 * @param unknown_type $id
	 * @throws ExceptionAnixEngineError
	 */
	public function setIdPOrder($id){
		if($this->idPOrder) throw new ExceptionAnixEngineError(_("L'identifiant de la commande d'achats a déjà été spécifié."));
		$this->idPOrder = $id;
		foreach ($this->items as $item) {
			$item->setIdPOrder($id);
		}
	}

	public function deleteItem($id){
		foreach($this->items as $position => $item){
			if($item->getId()==$id){
				$item->delete();
				unset($this->items[$position]);
				//re-index the array
				$this->items=array_values($this->items);
				return;
			}
		}
		throw new ExceptionAnixEngineError(_("La ligne à supprimer ne fait pas partie de la commande."));
	}

	public function deleteAll(){
		foreach ($this->items as $item){
			$item->delete();
		}
		$this->items=array();
	}
}
?>