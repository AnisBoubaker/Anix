<?
include ("../config.php");
include ("../ImageEditor.php");
include ("./module_config.php");
$link = dbConnect();
$delete=false;
$action="";
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idItem"])){
	$idItem=$_POST["idItem"];
} elseif(isset($_GET["idItem"])){
	$idItem=$_GET["idItem"];
} else $idItem=0;
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?php
include ("./mod_item.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'un élément");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'un élément");
else $title = _("Anix - Modification d'un élément");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'un élément"));break;
	case "insert":setTitleBar(_("Ajout d'un élément"));break;
	case "edit":setTitleBar(_("Modification d'un élément"));break;
	case "update":setTitleBar(_("Modification d'un élément"));break;
	default:setTitleBar(_("Modification d'un élément"));break;
}
?>
<form id='main_form' action='./mod_item.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='insert'>";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idItem' value='$idItem'>";
	echo "<input type='hidden' name='action' value='update'>";
	$result=request("SELECT id,id_category,active,image_file_large,image_file_small from $TBL_lists_items where id='$idItem'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_lists_info_categories,$TBL_lists_categories where id_news_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die("Erreur de protection: Cet élément n'existe pas.");
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
$cancelLink="./list_items.php?action=edit&idCat=$idCat";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
printLanguageToggles($link);
?>
<table id='main_table' border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2' bgcolor='#FFFFFF'>
<?
$parentCategories = getParentsPathIds($idCat,$link);
?>
<table width='100%'>
  <tr>
    <td valign='top' style='text-align:center;'>
      <?
      if($action=="edit" || $action=="update"){
      	echo "<center><a href='../".$CATALOG_folder_images.$edit->image_file_large."' target='_blank'><img class='item_image' src='../".$CATALOG_folder_images.$edit->image_file_small."' alt=\""._("Agrandir")."\"></a></center>";
      }
      if($action=="add" || $action=="insert"){
      	echo "<center><a href='../$CATALOG_folder_images/imgflex_large_no_image.jpg' target='_blank'><img class='item_image' src='../$CATALOG_folder_images/imgflex_small_no_image.jpg' alt=\""._("Agrandir")."\"></a></center>";
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
    <td valign='top'>
    <?php
    /**
	 * LOAD TABS
	 */
    include("./mod_item.tabs.php");
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
	$result=request("SELECT id,id_category,name,description,keywords,htmltitle,htmldescription from $TBL_lists_info_items,$TBL_lists_items where id='$idItem' and id=id_item and id_language='".$row_languages->id."'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_lists_info_categories,$TBL_lists_categories where id_news_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die("Erreur de protection: Cet élément n'existe pas.");
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
?>
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
  <td><font class='fieldTitle'><?php echo _("Nom"); ?>:</font></td>
  <td><input type='text' name='name_<? echo $row_languages->id?>' size='40'
  <?
  if($action=="edit"){
  	echo " value=\"".$edit->name."\"";
  }
  if($action=="insert" || $action=="update"){
  	echo " value='".$_POST["name_".$row_languages->id]."'";
  }
  ?>
  ></td>
  </tr>
  <tr>
  <td><font class='fieldTitle'><?php echo _("Titre(HTML):"); ?></font></td>
  <td><input type='text' name='htmltitle_<? echo $row_languages->id?>' style='width:100%;'
  <?
  if($action=="edit"){
  	echo " value=\"".$edit->htmltitle."\"";
  }
  if($action=="insert" || $action=="update"){
  	echo " value='".$_POST["htmltitle_".$row_languages->id]."'";
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
	  if($action=="edit") echo $edit->htmldescription;
	  if($action=="insert" || $action=="update") echo $_POST["htmldescription_".$row_languages->id];
	  ?></TEXTAREA>
	</td>
	</tr>
	</table>
  </td>
  </tr>
  <?
  //Show text extrafields
  //Parents Categories have been calculated already outside of the while for performances
  //Using the function getParentsPathIds
  $requestString="select * from $TBL_lists_extrafields,$TBL_lists_info_extrafields where (datatype='text' or datatype='selection' or datatype='date') and (";
  $first=true;
  foreach($parentCategories as $cat){
  	if(!$first) $requestString.=" OR ";
  	$requestString.="id_cat='$cat'";
  	$first = false;
  }
  $requestString.=") and id_language='".$row_languages->id."' and id_extrafield=id order by id_cat,ordering";
  $extra_fields=request($requestString,$link);
  while($field=mysql_fetch_object($extra_fields)){
  	if($field->datatype=="date"){
  		echo "<tr>";
  		$value="";
  		$exists=request("select value from $TBL_lists_extrafields_values where id_extrafield='".$field->id."' and id_item='$idItem' and id_language='".$row_languages->id."'",$link);
  		if(mysql_num_rows($exists)){
  			$extrafield_value=mysql_fetch_object($exists);
  			$value=$extrafield_value->value;
  		}
  		echo "<td><font class='fieldTitle'>".$field->name." : </font></td>";
  		echo "<td>";
  		echo "<input type='text' name='field".$field->id."_".$row_languages->id."' id='field".$field->id."_".$row_languages->id."' size='20' value=\"$value\" />";
  		echo "<img src='../images/calendar.gif' onclick=\"scwShow(document.getElementById('field".$field->id."_".$row_languages->id."'),this);\" style='vertical-align:bottom;' />";
  		echo "</td>";
  		echo "</tr>";
  	}
  	if($field->datatype=="text"){
  		echo "<tr>";
  		$value="";
  		$exists=request("select value from $TBL_lists_extrafields_values where id_extrafield='".$field->id."' and id_item='$idItem' and id_language='".$row_languages->id."'",$link);
  		if(mysql_num_rows($exists)){
  			$extrafield_value=mysql_fetch_object($exists);
  			$value=$extrafield_value->value;
  		}
  		echo "<td><font class='fieldTitle'>".$field->name." : </font></td>";
  		echo "<td><input name='field".$field->id."_".$row_languages->id."' type='text' size='40' value=\"$value\"></td>";
  		echo "</tr>";
  	}
  	if($field->datatype=="selection"){
  		echo "<tr>";
  		echo "<td><font class='fieldTitle'>".$field->name." : </font></td>";
  		echo "<td>";
  		$value="";
  		$exists=request("select value from $TBL_lists_extrafields_values where id_extrafield='".$field->id."' and id_item='$idItem' and id_language='".$row_languages->id."'",$link);
  		if(mysql_num_rows($exists)){
  			$extrafield_value=mysql_fetch_object($exists);
  			$value=$extrafield_value->value;
  		}
  		$selection=stripslashes($field->selection_values);
  		if($selection!=""){
  			//Fields names format:
  			echo "<select name='field".$field->id."_".$row_languages->id."'>";
  			$values=explode(";",$selection);
  			for($i=0;$i<count($values);$i++){
  				echo "<option value=\"".$values[$i]."\"";
  				if (htmlentities($values[$i],ENT_QUOTES,"UTF-8")==$value) echo " SELECTED";
  				echo ">".$values[$i]."</option>";
  			}
  			echo "</select><br>";
  		} else{
  			echo _("Indéfini");
  			echo "<input name='field".$field->id."_".$row_languages->id."' type='hidden' value=''><br>";
  		}
  		echo "</td>";
  		echo "</tr>";
  	}

  }
  ?>
  </tr>
  <tr>
  <td colspan='2'>
  <font class='fieldTitle'><?php echo _("Déscription"); ?>:</font><br>
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
	?>
	<BR>
	<?
	//Show rich extrafields
	$requestString="select * from $TBL_lists_extrafields,$TBL_lists_info_extrafields where datatype='rich' and (";
	$first=true;
	foreach($parentCategories as $cat){
		if(!$first) $requestString.=" OR ";
		$requestString.="id_cat='$cat'";
		$first = false;
	}
	$requestString.=") and id_language='".$row_languages->id."' and id_extrafield=id order by id_cat,ordering";
	$extra_fields=request($requestString,$link);
	while($field=mysql_fetch_object($extra_fields)){
		echo "<font class='fieldTitle'>".$field->name." : </font>";
		$value="";
		echo "<textarea name='field".$field->id."_".$row_languages->id."' style='width:100%;height:300px;'>";
		if($action=="add"){
			echo $CATALOG_editor_default_value;
		}
		if($action=="edit"){
			$exists=request("select value from $TBL_lists_extrafields_values where id_extrafield='".$field->id."' and id_item='$idItem' and id_language='".$row_languages->id."'",$link);
			if(mysql_num_rows($exists)){
				$extrafield_value=mysql_fetch_object($exists);
				echo unhtmlentities($extrafield_value->value);
			} else {
				echo unhtmlentities($CATALOG_editor_default_value);
			}
		}
		if($action=="insert" || $action=="update"){
			echo stripslashes($_POST["field".$field->id."_".$row_languages->id]);
		}
		echo "</textarea>";
	}
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
