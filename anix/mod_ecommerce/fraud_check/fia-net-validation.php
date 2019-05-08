<?php
	require_once("../../config.php");
	$link = dbConnect();
	$method="fia-net";

	$idOrder = $_GET["idOrder"];

    //$strFileName  = "https://secure.fia-net.com/fscreener/engine/get_validation.cgi?SiteID=6616&Pwd=1d3ut%26%40m&RefID=$idOrder&Mode=mini" ;
    $strFileName  = str_replace("%%ID_ORDER%%",$idOrder,$ECOMMERCE_fraudcheck_methods["fia-net"]["validation_url"]);
    $file      = fopen($strFileName, "r");
    $chaineFluxXMLretourEval = stream_get_contents($file);

    $xml=simplexml_load_string($chaineFluxXMLretourEval);

    if($xml===false) die("Lecture des donnees impossible: SAC indisponible.");

	$baltran=$xml->transaction;
	$balanalyse=$baltran->analyse;
	$baleval=$balanalyse->eval;
	$balclass=$balanalyse->classement;


	//check the refid
	if($xml["refid"]!=$idOrder){//Unknown return code
		die ("Le numero de commande obtenu ne correspond pas au numero de commande demande.");
	}

	//check the return code
	if($xml["retour"]=="absente"){//RefID does not exist => Delete DB logs & Resendit
		request("DELETE FROM `$TBL_ecommerce_fraud_check` WHERE `id_order`='$idOrder' && `method`='$method'",$link);
		mysql_close($link);
		Header("Location: ./fia-net.php?idOrder=$idOrder");
  		exit();
	}

	if($xml["retour"]!="trouvee"){//Unknown return code
		die ("Code de retour inconnu.");
	}

	//check the status
	if($baltran["avancement"]=="error"){//Last transfert was erronous, resend
		request("DELETE FROM `$TBL_ecommerce_fraud_check` WHERE `id_order`='$idOrder' && `method`='$method'",$link);
		mysql_close($link);
		Header("Location: ./fia-net.php?idOrder=$idOrder");
  		exit();
	}



	//load the last fia-net log for this order
	$request = request("SELECT * FROM `$TBL_ecommerce_fraud_check` WHERE `id_order`='$idOrder' && `method`='$method' ORDER BY `check_date` DESC LIMIT 1",$link);
	$log = mysql_fetch_object($request);

	//convert the spplus eval to Anix's eval
	if($baltran["avancement"]=="encours") $anixEval = $ECOMMERCE_faud_level_awaiting;
	elseif($baleval>0) $anixEval = $ECOMMERCE_faud_level_low;
	elseif($baleval==0) $anixEval = $ECOMMERCE_faud_level_alert;
	else $anixEval = $ECOMMERCE_faud_level_medium;

	if(!isset($baleval["info"])) { $baleval["info"]=""; }

	$evalChanged=false;

	if($anixEval!=$log->result || $baleval["info"]!=$log->info){//Something changed => insert new log and update the order
		$evalChanged=true;
	}
	//Insert new log
	$requestStr="
		INSERT INTO `$TBL_ecommerce_fraud_check` (`id_order`,`method`,`result`,`info`,`check_date`)
		VALUES ('$idOrder','$method','$anixEval','".addslashes($baleval["info"])."', NOW())
	";
	request($requestStr,$link);
	//Update the order even if it has not changed => to reflect the last check on the page....
	$requestStr ="UPDATE `$TBL_ecommerce_order` SET ";
	$requestStr.="`fraud_check_mode`='$method', ";
	$requestStr.="`fraud_check_result`='$anixEval', ";
	$requestStr.="`fraud_check_info`='".addslashes($baleval["info"])."', ";
	$requestStr.="`fraud_check_date`=NOW() ";
	$requestStr.="WHERE `id`='$idOrder' ";
	request($requestStr,$link);


?>

<?php

	include("./html_header.php");

	echo "<br /><br />";
	if($evalChanged) echo "<p style='color:0000FF;text-align:center;'>L'evaluation a changé. La mise à jour a été effectuée.</p>";
	else  echo "<p style='color:0000FF;text-align:center;'>L'evaluation est identique.</p>";

	echo " - Evaluation = ".$baleval."<br />" ;
	if(isset($baleval["validation"])) echo " - Validation = ".$baleval["validation"]."<br />" ;
	echo " - Infos = ".$baleval["info"]."<br />" ;

	/*echo "REFID = ".$xml["refid"]."<br />" ;
	echo " - RETOUR = ".$xml["retour"]."<br />" ;
	echo " - COUNT = ".$xml["count"]."<br />" ;
	echo " - AVANCEMENT = ".$baltran["avancement"]."<br />" ;

	// detail à indiquer

	echo " - Eval = ".$baleval."<br />" ;
	echo " - LibEval = ".$baleval["validation"]."<br />" ;
	echo " - Info = ".$baleval["info"]."<br />" ;

	echo " - Classement = ".$balclass ;*/
	if($evalChanged){
	?>
		<script type='text/javascript'>
			window.opener.hideFraud();
		</script>
	<?php
	}

?>


<?php
	mysql_close($link);
	include("html_footer.php");
?>
