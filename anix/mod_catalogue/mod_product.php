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
if(isset($_POST["idProduct"])){
	$idProduct=$_POST["idProduct"];
} elseif(isset($_GET["idProduct"])){
	$idProduct=$_GET["idProduct"];
} else $idProduct=0;
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat=0;
?>
<?php
include ("./mod_product.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'un produit");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'un produit");
else $title = _("Anix - Modification d'un produit");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'un produit"));break;
	case "insert":setTitleBar(_("Ajout d'un produit"));break;
	case "edit":setTitleBar(_("Modification d'un produit"));break;
	case "update":setTitleBar(_("Modification d'un produit"));break;
	default:setTitleBar(_("Modification d'un produit"));break;
}
?>
<form id='main_form' action='./mod_product.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='action' value='insert'>";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idProduct' value='$idProduct'>";
	echo "<input type='hidden' name='action' value='update'>";
	$result=request("SELECT * from $TBL_catalogue_products where id='$idProduct'",$link);
	if(!mysql_num_rows($result)) die("Erreur de protection: Ce produit n'existe pas.");
	$edit = mysql_fetch_object($result);
	$idCat=$edit->id_category;
}
$cancelLink="./list_products.php?action=edit&idCat=$idCat";
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
<?php
/**
 * LOAD TABS
 */
include("./mod_product.tabs.php");
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
<? //Rest of while languages
if($action=="edit"){
	$result=request("SELECT id,id_category,name,description,keywords,htmltitle,htmldescription from $TBL_catalogue_info_products,$TBL_catalogue_products where id='$idProduct' and id=id_product and id_language='".$row_languages->id."'",$link);
	//echo "SELECT id,id_parent,description,name from $TBL_catalogue_info_categories,$TBL_catalogue_categories where id_news_cat='$idCat' and id_language='".$row_languages->id."'";
	if(!mysql_num_rows($result)) die("Erreur de protection: Ce produit n'existe pas.");
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
  $requestString="select * from $TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields where (datatype='text' or datatype='selection') and (";
  $first=true;
  foreach($parentCategories as $cat){
  	if(!$first) $requestString.=" OR ";
  	$requestString.="id_cat='$cat'";
  	$first = false;
  }
  $requestString.=") and id_language='".$row_languages->id."' and id_extrafield=id order by id_cat,ordering";
  $extra_fields=request($requestString,$link);
  while($field=mysql_fetch_object($extra_fields)){
  	if($field->datatype=="text"){
  		echo "<tr>";
  		$value="";
  		$exists=request("select value from $TBL_catalogue_extrafields_values where id_extrafield='".$field->id."' and id_product='$idProduct' and id_language='".$row_languages->id."'",$link);
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
  		$exists=request("select value from $TBL_catalogue_extrafields_values where id_extrafield='".$field->id."' and id_product='$idProduct' and id_language='".$row_languages->id."'",$link);
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
	$requestString="select * from $TBL_catalogue_extrafields,$TBL_catalogue_info_extrafields where datatype='rich' and (";
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
			$exists=request("select value from $TBL_catalogue_extrafields_values where id_extrafield='".$field->id."' and id_product='$idProduct' and id_language='".$row_languages->id."'",$link);
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
