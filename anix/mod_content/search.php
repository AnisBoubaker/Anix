<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$nb=0;
if(isset($_POST["action"])){
	$action=$_POST["action"];
} elseif(isset($_GET["action"])){
	$action=$_GET["action"];
}
?>
<?php
include("./search.actions.php");
?>
<?
$title = _("Anix - Gestion de contenu");//$str2;
include("../html_header.php");
setTitleBar(_("Module Contenu"));
?>
<table width='60%' align='center'>
<tr valign='top'>
<td>
  <form action='./search.php' method='POST'>
  <input type='hidden' name='action' value='search_page'>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Rechercher une page dynamique");?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><?php echo _("Titre").":"; ?></td>
          <td><input type='text' name='title' size='30' <?
          if($action=="search_page") echo "value='".$_POST["title"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Contenu").":"; ?></td>
          <td><input type='text' name='content' size='30' <?
          if($action=="search_page") echo "value='".$_POST["content"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Mots cles").":";//$str6 ?></td>
          <td><input type='text' name='keywords' size='30' <?
          if($action=="search_page") echo "value='".$_POST["keywords"]."'";
          ?>></td>
        </tr>
        </table>
      </td>
    </tr>
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        &nbsp;
      </td>
      <td background='../images/button_back.jpg' align='right'>
        <input type='image' src='../locales/<?=$used_language?>/images/button_validate.jpg' border='0'>
      </td>
    </tr>
  </table>
  </form>
</td>
</tr>
</table>
<?
if(($action=="search_page") && $nb){
?>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      <font class='edittable_header'><?php echo _("Résultats de la recherche");?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
      &nbsp;
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <center><?php printf(ngettext("Votre recherche a retourne %d resultat", "Votre recherche a retourne %d resultats", $nbResults), $nbResults); ?></center><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width='100%' class='message'>
  <?
  if($nbResults){
  	while($result=mysql_fetch_object($request)){
  		echo "<tr>";
  		echo "<td valign='middle' width='42' bgcolor='#e7eff2' align='right'><a href='./mod_page.php?action=edit&idPage=".$result->id."'><img src='../images/edit.gif' border='0' alt='"._("Modifier la page")."'></a></td>";
  		echo "<td>".$result->title."</td>";
  		echo "</tr>";
  	}
  }
  ?>
    </table>
    </td>
  </tr>
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      &nbsp;
    </td>
    <td background='../images/button_back.jpg' align='right'>
      &nbsp;
    </td>
  </tr>
  </table>
<?
} //if search
?>
<?
include ("../html_footer.php");
mysql_close($link);
?>
