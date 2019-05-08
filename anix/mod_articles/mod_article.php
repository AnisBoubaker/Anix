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
if(isset($_POST["idArticle"])){
	$idArticle=$_POST["idArticle"];
} elseif(isset($_GET["idArticle"])){
	$idArticle=$_GET["idArticle"];
} else $idArticle=0;
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?php
include ("./mod_article.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'un article");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'un article");
else $title = _("Anix - Modification d'un article");
include("../html_header.php");
switch ($action) {
	case "add":setTitleBar(_("Ajout d'un article"));break;
	case "insert":setTitleBar(_("Ajout d'un article"));break;
	case "edit":setTitleBar(_("Modification d'un article"));break;
	case "update":setTitleBar(_("Modification d'un article"));break;
	default:setTitleBar(_("Modification d'un article"));break;
}
?>
<form id='main_form' action='./mod_article.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='insert'>";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idArticle' value='$idArticle'>";
	echo "<input type='hidden' name='action' value='update'>";
	$result=request("SELECT id,id_category,active,from_date,to_date,home_page from $TBL_articles_article where id='$idArticle'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_articles_info_categories,$TBL_articles_categories where id_article_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cet article n'existe pas"));
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
$cancelLink="./list_articles.php?action=edit&idCat=$idCat";
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
<td valign="top">
  <center><?php echo _("Veuillez renseigner les informations de la page ci-dessous.");?></center>
  </td>
  <td style='width:60%;'>
  <?php
  /**
  * LOAD TABS
  */
  include("./mod_article.tabs.php");
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
	$result=request("SELECT id,id_category,active,from_date,to_date,title,short_desc,details,keywords,htmltitle,htmldescription from $TBL_articles_info_article,$TBL_articles_article where id='$idArticle' and id=id_article and id_language='".$row_languages->id."'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_articles_info_categories,$TBL_articles_categories where id_article_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cet article n'existe pas."));
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
?>
  <table width='100%'>
  <tr>
  <td><font class='fieldTitle'><?php echo _("Catégorie parente"); ?>: </font></td>
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
  <td><font class='fieldTitle'><?php echo _("Titre"); ?>: </font></td>
  <td><input type='text' name='title_<? echo $row_languages->id?>' size='120'
  <?
  if($action=="edit"){
  	echo " value=\"".$edit->title."\"";
  }
  if($action=="insert" || $action=="update"){
  	echo " value=\"".$_POST["title_".$row_languages->id]."\"";
  }
  ?>
  ></td>
  </tr>
  <tr>
  <td><font class='fieldTitle'><?php echo _("Résumé"); ?>: </font></td>
  <td>
  <?php
  /*
  $oFCKeditor = new FCKeditor() ;
  $oFCKeditor->BasePath = $web_path.$folder_editor."/" ;
  if($action=="add"){
  $oFCKeditor->Value = $NEWS_editor_default_value;
  }
  if($action=="edit"){
  $oFCKeditor->Value = unhtmlentities($edit->short_desc);
  }
  if($action=="insert" || $action=="update"){
  $oFCKeditor->Value = $_POST["short_desc_".$row_languages->id];
  }
  $oFCKeditor->CreateFCKeditor( "short_desc_".$row_languages->id, "100%", 200 ) ;
  */
  echo "<textarea name='short_desc_".$row_languages->id."' style='width:100%;height:200px;'>";
  if($action=="add"){
  	echo $NEWS_editor_default_value;
  }
  if($action=="edit"){
  	echo unhtmlentities($edit->short_desc);
  }
  if($action=="insert" || $action=="update"){
  	echo $_POST["short_desc_".$row_languages->id];
  }
  echo "</textarea>";
	?>
  </td>
  </tr>
  <tr>
  <td><font class='fieldTitle'><?php echo _("Titre(HTML)"); ?> </font></td>
  <td><input type='text' name='htmltitle_<? echo $row_languages->id?>' size='120'
  <?
  if($action=="edit"){
  	echo " value=\"".$edit->htmltitle."\"";
  }
  if($action=="insert" || $action=="update"){
  	echo " value=\"".$_POST["htmltitle_".$row_languages->id]."\"";
  }
  ?>
  ></td>
  </tr>
  <tr>
  <td colspan='2'>
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
			  if($action=="edit") echo unhtmlentities($edit->htmldescription);
			  if($action=="insert" || $action=="update") echo $_POST["htmldescription_".$row_languages->id];
			  ?></TEXTAREA>
	  	</td>
	  </tr>
	  </table>
  <br />
  <font class='fieldTitle'><?php echo _("Détails"); ?>: </font><br>
  	<?php
  	echo "<textarea name='details_".$row_languages->id."' style='width:100%;height:500px;'>";
  	if($action=="add"){
  		echo $NEWS_editor_default_value;
  	}
  	if($action=="edit"){
  		echo unhtmlentities($edit->details);
  	}
  	if($action=="insert" || $action=="update"){
  		echo $_POST["details_".$row_languages->id];
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
