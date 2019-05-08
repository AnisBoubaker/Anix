<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo _("Evaluation de fraude"); ?></title>
</head>
<body>
<h3 style='text-align:center;'>Évaluation de fraude</h3><br /><br />
<?php
	echo "<b>"._("Méthode d'évaluation").":</b> ".$ECOMMERCE_fraudcheck_methods[$method]["name"]."<br />";
	echo "<b>"._("Commande").":</b> ".$_REQUEST["idOrder"]."<br />";
?>