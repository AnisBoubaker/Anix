<?
include ("../config.php");
include ("../ImageEditor.php");
include ("./module_config.php");
include("./mod_product.xcommon.php");
$link = dbConnect();
$delete=false;
$action="";
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
include ("./mod_category.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'une catégorie");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'une catégorie");
else $title = _("Anix - Modification d'une catégorie");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'une catégorie de produits"));break;
	case "insert":setTitleBar(_("Ajout d'une catégorie de produits"));break;
	case "edit":setTitleBar(_("Modification d'une catégories de produits"));break;
	case "update":setTitleBar(_("Modification d'une catégories de produits"));break;
	default:setTitleBar(_("Modification d'une catégories de produits"));break;
}
?>
<form id='main_form' action='./mod_category.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='insert'>";
	$cancelLink="./list_products.php?idCat=$idCat#$idCat";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='update'>";
	$cancelLink="./list_products.php?idCat=$idCat#$idCat";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
printLanguageToggles($link);
?>
<table id='main_table' border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<td colspan='2'>
  <?
  if($action=="edit"){
  	$result=request("SELECT id,image_file_small,image_file_large,contain_products,hide_products,reference_pattern,productimg_icon_width,productimg_icon_height,productimg_small_width,productimg_small_height,productimg_large_width,productimg_large_height from $TBL_catalogue_categories where id='$idCat'",$link);
  	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette catégorie de produits n'existe pas."));
  	$edit = mysql_fetch_object($result);
  }
  ?>
  <?php
  /**
	 * LOAD TABS
	 */
  include("./mod_category.tabs.php");
  if(isset($ANIX_TabSelect)) TABS_changeTab($ANIX_TabSelect);
  ?>
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
	$result=request("SELECT id,id_parent,description,name,keywords,htmltitle,htmldescription from $TBL_catalogue_info_categories,$TBL_catalogue_categories where id='$idCat' and id=id_catalogue_cat and id_language='".$row_languages->id."'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_catalogue_info_categories,$TBL_catalogue_categories where id_news_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die("Erreur de protection: Cette cat�orie de produits n'existe pas.");
	$edit = mysql_fetch_object($result);
}
?>
  <font class='fieldTitle'><?php echo _("Catégorie parente"); ?>: </font>
  <?
  if($action=="add" || $action=="insert") {
  	if($idCat==0) echo "<i>"._("Aucune")."</i>";
  	else echo getParentsPath($idCat,$row_languages->id,$link);
  }elseif($action=="edit" || $action=="update"){
  	if($edit->id_parent==0) echo "<i>"._("Aucune")."</i>";
  	else echo getParentsPath($edit->id_parent,$row_languages->id,$link);
  }
  ?><br>
  <font class='fieldTitle'><?php echo _("Nom"); ?>: </font><input type='text' name='name_<? echo $row_languages->id?>' size='50'
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
  </table>
  <font class='fieldTitle'><?php echo _("Description"); ?>: </font><br>
  	<?php
  	echo "<textarea name='description_".$row_languages->id."' style='width:100%;height:300px;'>";
  	if($action=="add"){
  		echo $CATALOG_editor_default_value;
  	}
  	if($action=="edit"){
  		echo unhtmlentities($edit->description);
  	}
  	if($action=="insert" || $action=="update"){
  		echo $_POST["description_".$row_languages->id];
  	}
  	echo "</textarea>";
	?><br>
	<?
	//Load the extra sections
	if($action=="edit" || $action=="update"){
		$extraSections=request("SELECT id,name,value from $TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection where id_cat='$idCat' and id_language='".$row_languages->id."' and id=id_extrasection ORDER BY ordering",$link);
		while($extraSection=mysql_fetch_object($extraSections)){
			echo "<font class='fieldTitle'>".$extraSection->name." : </font><br>";
			/*
			$oFCKeditor = new FCKeditor() ;
			$oFCKeditor->BasePath = $web_path.$folder_editor."/" ;
			if($action=="edit"){
			$oFCKeditor->Value = unhtmlentities($extraSection->value);
			}
			if($action=="update"){
			$oFCKeditor->Value = stripslashes($_POST["extrasection_".$extraSection->id."_".$row_languages->id]);
			}
			$oFCKeditor->CreateFCKeditor( "extrasection_".$extraSection->id."_".$row_languages->id, "100%", 300 ) ;
			*/
			echo "<textarea name='extrasection_".$extraSection->id."_".$row_languages->id."' style='width:100%;height:300px;'>";
			if($action=="edit"){
				echo unhtmlentities($extraSection->value);
			}
			if($action=="update"){
				echo stripslashes($_POST["extrasection_".$extraSection->id."_".$row_languages->id]);
			}
			echo "</textarea>";
			echo "<br>";
		}
	}
  ?>
</td></tr>
<?
} // while
?>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
