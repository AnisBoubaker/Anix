<?
include ("../config.php");
include ("./module_config.php");
include ("../ImageEditor.php");
$link = dbConnect();
$delete=false;
$action="";
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idPhoto"])){
	$idPhoto=$_POST["idPhoto"];
} elseif(isset($_GET["idPhoto"])){
	$idPhoto=$_GET["idPhoto"];
} else $idPhoto=0;
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?php
include("./mod_photo.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'une nouvelle");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'une nouvelle");
else $title = _("Anix - Modification d'une nouvelle");
include("../html_header.php");
?>
<form id='main_form' action='./mod_photo.php' method='POST' enctype='multipart/form-data' name='mainForm'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='insert'>";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idPhoto' value='$idPhoto'>";
	echo "<input type='hidden' name='action' value='update'>";
	$result=request("SELECT * from $TBL_gallery_photo where id='$idPhoto'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_gallery_info_categories,$TBL_gallery_categories where id_gallery_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette nouvelle n'existe pas."));
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
$cancelLink="./list_photos.php?action=edit&idCat=$idCat";
switch($action){
	case "add":setTitleBar(_("Ajout d'une nouvelle"));break;
	case "insert":setTitleBar(_("Ajout d'une nouvelle"));break;
	case "edit":setTitleBar(_("Modification d'une nouvelle"));break;
	case "update":setTitleBar(_("Modification d'une nouvelle"));break;
}
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
  	<?
  	if($action=="edit" || $action=="update"){
  		echo "<center><a href='../".$CATALOG_folder_images.$edit->image_file_large."' target='_blank'><img class='item_image' src='../".$CATALOG_folder_images.$edit->image_file_small."' alt=\""._("Agrandir")."\"></a></center><br>";
  	}
  	if($action=="add" || $action=="insert"){
  		echo "<center><a href='../$CATALOG_folder_images/imgphoto_large_no_image.jpg' target='_blank'><img class='item_image' src='../$CATALOG_folder_images/imgphoto_small_no_image.jpg' alt=\""._("Agrandir")."\"></a></center><br>";
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
  <td style='width:60%;'>
  <?php
  /**
  * LOAD TABS
  */
  include("./mod_photo.tabs.php");
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
	$result=request("SELECT id,id_category,active,from_date,to_date,title,date,short_desc,details,keywords,htmltitle,htmldescription from $TBL_gallery_info_photo,$TBL_gallery_photo where id='$idPhoto' and id=id_photo and id_language='".$row_languages->id."'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_gallery_info_categories,$TBL_gallery_categories where id_gallery_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Cette nouvelle n'existe pas."));
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
?>
  <table width='100%'>
  <tr>
  <td><font class='fieldTitle'><?php echo ("Catégorie parente");?>: </font></td>
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
  <td><font class='fieldTitle'><?php echo _("Date"); ?>: </font></td>
  <td><input type='text' id='date_<? echo $row_languages->id?>' name='date_<? echo $row_languages->id?>' size='30'
  <?
  if($action=="edit"){
  	echo " value=\"".$edit->date."\"";
  }
  if($action=="insert" || $action=="update"){
  	echo " value=\"".$_POST["date_".$row_languages->id]."\"";
  }
  ?>
  ><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('date_<? echo $row_languages->id?>'),this);" style='vertical-align:bottom;' />
  </td>
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
  <font class='fieldTitle'><?php echo _("Description Courte"); ?> </font><br>
  	<?php
  	echo "<textarea name='short_desc_".$row_languages->id."' style='width:100%;height:200px;'>";
  	if($action=="add"){
  		echo $PHOTO_editor_default_value;
  	}
  	if($action=="edit"){
  		echo unhtmlentities($edit->short_desc);
  	}
  	if($action=="insert" || $action=="update"){
  		echo $_POST["short_desc_".$row_languages->id];
  	}
  	echo "</textarea>";
	?>
  <br />
  <font class='fieldTitle'><?php echo _("Détails"); ?> </font><br>
  	<?php
  	echo "<textarea name='details_".$row_languages->id."' style='width:100%;height:600px;'>";
  	if($action=="add"){
  		echo $PHOTO_editor_default_value;
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
