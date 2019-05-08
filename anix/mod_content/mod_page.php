<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
if(isset($_POST["idPage"])){
	$idPage=$_POST["idPage"];
} elseif(isset($_GET["idPage"])){
	$idPage=$_GET["idPage"];
} else $idPage="";
if(isset($_POST["idCategory"])){
	$idCategory=$_POST["idCategory"];
} elseif(isset($_GET["idCategory"])){
	$idCategory=$_GET["idCategory"];
} else $idCategory=-1;
$errors = 0;
$errMessage="";
?>
<?php
include("./mod_page.actions.php");
?>
<?
if($action=="add" || $action=="insert") $title = _("Anix - Ajout d'une page dynamique");
elseif($action=="edit" || $action=="update") $title = _("Anix - Modification d'une page dynamique");
else $title = _("Anix - Modification d'une page dynamique");
include("../html_header.php");
switch($action){
	case "add":setTitleBar(_("Ajout d'une page dynamique"));break;
	case "insert":setTitleBar(_("Ajout d'une page dynamique"));break;
	case "edit":setTitleBar(_("Modification d'une page dynamique"));break;
	case "update":setTitleBar(_("Modification d'une page dynamique"));break;
}
?>
<form id='main_form' action='./mod_page.php' method='POST' enctype='multipart/form-data'>
<?
if($action=="add" || $action=="insert") {
	echo "<input type='hidden' name='idPage' value='$idPage'>";
	echo "<input type='hidden' name='action' value='insert'>";
	echo "<input type='hidden' name='idCategory' value='$idCategory'>";
}
if($action=="edit" || $action=="update"){
	echo "<input type='hidden' name='idPage' value='$idPage'>";
	echo "<input type='hidden' name='action' value='update'>";
	$request = request("SELECT `$TBL_content_pages`.`id_category` FROM `$TBL_content_pages` WHERE `id`='$idPage'",$link);
	if(mysql_num_rows($request)){
		$tmp = mysql_fetch_object($request);
		$idCategory = $tmp->id_category;
	}
}
$cancelLink="./list_pages.php?idCategory=$idCategory";
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"back","link"=>$cancelLink);
printButtons($buttons);
printLanguageToggles($link);
?>
  <table id='main_table' border="0" align="center" width="100%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr>
      <td valign="top">
      <center><?php echo _("Veuillez renseigner les informations de la page ci-dessous.");?></center>
      </td>
      <td style='width:60%;'>
      <?php
      /**
	  * LOAD TABS
	  */
      include("./mod_page.tabs.php");
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
    	if($action=="edit"){
    		$request = request(
    		"SELECT `$TBL_content_pages`.`id`,`$TBL_content_info_pages`.`title`,`$TBL_content_info_pages`.`short_desc`,`$TBL_content_info_pages`.`content`,`$TBL_content_info_pages`.`keywords`,`$TBL_content_info_pages`.`htmltitle`,`$TBL_content_info_pages`.`htmldescription`
        FROM `$TBL_content_pages`,`$TBL_content_info_pages`
        WHERE `$TBL_content_pages`.id='$idPage'
        AND `$TBL_content_info_pages`.`id_language`='".$row_languages->id."'
        AND `$TBL_content_info_pages`.`id_page`=`$TBL_content_pages`.`id`",$link);
    		if(mysql_num_rows($request)){
    			$edit = mysql_fetch_object($request);
    		} else {
    			die(_("Erreur de protection: La page specifiee n'existe pas."));
    		}
    	}
    	if($first){ $first=false; $displayLanguage='';}
    	else $displayLanguage='none';
    ?>
    <tr class='lang_<?php echo $row_languages->id;?>' style='display:<?php echo $displayLanguage; ?>;'>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><B><?php echo _("Titre");?>:</B></td>
          <td><input type='text' name='title_<?=$row_languages->id?>' size='128'<?
          if($action=="edit") echo " value=\"".$edit->title."\"";
          if($action=="update" || $action=="insert") echo " value=\"".$_POST["title_".$row_languages->id]."\"";
          ?>>
          </td>
        </tr>
        <tr>
          <td><B><?php echo _("Titre(HTML)");?>:</B></td>
          <td><input type='text' name='htmltitle_<?=$row_languages->id?>' size='128'<?
          if($action=="edit") echo " value=\"".$edit->htmltitle."\"";
          if($action=="update" || $action=="insert") echo " value=\"".$_POST["html_".$row_languages->id]."\"";
          ?>>
          </td>
        </tr>
        <tr>
          <td valign='top'><B><?php echo _("Mots cles");?>:</B></td>
          <td>
            <TEXTAREA class='mceNoEditor' name='keywords_<?=$row_languages->id?>' cols='80' rows='5'><?
            if($action=="edit") echo $edit->keywords;
            if($action=="insert" || $action=="update") echo $_POST["keywords_".$row_languages->id];
              ?></TEXTAREA>
          </td>
        </tr>
        <tr>
          <td valign='top'><B><?php echo _("Description");?>:</B></td>
          <td>
            <TEXTAREA class='mceNoEditor' name='htmldescription_<?=$row_languages->id?>' cols='80' rows='5'><?
            if($action=="edit") echo $edit->htmldescription;
            if($action=="insert" || $action=="update") echo $_POST["htmldescription_".$row_languages->id];
              ?></TEXTAREA>
          </td>
        </tr>
        <?php if(($action=="edit" || $action=="update") &&$edit_menu->id_menu){
        	$tmp = request("select `link` FROM `$TBL_content_info_menuitems` WHERE `id_menuitem`='".$edit_menu->id_menu."' AND `id_language`='".$row_languages->id."'",$link);
        	$menuurl = mysql_fetch_object($tmp);
        ?>
        <tr>
          <td><B><?php echo _("URL");?>:</B></td>
          <td><input type='text' name='url_<?=$row_languages->id?>' size='128'<?
          if($action=="edit") echo " value=\"".$menuurl->link."\"";
          if($action=="update" || $action=="insert") echo " value=\"".$_POST["url_".$row_languages->id]."\"";
          ?>>
          </td>
        <?php } ?>
        </tr>
        <tr height='20'>
	      <td colspan='2' background='../images/button_back.jpg' align='left' valign='middle'>
	      <B><?php echo _("Description courte");?>:</B>
	      </td>
	    </tr>
        <tr>
          <td colspan='2'>
          <?
          echo "<textarea name='shortdesc_".$row_languages->id."' style='width:100%;height:200px;'>";
          if($action=="add"){
          	echo $PAGES_editor_default_value;
          }
          if($action=="edit"){
          	echo unhtmlentities($edit->short_desc);
          }
          if($action=="insert" || $action=="update"){
          	echo $_POST["shortdesc_".$row_languages->id];
          }
          echo "</textarea>";
          ?>
         </td>
        </tr>
        <tr height='20'>
	      <td colspan='2' background='../images/button_back.jpg' align='left' valign='middle'>
	      <B><?php echo _("Contenu");?>:</B>
	      </td>
	    </tr>
        <tr>
          <td colspan='2'>
          <?
          echo "<textarea name='content_".$row_languages->id."' style='width:100%;height:500px;'>";
          if($action=="add"){
          	echo $PAGES_editor_default_value;
          }
          if($action=="edit"){
          	echo unhtmlentities($edit->content);
          }
          if($action=="insert" || $action=="update"){
          	echo $_POST["content_".$row_languages->id];
          }
          echo "</textarea>";
          ?>
         </td>
        </tr>
        </table>
      </td>
    </tr>
    <?
    } // While languages
    ?>
  </table>
</form>
<?
include ("../html_footer.php");
mysql_close($link);
?>
