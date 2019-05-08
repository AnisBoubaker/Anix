<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
//Defining default values
$idCat=0;
$requestString="";
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idExtraField"])){
	$idExtraField=$_POST["idExtraField"];
} elseif(isset($_GET["idExtraField"])){
	$idExtraField=$_GET["idExtraField"];
}
if(isset($_POST["idCat"])){
	$idCat=$_POST["idCat"];
} elseif(isset($_GET["idCat"])){
	$idCat=$_GET["idCat"];
} else $idCat="";
?>
<?
$title = _("Anix - Champs Supplémentaires - Edition");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'un champs additionnel d'éléments"));break;
	case "insert":setTitleBar(_("Ajout d'un champs additionnel d'éléments"));break;
	case "edit":setTitleBar(_("Modification d'un champs additionnel d'éléments"));break;
	case "update":setTitleBar(_("Modification d'un champs additionnel d'éléments"));break;
	default:setTitleBar(_("Modification d'un champs additionnel d'éléments"));break;
}
?>

<form id='main_form' action='./mod_category.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add") {
	echo "<input type='hidden' name='action' value='addExtraField'>";
	echo "<input type='hidden' name='idCat' value='$idCat'>";
}
if($action=="edit") {
	echo "<input type='hidden' name='action' value='updateExtraField'>";
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='idExtraField' value='$idExtraField'>";
	$result=request("select $TBL_lists_extrafields.id,$TBL_lists_extrafields.datatype,$TBL_lists_extrafields.id_cat,$TBL_lists_extrafields.params,$TBL_lists_info_extrafields.name from $TBL_lists_extrafields,$TBL_lists_info_extrafields,$TBL_gen_languages where $TBL_lists_extrafields.id=$idExtraField and $TBL_gen_languages.id='$used_language_id' and $TBL_lists_info_extrafields.id_extrafield=$TBL_lists_extrafields.id and $TBL_lists_info_extrafields.id_language=$TBL_gen_languages.id",$link);
	$extraField=mysql_fetch_object($result);
}
$cancelLink="./mod_category.php?idCat=$idCat&action=edit";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>

<table id='main_table' border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2'>
    <B><?php echo _("Nom du champs"); ?>:</B> <br>
    <?
    if($action=="edit"){
    	$languages=request("SELECT $TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_gen_languages.id,$TBL_lists_info_extrafields.name extrafield FROM `$TBL_gen_languages`,`$TBL_lists_info_extrafields` WHERE $TBL_gen_languages.used ='Y' and $TBL_lists_info_extrafields.id_extrafield=$idExtraField and $TBL_lists_info_extrafields.id_language=$TBL_gen_languages.id ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
    } elseif($action=="add") {
    	$languages=request("SELECT $TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_gen_languages.id FROM `$TBL_gen_languages` WHERE $TBL_gen_languages.used ='Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
    }
    echo "<table>";
    while($language=mysql_fetch_object($languages)){
    	echo "<tr>";
    	echo "<td align='center'><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'></td><td align='left'><input type='text' name='name_".$language->id."' value=\"";
    	if($action=="edit") echo $language->extrafield;
    	echo "\" size='30'></td>";
    	echo "</tr>";
    }
    echo "</table>";
    ?>

    <B><?php echo _("Type de données"); ?>:</B>
  <? if($action=="add"){ ?>
  <br>
  <table width='70%' border="0">
  <tr>
    <td align='center' valign='top'><input type='radio' name='type' value='text'></td>
	<td valign='top'>
	  <b><?php echo _("Texte Simple"); ?></b><br>
	  <?php echo _("Longueur maximale (<250)"); ?>: <input type='text' name='param1' value='50' size='3'>
	</td>
  </tr>
  <tr>
    <td align='center' valign='top'><input type='radio' name='type' value='date'></td>
	<td valign='top'>
	  <b><?php echo _("Champs de Date"); ?></b>
	</td>
  </tr>
  <tr>
    <td align='center' valign='top'><input type='radio' name='type' value='rich'></td>
	<td valign='top'>
	  <b><?php echo _("Texte Riche"); ?></b>
	</td>
  </tr>
  <tr>
    <td align='center' valign='top'><input type='radio' name='type' value='selection'></td>
	<td valign='top'>
	  <b><?php echo _("Sélection"); ?></b><br>
	  <?php echo _("Valeurs proposées")." ("._("séparées par des")." <b>;</b>&nbsp; ) :"; ?> <br>
	  <?
	  $languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used ='Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
	  echo"<table border='0' align='left'>";
	  while($language=mysql_fetch_object($languages)){
	  	echo"<tr valign='top'>";
	  	echo "<td align='right'><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'></td><td align='left'><textarea  class='mceNoEditor' cols='80' name='param3_".$language->id."'></textarea></td>";
	  	echo"</tr>";
	  }
	  echo"</table>";
	  ?>
	</td>
  </tr>
  </table>
  <b>Description</b>
  <?php
  $languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used ='Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
  echo"<table border='0' align='left'>";
  while($language=mysql_fetch_object($languages)){
  	echo"<tr valign='top'>";
  	echo "<td align='right'><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'></td><td align='left'><textarea  class='mceNoEditor' cols='80' name='description_".$language->id."'></textarea></td>";
  	echo"</tr>";
  }
  echo"</table>";
  ?>
  <? } //if action=add
  ?>
  <? if($action=="edit"){
  	echo $extraField->datatype."<br>";
  	echo "<input type='hidden' name='datatype' value='".$extraField->datatype."'>";
  	if($extraField->datatype=="text"){
  		echo _("Longueur maximale (<250)").": <input type='text' name='param1' value=\"".$extraField->params."\" size='3'>";
  	}
  	if($extraField->datatype=="selection"){
  		echo _("Valeurs proposées")." ("._("séparées par des")." <b>;</b>&nbsp; ) : <br>";
  		$languages=request("SELECT $TBL_gen_languages.id,$TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_lists_info_extrafields.selection_values FROM `$TBL_gen_languages`,`$TBL_lists_info_extrafields`  WHERE $TBL_gen_languages.used ='Y' and $TBL_lists_info_extrafields.id_language=$TBL_gen_languages.id and $TBL_lists_info_extrafields.id_extrafield=$idExtraField ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
  		echo"<table border='0' width='100%' align='center'>";
  		while($language=mysql_fetch_object($languages)){
  			echo"<tr>";
  			echo "<td align='center'><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'></td><td align='left'><textarea  class='mceNoEditor' cols='80' name='param3_".$language->id."'>".stripslashes($language->selection_values)."</textarea></td>";
  			echo"</tr>";
  		}
  		echo"</table>";
  	}
  	echo "<br /><b>Description:</b><br />";
  	$languages=request("SELECT $TBL_gen_languages.id,$TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_lists_info_extrafields.description FROM `$TBL_gen_languages`,`$TBL_lists_info_extrafields`  WHERE $TBL_gen_languages.used ='Y' and $TBL_lists_info_extrafields.id_language=$TBL_gen_languages.id and $TBL_lists_info_extrafields.id_extrafield=$idExtraField ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
  	echo"<table border='0' align='left'>";
  	while($language=mysql_fetch_object($languages)){
  		echo"<tr valign='top'>";
  		echo "<td align='right'><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'></td><td align='left'><textarea  class='mceNoEditor' cols='80' name='description_".$language->id."'>".stripslashes($language->description)."</textarea></td>";
  		echo"</tr>";
  	}
  	echo"</table>";
  }
  ?>
</td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
