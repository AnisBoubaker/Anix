<?php
	require_once("./interface/display/menuList.php");
	echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
	echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\">\n";
	//if($used_language_id==2) echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">\n";
?>
<head>
<title><?php if(isset($RUNTIMECONF_pageTitle)) echo utf8_encode(unhtmlentities($RUNTIMECONF_pageTitle)); else echo _("CGC | Canadian Geoexchange Coalition | Geothermal"); ?></title>
<meta name="description" content="<?php if(isset($RUNTIMECONF_description)) echo utf8_encode(unhtmlentities($RUNTIMECONF_description));?>" />
<meta name="keywords" content="<?php if(isset($RUNTIMECONF_keywords)) echo utf8_encode(unhtmlentities($RUNTIMECONF_keywords));?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta name="revisit-after" content="15 days" />
<meta name="robots" content="index,follow" />
<link rel="stylesheet" type="text/css" href="./css/content.css" />

<?php
	//use the printable css if we clicked on "Print this page"
	if(isset($_GET["print"]) && (!isset($RUNTIMECONF_printable) || $RUNTIMECONF_printable)){
		echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"./css/layout_print.css\" />";
	} else {
		echo "<link rel=\"stylesheet\" type=\"text/css\" media=\"screen\" href=\"./css/layout.css\" />";
		echo "<link rel=\"stylesheet\" type=\"text/css\" media=\"print\" href=\"./css/layout_print.css\" />";
	}
?>
<script src="js/general.js" type="text/javascript"></script>
<script src="js/menu.js" type="text/javascript"></script>
<script src="js/tabsMenu.js" type="text/javascript"></script>
<?php
if(isset($RUNTIMECONF_load_ajax_js)){
	$xajax->printJavascript('./','js/xajax.js');
}
?>
</head>
<body>
<div id='outterbox'>
<?php
//setup the menu
$menu = new menuList($RUNTIMECONF_selected_menu);
?>
<?php
	echo $menu->printSubtabs();
	if(!$menu->isHomePage()) echo $menu->printSubMenus();
?>
<div id='header'>
	<div id='logo_box'>
		<a href='http://www.geo-exchange.ca'><img src='./images/cgc_logo.jpg' alt='<?php echo _("Canadian Geoexchange Coalition"); ?>' /></a>
		<div id='search_box'>
		<img src='./images/search_magnify.gif' alt="<?php echo _("Search CGC's website"); ?>" />
			<input type='text' name='search' style='width:135px;' />
			<input type='image' src='./images/search_button_ok.jpg' alt="<?php echo _("Search CGC's website"); ?>" style='vertical-align:middle;border:0px;' />
		</div>
	</div>
	<div id='top_links'>
		<a href='./'><img src='./images/icon_home.png' alt='Home' />Home</a> |
		<a href='./'><img src='./images/icon_contact.png' alt='Contact CGC' />Contact CGC</a> |
		<a href='./'><img src='./images/icon_plan.png' alt="Plan" />Plan |
		<a href='./'><img src='./images/icon_language.jpg' alt='Français' /><b>Français</b></a>
	</div>
	<div id='banner_box'>
		<img class='banner' src='./images/banner_sample.jpg' alt='' />
	</div>
	<?php
		echo $menu->printTabs();
	?>
</div>
<div id='center'>
	<div id='left_zone'>
		<?php
			if(!$menu->isHomePage()) echo $menu->printMenu();
			else {
		?>
			<div class='box'>
				<h1>Scheduled trainings</h1>
				<h2>Training for drillers</h2>
				<div class='content2'>
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
				</div>
				<h2>Training for drillers</h2>
				<div class='content2'>
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
				</div>
				<h2>Training for drillers</h2>
				<div class='content2'>
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
				</div>
				<h2>Training for drillers</h2>
				<div class='content2'>
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
					Montreal, PQ: 2007-07-11<br />
				</div>
				<div class='close_box'></div>
			</div>
		<?php
			} //else idHomePage
		?>

		<?php
			//small banner 1
			echo "<div class='banner'>\n";
			echo _("Advertising")."<br />\n";
			echo "<a href='./'>";
			echo "<img src='./images/banner_sample2.png' alt='Advertising' />";
			echo "</a>";
			echo "</div>\n";

			//small banner 2
			echo "<div class='banner'>\n";
			echo _("Advertising")."<br />\n";
			echo "<img src='./images/banner_sample2.png' alt='Advertising'/>\n";
			echo "</div>\n";
		?>

	</div> <!-- left zone -->
	<div id='main_zone'><div class='open'></div>