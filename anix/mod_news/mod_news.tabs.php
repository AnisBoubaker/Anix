<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
	<font class='fieldTitle'><?php echo _("Affichage");?>:</font><br>
    <?
    if($action=="edit" || $action=="update"){
    }
    ?>
    <input type='radio' name='active' value='Y' <?
    if($action=="edit" && $edit->active=="Y") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="Y") echo " CHECKED";
    ?>> <?php echo _("Cette nouvelle est toujours affichée.");?><br>
    <input type='radio' name='active' value='DATE' <?
    if($action=="edit" && $edit->active=="DATE") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="DATE") echo " CHECKED";
    ?>> <?php echo _("Afficher cette nouvelle du");?> <input type='text' name='from_date' id='from_date' size='10'<?
    if($action=="edit" && $edit->active=="DATE") echo " value='".$edit->from_date."'";
    if($action=="insert" || $action=="update") echo " value='".$_POST["from_date"]."'";
    ?> READONLY><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('from_date'),this);" style='vertical-align:bottom;' />
     <?php echo _("au"); ?> <input type='text' name='to_date' id='to_date' size='10'<?
     if($action=="edit" && $edit->active=="DATE") echo " value='".$edit->to_date."'";
     if($action=="insert" || $action=="update") echo " value='".$_POST["to_date"]."'";
    ?> READONLY><img src='../images/calendar.gif' onclick="scwShow(document.getElementById('to_date'),this);" style='vertical-align:bottom;' />
    <br />
    <input type='radio' name='active' value='ARCHIVE' <?
    if($action=="edit" && $edit->active=="ARCHIVE") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="ARCHIVE") echo " CHECKED";
    ?>> <?php echo _("Cette nouvelle est archivée (affichée avec les archives)."); ?><br>
    <input type='radio' name='active' value='N' <?
    if($action=="add") echo " CHECKED";
    if($action=="edit" && $edit->active=="N") echo " CHECKED";
    if(($action=="insert" || $action=="update") && $_POST["active"]=="N") echo " CHECKED";
    ?>> <?php echo _("Cette nouvelle est désactivée (non affichée)."); ?><br>
<?php
TABS_closeTab();
/**
 * TAB2: LINKS
 */
TABS_addTab(2,_("Liens"));
?>
<?php
TABS_closeTab();
/**
 * TAB3: ATTACHMENTS
 */
TABS_addTab(3,_("Fichiers attachés"));
?>
	  <table class='message' width='95%'>
      <tr>
        <td colspan='2'><font class='fieldTitle'><?php echo _("Fichiers attaché"); ?>:</font></td>
      </tr>
      <?
      //item attachments
      $attachments=request("SELECT $TBL_news_attachments.id id, $TBL_news_attachments.title attachment,$TBL_gen_languages.name language,$TBL_news_attachments.ordering FROM `$TBL_news_attachments`,`$TBL_gen_languages` WHERE $TBL_news_attachments.id_news='$idNews' AND $TBL_news_attachments.id_language=$TBL_gen_languages.id order by $TBL_news_attachments.ordering",$link);
      if(mysql_num_rows($attachments)>0){
      	//Get the maximum order
      	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_news_attachments` WHERE id_news='$idNews' GROUP BY id_news",$link);
      	if(mysql_num_rows($tmp)) {
      		$maxOrder= mysql_fetch_object($tmp);
      		$maxOrderValue = $maxOrder->maximum;
      	} else $maxOrderValue=1;
      	while($attachment = mysql_fetch_object($attachments)){
      		echo "<tr>";
      		echo "<td>";
      		echo $attachment->attachment."(".$attachment->language.")";
      		echo "</td>";
      		echo "<td align='right'>";
      		if($attachment->ordering>1){
      			echo "<a href='./mod_news.php?idNews=$idNews&idAttachment=".$attachment->id."&action=moveAttachmentUp'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
      		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
      		if($attachment->ordering<$maxOrderValue){
      			echo "<a href='./mod_news.php?idNews=$idNews&idAttachment=".$attachment->id."&action=moveAttachmentDown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
      		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
      		echo "<a href='./mod_attachment.php?action=edit&idNews=$idNews&idAttachment=".$attachment->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a>";
      		echo "&nbsp;<a href='./del_attachment.php?idAttachment=".$attachment->id."&idNews=$idNews'><img src='../images/del.gif' border='0' alt=\""._("Supprimer")."\"></a>";
      		echo "</td>";
      		echo "</tr>";
      	}
      } else {
      	echo "<tr><td colspan='2' align='center'><i>"._("Aucun fichier attaché à ce élément")."</i></td></tr>";
      }
      ?>
      <?
      if($action!="add" && $action!="insert"){
     ?>
      <tr>
        <td NOWRAP align='right' colspan='2'><A href='./mod_attachment.php?action=add&idNews=<?=$idNews?>'><?php echo _("Ajouter"); ?></A></td>
      </tr>
      <?
      } //IF
      ?>
      </table>
<?php
TABS_closeTab();
TABS_closeTabManager();
if($action=="add" || $action=="insert"){
	TABS_disableTab(2);
	TABS_disableTab(3);
}
TABS_disableTab(2);
/**
 * END OF TABS
 */
//TABS_disableTab(5);
//TABS_enableTab(3);
?>