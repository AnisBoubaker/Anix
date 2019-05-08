<?php
class POrder{
	/**
	 * Purchase order ID
	 *
	 * @var int
	 */
	private $id=0;
	/**
	 * Supplier ID
	 *
	 * @var int
	 */
	private $idSupplier=0;
	/**
	 * True if the purchase order has been sent to the supplier
	 *
	 * @var boolean
	 */
	private $orderSent = false;
	/**
	 * Order creation date
	 *
	 * @var string
	 */
	private $orderDate = "";
	private $sentDate = "";
	private $expectedReceptionDate = "";

	private $receptionDate = "";
	/**
	 * Order status: created, sent or received
	 *
	 * @var string
	 */
	private $status = "";
	/**
	 * Purchase order subtotal
	 *
	 * @var int
	 */
	private $subtotal = 0;
	/**
	 * Items list of the purchase Order
	 *
	 * @var EcommercePOrderItemsList
	 */
	private $items=null;

	private $TBL_ecommerce_porder="";

	/**
	 * Create a new purchase order from the supplier with ID $idSupplier.
	 * If the optional $id is provided, the purchase order will be loaded from database.
	 *
	 * @param int $idSupplier
	 * @param int $id
	 * @throws ExceptionAnixEngineError
	 */
	public function __construct($id=0, $idSupplier=0){
		global $TBL_ecommerce_porder;

		$this->TBL_ecommerce_porder = $TBL_ecommerce_porder;
		$this->idSupplier = $idSupplier;
		$this->id=$id;

		if(!$this->id && !$this->idSupplier) throw new ExceptionAnixEngineError(_("L'identifiant du fournisseur n'est pas valide."));

		if($this->id) $this->load();
	}

	/**
	 * Load a purchase order from database
	 *
	 * @throws ExceptionAnixError
	 */
	private function load(){
		if(!$this->id) throw new ExceptionAnixEngineError(_("L'identifiant de la commande n'est pas valide."));
		$link = dbConnect();
		$requestStr = "SELECT * FROM `$this->TBL_ecommerce_porder` WHERE `id`='$this->id'";
		$request = request($requestStr,$link);
		if(!mysql_num_rows($request)){
			$this->id = 0;
			mysql_close($link);
			throw new ExceptionAnixError(_("Cette commande d'achat n'existe pas."));
		}
		$order = mysql_fetch_object($request);
		$this->idSupplier = $order->id_supplier;
		$this->orderDate = $order->order_date;
		$this->orderSent = ($order->order_sent=="Y");
		$this->sentDate = $order->sent_date;
		$this->expectedReceptionDate = $order->expected_reception_date;
		$this->receptionDate = $order->reception_date;
		$this->status = $order->status;
		$this->subtotal = $order->subtotal;
	}

	/**
	 * Save the purchase order into database.
	 *
	 */
	public function save(){
		$this->updateOrderInfos();
		$link = dbConnect();

		if($this->id){
			$requestStr = "";
			$requestStr.= "UPDATE `$this->TBL_ecommerce_porder` SET ";
			$requestStr.= "`order_date`='$this->orderDate', ";
			$requestStr.= "`order_sent`='".($this->orderSent?"Y":"N")."', ";
			$requestStr.= "`sent_date`='$this->sentDate', ";
			$requestStr.= "`expected_reception_date`='$this->expectedReceptionDate', ";
			$requestStr.= "`reception_date`='$this->receptionDate', ";
			$requestStr.= "`subtotal`='$this->subtotal', ";
			$requestStr.= "`status`='$this->status' ";
			$requestStr.= "WHERE `id`='$this->id' ";
			request($requestStr,$link);
		} else {
			$requestStr = "";
			$requestStr.= "INSERT INTO `$this->TBL_ecommerce_porder` (`id_supplier`,`order_date`,`order_sent`,`sent_date`,`expected_reception_date`,`reception_date`,`subtotal`,`status`) VALUES ";
			$requestStr.= "(";
			$requestStr.= "'$this->idSupplier',";
			$requestStr.= "'$this->orderDate',";
			$requestStr.= "'".($this->orderSent?"Y":"N")."',";
			$requestStr.= "'$this->sentDate',";
			$requestStr.= "'$this->expectedReceptionDate',";
			$requestStr.= "'$this->receptionDate',";
			$requestStr.= "'$this->subtotal',";
			$requestStr.= "'$this->status' ";
			$requestStr.= ")";
			request($requestStr,$link);
			$this->id = mysql_insert_id();
			$this->items->setIdPOrder($this->id);
		}
		mysql_close($link);

		$this->items->save();
	}

	public function delete(){
		if(!$this->id) throw new ExceptionAnixEngineError(_("Cette commande d'achat ne peut être supprimée car elle n'existe pas en base de données."));
		if(!$this->items) $this->loadItems();
		$this->items->deleteAll();
		$link = dbConnect();
		$requestStr = "DELETE FROM `$this->TBL_ecommerce_porder` WHERE `id`='$this->id'";
		request($requestStr,$link);
		$this->id=0;
	}

	/**
	 * Updates the order subtotal and status.
	 *
	 */
	private function updateOrderInfos(){
		if(!$this->items) $this->loadItems();
		$this->subtotal = $this->items->getTotalAmount();
		$itemsReceived = $this->items->isAllItemsReceived();
		if($this->orderSent && $itemsReceived) $this->status="received";
		elseif($this->orderSent) $this->status="ordered";
		else $this->status="created";
	}

	/**
	 * Get the Purchase Order ID
	 *
	 * @return int
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Get the supplier ID
	 *
	 * @return int
	 */
	public function getSupplierId(){
		return $this->idSupplier;
	}

	/**
	 * Get the date the purchase order has created sent on
	 *
	 * @return string
	 */
	public function getOrderDate(){
		return $this->orderDate;
	}

	/**
	 * Returns true if the order has been sent
	 *
	 * @return boolean
	 */
	public function isOrderSent(){
		return $this->orderSent;
	}

	/**
	 * Get the date the purchase order has been sent on
	 *
	 * @return string
	 */
	public function getSentDate(){
		return $this->sentDate;
	}

	/**
	 * Get the expected reception date of the purchase order
	 *
	 * @return string
	 */
	public function getExpectedReceptionDate(){
		return $this->expectedReceptionDate;
	}

	/**
	 * Get the date the purchase order has been received on
	 *
	 * @return string
	 */
	public function getReceptionDate(){
		return $this->receptionDate;
	}

	/**
	 * Get the date the purchase order status
	 *
	 * @return string
	 */
	public function getStatus(){
		return $this->status;
	}

	/**
	 * Returns the list of line items of the purchase order
	 *
	 * @return EcommercePOrderItemsList
	 */
	public function getItems(){
		if(!$this->items) throw new ExceptionAnixEngineError(_("Les lignes de la commande n'ont pas été chargés."));
		return $this->items;
	}

	/**
	 * Get the date the purchase order subtotal amount
	 *
	 * @return int
	 */
	public function getSubtotal(){
		return $this->subtotal;
	}

	/**
	 * Determines whether the inventory stock needs to be updated
	 *
	 * @return boolean
	 */
	public function isStockNeedsUpdate(){
		if($this->status=="created") return false;
		if(!$this->items) $this->loadItems();
		return $this->items->isStockNeedsUpdate();
	}

	public function updateStock(){
		if(!$this->items) $this->loadItems();
		$this->items->updateStock();
	}

	public function setOrderDate($date){
		$this->orderDate = $date;
	}

	public function setOrderSent($sent){
		$this->orderSent = $sent;
	}

	public function setSentDate($date){
		if($date<$this->orderDate) throw new ExceptionAnixError(_("La date d'envoi de la commande ne peut-être antérieure à la date de la commande."));
		$this->sentDate = $date;
	}

	public function setExpectedReceptionDate($date){
		if($date<$this->orderDate) throw new ExceptionAnixError(_("La date prévue de réception ne peut-être antérieure à la date de la commande."));
		$this->expectedReceptionDate=$date;
	}



	/**
	 * Loads the order line items (into $this->items)
	 *
	 */
	public function loadItems(){
		if($this->id) $this->items = new EcommercePOrderItemsList($this->id);
		else $this->items = new EcommercePOrderItemsList();
		return $this->items;
	}

	/**
	 * Add a new line item to the order
	 *
	 * @param EcommercePOrderItem $item
	 */
	public function addItem($item){
		if(!is_a($item,"EcommercePOrderItem")) throw new ExceptionAnixEngineError(_("La nouvelle ligne à ajouter n'est pas valide."));
		if(!$this->items) $this->loadItems();
		$this->items->addItem($item);
	}
}
?>