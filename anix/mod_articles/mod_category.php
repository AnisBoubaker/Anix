<?
include ("../config.php");
include ("../ImageEditor.php");
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

if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat="";
?>
<?php
include("./mod_category.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'une catégorie");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'une catégorie");
else $title = _("Ajout d'une catégorie d'articles:");
include("../html_header.php");
switch ($action) {
	case "add":setTitleBar(_("Ajout d'une catégorie d'articles"));break;
	case "insert":setTitleBar(_("Ajout d'une catégorie d'articles"));break;
	case "edit":setTitleBar(_("Modification d'une catégories d'articles"));break;
	case "update":setTitleBar(_("Modification d'une catégories d'articles"));break;
	default:setTitleBar(_("Modification d'une catégories d'articles"));break;
}
?>
<form id='main_form' action='./mod_category.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='insert'>";
	$cancelLink="./list_categories.php?action=add";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./list_categories.php?action=edit";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
printLanguageToggles($link);
?>
<table id='main_table' border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<tr>
<td colspan='2'>
  <table width='100%'>
  <tr>
    <td>
    <?
    if($action=="edit"){
    	$result=request("SELECT id,image_file_small,image_file_large from $TBL_articles_categories where id='$idCat'",$link);
    	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette catégorie d'éléments n'existe pas."));
    	$edit = mysql_fetch_object($result);
    }
    if($action=="edit" || $action=="update"){
    	echo "<center><a href='../".$CATALOG_folder_images.$edit->image_file_large."' target='_blank'><img class='item_image' src='../".$CATALOG_folder_images.$edit->image_file_small."' alt=\"Agrandir l'image\"></a></center>";
    }
    if($action=="add" || $action=="insert"){
    	echo "<center><a href='../".$CATALOG_folder_images."imgcatarticle_large_no_image.jpg' target='_blank'><img class='item_image' src='../".$CATALOG_folder_images."imgcatarticle_small_no_image.jpg' alt=\""._("Agrandir l'image")."\"></a></center>";
    }
    ?>
    <table>
	  	<tr>
	  		<td><input type='radio' name='image_action' value='keep' checked='checked' /></td>
	  		<td><?php echo _("Conserver cette image"); ?></td>
	  	</tr>
	  	<tr>
	  		<td style='vertical-align:top;'><input type='radio' name='image_action' value='change' /></td>
	  		<td><?php echo _("Modifier l'image:"); ?><br /><input type='file' name='image_file'>
	  		</td>
	  	</tr>
	  	<tr>
	  		<td><input type='radio' name='image_action' value='delete' /></td>
	  		<td><?php echo _("Supprimer l'image"); ?></td>
	  	</tr>
	</table>
    </td>
    <td>
    <?php
    /**
	 * LOAD TABS
	 */
    include("./mod_category.tabs.php");
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
<?
if($action=="edit"){
	$result=request("SELECT id,id_parent,description,name,htmldescription,htmltitle,keywords from $TBL_articles_info_categories,$TBL_articles_categories where id='$idCat' and id=id_article_cat and id_language='".$row_languages->id."'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_articles_info_categories,$TBL_articles_categories where id_article_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die("Erreur de protection: Cette catégorie d'articles n'existe pas.");
	$edit = mysql_fetch_object($result);
}
?>
  <font class='fieldTitle'><?php echo _("Catégorie parente"); ?>:</font>
  <?
  if($action=="add" || $action=="insert") {
  	if($idCat==0) echo "<i>"._("Aucune")."</i>";
  	else echo getParentsPath($idCat,$row_languages->id,$link);
  }elseif($action=="edit" || $action=="update"){
  	if($edit->id_parent==0) echo "<i>Aucune</i>";
  	else echo getParentsPath($edit->id_parent,$row_languages->id,$link);
  }
  ?><br>
  <font class='fieldTitle'><?php echo _("Nom");?>: </font><input type='text' name='name_<? echo $row_languages->id?>' size='50'
  <?
  if($action=="edit"){
  	echo " value=\"".$edit->name."\"";
  }
  if($action=="insert" || $action=="update"){
  	echo " value=\"".$_POST["name_".$row_languages->id]."\"";
  }
  ?>
  ><br>
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
  </table><br>
  <font class='fieldTitle'><?php echo _("Déscription"); ?></font><br>
  	<?php
  	echo "<textarea name='description_".$row_languages->id."' style='width:100%;height:300px;'>";
  	if($action=="add"){
  		echo $NEWS_editor_default_value;
  	}
  	if($action=="edit"){
  		echo unhtmlentities($edit->description);
  	}
  	if($action=="insert" || $action=="update"){
  		echo $_POST["description_".$row_languages->id];
  	}
  	echo "</textarea>";
	?>
</td></tr>
<?
} // while
?>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='insert'>";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='update'>";
}
?>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
