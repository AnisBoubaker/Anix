<?php

class ordersList{
	var $idClient;
	var $orders;

	/**
	 * Constructor: $status must be either "current" or "history". "current" is the default value.
	 *
	 * @param Integer $idClient
	 * @param String $status
	 * @param Integer $link
	 * @return ordersList
	 */
	function ordersList($idClient, $status="current" , $link=0){
		global $TBL_ecommerce_order,$TBL_ecommerce_shipping_transporters, $TBL_ecommerce_info_transporter;
		global $used_language_id;
		$this->orders=array();
  		$this->idClient = $idClient;
		if($this->idClient){
			if(!$link) $insideLink = dbConnect();
  			else $insideLink=$link;
	  		//load orders from database
			$requestStr = "
				SELECT `$TBL_ecommerce_order`.*,
					   `$TBL_ecommerce_info_transporter`.`name` as transporter,
					   `$TBL_ecommerce_shipping_transporters`.`tracking_url`
				FROM `$TBL_ecommerce_order`
				LEFT JOIN `$TBL_ecommerce_shipping_transporters` ON (`$TBL_ecommerce_order`.`id_transporter`=`$TBL_ecommerce_shipping_transporters`.`id`)
				LEFT JOIN `$TBL_ecommerce_info_transporter` ON (`$TBL_ecommerce_shipping_transporters`.`id`=`$TBL_ecommerce_info_transporter`.`id_transporter` AND `$TBL_ecommerce_info_transporter`.`id_language`='$used_language_id')
				WHERE `id_client`='".$this->idClient."'";
			switch($status){
				case "current":$requestStr.=" AND `status` IN ('stand by', 'ordered') AND `shipping_date`='0000-00-00'";break;
				case "history":$requestStr.=" AND (`status` IN ('invoiced', 'payed') OR (`status` IN ('stand by', 'ordered') AND `shipping_date`!='0000-00-00'))";break;
				default:$requestStr.=" AND `status` IN ('stand by', 'ordered')";
			}
			$requestStr.=" ORDER BY `order_date` DESC,`id` DESC";
			$request=request($requestStr,$insideLink);
			while($order = mysql_fetch_object($request)){
				$this->orders[] = $order;
			}
			if(!$link) mysql_close($insideLink);
		}
	}
}

?>