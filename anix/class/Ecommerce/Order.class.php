<?php
class Order{
	private $id=0;
	private $idClient=0;
	private $mailingAddress="";
	private $billingAddress="";
	private $orderDate="";
	private $deliveryDate=""; //Expected shipping date
	private $deliveryDelay=0; //Delay needed by the transporter
	private $receptionDate=""; //Expected reception date (delivery delay + delivery date)
	private $subtotal=0;
	private $depositRequested=0;
	private $depositAmount=0;
	private $status="";
	private $idInvoice = 0;
	private $idTransporter=0;
	private $shippingDate=""; //Date at which the items has been shipped
	private $tracking = "";
	private $fraudCheckMode = "";
	private $fraudCheckResult =0;
	private $fraudCheckInfo = "";
	private $fraudCheckDate = "";
	public $fraudCheckFianetDone = false;

	private $TBL_ecommerce_order="";

	public function __construct($idOrder=0){
		global $TBL_ecommerce_order;
		$this->TBL_ecommerce_order = $TBL_ecommerce_order;
		$this->id = $idOrder;
		if($this->id) $this->load();
	}

	private function load(){
		if(!$this->id) throw new ExceptionAnixEngineError(_("Le numéro de commande n'a pas été spécifié."));
		$dbLink = dbConnect();
		$requestStr = "SELECT * FROM `$this->TBL_ecommerce_order` WHERE `id`='$this->id'";
		$request = request($requestStr,$dbLink);
		if(!mysql_num_rows($request)) throw new ExceptionAnixEngineError(_("Commande inexistante"));
		$order = mysql_fetch_object($request);
		$this->idClient = $order->id_client;
		$this->mailingAddress = $order->mailing_address;
		$this->billingAddress = $order->billing_address;
		$this->orderDate = $order->order_date;
		$this->deliveryDate = $order->delivery_date;
		$this->mailingAddress = $order->mailing_address;
		$this->deliveryDelay = $order->delivery_delay;
		$this->receptionDate = $order->reception_date;
	}
}
?>