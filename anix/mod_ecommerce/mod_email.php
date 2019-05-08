<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idEmail"])){
	$idEmail=$_POST["idEmail"];
} elseif(isset($_GET["idEmail"])){
	$idEmail=$_GET["idEmail"];
} else $idEmail="";
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory=-1;
$errors = 0;
$errMessage="";
?>
<?php
require_once("./mod_email.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'un courriel type");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'un courriel type");
else $title = _("Anix - Modification d'un courriel type");
$menu_ouvert = 4;$module_name="ecommerce";include("../html_header.php");

if($action=="add" || $action=="insert") setTitleBar(_("Anix - Ajout d'un courriel type"));
elseif($action=="edit" || $action=="update") setTitleBar(_("Anix - Modification d'un courriel type"));
?>
<form action='./mod_email.php' method='POST' enctype='multipart/form-data' id='main_form'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idEmail' value='$idEmail'>";
	echo "<input type='hidden' name='action' value='insert'>";
	echo "<input type='hidden' name='idCategory' value='$idCategory'>";
	$cancelLink="./list_email.php";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idEmail' value='$idEmail'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./list_emails.php";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
printLanguageToggles($link);
?>
  <table id='main_table' border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'>
        </font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2'>
      <center><?php echo _("Veuillez renseigner les informations du courriel ci-dessous.");?></center><br /><br />
      </td>
    </tr>
    <tr>
      <td colspan='2'>
	      <?php
	      if($action=="edit" || $action=="update"){
	      	$request = request(
	      	"SELECT `$TBL_ecommerce_emails`.`id`,`$TBL_ecommerce_emails`.`cc_email`,`$TBL_ecommerce_emails`.`bcc_email`,`$TBL_ecommerce_emails`.`fields`,`$TBL_ecommerce_emails`.`description`,`$TBL_ecommerce_emails`.`enabled`
	        FROM `$TBL_ecommerce_emails`
	        WHERE `$TBL_ecommerce_emails`.id='$idEmail'",$link);
	      	if(mysql_num_rows($request)){
	      		$edit = mysql_fetch_object($request);
	      	} else {
	      		die(_("Erreur de protection: Le courriel specifié n'existe pas."));
	      	}
	      }
	      ?>
	      <table>
	      <tr>
	      	<td><B><?php echo _("Activer l'envoi de ce courriel");?>:</B></td>
	      	<td><input type='checkbox' name='enabled' size='128'<?
	      	if($action=="add") echo " checked='checked'";
	      	if($action=="edit" && $edit->enabled=="Y") echo " checked='checked'";
	      	if(($action=="update" || $action=="insert") && isset($_POST["enabled"])) echo " checked='checked'";
	          ?>></td>
	      </tr>
	      <tr>
	      	<td><B><?php echo _("Envoyer le courriel en copie (CC) à");?>:</B></td>
	      	<td><input type='text' name='cc_email' size='128'<?
	      	if($action=="edit") echo " value=\"".$edit->cc_email."\"";
	      	if($action=="update" || $action=="insert") echo " value=\"".$_POST["cc_email"]."\"";
	          ?>></td>
	      </tr>
	      <tr>
		      <td><B><?php echo _("Envoyer le courriel en copie cachée (BCC) à");?>:</B></td>
		      <td><input type='text' name='bcc_email' size='128'<?
		      if($action=="edit") echo " value=\"".$edit->bcc_email."\"";
		      if($action=="update" || $action=="insert") echo " value=\"".$_POST["bcc_email"]."\"";
		          ?>></td>
	      </tr>
	      </table>
      </td>
    </tr>
    <?
    $languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used = 'Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default", $link);
    $first=true;
    while ($row_languages=mysql_fetch_object($languages)){
    	if($first){ $first=false; $displayLanguage='';}
    	else $displayLanguage='none';

    	if($action=="edit"){
    		$request = request(
    		"SELECT `$TBL_ecommerce_emails`.`id`,`$TBL_ecommerce_info_emails`.`title`,`$TBL_ecommerce_info_emails`.`sender_name`,`$TBL_ecommerce_info_emails`.`sender_email`,`$TBL_ecommerce_info_emails`.`subject`,`$TBL_ecommerce_info_emails`.`content`,`$TBL_ecommerce_emails`.`fields`,`$TBL_ecommerce_emails`.`description`
        FROM `$TBL_ecommerce_emails`,`$TBL_ecommerce_info_emails`
        WHERE `$TBL_ecommerce_emails`.id='$idEmail'
        AND `$TBL_ecommerce_info_emails`.`id_language`='".$row_languages->id."'
        AND `$TBL_ecommerce_info_emails`.`id_email`=`$TBL_ecommerce_emails`.`id`",$link);
    		if(mysql_num_rows($request)){
    			$edit = mysql_fetch_object($request);
    		} else {
    			die(_("Erreur de protection: Le courriel specifié n'existe pas."));
    		}
    	}
    ?>
    <tr class='lang_<?php echo $row_languages->id;?>' style='display:<?php echo $displayLanguage; ?>;'>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><B><?php echo _("Titre");?>:</B></td>
          <td><input type='text' name='title_<?=$row_languages->id?>' size='128'<?
          if($action=="edit") echo " value=\"".stripslashes($edit->title)."\"";
          if($action=="update" || $action=="insert") echo " value=\"".$_POST["title_".$row_languages->id]."\"";
          ?>>
        </tr>
        <tr>
          <td><B><?php echo _("Nom de l'auteur");?>:</B></td>
          <td><input type='text' name='sendername_<?=$row_languages->id?>' size='128'<?
          if($action=="edit") echo " value=\"".stripslashes($edit->sender_name)."\"";
          if($action=="update" || $action=="insert") echo " value=\"".$_POST["sendername_".$row_languages->id]."\"";
          ?>>
        </tr>
        <tr>
          <td><B><?php echo _("Courriel de l'auteur");?>:</B></td>
          <td><input type='text' name='senderemail_<?=$row_languages->id?>' size='128'<?
          if($action=="edit") echo " value=\"".stripslashes($edit->sender_email)."\"";
          if($action=="update" || $action=="insert") echo " value=\"".$_POST["senderemail_".$row_languages->id]."\"";
          ?>>
        </tr>
        <tr>
          <td><B><?php echo _("Sujet du courriel");?>:</B></td>
          <td><input type='text' name='subject_<?=$row_languages->id?>' size='128'<?
          if($action=="edit") echo " value=\"".stripslashes($edit->subject)."\"";
          if($action=="update" || $action=="insert") echo " value=\"".$_POST["subject_".$row_languages->id]."\"";
          ?>>
        </tr>
        <tr>
          <td colspan='2'><B><?php echo _("Corps du courriel");?>:</B><br><br>
	          <table width='100%'>
	          <tr>
	          <td width='50%' style='vertical-align:top;'>
	          <?php
	          echo "<TEXTAREA class='mceNoEditor' cols='80' rows='20' name='content_".$row_languages->id."' style='border:1px solid #000000;'>";
	          if($action=="edit"){
	          	echo stripslashes($edit->content);
	          }
	          if($action=="insert" || $action=="update"){
	          	echo stripslashes($_POST["content_".$row_languages->id]);
	          }
	          echo "</TEXTAREA>";
	          ?>
	          </td>
	          <td width='40%' style='vertical-align:top;padding-left:10px;'>
	          	<?php
	          	echo "<b>"._("Infos et champs utilisables").":</b><br />";
	          	if(($action=="edit" || $action=="update") && $edit->fields!="") echo nl2br($edit->description);
	          	else echo _("Aucun champs n'a ete defini");
	          	?>
	          </td>
	          </tr>
	          </table>
         </td>
        </tr>
        </table>
      </td>
    </tr>
    <?
    } // While languages
    ?>
  </table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
