<div class='close'></div>
	</div><!-- main zone -->
	<div id='footer'>
		<div id='left'>
			<?php echo _("Copyright &copy;Canadian Geothermal Coalition (CGC)")." 2004-".date("Y"); ?><br /><br />
			<a href='http://www.cibaxion.com' rel='external'><?php echo _("Website development: Cibaxion"); ?></a>
			&nbsp;&nbsp;<a href='http://validator.w3.org/check?uri=referer' rel='external' style='border:0px;'><img src='./images/valid_xhtml_w3c.png' alt="<?php echo _("This website fully respects W3C's XHTML 1.0 standard.");?>" /></a>
		</div>
		<div id='right'>
			<a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> -
			<a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> -
			<a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a> - <a href='./'>Referral link</a>
		</div>
	</div><!-- footer -->
</div> <!-- center -->
<div id='divHelpForm' style='display:none;'></div>
</div><!-- outterbox -->
<?php
	if(isset($_GET["print"]) && (!isset($RUNTIMECONF_printable) || $RUNTIMECONF_printable)){
		echo "<script type=\"text/javascript\">window.print();</script>";
	}
?>
</body>
</html>