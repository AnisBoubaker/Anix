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
<?php
$title = _("Anix - Nouvelles");
include("../html_header.php");
setTitleBar(_("Nouvelles - Recherche"));
?>
<table width='100%' align='center'>
<tr valign='top'>
<td>
  <form action='./search.php' method='POST'>
  <input type='hidden' name='action' value='search_photo'>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Rechercher une nouvelle");?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><?php echo _("Titre"); ?>:</td>
          <td><input type='text' name='title' size='30' <?
          if($action=="search_photo") echo "value='".$_POST["title"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Catégorie"); ?>:</td>
          <td><SELECT name='category'>
        	  <option value='0'>-- <?php echo _("Toutes"); ?> --</option>
        	  <?
        	  $categories=request("select $TBL_gallery_categories.id, $TBL_gallery_categories.id_parent, $TBL_gallery_categories.ordering,$TBL_gallery_info_categories.name, $TBL_gallery_info_categories.description from  $TBL_gallery_categories,$TBL_gen_languages,$TBL_gallery_info_categories where $TBL_gallery_info_categories.id_gallery_cat=$TBL_gallery_categories.id and $TBL_gallery_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_gallery_categories.id_parent, $TBL_gallery_categories.ordering", $link);
        	  $tableCategories = getCatTable($categories);
        	  $listCategories = getCategoriesList($tableCategories,0 , 0);
        	  foreach($listCategories as $row){
        	  	echo "<option value='".$row["id"]."'";
        	  	if($action=="search_photo" && $_POST["category"]==$row["id"]) echo " SELECTED";
        	  	echo ">".$row["name"]."</option>";
        	  }
        	  ?>
        	</SELECT></td>
      	</tr>
        <tr>
          <td><?php echo _("Mots clés"); ?>:</td>
          <td><input type='text' name='keywords' size='30' <?
          if($action=="search_photo") echo "value='".$_POST["keywords"]."'";
          ?>></td>
        </tr>
        <tr>
          <td valign='top'><?php echo _("Filtres"); ?>:</td>
          <td><input type='checkbox' name='active' <?
          if($action=="search_photo" && isset($_POST["active"])) echo "CHECKED";
          ?>> <?php echo _("Actives"); ?>
          &nbsp;&nbsp;
          <input type='checkbox' name='disactivated' <?
          if($action=="search_photo" && isset($_POST["disactivated"])) echo "CHECKED";
          ?>> <?php echo _("Désactivées"); ?><br>
          <input type='checkbox' name='awaiting' <?
          if($action=="search_photo" && isset($_POST["awaiting"])) echo "CHECKED";
          ?>> <?php echo _("En attente"); ?>
          &nbsp;&nbsp;
          <input type='checkbox' name='expired' <?
          if($action=="search_photo" && isset($_POST["expired"])) echo "CHECKED";
          ?>> <?php echo _("Expirées"); ?><br>
          <input type='checkbox' name='archived' <?
          if($action=="search_photo" && isset($_POST["archived"])) echo "CHECKED";
          ?>> <?php echo _("Archivées"); ?>
          </td>
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
          <td><?php echo _("Nom"); ?>:</td>
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
if(($action=="search_photo" || $action=="search_category") && $nb){
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
      <center><?php echo _("Votre recherche a retourné"); ?> <?=$nbResults?> <?php echo _("résultat(s)"); ?></center><br>
    </td>
  </tr>
  <tr>
    <td colspan='2'>
    <table width='100%' class='message'>
  <?
  if($nbResults){
  	while($result=mysql_fetch_object($request)){
  		if($action=="search_photo"){
  			echo "<tr>";
  			echo "<td valign='middle' width='42' bgcolor='#e7eff2' align='right'><a href='./mod_photo.php?action=edit&idPhoto=".$result->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a></td>";
  			echo "<td>".$result->title."</td>";
  			echo "</tr>";
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
