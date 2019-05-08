<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="";
$errMessage="";
$message="";
$errors=0;

//Handles results ordering
$orderChangement = false;
if(isset($_POST["order"])){
	$order=$_POST["order"];
	$orderChangement = true;
} elseif(isset($_GET["order"])){
	$order=$_GET["order"];
	$orderChangement = true;
}
if($orderChangement){
	switch ($order){
		case 1: $_SESSION["ecommerce_clients_orderby"]="firstname,lastname,company";
		$_SESSION["ecommerce_clients_sortby"]="UPPER(firstname) LIKE ";
		break;
		case 2: $_SESSION["ecommerce_clients_orderby"]="lastname,firstname,company";
		$_SESSION["ecommerce_clients_sortby"]="UPPER(lastname) LIKE ";
		break;
		case 3: $_SESSION["ecommerce_clients_orderby"]="company,firstname,lastname";
		$_SESSION["ecommerce_clients_sortby"]="UPPER(company) LIKE ";
		break;
		default: $_SESSION["ecommerce_clients_orderby"]="firstname,lastname,company";
		$_SESSION["ecommerce_clients_sortby"]="UPPER(firstname) LIKE ";
		break;
	}
}
if(!isset($_SESSION["ecommerce_clients_orderby"])) {
	$_SESSION["ecommerce_clients_orderby"]="firstname,lastname,company";
	$_SESSION["ecommerce_clients_sortby"]="UPPER(firstname) LIKE ";
}

//Handles results sorting
if(isset($_POST["sort"])){
	$sort=$_POST["sort"];
} elseif(isset($_GET["sort"])){
	$sort=$_GET["sort"];
} else $sort=27;
$sortQuery = $_SESSION["ecommerce_clients_sortby"];
if($sort>=1 && $sort<=26) $sortQuery.="'".chr(64+$sort)."%'";
elseif($sort==0) $sortQuery = "0"; //no result
elseif($sort==27) $sortQuery = "1"; //all
?>
<?
if($action=="delete"){
	$idClient=$_POST["idClient"];
	$return = deleteClient($idClient,$link);
	$errors=+$return["errors"];
	$errMessage.=$return["errMessage"];
	$message.=$return["message"];
}
?>
<? $title = _("Liste des clients");
$menu_ouvert = 1;
$module_name="ecommerce";
include("../html_header.php");
?>
<table border="0" align="center" width="60%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'>Liste des clients </font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  &nbsp;
  </td>
</tr>
<tr>
<td colspan='2'>
<center>
<?
//Criteria
for($i=1;$i<27;$i++){
	echo "[";
	if($sort==$i) echo "<b>";
	else echo "<a href='list_clients.php?sort=$i'>";
	echo chr(64+$i);
	if($sort==$i) echo "</b>";
	else echo "</a>";
	echo "] ";
}
if($sort==27) echo "<b>";
else echo "<a href='list_clients.php?sort=27'>";
echo _("TOUS");
if($sort==27) echo "</b> ";
else echo "</a> ";

?>
</center><br><br>
<?
echo showClients("WHERE $sortQuery",$_SESSION["ecommerce_clients_orderby"],$link);
?>
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    &nbsp;
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  &nbsp;
  </td>
</tr>
</table>
<?
include ("../html_footer.php");
mysql_close($link);
?>
