<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$delete=false;
$errMessage="";
$action="";
$message="";
$errors=0;
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idFaq"])){
	$idFaq=$_POST["idFaq"];
} elseif(isset($_GET["idFaq"])){
	$idFaq=$_GET["idFaq"];
} else $idFaq=0;
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?php
include("./mod_faq.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'une question");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'une question");
else $title = _("Anix - Modification d'une question");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'une question"));break;
	case "insert":setTitleBar(_("Ajout d'une question"));break;
	case "edit":setTitleBar(_("Modification d'une question"));break;
	case "update":setTitleBar(_("Modification d'une question"));break;
	default:setTitleBar(_("Modification d'une question"));
}
?>
<form id='main_form' action='./mod_faq.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='insert'>";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idFaq' value='$idFaq'>";
	echo "<input type='hidden' name='action' value='update'>";
	$result=request("SELECT id,id_category,active from $TBL_faq_faq where id='$idFaq'",$link);
	if(!mysql_num_rows($result)) die("Erreur de protection: Cette question n'existe pas.");
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
$cancelLink="./list_faq.php?action=edit&idCat=$idCat";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
printLanguageToggles($link);
?>
<table id='main_table' border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2' bgcolor='#FFFFFF'>
<table width='100%'>
<tr>
  <td style='width:30%;'>
    &nbsp;
  </td>
  <td>
	  <?php
	  include("./mod_faq.tabs.php");
	  if(isset($ANIX_TabSelect)) TABS_changeTab($ANIX_TabSelect);
	  ?>
  </td>
</tr>
</table>
</td>
</tr>
<tr height='20'>
  <td background='../images/button_back.jpg' align='left' valign='middle'>
  </td>
  <td background='../images/button_back.jpg' align='right'>
  </td>
</tr>
<?
$languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used = 'Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default", $link);
$first=true;
while ($row_languages=mysql_fetch_object($languages)){
	if($first){ $first=false; $displayLanguage='';}
	else $displayLanguage='none';
?>
<tr class='lang_<?php echo $row_languages->id;?>' style='display:<?php echo $displayLanguage; ?>;'>
<td colspan='2'>
<? //Rest of while languages
if($action=="edit"){
	$result=request("SELECT id,id_category,active,question,response,keywords,htmltitle,htmldescription from $TBL_faq_info_faq,$TBL_faq_faq where id='$idFaq' and id=id_faq and id_language='".$row_languages->id."'",$link);
	if(!mysql_num_rows($result)) die("Erreur de protection: Cette question n'existe pas.");
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
?>
  <font class='fieldTitle'><?php echo _("Titre(HTML):"); ?> </font><input type='text' name='htmltitle_<? echo $row_languages->id?>' size='50'
  <?
  if($action=="edit"){
  	echo " value=\"".$edit->htmltitle."\"";
  }
  if($action=="insert" || $action=="update"){
  	echo " value=\"".$_POST["htmltitle_".$row_languages->id]."\"";
  }
  ?>
  ><br>
  <table width='100%'>
  <tr>
  	<td width="50%">
  		<font class='fieldTitle'><?php echo _("Mots cles");?> </font><br />
		  <TEXTAREA  class='mceNoEditor' name='keywords_<?=$row_languages->id?>' cols='50' rows='3'><?
		  if($action=="edit") echo $edit->keywords;
		  if($action=="insert" || $action=="update") echo $_POST["keywords_".$row_languages->id];
		  ?></TEXTAREA>
  	</td>
  	<td width="50%">
  		<font class='fieldTitle'><?php echo _("Description(HTML)");?> </font><br />
		  <TEXTAREA  class='mceNoEditor' name='htmldescription_<?=$row_languages->id?>' cols='50' rows='3'><?
		  if($action=="edit") echo $edit->htmldescription;
		  if($action=="insert" || $action=="update") echo $_POST["htmldescription_".$row_languages->id];
		  ?></TEXTAREA>
  	</td>
  </tr>
  </table>
  <table width='100%'>
  <tr>
  <td><font class='fieldTitle'><?php echo _("Catégorie parente"); ?>:</font></td>
  <td>
  <?
  if($action=="add" || $action=="insert") {
  	echo getParentsPath($idCat,$row_languages->id,$link);
  }elseif($action=="edit" || $action=="update"){
  	echo getParentsPath($edit->id_category,$row_languages->id,$link);
  }
  ?></td>
  </tr>
  <tr>
  <td><font class='fieldTitle'><?php echo _("Question"); ?>:</font></td>
  <td><input type='text' name='question_<? echo $row_languages->id?>' size='120'
  <?
  if($action=="edit"){
  	echo " value=\"".$edit->question."\"";
  }
  if($action=="insert" || $action=="update"){
  	echo " value=\"".$_POST["question_".$row_languages->id]."\"";
  }
  ?>
  ></td>
  </tr>
  <tr>
  <td colspan='2'>
  <font class='fieldTitle'><?php echo _("Réponse");?>:</font><br>
  	<?php
  	echo "<textarea name='response_".$row_languages->id."' style='width:100%;height:300px;'>";
  	if($action=="add"){
  		echo $FAQ_editor_default_value;
  	}
  	if($action=="edit"){
  		echo unhtmlentities($edit->response);
  	}
  	if($action=="insert" || $action=="update"){
  		echo $_POST["response_".$row_languages->id];
  	}
  	echo "</textarea>";
	?>
	</td>
	</tr>
	</table>
</td>
</tr>
<?
} // while
?>
</td></tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
