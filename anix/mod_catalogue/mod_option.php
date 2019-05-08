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
if(isset($_POST["idProduct"])){
	$idProduct=$_POST["idProduct"];
} elseif(isset($_GET["idProduct"])){
	$idProduct=$_GET["idProduct"];
} else $idProduct=0;
if(isset($_POST["idOption"])){
	$idOption=$_POST["idOption"];
} elseif(isset($_GET["idOption"])){
	$idOption=$_GET["idOption"];
} else $idOption=0;
?>
<?php
include("mod_option.actions.php");
?>
<?
$title = _("Anix - Options du produit - Edition");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'une option au produit"));break;
	case "insert":setTitleBar(_("Ajout d'une option au produit"));break;
	case "edit":setTitleBar(_("Modification d'une option du produit"));break;
	case "update":setTitleBar(_("Modification d'une option du produit"));break;
	default:setTitleBar(_("Modification d'une option du produit"));break;
}
?>
<?
//Javascript only available if we are in edit mode...
if($action=="edit"){
?>
<SCRIPT language='javascript'>
function updateFields(radioNumber,defaultValue){
	<?
	$languages=request("SELECT $TBL_gen_languages.id FROM `$TBL_gen_languages` WHERE $TBL_gen_languages.used ='Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
	$nbLanguages= mysql_num_rows($languages);
	while($language = mysql_fetch_object($languages)){
		echo "document.mainForm.choice_new_".$language->id.".disabled=(!document.mainForm.addChoice.checked);\n";
		echo "if(!document.mainForm.addChoice.checked) {document.mainForm.choice_new_".$language->id.".value=\"\";}\n";
	}
	?>
	document.mainForm.default_choice[radioNumber].disabled=(!document.mainForm.addChoice.checked);
	document.mainForm.price_diff_new.disabled=(!document.mainForm.addChoice.checked);
	document.mainForm.price_value_new.disabled=(!document.mainForm.addChoice.checked);
	document.mainForm.price_method_new.disabled=(!document.mainForm.addChoice.checked);
	if(!document.mainForm.addChoice.checked){
		document.mainForm.default_choice[defaultValue].checked=true;
		document.mainForm.price_value_new.value="";
	}
}
</SCRIPT>
<?
} //if action = edit
?>
<form id='main_form' name='mainForm' action='./mod_option.php' method='POST' enctype='multipart/form-data'>
<input type='hidden' name='idProduct' value='<?=$idProduct?>'>
<?
$request = request("SELECT $TBL_catalogue_info_products.name from $TBL_catalogue_info_products,$TBL_gen_languages WHERE $TBL_catalogue_info_products.id_product='$idProduct' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id",$link);
if(!mysql_num_rows($request)) die (_("Erreur de protection : Ce produit n'existe pas."));
$product = mysql_fetch_object($request);
if($action=="add") {
	echo "<input type='hidden' name='action' value='insert'>";
}
if($action=="edit") {
	echo "<input type='hidden' name='action' value='update'>";
	echo "<input type='hidden' name='idOption' value='$idOption'>";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>"mod_product.php?action=edit&idProduct=$idProduct");
printButtons($buttons);
?>
<table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2'>
  <table width='100%'>
  <tr>
    <td>
      <B><?php echo _("Option pour le produit"); ?>: </B><br>
      &nbsp;&nbsp;&nbsp;<?=$product->name?>
    </td>
    <td>
      <B><?php echo _("Nom de l'option"); ?>:</B><br>
      <table>
        <?
        if($action=="edit"){
        	$languages=request("SELECT $TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_gen_languages.id,$TBL_catalogue_info_options.name 'option' FROM `$TBL_gen_languages`,`$TBL_catalogue_info_options` WHERE $TBL_gen_languages.used ='Y' and $TBL_catalogue_info_options.id_option='$idOption' and $TBL_catalogue_info_options.id_language=$TBL_gen_languages.id ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
        } elseif($action=="add") {
        	$languages=request("SELECT $TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_gen_languages.id FROM `$TBL_gen_languages` WHERE $TBL_gen_languages.used ='Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
        }
        while($language=mysql_fetch_object($languages)){
        	echo "<tr>";
        	echo "<td align='center'><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'></td><td align='left'><input type='text' name='name_".$language->id."' value=\"";
        	if($action=="edit") echo $language->option;
        	echo "\" size='30'></td>";
        	echo "</tr>";
        }
        ?>
      </table>
    </td>
  </tr>
  </table>
</td>
</tr>
<?
//if the option exists we may change or add choices...
$nbChoices=0;$defaultChoice=0;
if($action=="edit"){
?>
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      <font class='edittable_header'>
  	     <?php echo _("Liste des choix proposée"); ?>
  	  </font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width='100%' border='1' align='center'>
    <tr>
      <?
      $languages=request("SELECT $TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_gen_languages.id FROM `$TBL_gen_languages` WHERE $TBL_gen_languages.used ='Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
      $nbLanguages= mysql_num_rows($languages);
      while($language = mysql_fetch_object($languages)){
      	echo "<td align='center'><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'></td>";
      }
      echo "<td align='center'><b>"._("Défaut?")."</b></td>";
      echo "<td align='center'><b>"._("Différence de prix")."</b></td>";
      echo "<td align='center'><b>"._("Action")."</b></td>";
      ?>
    </tr>
    <?
    $choices=request("
        SELECT id,default_choice,price_diff,price_value,price_method,ordering
        FROM `$TBL_catalogue_product_option_choices`
        WHERE $TBL_catalogue_product_option_choices.id_option = '$idOption'
        ORDER BY $TBL_catalogue_product_option_choices.ordering",$link);
    if(mysql_num_rows($choices)){
    	//get the max ordering
    	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_catalogue_product_option_choices` WHERE id_option='$idOption' GROUP BY id_option",$link);
    	if(mysql_num_rows($tmp)) {
    		$maxOrder= mysql_fetch_object($tmp);
    		$maxOrderValue = $maxOrder->maximum;
    	} else $maxOrderValue=1;
    }
    while($choice=mysql_fetch_object($choices)){
    	$choiceInfos=request("
          SELECT $TBL_gen_languages.id id_language,$TBL_catalogue_info_choices.value
          FROM `$TBL_gen_languages`,`$TBL_catalogue_info_choices`
          WHERE $TBL_catalogue_info_choices.id_choice='".$choice->id."'
          AND $TBL_gen_languages.used ='Y'
          AND $TBL_catalogue_info_choices.id_language=$TBL_gen_languages.id
          ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default
        ",$link);
    	echo "<tr>";
    	while($choiceInfo=mysql_fetch_object($choiceInfos)){
    		echo "<td><input type='text' name='choice_".$choice->id."_".$choiceInfo->id_language."' value=\"".$choiceInfo->value."\"></td>";
    	}
    	echo "<td align='center'><input type='radio' name='default_choice' value='".$choice->id."'";
    	if($choice->default_choice=="Y") { echo " CHECKED"; $defaultChoice=$nbChoices;}
    	echo "></td>";
    	echo "<td>";
    	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    	echo "<select name='price_diff_".$choice->id."'>";
    	echo "<option value='increment' ".(($choice->price_diff=="increment")?"SELECTED":"")."> + </option>";
    	echo "<option value='decrement' ".(($choice->price_diff=="decrement")?"SELECTED":"")."> - </option>";
    	echo "</select>&nbsp;";
    	echo "<input type='text' size='5' name='price_value_".$choice->id."' value='".$choice->price_value."'>";
    	echo "&nbsp;<select name='price_method_".$choice->id."'>";
    	echo "<option value='currency' ".(($choice->price_method=="currency")?"SELECTED":"").">$currency_symbol</option>";
    	echo "<option value='percentage' ".(($choice->price_method=="percentage")?"SELECTED":"").">%</option>";
    	echo "</select>";
    	echo "</td>";
    	echo "<td align='center'>";
    	if($choice->ordering>1){
    		echo "<a href='./mod_option.php?idProduct=$idProduct&idOption=$idOption&idChoice=".$choice->id."&action=moveChoiceUp'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
    	} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
    	if($choice->ordering<$maxOrderValue){
    		echo "<a href='./mod_option.php?idProduct=$idProduct&idOption=$idOption&idChoice=".$choice->id."&action=moveChoiceDown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
    	} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
    	echo "<a href='./del_choice.php?action=delChoice&idChoice=".$choice->id."&idOption=$idOption&idProduct=$idProduct'><img src='../images/del.gif' border='0' alt=\""._("Supprimer")."\"></a>";
    	echo "</td>";
    	echo "</tr>";
    	$nbChoices++;
    }
    ?>
    <tr>
      <td colspan='<?=$nbLanguages+3?>'>
        <input type='checkbox' name='addChoice'<?echo " onClick='javascript:updateFields($nbChoices,$defaultChoice)'";?>><B><?php echo _("Ajouter un choix"); ?>: </B>
      </td>
    </tr>
    <tr>
    <?
    $languages=request("SELECT $TBL_gen_languages.id FROM `$TBL_gen_languages` WHERE $TBL_gen_languages.used ='Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
    $nbLanguages= mysql_num_rows($languages);
    while($language = mysql_fetch_object($languages)){
    	echo "<td><input type='text' name='choice_new_".$language->id."'></td>";
    }
    echo "<td align='center'><input type='radio' name='default_choice' value='0'";
    if(!$nbChoices) echo " CHECKED";
    echo "></td>";
    echo "<td>";
    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    echo "<select name='price_diff_new'>";
    echo "<option value='increment'>+</option>";
    echo "<option value='decrement'>-</option>";
    echo "</select>&nbsp;";
    echo "<input type='text' size='5' name='price_value_new'>";
    echo "&nbsp;<select name='price_method_new'>";
    echo "<option value='currency'>$currency_symbol</option>";
    echo "<option value='percentage'>%</option>";
    echo "</select>";
    echo "</td>";
    ?>
      <td>&nbsp;</td>
    </tr>
    </table>
    </td>
  </tr>
  <SCRIPT Langage='javascript'>
  updateFields(<?=$nbChoices?>,<?=$defaultChoice?>);
  </SCRIPT>
<?
} //if action=='edit'
?>
<?
if($action=="edit" && $nbChoices){
?>
  <SCRIPT Langage='javascript'>
  updateFields(<?=$nbChoices?>,<?=$defaultChoice?>);
  </SCRIPT>
<?
} //if action = edit && nbchoices
?>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
