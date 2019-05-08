<?
include ("../config.php");
include ("../ImageEditor.php");
include ("./module_config.php");
$link = dbConnect();
$action="";
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idFeatured"])){
	$idFeatured=$_POST["idFeatured"];
} elseif(isset($_GET["idFeatured"])){
	$idFeatured=$_GET["idFeatured"];
} else $idFeatured="";
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory="";
?>
<?php
include("./mod_featured.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'une vedette");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'une vedette");
else $title = _("Anix - Modification d'une vedette");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'une vedette"));break;
	case "insert":setTitleBar(_("Ajout d'une vedette"));break;
	case "edit":setTitleBar(_("Modification d'une vedette"));break;
	case "update":setTitleBar(_("Modification d'une vedette"));break;
	default:setTitleBar(_("Modification d'une vedette"));break;
}
?>
<form id='main_form' action='./mod_featured.php' method='POST' enctype='multipart/form-data' name='mainForm'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='action' value='insert'>";
	echo "<input type='hidden' name='idCategory' value='$idCategory'>";
	$cancelLink="./list_featured.php";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idFeatured' value='$idFeatured'>";
	echo "<input type='hidden' name='action' value='update'>";
	echo "<input type='hidden' name='idCategory' value='$idCategory'>";
	$cancelLink="./list_featured.php";
}
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
printLanguageToggles($link);
?>
  <table id='main_table' border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr>
      <td colspan='2'>
<?
if($action=="edit" || $action=="update"){
	$result=request("SELECT * from $TBL_catalogue_featured,$TBL_catalogue_info_featured where id='$idFeatured'",$link);
	if(!mysql_num_rows($result)) die("Erreur de protection: Cette vedette n'existe pas.");
	$edit = mysql_fetch_object($result);
}
?>
        <table width='100%'>
          <tr valign='top'>
            <td>
<?
if($action=="edit" || $action=="update"){
	echo "<center><a href='../".$CATALOG_folder_images.$edit->image_file_large."' target='_blank'><IMG src='../".$CATALOG_folder_images.$edit->image_file_small."' border='1' alt=\""._("Agrandir")."\"></a></center><br>";
	echo "<i>"._("Modifier l'image").":</i><br>";
	echo "<input type='file' name='image_file'>";
}
if($action=="add" || $action=="insert"){
	echo "<center><a href='../".$CATALOG_folder_images."imgfeatured_large_no_image.jpg' target='_blank' alt='Agrandir l\'image'><IMG src='../".$CATALOG_folder_images."imgfeatured_small_no_image.jpg' border='1' alt=\""._("Agrandir")."\"></a></center><br>";
	echo "<i>"._("Modifier l'image").":</i><br>";
	echo "<input type='file' name='image_file'>";
}
              ?>
            </td>
            <td width='33%'>
              <table class='message' width='100%'>
                <tr>
                  <td colspan='2'>
                    <FONT><B><?php echo _("Informations"); ?>:</B></FONT>
                  </td>
                </tr>
                <tr>
                  <td colspan='2'><?php echo _("Affichage"); ?>:
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='radio' name='active' value='Y' <?
                    if($action=="add") echo " CHECKED";
                    if($action=="edit" && $edit->active=='Y') echo " CHECKED";
                    if(($action=="insert" || $action=="update") && $_POST["active"]=='Y')  echo " CHECKED";
                    ?>>
                  </td>
                  <td>
                    <?php echo _("Toujours affichée"); ?>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='radio' name='active' value='DATE' <?
                    if($action=="edit" && $edit->active=='DATE') echo " CHECKED";
                    if(($action=="insert" || $action=="update") && $_POST["active"]=='DATE')  echo " CHECKED";
                    ?>>
                  </td>
                  <td>
                    <?php echo _("Du"); ?> <input type='text' name='from_date' id='from_date' size='10' <?
                    if($action=="edit") echo " value='".$edit->from_date."'";
                    if($action=="insert" || $action=="update")  echo " value='".$_POST["from_date"]."'";
                    ?> READONLY><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('from_date'),this);" style='vertical-align:bottom;' />
                    <?php echo _("Au"); ?> <input type='text' name='to_date' id='to_date' size='10' <?
                    if($action=="edit") echo " value='".$edit->to_date."'";
                    if($action=="insert" || $action=="update")  echo " value='".$_POST["to_date"]."'";
                    ?> READONLY><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('to_date'),this);" style='vertical-align:bottom;' />
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type='radio' name='active' value='N' <?
                    if($action=="edit" && $edit->active=='N') echo " CHECKED";
                    if(($action=="insert" || $action=="update") && $_POST["active"]=='N')  echo " CHECKED";
                    ?>>
                  </td>
                  <td>
                    <?php echo _("Désactivée"); ?>
                  </td>
                </tr>
              </table>
            </td>
            <td width='33%'>
              <table class='message' width='100%'>
                <tr>
                  <td><font><?php echo _("Lien"); ?>:</font><td>
                </tr>
                <tr>
                  <td><?
                  if($action=="edit" && $edit->id_catalogue_prd){
                  	$request = request("SELECT $TBL_catalogue_info_products.name FROM $TBL_catalogue_info_products,$TBL_gen_languages WHERE $TBL_catalogue_info_products.id_product='".$edit->id_catalogue_prd."' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_products.id_language=$TBL_gen_languages.id",$link);
                  	$tmp = mysql_fetch_object($request);
                  	echo _("Produit").": ".$tmp->name;
                  }
                  elseif($action=="edit" && $edit->id_catalogue_cat){
                  	$request = request("SELECT $TBL_catalogue_info_categories.name FROM $TBL_catalogue_info_categories,$TBL_gen_languages WHERE $TBL_catalogue_info_categories.id_catalogue_cat='".$edit->id_catalogue_cat."' AND $TBL_gen_languages.id='$used_language_id' AND $TBL_catalogue_info_categories.id_language=$TBL_gen_languages.id",$link);
                  	$tmp = mysql_fetch_object($request);
                  	echo _("Catégorie").": ".$tmp->name;
                  } else {
                  	echo "<center><i>"._("Non liée")."</i></center>";
                  }
                      ?>
                  </td>
                </tr>
                <tr>
                  <td align='right'>
                    <?
                    if($action=="edit" && $edit->id_catalogue_prd){
                    	echo "<a href='./list_productsFeatured.php?action=addCat&idFeatured=$idFeatured'>"._("Lier à une catégorie")."</a>";
                    	echo "&nbsp;&nbsp;<a href='./mod_featured.php?action=unlink&idFeatured=$idFeatured&idCategory=$idCategory'>"._("Non lié")."</a>";
                    }
                    elseif($action=="edit" && $edit->id_catalogue_cat){
                    	echo "<a href='./list_productsFeatured.php?action=addProduct&idFeatured=$idFeatured'>"._("Lier à un produit")."</a>";
                    	echo "&nbsp;&nbsp;<a href='./mod_featured.php?action=unlink&idFeatured=$idFeatured&idCategory=$idCategory'>"._("Non lié")."</a>";
                    } elseif($action=="edit"){
                    	echo "<a href='./list_productsFeatured.php?action=addProduct&idFeatured=$idFeatured'>"._("Lier à un produit")."</a>";
                    	echo "&nbsp;&nbsp;<a href='./list_productsFeatured.php?action=addCat&idFeatured=$idFeatured'>"._("Lier à une catégorie")."</a>";
                    }
                      ?>
                  </td>
                </tr>
              </table>
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
    	$result=request("SELECT * from $TBL_catalogue_info_featured where id_featured='$idFeatured' and id_language='".$row_languages->id."'",$link);
    	$infosEdit = mysql_fetch_object($result);
    }
    ?>
      <table width='100%'>
      <tr>
      <td><font class='fieldTitle'><?PHP echo _("Titre"); ?>: </font></td>
      <td><input type='text' name='title_<? echo $row_languages->id?>' size='120'
      <?
      if($action=="edit"){
      	echo " value=\"".$infosEdit->title."\"";
      }
      if($action=="insert" || $action=="update"){
      	echo " value=\"".$_POST["title_".$row_languages->id]."\"";
      }
      ?>
      ></td>
      </tr>
      <tr>
      <td colspan='2'>
      <font class='fieldTitle'><?=$featuredField1Name?>:</font><br>
      	<?php
      	/*
      	$oFCKeditor = new FCKeditor() ;
      	$oFCKeditor->BasePath = $web_path.$folder_editor."/" ;
      	if($action=="add"){
      	$oFCKeditor->Value = $FEATURED_editor_default_value;
      	}
      	if($action=="edit"){
      	$oFCKeditor->Value = unhtmlentities($infosEdit->field1);
      	}
      	if($action=="insert" || $action=="update"){
      	$oFCKeditor->Value = $_POST["field1_".$row_languages->id];
      	}
      	$oFCKeditor->CreateFCKeditor( "field1_".$row_languages->id, "100%", 300 ) ;
      	*/
      	echo "<textarea name='field1_".$row_languages->id."' style='width:100%;height:300px;'>";
      	if($action=="add"){
      		echo $FEATURED_editor_default_value;
      	}
      	if($action=="edit"){
      		echo unhtmlentities($infosEdit->field1);
      	}
      	if($action=="insert" || $action=="update"){
      		echo $_POST["field1_".$row_languages->id];
      	}
      	echo "</textarea>";
    	?>
    	</td>
    	</tr>
    	<tr>
      <td colspan='2'>
      <font class='fieldTitle'><?=$featuredField2Name?>:</font><br>
      	<?php
      	/*
      	$oFCKeditor = new FCKeditor() ;
      	$oFCKeditor->BasePath = $web_path.$folder_editor."/" ;
      	if($action=="add"){
      	$oFCKeditor->Value = $FEATURED_editor_default_value;
      	}
      	if($action=="edit"){
      	$oFCKeditor->Value = unhtmlentities($infosEdit->field2);
      	}
      	if($action=="insert" || $action=="update"){
      	$oFCKeditor->Value = $_POST["field2_".$row_languages->id];
      	}
      	$oFCKeditor->CreateFCKeditor( "field2_".$row_languages->id, "100%", 300 ) ;
      	*/
      	echo "<textarea name='field2_".$row_languages->id."' style='width:100%;height:300px;'>";
      	if($action=="add"){
      		echo $FEATURED_editor_default_value;
      	}
      	if($action=="edit"){
      		echo unhtmlentities($infosEdit->field2);
      	}
      	if($action=="insert" || $action=="update"){
      		echo $_POST["field2_".$row_languages->id];
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
