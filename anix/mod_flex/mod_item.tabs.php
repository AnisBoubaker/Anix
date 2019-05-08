<?php
TABS_startTabManager("95%");
/**
 * TAB1: GENERAL
 */
TABS_addTab(1,_("Général"));
?>
	<table class='message' width='95%'>
      <tr>
        <td>
          <font class='fieldTitle'><?php echo _("Affichage"); ?>:</font>
        </td>
        <td>
          <select name='active'>
          <option value='Y' <?
          if($action=="add") echo " CHECKED";
          if($action=="edit" && $edit->active=="Y") echo " CHECKED";
          if(($action=="insert" || $action=="update") && $_POST["active"]=="Y") echo " CHECKED";
          ?>><?php echo _("Affiché"); ?></option>
          <option value='N' <?
          if($action=="edit" && $edit->active=="N") echo " SELECTED";
          if(($action=="insert" || $action=="update") && $_POST["active"]=="N") echo " SELECTED";
          ?>><?php echo _("Déactivé"); ?></option>
          </select>
        </td>
      </tr>
      </table>
<?php
TABS_closeTab();
/**
 * TAB2: ATTACHMENTS
 */
TABS_addTab(2,_("Fichiers attachés"));
?>
	  <table class='message' width='95%'>
      <tr>
        <td colspan='2'><font class='fieldTitle'><?php echo _("Fichiers attaché"); ?>:</font></td>
      </tr>
      <?
      //item attachments
      $attachments=request("SELECT $TBL_lists_attachments.id id, $TBL_lists_attachments.title attachment,$TBL_gen_languages.name language,$TBL_lists_attachments.ordering FROM `$TBL_lists_attachments`,`$TBL_gen_languages` WHERE $TBL_lists_attachments.id_item='$idItem' AND $TBL_lists_attachments.id_language=$TBL_gen_languages.id order by $TBL_lists_attachments.ordering",$link);
      if(mysql_num_rows($attachments)>0){
      	//Get the maximum order
      	$tmp = request("SELECT MAX(ordering) as maximum from `$TBL_lists_attachments` WHERE id_item='$idItem' GROUP BY id_item",$link);
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
      			echo "<a href='./mod_item.php?idItem=$idItem&idAttachment=".$attachment->id."&action=moveAttachmentUp'><img src='../images/order_up.gif' border='0' alt=\""._("Monter")."\"></a>&nbsp;";
      		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
      		if($attachment->ordering<$maxOrderValue){
      			echo "<a href='./mod_item.php?idItem=$idItem&idAttachment=".$attachment->id."&action=moveAttachmentDown'><img src='../images/order_down.gif' border='0' alt=\""._("Descendre")."\"></a>&nbsp;";
      		} else echo "<img src='../images/order_blank.gif' border='0'>&nbsp;";
      		echo "<a href='./mod_attachment.php?action=edit&idItem=$idItem&idAttachment=".$attachment->id."'><img src='../images/edit.gif' border='0' alt=\""._("Modifier")."\"></a>";
      		echo "&nbsp;<a href='./del_attachment.php?idAttachment=".$attachment->id."&idItem=$idItem'><img src='../images/del.gif' border='0' alt=\""._("Supprimer")."\"></a>";
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
        <td NOWRAP align='right' colspan='2'><A href='./mod_attachment.php?action=add&idItem=<?=$idItem?>'><?php echo _("Ajouter"); ?></A></td>
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
}
/**
 * END OF TABS
 */
//TABS_disableTab(5);
//TABS_enableTab(3);
?>