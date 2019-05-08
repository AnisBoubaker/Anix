<?
include ("../config.php");
include ("../ImageEditor.php");
include ("./module_config.php");
$link = dbConnect();
$action="";
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idBrand"])){
	$idBrand=$_POST["idBrand"];
} elseif(isset($_GET["idBrand"])){
	$idBrand=$_GET["idBrand"];
} else $idBrand="";
?>
<?php
include("./mod_brand.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'une marque");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'une marque");
else $title = _("Anix - Modification d'une marque");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'une marque"));break;
	case "insert":setTitleBar(_("Ajout d'une marque"));break;
	case "edit":setTitleBar(_("Modification d'une marque"));break;
	case "update":setTitleBar(_("Modification d'une marque"));break;
	default:setTitleBar(_("Modification d'une marque"));break;
}
?>
<form id='main_form' action='./mod_brand.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idBrand' value='$idBrand'>";
	echo "<input type='hidden' name='action' value='insert'>";
	$cancelLink="./list_brands.php";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idBrand' value='$idBrand'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./list_brands.php";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
      <td colspan='2'>
<?
if($action=="edit"){
	$result=request("SELECT id,name,image_file_small,image_file_large,URL,customer_service_phone,customer_service_email from $TBL_catalogue_brands where id='$idBrand'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette marque n'existe pas."));
	$edit = mysql_fetch_object($result);
}
        ?>
        <table width='100%'>
          <tr valign='top'>
            <td>
<?
if($action=="edit" || $action=="update"){
	echo "<center><a href='../".$CATALOG_folder_images.$edit->image_file_large."' target='_blank'><IMG src='../".$CATALOG_folder_images.$edit->image_file_small."' border='1' alt=\"Agrandir l'image\"></a></center><br>";
	echo "<i>"._("Modifier l'image").":</i><br>";
	echo "<input type='file' name='image_file'>";
}
if($action=="add" || $action=="insert"){
	echo "<center><a href='../".$CATALOG_folder_images."imgbrand_large_no_image.jpg' target='_blank' alt=\""._("Agrandir l'image")."\"><IMG src='../".$CATALOG_folder_images."imgbrand_small_no_image.jpg' border='1'></a></center><br>";
	echo "<i>"._("Modifier l'image").":</i><br>";
	echo "<input type='file' name='image_file'>";
}
              ?>
            </td>
            <td width='66%'>
              <table class='message'>
                <tr>
                  <td colspan='2'>
                    <B><?php echo _("Informations"); ?>: </B>
                  </td>
                </tr>
                <tr>
                  <td>
                    <?php echo _("Nom"); ?>:
                  </td>
                  <td>
                    <input type='text' name='name' size='20' <?
                    if($action=="edit") echo " value=\"".$edit->name."\"";
                    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["name"]."\"";
                    ?>>
                  </td>
                </tr>
                <tr>
                  <td>
                    <?php echo _("Site Web (URL)"); ?>:
                  </td>
                  <td>
                    <input type='text' name='URL' size='40' <?
                    if($action=="edit") echo " value=\"".$edit->URL."\"";
                    if($action=="add") echo " value=\"http://\"";
                    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["URL"]."\"";
                    ?>>
                  </td>
                  <tr>
                    <tr>
                      <td>
                        <?php echo _("Service à la clientèle (tél.)"); ?>:
                      </td>
                      <td>
                        <input type='text' name='customer_service_phone' size='20' <?
                        if($action=="edit") echo " value=\"".$edit->customer_service_phone."\"";
                        if($action=="insert" || $action=="update")  echo " value=\"".$_POST["customer_service_phone"]."\"";
                        ?>>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <?php echo _("Service à la clientèle (courriel.)"); ?>
                      </td>
                      <td>
                        <input type='text' name='customer_service_email' size='20' <?
                        if($action=="edit") echo " value=\"".$edit->customer_service_email."\"";
                        if($action=="insert" || $action=="update")  echo " value=\"".$_POST["customer_service_phone"]."\"";
                        ?>>
                      </td>
                    </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
