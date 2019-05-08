<?
include ("../config.php");
include ("../ImageEditor.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idMenuitem"])){
	$idMenuitem=$_POST["idMenuitem"];
} elseif(isset($_GET["idMenuitem"])){
	$idMenuitem=$_GET["idMenuitem"];
} else $idMenuitem="";
if(isset($_POST["idParent"])){
	$idParent=$_POST["idParent"];
} elseif(isset($_GET["idParent"])){
	$idParent=$_GET["idParent"];
} else $idParent="";
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
	foreach($menuCategories as $tmp) if($tmp["id"]==$idCategory) $category=$tmp;
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
	foreach($menuCategories as $tmp) if($tmp["id"]==$idCategory) $category=$tmp;
} else {
	$idCategory="";
	$category=null;
}
?>
<?php
include("./mod_menus.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'un composant de menu");//$str31;
elseif($action=="edit" || $action=="update") $title =_("Anix - Modification d'un composant de menu");// $str32;
else $title = _("Anix - Modification d'un composant de menu");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'un composant de menu"));break;
	case "insert":setTitleBar(_("Ajout d'un composant de menu"));break;
	case "edit":setTitleBar(_("Modification d'un composant de menu:"));break;
	case "update":setTitleBar(_("Modification d'un composant de menu:"));break;
}
?>
<form id='main_form' action='./mod_menus.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='action' value='insert'>";
	echo "<input type='hidden' name='idCategory' value='$idCategory'>";
	echo "<input type='hidden' name='idParent' value='$idParent'>";
	$cancelLink="./list_menus.php?idCategory=$idCategory#$idParent";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idMenuitem' value='$idMenuitem'>";
	echo "<input type='hidden' name='action' value='update'>";
	echo "<input type='hidden' name='idCategory' value='$idCategory'>";
	echo "<input type='hidden' name='idParent' value='$idParent'>";
	$cancelLink="./list_menus.php?idCategory=$idCategory#$idMenuitem";
}

$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
?>
  <table border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
      </td>
      <td background='../images/button_back.jpg' align='right'>
      </td>
    </tr>
    <tr>
      <td colspan='2'>
<?
if($action=="edit" || $action=="update"){
	$result=request("SELECT * from $TBL_content_menuitems where id='$idMenuitem'",$link);
	if(!mysql_num_rows($result)) die(_("Erreur de protection: Ce composant de menu n'existe pas."));
	$edit = mysql_fetch_object($result);
}
        ?>
        <table width='100%'>
          <tr valign='top'>
            <td>
<?
if($action=="edit" || $action=="update"){
	$result = request("SELECT `id`,`title` FROM `$TBL_content_pages`,`$TBL_content_info_pages` WHERE `id_menu`='$idMenuitem' AND `id_language`='".$_SESSION["used_languageid2"]."' AND `$TBL_content_info_pages`.`id_page`=`$TBL_content_pages`.`id`",$link);
	if(mysql_num_rows($result)){
		$related_page = mysql_fetch_object($result);
	}
	$result = request("SELECT `id`,`name` FROM `$TBL_catalogue_categories`,`$TBL_catalogue_info_categories` WHERE `id_menu`='$idMenuitem' AND `id_language`='".$_SESSION["used_languageid2"]."' AND `$TBL_catalogue_info_categories`.`id_catalogue_cat`=`$TBL_catalogue_categories`.`id`",$link);
	if(mysql_num_rows($result)){
		$related_category = mysql_fetch_object($result);
	}
}
if($action=="edit"){
	echo "<center>"._("Type de composant").":<br><br>";
	if($edit->type=="link") echo "<b>"._("Lien")."</b>";
	if($edit->type=="submenu") echo "<b>"._("Sous-menu")."</b>";
	if(isset($related_page)){
		echo "<br /><br /><b>"._("Relié à la page: ").$related_page->title." (".$related_page->id.")</b>";
	}
	if(isset($related_category)){
		echo "<br /><br /><b>"._("Relié à la catégorie: ").$related_category->name." (".$related_category->id.")</b>";
	}
	echo "</center>";
}
if($action=="update"){
	echo "<center>"._("Type de composant").":<br><br>";
	if($_POST["type"]=="link") echo "<b>"._("Lien")."</b>";
	if($_POST["type"]=="submenu") echo "<b>"._("Sous-menu")."</b>";
	echo "</center>";
}
if($action=="add"){
	echo "<center>"._("Type de composant").":<br><br>";
	echo "<select name='type'>";
	echo "<option value='link' SELECTED>"._("Lien")."</option>";
	echo "<option value='submenu'>"._("Sous-menu")."</option>";
	echo "</select>";
	echo "</center>";
}
if($action=="insert"){
	echo "<center>"._("Type de composant").":<br><br>";
	echo "<select name='type'>";
	echo "<option value='link' ";
	if($_POST["type"]=="link") echo "SELECTED";
	echo ">"._("Lien")."</option>";
	echo "<option value='submenu' ";
	if($_POST["type"]=="submenu") echo "SELECTED";
	echo ">"._("Sous-menu")."</option>";
	echo "</select>";
	echo "</center>";
}
?>
            </td>
            <td width='33%'>
              <table class='message' width='100%'>
                <tr>
                  <td colspan='2'>
                    <FONT><B><?php echo _("Couleur du texte").":";?></B></FONT>
                  </td>
                </tr>
                <tr>
                  <td><?php echo _("Composant desactive").":";?></td>
                  <td>
                    <input type='text' name='txt_color_off' Maxlength='7' size='10' <?
                    if ($action=="add") echo " value='#'";
                    if($action=="edit") echo " value=\"".$edit->txt_color_off."\"";
                    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["txt_color_off"]."\"";
                    ?>>
                  </td>
                </tr>
                <tr>
                  <td><?php echo _("Composant active").":";?></td>
                  <td>
                    <input type='text' name='txt_color_on' Maxlength='7' size='10' <?
                    if ($action=="add") echo " value='#'";
                    if($action=="edit") echo " value=\"".$edit->txt_color_on."\"";
                    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["txt_color_on"]."\"";
                    ?>>
                  </td>
                </tr>
                <tr>
                  <td><?php echo _("Souris sur composant").":";?></td>
                  <td>
                    <input type='text' name='txt_color_mover' Maxlength='7' size='10' <?
                    if ($action=="add") echo " value='#'";
                    if($action=="edit") echo " value=\"".$edit->txt_color_mover."\"";
                    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["txt_color_mover"]."\"";
                    ?>>
                  </td>
                </tr>
                <tr>
                  <td><?php echo _("Click sur composant");?>:</td>
                  <td>
                    <input type='text' name='txt_color_click' Maxlength='7' size='10' <?
                    if ($action=="add") echo " value='#'";
                    if($action=="edit") echo " value='".$edit->txt_color_click."'";
                    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["txt_color_click"]."\"";
                    ?>>
                  </td>
                </tr>
                <tr>
                  <td><?php echo _("Composant relache");?>:</td>
                  <td>
                    <input type='text' name='txt_color_release' Maxlength='7' size='10' <?
                    if ($action=="add") echo " value='#'";
                    if($action=="edit") echo " value=\"".$edit->txt_color_release."\"";
                    if($action=="insert" || $action=="update")  echo " value=\"".$_POST["txt_color_release"]."\"";
                    ?>>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <?
    $languages=request("SELECT * FROM `$TBL_gen_languages` WHERE used = 'Y' ORDER BY $TBL_gen_languages.id!='$used_language_id',$TBL_gen_languages.default", $link);
    while ($row_languages=mysql_fetch_object($languages)){
    ?>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'>
    	<img src='<? echo "../locales/".$row_languages->locales_folder."/images/".$row_languages->image_file; ?>' border='0'> <?=$row_languages->name; ?>
    	</font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
      </td>
    </tr>
    <tr>
    <td colspan='2'>
    <? //Rest of while languages
    if($action=="edit"){
    	$result=request("SELECT * from $TBL_content_info_menuitems where id_menuitem='$idMenuitem' and id_language='".$row_languages->id."'",$link);
    	$infosEdit = mysql_fetch_object($result);
    }
    ?>
      <table width='100%'>
      <tr>
        <td>
          <table>
          <tr>
          <td><font class='fieldTitle'><?php echo _("Titre");?>: </font></td>
          <td><input type='text' name='title_<? echo $row_languages->id?>' size='30'
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
          <td><font class='fieldTitle'><?php echo _("Titre alternatif");?>: </font></td>
          <td><input type='text' name='alt_title_<? echo $row_languages->id?>' size='30'
          <?
          if($action=="edit"){
          	echo " value=\"".$infosEdit->alt_title."\"";
          }
          if($action=="insert" || $action=="update"){
          	echo " value=\"".$_POST["alt_title_".$row_languages->id]."\"";
          }
          ?>
          ></td>
          </tr>
          <tr>
          <td><font class='fieldTitle'><?php echo _("URL"); ?>: </font></td>
          <td><input type='text' name='link_<? echo $row_languages->id?>' size='30'
          <?
          if($action=="edit"){
          	echo " value=\"".$infosEdit->link."\"";
          }
          if($action=="insert" || $action=="update"){
          	echo " value=\"".$_POST["link_".$row_languages->id]."\"";
          }
          ?>
          ></td>
          </tr>
        	</table>
        </td>
        <td>
          <table>
          <tr>
            <td colspan='3'><font class='fieldTitle'><?php echo _("Images");?>: </font><br>
            <i><?php echo _("NB: Les images seront redimentionnees a");?> <?=$category["img_maxW"]?>x<?=$category["img_maxH"]?></i>
            </td>
          </tr>
          <tr>
            <td><font class='fieldTitle'><?php echo _("Composant desactive").":";?> </font></td>
            <td><input type='file' size='20' name='img_off_<?=$row_languages->id?>'></td>
            <td align='center'><?
            if($action=="edit" && $infosEdit->img_off!="") {
            	echo "<IMG src='../$folder_webLocalesRoot".$row_languages->locales_folder."/images/".$infosEdit->img_off."' border='0'>";
            	echo "<br><center><a href='./mod_menus.php?action=removeImage&idLanguage=".$row_languages->id."&imgType=off&idMenuitem=$idMenuitem&idCategory=$idCategory'>"._("Supprimer")."</a></center>";
            }
            else echo "<i>"._("Pas d'image actuellement")."</i>";
            ?></td>
          </tr>
          <tr>
            <td><font class='fieldTitle'><?php echo _("Composant active").":";?> </font></td>
            <td><input type='file' size='20' name='img_on_<?=$row_languages->id?>'></td>
            <td align='center'><?
            if($action=="edit" && $infosEdit->img_on!="") {
            	echo "<IMG src='../$folder_webLocalesRoot".$row_languages->locales_folder."/images/".$infosEdit->img_on."' border='0'>";
            	echo "<br><center><a href='./mod_menus.php?action=removeImage&idLanguage=".$row_languages->id."&imgType=on&idMenuitem=$idMenuitem&idCategory=$idCategory'>"._("Supprimer")."</a></center>";
            }
            else echo "<i>"._("Pas d'image actuellement")."</i>";
            ?></td>
          </tr>
          <tr>
            <td><font class='fieldTitle'><?php echo _("Souris sur composant").":";?> </font></td>
            <td><input type='file' size='20' name='img_mover_<?=$row_languages->id?>'></td>
            <td align='center'><?
            if($action=="edit" && $infosEdit->img_mover!="") {
            	echo "<IMG src='../$folder_webLocalesRoot".$row_languages->locales_folder."/images/".$infosEdit->img_mover."' border='0'>";
            	echo "<br><center><a href='./mod_menus.php?action=removeImage&idLanguage=".$row_languages->id."&imgType=mover&idMenuitem=$idMenuitem&idCategory=$idCategory'>"._("Supprimer")."</a></center>";
            }
            else echo "<i>"._("Pas d'image actuellement")."</i>";
            ?></td>
          </tr>
          <tr>
            <td><font class='fieldTitle'><?php echo _("Click sur composant");?>: </font></td>
            <td><input type='file' size='20' name='img_click_<?=$row_languages->id?>'></td>
            <td align='center'><?
            if($action=="edit" && $infosEdit->img_click!="") {
            	echo "<IMG src='../$folder_webLocalesRoot".$row_languages->locales_folder."/images/".$infosEdit->img_click."' border='0'>";
            	echo "<br><center><a href='./mod_menus.php?action=removeImage&idLanguage=".$row_languages->id."&imgType=click&idMenuitem=$idMenuitem&idCategory=$idCategory'>"._("Supprimer")."</a></center>";
            }
            else echo "<i>"._("Pas d'image actuellement")."</i>";
            ?></td>
          </tr>
          <tr>
            <td><font class='fieldTitle'><?php echo _("Composant relache");?>:</font></td>
            <td><input type='file' size='20' name='img_release_<?=$row_languages->id?>'></td>
            <td align='center'><?
            if($action=="edit" && $infosEdit->img_release!="") {
            	echo "<IMG src='../$folder_webLocalesRoot".$row_languages->locales_folder."/images/".$infosEdit->img_release."' border='0'>";
            	echo "<br><center><a href='./mod_menus.php?action=removeImage&idLanguage=".$row_languages->id."&imgType=release&idMenuitem=$idMenuitem&idCategory=$idCategory'>"._("Supprimer")."</a></center>";
            }
            else echo "<i>"._("Pas d'image actuellement")."</i>";
            ?></td>
          </tr>
          </table>
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
