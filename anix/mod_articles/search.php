<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$errMessage="";
$message ="";
$action="";
$errors=0;
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
$title = _("Anix - Articles");
include("../html_header.php");
setTitleBar(_("Articles - Recherche"));
?>
<table width='95%' align='center'>
<tr valign='top'>
<td>
  <form action='./search.php' method='POST'>
  <input type='hidden' name='action' value='search_article'>
  <table border="0" align="center" width="95%" bgcolor='#FFFFFF' CellPadding="0" CellSpacing="0">
    <tr height='20'>
      <td  background='../images/button_back.jpg' align='left' valign='middle'>
        <font class='edittable_header'><?php echo _("Rechercher un article"); ?></font>
      </td>
      <td background='../images/button_back.jpg' align='right'>
        &nbsp;
      </td>
    </tr>
    <tr>
      <td colspan='2'>
        <table width='100%'>
        <tr>
          <td><?php echo _("Déscription"); ?>:</td>
          <td><input type='text' name='short_desc' size='30' <?
          if($action=="search_article") echo "value='".$_POST["short_desc"]."'";
          ?>></td>
        </tr>
        <tr>
          <td><?php echo _("Catégorie"); ?>:</td>
          <td><SELECT name='category'>
        	  <option value='0'>-- <?php echo _("Toutes") ?> --</option>
        	  <?
        	  $categories=request("select $TBL_articles_categories.id, $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering,$TBL_articles_info_categories.name, $TBL_articles_info_categories.description from  $TBL_articles_categories,$TBL_gen_languages,$TBL_articles_info_categories where $TBL_articles_info_categories.id_article_cat=$TBL_articles_categories.id and $TBL_articles_info_categories.id_language=$TBL_gen_languages.id and $TBL_gen_languages.id='$used_language_id' order by $TBL_articles_categories.id_parent, $TBL_articles_categories.ordering", $link);
        	  $tableCategories = getCatTable($categories);
        	  $listCategories = getCategoriesList($tableCategories,0 , 0);
        	  foreach($listCategories as $row){
        	  	echo "<option value='".$row["id"]."'";
        	  	if($action=="search_article" && $_POST["category"]==$row["id"]) echo " SELECTED";
        	  	echo ">".$row["name"]."</option>";
        	  }
        	  ?>
        	</SELECT></td>
      	</tr>
        <tr>
          <td><?php echo _("Mots clés"); ?>:</td>
          <td><input type='text' name='keywords' size='30' <?
          if($action=="search_article") echo "value='".$_POST["keywords"]."'";
          ?>></td>
        </tr>
        <tr>
          <td valign='top'><?php echo _("Filtres"); ?>:</td>
          <td><input type='checkbox' name='active' <?
          if($action=="search_article" && isset($_POST["active"])) echo "CHECKED";
          ?>> <?php echo _("Actifs"); ?>
          &nbsp;&nbsp;
          <input type='checkbox' name='disactivated' <?
          if($action=="search_article" && isset($_POST["disactivated"])) echo "CHECKED";
          ?>> <?php echo _("Désactivés"); ?><br>
          <input type='checkbox' name='awaiting' <?
          if($action=="search_article" && isset($_POST["awaiting"])) echo "CHECKED";
          ?>> <?php echo _("En attente"); ?>
          &nbsp;&nbsp;
          <input type='checkbox' name='expired' <?
          if($action=="search_article" && isset($_POST["expired"])) echo "CHECKED";
          ?>> <?php echo _("Expirés"); ?><br>
          <input type='checkbox' name='archived' <?
          if($action=="search_article" && isset($_POST["archived"])) echo "CHECKED";
          ?>> <?php echo _("Archivés"); ?>
          &nbsp;&nbsp;
          <input type='checkbox' name='home_page' <?
          if($action=="search_article" && isset($_POST["home_page"])) echo "CHECKED";
          ?>> <?php echo _("Page d'accueil"); ?>
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
if(($action=="search_article" || $action=="search_category") && $nb){
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
  		if($action=="search_article"){
  			echo "<tr>";
  			echo "<td valign='middle' width='42' bgcolor='#e7eff2' align='right'><a href='./mod_article.php?action=edit&idArticle=".$result->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a></td>";
  			echo "<td>".unhtmlentities($result->title)."</td>";
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
