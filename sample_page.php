<?php
	require_once("./config.php");
	$RUNTIMECONF_pageTitle=_("CGC | Canadian Geoexchange Coalition | Geothermal");
	if(isset($_GET["id"])) $RUNTIMECONF_selected_menu=$_GET["id"];
	else $RUNTIMECONF_selected_menu=-1;
	include("./html_header.php");
	//print_r($menu->menuTable);
?>
<div id='main_center'>
	<?php
		echo $menu->printBreadCrumb();
		echo printToolBox(true,true,true);
	?>
	<h1>Sample Page</h1>
	<div class='main_section'>
		<h2>This is an example page</h2>
		This sample page helps to figure out how the CGC's website menus will work.
		<a class='readmore' href='./'>Read more</a>
	</div>
</div><!-- main_center -->
<div id='main_right'>
	<img id='wgein_button' src='./images/button_wgein.jpg' alt="<?php echo _("World Ground Energy Information Network"); ?>" />
	<div class='box'>
		<h1>Latest News</h1>
		<div class='content'>
			<h3>CGC's 2007 board elections</h3>
			<img src='./images/news_image.jpg' alt="CGC's 2007 board elections" style='float:right;padding-right:6px' />
			LIoyd Kuczek (Manitoba Hydro) has been re-elected as the chairman of the CGC's board.
			<a class='readmore' href='./'>Read more</a>
		</div>
		<div class='content'>
			<h3>CGC Releases Accreditation Application Form for Residential Designers</h3>
			The Canadian GeoExchange™ Coalition (CGC) has released the application form for CGC Residential Design Accreditation today. This is the second of four accreditations for industry specialists.
			<a class='readmore' href='./'>Read more</a>
		</div>
		<div class='close_box'></div>
	</div><!-- box -->
	<div class='box'>
		<h1>Latest Publications</h1>
		<div class='content'>
			<h3>Cruise ship onboard heatpumps</h3>
			<span class='note'>2007-06-11 - Market Studies</span>
		</div>
		<div class='content'>
			<h3>Cruise ship onboard heatpumps</h3>
			<span class='note'>2007-06-11 - Market Studies</span>
		</div>
		<div class='close_box'></div>
	</div><!-- box -->
</div><!-- main_right -->
<?php
	include("./html_footer.php");
?>