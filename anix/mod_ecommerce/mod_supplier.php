<?php
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$action="";
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idSupplier"])){
	$idSupplier=$_POST["idSupplier"];
} elseif(isset($_GET["idSupplier"])){
	$idSupplier=$_GET["idSupplier"];
} else $idSupplier="";
?>
<?php
include("./mod_supplier.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Ajout d'un fournisseur");
elseif($action=="edit" || $action=="update") $title = _("Modification d'un fournisseur");
else $title = _("Modification d'un fournisseur");;
include("../html_header.php");
$cancelLink="./list_suppliers.php";
switch($action){
	case "add":setTitleBar(_("Ajout d'un fournisseur"));break;
	case "insert":setTitleBar(_("Ajout d'un fournisseur"));break;
	case "edit":setTitleBar(_("Modification d'un fournisseur"));break;
	case "update":setTitleBar(_("Modification d'un fournisseur"));break;
	default:setTitleBar(_("Modification d'un fournisseur"));break;
}
?>
<form id='main_form' action='./mod_supplier.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idSupplier' value='$idSupplier'>";
	echo "<input type='hidden' name='action' value='insert'>";
	$cancelLink="./list_suppliers.php";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idSupplier' value='$idSupplier'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./list_suppliers.php";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>
<?php
if($action=="edit" || $action=="update"){
	try{
		$supplierObj = new EcommerceSupplier($idSupplier);
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
}
?>
<table style='width:100%'>
<tr>
<td style='vertical-align:top; width:180px;'>
	<h3><?php
	if($action=="edit" || $action=="update") echo _("Fournisseur")." #".$supplierObj->getId();
	else echo _("Nouveau fournisseur");
	?></h3>
</td>
<td style='vertical-align:top;'>
	<?php
	/**
	 * LOAD TABS
	 */
	include("./mod_supplier.tabs.php");
	if(isset($ANIX_TabSelect)) TABS_changeTab($ANIX_TabSelect);
	?>
</td>
</tr>
</table>
</form>
<script type='text/javascript'>
function showHideEmailDetails(){
	if(document.getElementById('accept_email_orders').checked){
		document.getElementById('orders_details').style.display='';
		document.getElementById('orders_email_template').style.display='';
	} else {
		document.getElementById('orders_details').style.display='none';
		document.getElementById('orders_email_template').style.display='none';
	}
}

showHideEmailDetails();
</script>
<?
include ("../html_footer.php");
mysql_close($link);
?>
