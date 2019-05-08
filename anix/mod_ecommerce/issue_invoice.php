<?php
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();

if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
} else $action="";
if(isset($_POST["idInvoice"])){
	$idInvoice=$_POST["idInvoice"];
} elseif(isset($_GET["idInvoice"])){
	$idInvoice=$_GET["idInvoice"];
} else $idInvoice=0;

require_once("./issue_invoice.actions.php");
?>
<?
if($action=="issue" || $action=="issue_confirm") $title = _("Anix - Émission d'une facture");
if($action=="un_issue" || $action=="un_issue_confirm") $title = _("Anix - Ré-édition d'une facture");
$menu_ouvert = 2;$module_name="ecommerce";include("../html_header.php");
switch($action){
	case "issue_confirm":setTitleBar(_("Émission d'une facture"));break;
	case "un_issue_confirm":setTitleBar(_("Ré-édition d'une facture"));break;
}


$request = request("SELECT `id_client` FROM `$TBL_ecommerce_invoice` WHERE `id`='$idInvoice'",$link);
$client = mysql_fetch_object($request);

$cancelLink="./view_client.php?idClient=$client->id_client";

$button=array();
if($action=="issue_confirm" || $action=="un_issue_confirm") $buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);

if($action=="issue_confirm" || $action=="un_issue_confirm"){
	echo "<form id='main_form' action='./issue_invoice.php' method='POST' enctype='multipart/form-data' name='main_form'>";
	echo "<input type='hidden' name='idInvoice' value='$idInvoice'>";
	if($action=="issue_confirm") echo "<input type='hidden' name='action' value='issue'>";
	if($action=="un_issue_confirm") echo "<input type='hidden' name='action' value='un_issue'>";
}
?>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'>
        <?
        if($action=="issue" || $action=="issue_confirm"){
        	echo _("Émission d'une facture");
        } elseif ($action=="un_issue" || $action=="un_issue_confirm") {
        	echo _("Ré-édition d'une facture");
        }
        ?>
        </font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
      </td>
    </tr>
    <tr>
      <td colspan='2' align='center'>
      <?php
      if($action=="issue_confirm"){
      	echo _("Êtes-vous sûr de vouloir émettre la facture").": ".$idInvoice."?<br><br>";
      	echo "<font color='red'><b>"._("N.B.: Une fois émise, la facture ne sera plus modifiable.")."</b></font>";
      }
      if($action=="issue" && !$errors){
      	echo _("La facture")." #".$idInvoice." "._("a été émise avec succès.")."<br><br>";
      }
      if($action=="un_issue_confirm"){
      	echo _("Êtes-vous sûr de vouloir ré-éditer la facture").": ".$idInvoice."?<br><br>";
      	echo "<font color='red'><b>"._("N.B.: Tous les paiements effectués à cette facture seront désalloués.")."</b></font>";
      }
      if($action=="un_issue" && !$errors){
      	echo _("La facture")." #".$idInvoice." "._("a été rétablie dans l'état éditable.")."<br><br>";
      }
      ?>
      </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
      </td>
      <td background='../images/button_back.jpg' align='right'>
      </td>
    </tr>
  </table>
<?
include ("../html_footer.php");
mysql_close($link);
?>