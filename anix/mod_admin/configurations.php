<?
  include ("../config.php");
  include ("./module_config.php");
  $link = dbConnect();
	$nb=0;
?>
<? $title = _("Anix - Configuration d'Anix");include("../html_header.php");
	setTitleBar(_("Configuration d'Anix"));
?>
<br><br><br><br><br>
<center>
  <img src='../images/unavailable.gif' border='0' alt="<?php echo _("Indisponible");?>"><br>
  <i><h3><?php echo _("Désolé, cette section n'est pas disponible actuellement."); ?></h3></i>
</center>
<?
  include ("../html_footer.php");
  mysql_close($link);
?>
