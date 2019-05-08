<?php
	require_once("../../config.php");
	if(!isset($_GET["idOrder"])) die(_("Numéro de commande non spécifié."));
	$idOrder=$_GET["idOrder"];
	$link=dbConnect();
?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo _("Historique: Evaluation de fraude"); ?></title>
</head>
<body>
<h3 style='text-align:center;'>Historique d'évaluation: Commande #<?php echo $idOrder; ?></h3><br /><br />
<?php
	$request=request("SELECT * FROM `$TBL_ecommerce_fraud_check` WHERE `id_order`='$idOrder' ORDER BY `check_date` DESC",$link);
	if(mysql_num_rows($request)){
		echo "<table width='100%' border='1px'>";
		echo "<tr>";
			echo "<td><b>"._("Date")."</b></td>";
			echo "<td><b>"._("Evaluation")."</b></td>";
			echo "<td><b>"._("Méthode")."</b></td>";
			echo "<td><b>"._("Infos")."</b></td>";
		echo "</tr>";
		while($log=mysql_fetch_object($request)){
			echo "<tr>";
			echo "<td nowrap='nowrap'>".$log->check_date."</td>";
			echo "<td nowrap='nowrap'>";
				if($log->result==$ECOMMERCE_faud_level_awaiting) echo "<img src='../../images/fraud_level_awaiting.jpg' alt='"._("Niveau de fraude: ÉVALUATION EN COURS")."' title='"._("Niveau de fraude: ÉVALUATION EN COURS")."' style='vertical-align:middle;' /> "._("En cours");
	          	elseif($log->result<=$ECOMMERCE_faud_level_low) echo "<img src='../../images/fraud_level_low.jpg' alt='"._("Niveau de fraude: BAS")."' title='"._("Niveau de fraude: BAS")."' style='vertical-align:middle;' /> "._("Niveau de fraude: BAS");
	          	elseif($log->result<=$ECOMMERCE_faud_level_medium) echo "<img src='../../images/fraud_level_medium.jpg' alt='"._("Niveau de fraude: MOYEN")."' title='"._("Niveau de fraude: MOYEN")."' style='vertical-align:middle;' /> "._("Niveau de fraude: MOYEN");
	          	elseif($log->result<=$ECOMMERCE_faud_level_high) echo "<img src='../../images/fraud_level_high.jpg' alt='"._("Niveau de fraude: ÉLEVÉ")."' title='"._("Niveau de fraude: ÉLEVÉ")."' style='vertical-align:middle;' /> "._("Niveau de fraude: ÉLEVÉ");
	          	elseif($log->result<=$ECOMMERCE_faud_level_alert) echo "<img src='../../images/fraud_level_alert.jpg' alt='"._("Niveau de fraude: CRITIQUE")."' title='"._("Niveau de fraude: CRITIQUE")."' style='vertical-align:middle;' /> "._("Niveau de fraude: CRITIQUE");
			echo "</td>";
			echo "<td nowrap='nowrap'>";
				if(isset($ECOMMERCE_fraudcheck_methods[$log->method])) echo $ECOMMERCE_fraudcheck_methods[$log->method]["name"];
				else echo _("Inconnue");
			echo "</td>";
			echo "<td>".$log->info."</td>";
			echo "</tr>";
		}
		echo "</table>";
	} else {
		echo "<p style='text-align:center;'><i>"._("Aucune évaluation pour cette commande")."</i></p>";
	}
	mysql_close($link);
?>
</body>
</html>