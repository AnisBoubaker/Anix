<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$action="";
$nb=0;
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
?>
<?
if($action=="search_client"){
	//retrieve search criteria
	if(isset($_POST["firstname"])) $sc_firstname=$_POST["firstname"]; elseif(isset($_GET["firstname"])) $sc_firstname=$_GET["firstname"]; else $sc_firstname="";
	if(isset($_POST["company"])) $sc_company=$_POST["company"]; elseif(isset($_GET["company"])) $sc_company=$_GET["company"]; else $sc_company="";
	if(isset($_POST["email"])) $sc_email=$_POST["email"]; elseif(isset($_GET["email"])) $sc_email=$_GET["email"]; else $sc_email="";

	//$tbl_List = "`$TBL_ecommerce_order`";
	$requestString ="SELECT DISTINCT `$TBL_ecommerce_customer`.*
      				 FROM `$TBL_ecommerce_customer`
    				 WHERE 1 ";
	if($sc_firstname!=""){
		$requestString.=" AND (`$TBL_ecommerce_customer`.`firstname` LIKE '%".$sc_firstname."%' OR `$TBL_ecommerce_customer`.`lastname` LIKE '%".$sc_firstname."%')";
		$nb++;
	}
	if($sc_company!=""){
		$requestString.=" AND `$TBL_ecommerce_customer`.`company` LIKE '%".$sc_company."%' ";
		$nb++;
	}
	if($sc_email!=""){
		$requestString.=" AND `$TBL_ecommerce_customer`.`email` LIKE '%".$sc_email."%' ";
		$nb++;
	}
	$requestString.= "ORDER BY `$TBL_ecommerce_customer`.`firstname`, `$TBL_ecommerce_customer`.`lastname`";
	$requestString.= " LIMIT $MAX_SEARCH_RESULTS";
	$request=request($requestString,$link);
	$nbResults = mysql_num_rows($request);
	/*if($nb){
	$request=request($requestString,$link);
	$nbResults = mysql_num_rows($request);
	} else {
	$errors++;
	$errMessage.="- "._("Vous n'avez spécifié aucun critère de recherche.")."<br />";
	}*/
}
?>
<? $title = _("Recherche d'un client");$menu_ouvert = 1;$module_name="ecommerce";include("../html_header.php"); ?>
<form action='./search_client.php' method='GET'>
<input type='hidden' name='action' value='search_client'>
<table border="0" align="center" width="40%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Recherche d'un client");?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
        &nbsp;
    </td>
    </tr>
    <tr>
    <td colspan='2'>
        <table width='100%'>
        <tr>
	        <td><?php echo _("Nom ou prénom"); ?>:</td>
	        <td><input type='text' name='firstname' size='20' <?
	        if($action=="search_client") echo "value='".$sc_firstname."'";
	        ?>></td>
        </tr>
        <tr>
	        <td><?php echo _("Société"); ?>:</td>
	        <td><input type='text' name='company' size='20' <?
	        if($action=="search_client") echo "value='".$sc_company."'";
	        ?>></td>
        </tr>
        <tr>
	        <td><?php echo _("Courriel"); ?>:</td>
	        <td><input type='text' name='email' size='20' <?
	        if($action=="search_client") echo "value='".$sc_email."'";
	        ?>></td>
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
if(($action=="search_client" || $action=="search_category")){
?>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      <font class='edittable_header'><?php echo _("Résultats de la recherche"); ?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
      &nbsp;
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <center><?php
      if($nbResults<$MAX_SEARCH_RESULTS){
      	echo _("Votre recherche a retourné ");
      	printf(ngettext("%d résultat", "%d résultats", $nbResults), $nbResults);
      } else {
      	echo "<b>"._("ATTENTION:")."</b>"._("Votre recherche a retourné plus de ");
      	printf(ngettext("%d résultat", "%d résultats", $MAX_SEARCH_RESULTS), $MAX_SEARCH_RESULTS);
      	echo "<br />";
      	printf(ngettext("Seul le %d résultat a été retenu.", "Seuls les %d résultats on été retenus.", $MAX_SEARCH_RESULTS), $MAX_SEARCH_RESULTS);
      }
      ?></center><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width='100%' class='message'>
  <?
  if($nbResults){
  ?>
  <tr>
        <td>&nbsp;</td>
        <td align='center'><b>#ID<?php //echo _("Commande");?></b></td>
        <td align='center'><b><?php echo _("Nom & Prénom");?></b></td>
        <td align='center'><b><?php echo _("Téléphone");?></b></td>
        <td align='center'><b><?php echo _("Courriel");?></b></td>
    </tr>
  <?
  while($result=mysql_fetch_object($request)){
  	echo "<tr>";
  	echo "<td valign='middle' width='40px' bgcolor='#e7eff2' align='right'>";
  	echo "<a href='./view_client.php?idClient=$result->id'><img src='../images/view.gif' border='0' alt="._("Voir")."></a>";
  	echo "<a href='./mod_client.php?action=edit&idClient=$result->id'><img src='../images/edit.gif' border='0' alt="._("Voir")."></a>";
  	echo "</td>";
  	echo "<td align='center'><b>".$result->id."</b></td>";
  	echo "<td><a href='./view_client.php?idClient=$result->id'>".$result->firstname." ".$result->lastname."</a></td>";
  	echo "<td align='center'>$result->phone</td>";
  	echo "<td align='center'>$result->email</td>";
  	echo "</tr>";
  }
  }
  ?>
    </table>
    </td>
  </tr>
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
} //if search
?>
<?
include ("../html_footer.php");
mysql_close($link);
?>
