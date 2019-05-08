<?
  include ("../config.php");
  include ("./module_config.php");
  $link = dbConnect();
	$nb=0;
?>
<?
$title = _("Anix - Administration");
include("../html_header.php");
setTitleBar(_("Anix | Administration"));
?>
<?
  include ("../html_footer.php");
  mysql_close($link);
?>
