<?php
/**
 * Iterator containing a list of purchase orders of a given supplier.
 *
 * @version 1.0
 * @author Anis Boubaker
 * @copyright CIBAXION Inc.
 *
 */
class EcommercePOrdersList implements Iterator {

	/**
	 * Array containing the orders list
	 *
	 * @var array
	 */
	private $orders;
	/**
	 * Supplier ID
	 *
	 * @var int
	 */
	private $idSupplier;
	/**
	 * Holds the position of the iterator inside the $orders array
	 *
	 * @var int
	 */
	private $pointer;

	//DB TABLES
	private $TBL_ecommerce_porder;

	/**
	 * Creates the purchase orders list for a given supplier.
	 *
	 * @param int $idSupplier
	 * @throws ExceptionAnixEngineError
	 */
	function __construct($idSupplier){
		global $TBL_ecommerce_porder;
		$this->TBL_ecommerce_porder = $TBL_ecommerce_porder;

		if(!$idSupplier) throw new ExceptionAnixEngineError(_("L'identifiant du fournissieur n'est pas valide."));
		$this->idSupplier = $idSupplier;
		$this->pointer=0;
		$this->load();
	}

	/**
	 * Loads the orders from database
	 *
	 * @throws ExceptionAnixEngineError
	 */
	private function load(){
		if(!$this->idSupplier) throw new ExceptionAnixEngineError(_("L'identifiant du fournisseur n'est pas valide."));
		$link = dbConnect();
		$requestStr = "SELECT `id` ";
		$requestStr.= "FROM (`$this->TBL_ecommerce_porder`) ";
		$requestStr.= "WHERE 1 ";
		$requestStr.= "AND `id_supplier`='$this->idSupplier' ";
		$request=request($requestStr,$link);
		$count = 0;
		while($order = mysql_fetch_object($request)){
			$this->orders[$count] = new EcommercePOrder($order->id);
			$count++;
		}
		mysql_close($link);
	}

	public function rewind(){
		$this->pointer=0;
	}
	/**
	 * Return the current purchase order inside the iterator
	 *
	 * @return EcommercePOrder
	 */
	public function current(){
		return $this->orders[$this->pointer];
	}
	/**
	 * Returns the current position inside the Iterator
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
		return ($this->pointer>=0 && $this->pointer<count($this->orders));
	}

	/**
	 * Return the number of the orders contained in the object
	 *
	 * @return int
	 */
	public function getNbOrders(){
		return count($this->orders);
	}

}
?>