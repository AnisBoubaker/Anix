<?php
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["target"])){
	$target=$_POST["target"];
} elseif(isset($_GET["target"])){
	$target=$_GET["target"];
} else $target="order";
if($target=="order"){
	$targetUrl="./mod_order.php";
}
if($target=="invoice"){
	$targetUrl="./mod_invoice.php";
}
if($target=="payment"){
	$targetUrl="./mod_payment.php";
}
$title = _("Choix du client");
$menu_ouvert = 2;$module_name="ecommerce";include("../html_header.php");
switch($target){
	case "order":setTitleBar(_("Nouvelle commande")." - "._("Choix du client"));break;
	case "invoice":setTitleBar(_("Nouvelle facture")." - "._("Choix du client"));break;
	case "payment":setTitleBar(_("Nouveau paiement")." - "._("Choix du client"));break;
}
?>
<form action='<?=$targetUrl?>' method='POST' enctype='multipart/form-data' name='mainForm'>
<input type='hidden' name='action' value='add'>
  <table border="0" align="center" width="50%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'>
          <?php echo _("Choisissez le client"); ?>
        </font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table align='center'>
        <tr>
          <td>
          <?php
          echo _("Veuillez choisir le client pour")." ";
          if($target=="order") echo _("la nouvelle commande");
          if($target=="invoice") echo _("la nouvelle facture");
          if($target=="payment") echo _("le nouveau payement");
          ?>:<br><br>
          <select name='idClient'>
          <option value='0'>-- <?php
          	if($target=="order") echo _("NOUVEAU CLIENT");
          	else echo _("CHOISISSEZ");
          ?> --</option>
          <?
          $clients = request("SELECT `id`,`firstname`,`lastname`,`company`
                                FROM `$TBL_ecommerce_customer`
                                ORDER BY company,firstname,lastname",$link);
          while($client=mysql_fetch_object($clients)){
          	echo "<option value='".$client->id."'";
          	if($action=="edit" && $edit->id_client==$client->id) echo " SELECTED";
          	if(($action=="insert" || $action=="update") && $_POST[idClient]==$client->id) echo " SELECTED";
          	echo ">";
          	echo "[".$client->company."] ".$client->firstname." ".$client->lastname;
          	echo "</option>";
          }
          ?>
          </select>
          </td>
        </tr>
        </table>
      </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
      </td>
    </tr>
  </table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
