<?php
	require_once("../../config.php");
	$link = dbConnect();
	//plugin config
	$method="fia-net";
	if(!isset($_GET["idOrder"])) die ("Commande non specifiee...");
	$idOrder = $_GET["idOrder"];
	//load the order
	$request = request("SELECT * FROM `$TBL_ecommerce_order` WHERE `id`='$idOrder'",$link);
	if(!mysql_num_rows($request)) die ("Commande inexistante...");
	$order = mysql_fetch_object($request);
	if($order->status!='ordered' && $order->status!='invoiced' && $order->status!='payed') die ("Etat de la commande non valide pour verification...");
	if($order->xml_address=="") die ("Erreur: Cette commande ne peut etre vérifiée avec FIA-NET car elle est antérieure à la date de mise en place du système de vérification de commandes sous Anix.");

	//check if we already did a fia-net check
	$request = request("SELECT id FROM `$TBL_ecommerce_fraud_check` WHERE `id_order`='$idOrder' AND `method`='$method'",$link);
	if(mysql_num_rows($request)) {//We already did a fia-net check
		//redirect to fia-net_validation.php
		mysql_close($link);
		Header("Location: ./fia-net-validation.php?idOrder=$idOrder");
  		exit();
	}
?>
<?php
	//GENERATE XML FLUX

	//Call back url parameters
	$paraCallBack = "<?xml version='1.0' encoding='UTF8'?>";
	$paraCallBack.= "<ParamCBack>";
	$paraCallBack.= "<obj>";
	$paraCallBack.= "<name>idOrder</name>";
	$paraCallBack.= "<value>$idOrder</value>";
	$paraCallBack.= "</obj>";
	$paraCallBack.= "</ParamCBack>";

	//control call back
	$controlCallBack = "<?xml version='1.0' encoding='utf-8'?>";
	$controlCallBack.= "<control>";
	$controlCallBack.= $order->xml_address;
	$controlCallBack.= "<infocommande>";
	$controlCallBack.= "<refid>$idOrder</refid>";
	$controlCallBack.= "<siteid>".$ECOMMERCE_fraudcheck_methods[$method]["site_id"]."</siteid>";
	$controlCallBack.= "<montant devise='EUR'>".$order->subtotal."</montant>";
	$controlCallBack.= "<ip timestamp='".$order->order_timestamp."'>".$order->remote_ip."</ip>";
		//transporter
		switch($order->id_transporter){
			case 1:$transporterName="Colissimo";$transporterType=4;$transporterSpeed=2;break;
			case 2:$transporterName="Numeridog Paris 13e";$transporterType=1;$transporterSpeed=2;break;
			case 3:$transporterName="Distingo";$transporterType=4;$transporterSpeed=2;break;
			case 4:$transporterName="Distingo";$transporterType=4;$transporterSpeed=2;break;
			case 5:$transporterName="Colissimo";$transporterType=4;$transporterSpeed=2;break;
			case 6:$transporterName="Colissimo";$transporterType=4;$transporterSpeed=2;break;
			case 8:$transporterName="Drop-Ship";$transporterType=4;$transporterSpeed=2;break;
			default:$transporterName="Autre";$transporterType=4;$transporterSpeed=2;
		}
		$controlCallBack.= "<transport>";
			$controlCallBack.= "<type>$transporterType</type>";
			$controlCallBack.= "<rapidite>$transporterSpeed</rapidite>";
			$controlCallBack.= "<nom>$transporterName</nom>";
		$controlCallBack.= "</transport>";
		//products listing
		$request = request("SELECT * FROM `$TBL_ecommerce_invoice_item` WHERE `id_order`='$idOrder'",$link);
		$itemsXML = "";
		$nbProducts = 0;
		while($item = mysql_fetch_object($request)){
			if(strpos($item->reference,"TRANSP")===false && strpos($item->reference,"ASS_TRP")===false && strpos($item->reference,"ECOTAXE")===false){
				$itemsXML.="<produit type='15' ref='".$item->reference."' nb='".intval($item->qty)."'>".str_replace("\""," ",unhtmlentities($item->description))."</produit>";
				$nbProducts+=$item->qty;
			}
		}
		$controlCallBack.= "<list nbproduit='$nbProducts'>$itemsXML</list>";
	$controlCallBack.= "</infocommande>";
	//paiement
	$controlCallBack.= "<paiement>";
		$request = request("SELECT `$TBL_ecommerce_payment`.`id`, `$TBL_ecommerce_payment`.`id_payment_type` FROM `$TBL_ecommerce_payment`,`$TBL_ecommerce_payment_allocation` WHERE `$TBL_ecommerce_payment_allocation`.`id_order`='$idOrder' AND `$TBL_ecommerce_payment_allocation`.`id_payment`=`$TBL_ecommerce_payment`.`id`",$link);
		if(!mysql_num_rows($request))  die ("Paiement introuvable.");
		$payment = mysql_fetch_object($request);
		switch($payment->id_payment_type){
			case 4: $paymentType="Carte";break;
			case 7: $paymentType="Carte";break;
			default: $paymentType="Autre";
		}
		$controlCallBack.= "<type>$paymentType</type>";
	$controlCallBack.= "</paiement>";
	$controlCallBack.= "</control>";
	//sanitize
	$controlCallBack = trim($controlCallBack);
	$controlCallBack = str_replace("\\'","'",$controlCallBack);
	$controlCallBack = str_replace("\\\"","\"",$controlCallBack);
	$controlCallBack = str_replace("\\\\","\\",$controlCallBack);
	$controlCallBack = str_replace("\t","",$controlCallBack);
	$controlCallBack = str_replace("\n","",$controlCallBack);
	$controlCallBack = str_replace("\r","",$controlCallBack);

	//POST THE FORM WITH cURL
	$crl = curl_init();
    $url = $ECOMMERCE_fraudcheck_methods[$method]["post_url"];
    curl_setopt($crl, CURLOPT_URL, $url);
    curl_setopt($crl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($crl, CURLOPT_POST, 1);
    curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, 0);
    $post_data = array();
    $post_data['siteid'] = $ECOMMERCE_fraudcheck_methods[$method]["site_id"];
    $post_data['controlcallback'] = urlencode($controlCallBack);
    $post_data['urlcallback'] = urlencode("http://www.numeridog.com");
    $post_data['paracallback'] = urlencode($paraCallBack);

    $o="";
	foreach ($post_data as $k=>$v){
		$o.= "$k=$v&";
	}
	$post_data=substr($o,0,-1);

    curl_setopt ($crl, CURLOPT_POSTFIELDS, $post_data);

    $result=curl_exec($crl);

    if(!$result) die ("Transmission des donnees impossible: SAC indisponible");

	curl_close($crl);


	//INSERT THE LOG IN DB
	$requestStr="
		INSERT INTO `$TBL_ecommerce_fraud_check` (`id_order`,`method`,`result`,`info`,`check_date`)
		VALUES ('$idOrder','$method','-1','Interrogation Fia-net: informations de la commande envoyées', NOW())
	";
	request($requestStr,$link);

	//REDIRECT TO FIA-NET-VALIDATION
	mysql_close($link);
	Header("Location: ./fia-net-validation.php?idOrder=$idOrder");
	exit();
?>
