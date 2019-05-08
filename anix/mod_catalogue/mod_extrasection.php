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
if(isset($_POST["idExtraSection"])){
	$idExtraSection=$_POST["idExtraSection"];
} elseif(isset($_GET["idExtraSection"])){
	$idExtraSection=$_GET["idExtraSection"];
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
	case "add":setTitleBar(_("Ajout d'une section additionnelle"));break;
	case "insert":setTitleBar(_("Ajout d'une section additionnelle"));break;
	case "edit":setTitleBar(_("Modification d'une section additionnelle"));break;
	case "update":setTitleBar(_("Modification d'une section additionnelle"));break;
	default:setTitleBar(_("Modification d'une section additionnelle"));break;
}
?>

<form id='main_form' action='./mod_category.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add") {
	echo "<input type='hidden' name='action' value='addExtraSection'>";
	echo "<input type='hidden' name='idCat' value='$idCat'>";
}
if($action=="edit") {
	echo "<input type='hidden' name='action' value='updateExtraSection'>";
	echo "<input type='hidden' name='idCat' value='$idCat'>";
	echo "<input type='hidden' name='idExtraSection' value='$idExtraSection'>";
	$result=request("select $TBL_catalogue_extracategorysection.id,$TBL_catalogue_extracategorysection.id_cat,$TBL_catalogue_info_extracategorysection.name from $TBL_catalogue_extracategorysection,$TBL_catalogue_info_extracategorysection,$TBL_gen_languages where $TBL_catalogue_extracategorysection.id=$idExtraSection and $TBL_gen_languages.id='$used_language_id' and $TBL_catalogue_info_extracategorysection.id_extrasection=$TBL_catalogue_extracategorysection.id and $TBL_catalogue_info_extracategorysection.id_language=$TBL_gen_languages.id",$link);
	$extraField=mysql_fetch_object($result);
}

$cancelLink="./mod_category.php?idCat=$idCat&action=edit";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>

<table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
<tr>
<td colspan='2'>
    <B><?php echo _("Nom du champs"); ?>:</B> <br>
    <?
    if($action=="edit"){
    	$languages=request("SELECT $TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_gen_languages.id,$TBL_catalogue_info_extracategorysection.name extrasection FROM `$TBL_gen_languages`,`$TBL_catalogue_info_extracategorysection` WHERE $TBL_gen_languages.used ='Y' and $TBL_catalogue_info_extracategorysection.id_extrasection='$idExtraSection' and $TBL_catalogue_info_extracategorysection.id_language=$TBL_gen_languages.id ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
    } elseif($action=="add") {
    	$languages=request("SELECT $TBL_gen_languages.image_file,$TBL_gen_languages.locales_folder,$TBL_gen_languages.id FROM `$TBL_gen_languages` WHERE $TBL_gen_languages.used ='Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default",$link);
    }
    echo "<table>";
    while($language=mysql_fetch_object($languages)){
    	echo "<tr>";
    	echo "<td align='center'><img src='../locales/".$language->locales_folder."/images/".$language->image_file."' border='0'></td><td align='left'><input type='text' name='name_".$language->id."' value=\"";
    	if($action=="edit") echo $language->extrasection;
    	echo "\" size='30'></td>";
    	echo "</tr>";
    }
    echo "</table>";
    ?>
</td>
</tr>
</table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
