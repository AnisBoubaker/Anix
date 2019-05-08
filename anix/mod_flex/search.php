<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$action="";
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
$title = _("Anix - Catalogue");
include("../html_header.php");
setTitleBar(_("Module: Catalogue - Recherche"));
?>
<table width='95%' align='center'>
<tr valign='top'>
<td>
  <form action='./search.php' method='POST'>
  <input type='hidden' name='action' value='search_item'>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Rechercher un élément"); ?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><?php echo _("Nom"); ?>:</td>
          <td><input type='text' name='name' size='30' <?
          if($action=="search_item") echo "value='".$_POST["name"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Réf."); ?>:</td>
          <td><input type='text' name='ref' size='20' <?
          if($action=="search_item") echo "value='".$_POST["ref"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Catégorie"); ?>:</td>
          <td><SELECT name='category'>
        	  <option value='0'>-- <?php echo _("Toutes")?> --</option>
        	  <?
        	  $tableCategories = getCatTable();
        	  $listCategories = getCategoriesList($tableCategories,0 , 0);
        	  foreach($listCategories as $row){
        	  	echo "<option value='".$row["id"]."'";
        	  	if($action=="search_item" && $_POST["category"]==$row["id"]) echo " SELECTED";
        	  	echo ">".$row["name"]."</option>";
        	  }
        	  ?>
        	</SELECT></td>
      	</tr>
        <tr>
          <td><?php echo _("Status"); ?>:</td>
          <td><SELECT name='active'>
        	  <option value='0'<?
        	  if($action=="search_item" && $_POST["active"]=="0") echo " SELECTED";
            ?>>-- <?php echo _("Tous"); ?> --</option>
        	  <option value='Y'<?
        	  if($action=="search_item" && $_POST["active"]=="Y") echo " SELECTED";
            ?>><?php echo _("Activés seulement"); ?></option>
        	  <option value='N'<?
        	  if($action=="search_item" && $_POST["active"]=="N") echo " SELECTED";
            ?>><?php echo _("Désactivés seulement"); ?></option>
        	</SELECT></td>
      	</tr>
        <tr>
          <td><?php echo _("Mots clés"); ?>:</td>
          <td><input type='text' name='keywords' size='30' <?
          if($action=="search_item") echo "value='".$_POST["keywords"]."'";
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
<td>
  <form action='./search.php' method='POST'>
  <input type='hidden' name='action' value='search_category'>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Rechercher une catégorie"); ?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><?php echo _("Catégorie"); ?>:</td>
          <td><input type='text' name='name' size='30' <?
          if($action=="search_category") echo "value='".$_POST["name"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Mots clés"); ?>:</td>
          <td><input type='text' name='keywords' size='30' <?
          if($action=="search_category") echo "value='".$_POST["keywords"]."'";
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
if(($action=="search_item" || $action=="search_category") && $nb){
?>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
  <tr height='20'>
    <td  background='../images/button_back.jpg' align='left' valign='middle'>
      <font class='edittable_header'><?php echo _("Résultats de la recherche"); ?></font>
    </td>
    <td background='../images/button_back.jpg' align='right'>
      &nbsp;
    </td>
  </tr>
  <tr>
    <td colspan='2'>
      <center><?php echo _("Votre recherche a retourné")." ".$nbResults." "._("résultat(s)"); ?></center><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width='100%' class='message'>
  <?
  if($nbResults){
  	while($result=mysql_fetch_object($request)){
  		if($action=="search_item"){
  			echo "<tr>";
  			echo "<td valign='middle' width='42' bgcolor='#e7eff2' align='right'><a href='./mod_item.php?action=edit&idItem=".$result->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a></td>";
  			echo "<td>".$result->name."</td>";
  			echo "</tr>";
  			//echo "<tr><td valign='middle' width='42' bgcolor='#e7eff2' align='right'>&nbsp;</td><td><hr></td></tr>";
  		}
  		if($action=="search_category"){
  			echo "<tr>";
  			echo "<td valign='middle' width='42' bgcolor='#e7eff2' align='right'><a href='./mod_category.php?action=edit&idCat=".$result->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a></td>";
  			echo "<td>".$result->name."</td>";
  			echo "</tr>";
  		}
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
