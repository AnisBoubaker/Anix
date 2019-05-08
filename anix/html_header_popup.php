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
	<script type="text/javascript" src="../js/anix_tabmanager.js"></script>
	<script type="text/javascript"><?php require_once("../custom/editor/init.php"); ?></script>
	<script language="JavaScript" src="../js/general.js"></script>
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
</head>
<body onUnload="javascript:showSpinner();">
<script src="../3rdparty/scw/scw.js" type="text/javascript"></script>

<div style='position:relative;width:100%;background:#ffffff;'>
<div style='clear:both;width:100%; background:#efefef;text-align:right;vertical-align:middle;border-top:1px solid #ffffff;'>
	<div id='buttons_bar'>
	</div>
</div>
<div style='float:left;width:100%;background:#2e72a3;color:#ffffff;border-bottom:1px solid #ffffff;padding:3px 0 3px 0;'>
	<div id='title_bar' style='float:left;width:100%;padding-top:5px;font-weight:bold;'>&nbsp;</div>
	</div>
</div>

<div id='spinner' style='position:absolute;top:50px;left:0;font-weight:bold;background:#a92b3a;color:#ffffff;margin:5px 0 0 0;height:20px;padding:3px;display:none;'><img src='../images/spinner.gif' style='padding-right:10px;vertical-align:middle;' alt='' /><?php echo _("Traitement en cours. Patientez SVP..."); ?></div>
<!-- Table principale -->
<div id='main_container' style='float:left;width:100%;overflow:scroll;overflow-x: hidden;padding:10px 0 0 0;background:#ffffff;'>
<script  language="JavaScript">
setMainDivHeight(70);
</script>