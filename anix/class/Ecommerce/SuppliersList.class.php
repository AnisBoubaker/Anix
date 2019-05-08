<?php
class EcommerceSuppliersList implements Iterator {
	/**
	 * Array containing the list of suppliers
	 *
	 * @var array
	 */
	private $suppliers=array();
	/**
	 * Position inside the iterator
	 *
	 * @var int
	 */
	private $pointer=0;

	//DB TABLES
	private $TBL_ecommerce_supplier;

	function __construct(){
		global $TBL_ecommerce_supplier;
		$this->TBL_ecommerce_supplier = $TBL_ecommerce_supplier;
		$this->pointer=0;
		$this->suppliers=array();
		$this->load();
	}

	private function load(){
		$link = dbConnect();
		$requestStr = "SELECT `$this->TBL_ecommerce_supplier`.`id` ";
		$requestStr.= "FROM (`$this->TBL_ecommerce_supplier`) ";
		$requestStr.= "WHERE 1 ";
		$requestStr.= "ORDER BY `$this->TBL_ecommerce_supplier`.`name` ";
		$request=request($requestStr,$link);
		$count = 0;
		while($supplier = mysql_fetch_object($request)){
			$this->suppliers[$count] = new EcommerceSupplier($supplier->id);
			$count++;
		}
		mysql_close($link);
	}

	public function rewind(){
		$this->pointer=0;
	}
	/**
	 * Return the current supplier inside the iterator
	 *
	 * @return EcommerceSupplier
	 */
	public function current(){
		return $this->suppliers[$this->pointer];
	}

	/**
	 * Return the current position inside the iterator
	 *
	 * @return int
	 */
	public function key(){
		return $this->pointer;
	}

	/**
	 * Moves the iterator to the next position
	 *
	 */
	public function next(){
		$this->pointer++;
	}

	/**
	 * Moves the iterator to the previous position
	 *
	 */
	public function previous(){
		if($this->pointer>0) $this->pointer--;
	}

	/**
	 * Checks if the current position refers to a valid element
	 *
	 * @return boolean
	 */
	public function valid(){
		return ($this->pointer>=0 && $this->pointer<count($this->suppliers));
	}
}
?>