<?
  include ("../config.php");
  include ("./module_config.php");
  require "class/pclzip.lib.php";
  $link = dbConnect();
	$nb=0;
	if(isset($_POST["action"])){
	  $action=$_POST["action"];
	} else $action="";
  $errMessage="";
	$message="";
	$errors=0;
?>
<?
  if($action=="doBackup"){
    //catalogue tables
    $tables = "$TBL_catalogue_categories $TBL_catalogue_info_categories $TBL_catalogue_products $TBL_catalogue_info_products $TBL_catalogue_extrafields $TBL_catalogue_info_extrafields $TBL_catalogue_extrafields_values $TBL_catalogue_extracategorysection $TBL_catalogue_info_extracategorysection $TBL_catalogue_price_groups $TBL_catalogue_info_price_groups $TBL_catalogue_product_prices $TBL_catalogue_attachments $TBL_catalogue_brands $TBL_catalogue_product_options $TBL_catalogue_info_options $TBL_catalogue_product_option_choices $TBL_catalogue_info_choices $TBL_catalogue_featured $TBL_catalogue_info_featured ";
    //news tables
    $tables.= "$TBL_news_categories $TBL_news_info_categories $TBL_news_info_news $TBL_news_news ";
    //faq tables
    $tables.= "$TBL_faq_categories $TBL_faq_info_categories $TBL_faq_info_faq $TBL_faq_faq ";
    //content tables
    $tables.= "$TBL_content_pages $TBL_content_info_pages $TBL_content_menuitems $TBL_content_info_menuitems ";
    //dump database
    system("mysqldump --host=".$hostName." --user=".$userName." --password=".$password." --no-create-db --no-create-info --quick --skip-comments ".$dbName." --tables $tables > ./".$dbName.".sql");
    $filename = $dbName.".sql";
    //Zip the file....
    //require "class/mime_mail.class.php";
    $aFilelist = array($dbName.".sql","../../catalogue_images/","../../catalogue_attachments/","../../userfiles/","../../userimages/");
    $file_backup = $AdministredSiteName.".anx";
    //$zipFlags=array();
    //$zipFlags['recursesd']=1;
    //$zipFlags['storepath']=1;
    //$zipFlags['comment']="On ".getDBDate()." By ".$_SESSION["anix_user"];
    $fileComments = array();
    $fileComments["date"]=getDBDate();
    $fileComments["by"]=$_SESSION["anix_user"];
    $zip = new PclZip($file_backup);
    $v_list = $zip->create($aFilelist, PCLZIP_OPT_COMMENT, serialize($fileComments));
    if ($v_list == 0) {
      $errors++;
      $errMessage.=$archive->errorInfo(true);
    }

    $zip2 = new PclZip($file_backup);

  }
?>
<? $title = "Sauvegarde du site";include("../html_header.php"); ?>
<table border="0" align="center" width="60%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr height='20'>
  <td  background='../images/button_back.jpg' align='left' valign='middle'>
    <font class='edittable_header'>Sauvegarde du site </font>
  </td>
  <td background='../images/button_back.jpg' align='right'>
	  &nbsp;
  </td>
</tr>
<tr>
<td colspan='2'>
<?
  if($action=="doBackup"){
?>
  <center>
  Le site a �t� sauvegard�. Vous pouvez t�l�charger la sauvegarde sur votre disque dur en cliquant sur le lien ci-dessous :<br>
  <a href='./<?=$file_backup?>'><?=$file_backup?></a>
  </center>
  <br><br>
  Informations de la sauvegarde :
  <?
    $prop=$zip2->properties();
    if($prop){
      $comments=unserialize($prop["comment"]);
      echo "Created on : ".$comments["date"];
      echo "By : ".$comments["by"];
    } else {
      echo "Error !!";
    }
  ?>
<?
  } else { //not doBackup
?>
  <center>
  Veuillez cliquer sur le bouton ci-dessous pour sauvegarder le contenu de votre site dans un fichier. Vous pourrez t�l�charger ce fichier par la suite et le sauvegarder sur un disque.<br>
  <form action='./backup.php' method='POST'>
    <input type='hidden' name='action' value='doBackup'>
    <input type='submit' value='Sauvegarder'>
  </form>
  </center>
<?
  }
?>
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
  include ("../html_footer.php");
  mysql_close($link);
?>
<?
$db_host='localhost';
$db_log='root';
$db_pass='';
$db_base='anix';








/*$fichier_attache = fread(fopen($db_base.".zip", "r"), filesize($db_base.".zip"));

$mail = new mime_mail();
$mail->to = "destmail@domain.tld";
$mail->subject = "svg sql";
$mail->body = "svg ci-joint.";
$mail->from = "ton_mail@ton_serveur.tld";
$mail->attach("$fichier_attache", $db_base.".zip");
$mail->send();

echo "Fichier envoy� !";
*/
?>
</body>
</html>
