<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idClient"])){
	$idClient=$_POST["idClient"];
} elseif(isset($_GET["idClient"])){
	$idClient=$_GET["idClient"];
} else $idClient="";

$countryList = getCountriesList();
?>
<?php
require_once("./mod_client.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Ajout d'un client");
elseif($action=="edit" || $action=="update") $title = _("Modification d'un client");
else $title = _("Modification d'un client");
$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php");
setTitleBar($title);
?>
<SCRIPT language='Javascript'>
function chkBox_same_address(){
	box_state = document.main_form.same_address.checked;
	document.main_form.billing_num.disabled=box_state;
	document.main_form.billing_street1.disabled=box_state;
	document.main_form.billing_street2.disabled=box_state;
	document.main_form.billing_building.disabled=box_state;
	document.main_form.billing_stairs.disabled=box_state;
	document.main_form.billing_floor.disabled=box_state;
	document.main_form.billing_code.disabled=box_state;
	document.main_form.billing_city.disabled=box_state;
	document.main_form.billing_province.disabled=box_state;
	document.main_form.billing_zip.disabled=box_state;
	document.main_form.billing_country.disabled=box_state;
}
/**
* This function automatically fill the login field as a combiantion of the first name and last name : firstname.lastname
**/
function autoLogin(){
	document.main_form.login.value=document.main_form.firstname.value+'.'+document.main_form.lastname.value;
}
</SCRIPT>

<form action='./mod_client.php' method='POST' enctype='multipart/form-data' id='main_form' name='main_form'>

<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idClient' value='$idClient'>";
	echo "<input type='hidden' name='action' value='insert'>";
	$cancelLink="./list_clients.php";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idClient' value='$idClient'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./view_client.php?idClient=$idClient";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>
<?
if($action=="edit"){
	$result=request("SELECT *
                       FROM $TBL_ecommerce_customer where `id`='$idClient'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Ce client n'existe pas."));
	$edit = mysql_fetch_object($result);
	$result=request("SELECT *
                       FROM $TBL_ecommerce_address where `id`='".$edit->id_address_mailing."'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: L'adresse de livraison n 'existe pas."));
	$edit_mailing = mysql_fetch_object($result);
	$result=request("SELECT *
                       FROM $TBL_ecommerce_address where `id`='".$edit->id_address_billing."'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: L'adresse de facturation n 'existe pas."));
	$edit_billing = mysql_fetch_object($result);
}
?>
<table style='width:100%'>
<tr>
	<td style='vertical-align:top; width:180px;'>
	<?php
	if($action=="add" || $action=="insert") echo "<h2>"._("Nouveau client")."</h2>";
	elseif($action=="edit" || $action=="update")  echo "<h2>"._("Client")." #$idClient"."</h2>";
	?>
	</td>
	</td>
	<td style='vertical-align:top;'>
		<?php
		/**
		 * LOAD TABS
		 */
		include("./mod_client.tabs.php");
		if(isset($ANIX_TabSelect)) TABS_changeTab($ANIX_TabSelect);
		?>
	</td>
</tr>
</table>
</form>
<SCRIPT language='Javascript'>
chkBox_same_address();
</SCRIPT>
<?
include ("../html_footer.php");
mysql_close($link);
?>
