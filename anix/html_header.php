<?
/* Page Params :
* $title : Title of the web page
* $ANIX_module_name : name of the module to be selected
* $menu_ouvert : Number of the enabled submenu
*/
//$editor_language = $used_language;
$editor_language = substr($used_language,0,strpos($used_language,"_"));
require_once('../anix_tabmanager.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$title?></title>
	<link rel="stylesheet" type="text/css" href="../css/anix.css"></link>
	<link rel="stylesheet" type="text/css" href="../css/anix_tabmanager.css" />
	<link rel="stylesheet" type="text/css" href="../js/ThemeOffice/theme.css" />
	<script type="text/javascript" src="../3rdparty/tinymce/jscripts/tiny_mce/tiny_mce_gzip.js"></script>
	<script type="text/javascript" src="../js/JSCookMenu.js"></script>
	<script type="text/javascript"> var myJSCookMenuThemeBase = '../js/'; </script>
	<script type="text/javascript" src="../js/ThemeOffice/theme.js"></script>
	<script type="text/javascript" src="../js/anix_tabmanager.js"></script>
	<script type="text/javascript"><?php require_once("../custom/editor/init.php"); ?></script>
	<script type="text/javascript" src="../js/general.js"></script>
	<?php
	if(isset($ANIX_JS_load) && $ANIX_JS_load!="") {
		if(is_array($ANIX_JS_load)) foreach($ANIX_JS_load as $JS_to_load){
			echo "<script type=\"text/javascript\" src=\"../js/$JS_to_load\"></script>\n";
		} else {
			echo "<script type=\"text/javascript\" src=\"../js/$ANIX_JS_load\"></script>\n";
		}
	}
	?>

	<script type="text/javascript">
	tinyMCE_GZ.init({
		plugins : "table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu,fullscreen,xhtmlxtras,template",
		themes : 'simple,advanced',
		languages : "<?php echo $editor_language; ?>",
		disk_cache : true,
		debug : false
	});
	</script>
	<script type="text/javascript">
	tinyMCE.init($editor_init_options);
	</script>
	<script  language="JavaScript">
	<? include("./menu.php")?>
 	</script>
 	<?php if(isset($xajax)) $xajax->printJavascript('../','/js/xajax.js'); ?>
</head>
<body onResize="javascript:setMainDivHeight(140);" onUnload="javascript:showSpinner();">
<script src="../3rdparty/scw/scw.js" type="text/javascript"></script>

<div style='position:relative;width:100%;background:#ffffff;'>
<div id='header' style='float:left;width:100%;background:#ffffff;'>
	<div id='logo' style='float:left;width:200px;'><img src="../locales/<?php echo $used_language; ?>/images/logo.jpg" style='padding:0 5px 0 5px;' alt="<?php echo _("Anix 2 - Par Cibaxion"); ?>"/></div>
	<table style='float:left;'>
	<tr>
		<td nowrap='nowrap' style='vertical-align:top;'><span class='siteTitle'><b><?php echo _("Administration du site"); ?>:<b> <?=$AdministredSiteName?>&nbsp;</span></td>
		<td style='width:100%;text-align:right;vertical-align:top;'>
			<img src='../images/icon_new_window.gif' alt='' style='vertical-align:middle;' /> <a href='javascript:void(0);' onclick='javascript:window.opener.newWindow(true);'><?php echo _("Nouvelle fenêtre"); ?></a> |
			<img src='../images/icon_close.gif' alt='' style='vertical-align:middle;' /> <a href='javascript:void(0);' onclick="javascript:logoutConfirm('<?php echo _("Êtes-vous sûr de vouloir vous déconnecter?")."\\n\\n"._("ATTENTION: Toutes les fenêtres ouvertes d\'ANIX seront fermées. Veuillez sauvegarder vos modifications avant de vous déconnecter."); ?>');"><?php echo _("Déconnexion"); ?></a>
			<div id='spinner' style='float:right;font-weight:bold;background:#a92b3a;color:#ffffff;margin:5px 0 0 0;height:20px;padding:3px;'><img src='../images/spinner.gif' style='padding-right:10px;vertical-align:middle;' alt='' /><?php echo _("Traitement en cours. Patientez SVP..."); ?></div>
		</td>
	</tr>
	</table>
	<div id='modules_bar'>
		<?php
		$modulesStr="";
		foreach($modules as $idModule=>$module){
			if($module["name"]==$ANIX_module_name){
				$_SESSION["current_module"]=$idModule;
				$modulesStr="<div class='module_on'><div><img src='./icon.gif' alt='' />".$module["display"]."</div></div>".$modulesStr;
			} else {
				$modulesStr="<div class='module' style='float:right;'><a href='../".$module["folder"]."/'><img src='../".$module["folder"]."/icon.gif' alt='' />".$module["display"]."</a></div>".$modulesStr;
			}
		}
		echo $modulesStr;
		?>
	</div>
</div>
<div style='clear:both;width:100%; background:#efefef;text-align:right;vertical-align:middle;border-top:1px solid #ffffff;'>
	<div id='anix_menu' style='float:left;margin:3px 0 3px 0;'></div>
	<div id='buttons_bar'>
	</div>
</div>
<SCRIPT LANGUAGE="JavaScript"><!--
cmDraw ('anix_menu', anixMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
--></SCRIPT>
<div style='float:left;width:100%;background:#2e72a3;color:#ffffff;border-bottom:1px solid #ffffff;padding:3px 0 3px 0;'>
	<div id='title_bar' style='float:left;width:60%;padding-top:5px;font-weight:bold;'>&nbsp;</div>
	<div id='language_selector_bar' style='float:left;width:40%;text-align:right;'>
	</div>
</div>

<!-- Table principale -->
<div id='main_container' style='float:left;width:100%;height:400px;overflow:scroll;overflow-x: hidden;padding:10px 0 0 0;'>
<script  language="JavaScript">
setMainDivHeight(185);
</script>