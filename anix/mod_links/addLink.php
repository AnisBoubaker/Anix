<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
try{
	$linkCart = new LinkCart();
} catch (Exception $e){
	//header("Location: ./index.php?action=error");
	//exit();
	echo $e->getMessage();
}
try{
	$sideInfo =  Link::getSideInfos($linkCart->getFromModule(),$linkCart->getFromId(),0);
} catch(Exception $e){
	header("Location: ./index.php?action=error");
	exit();
}

//load the available linkable modules
$modules = new LinkModulesList($used_language_id);

if(isset($_REQUEST["action"])){
	$action=$_REQUEST["action"];
} else $action="";

if($action=="add"){
	if(isset($_REQUEST["linkType"])) $linkType=$_REQUEST["linkType"];
	else $ANIX_messages->addError(_("Type de lien non spécifié."));
	if(isset($_REQUEST["linkTo"])) $linkTo=$_REQUEST["linkTo"];
	else $ANIX_messages->addError(_("Destination du lien non spécifiée."));
	if(!$ANIX_messages->nbErrors) try{
		$linkCart->addLink($linkType,$linkTo);
		$ANIX_messages->addMessage(_("Le lien a bien été ajouté."));
	} catch (Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
}
?>
<?
//$title = _("Anix - Ajout d'un lien");
//title is handled in module_config
$title=_("Ajout d'un lien de type").": ".$linkCart->getCategoryName();
include("../html_header_popup.php");
setTitleBar($title);
$button=array();
$buttons[]=array("type"=>"validate","link"=>"javascript:opener.addLinksFromCart(); window.close();");
$buttons[]=array("type"=>"cancel","link"=>"javascript:window.close();");
printButtons($buttons);
?>
<table style='width:100%'>
<tr>
	<td style='width:50%;vertical-align:top;'>
	<?php
	echo "<b>"._("Lier l'objet:")."</b><br /><br /> ";
	$from = $modules->getName($linkCart->getFromModule());
	echo "<i>".$from."</i>:<br />";
	echo $sideInfo;
	?>
	</td>
	<td style='width:50%;vertical-align:top;'>
	<?php
	echo "<b>"._("À un objet de type:")."</b><br /><br />";
	?>
	<select name='new_link_target' id='new_link_target'>
	<?php
	$jsCode = "urls = Array();\n";
	echo "<option value='0'>---"._("CHOISISSEZ")."---</option>";
	foreach ($modules as $module){
		echo "<option value='".$module["id"]."'>".$module["name"]."</option>";
		$jsCode.="urls['".$module["id"]."']=\"".$module["addLinkURL"]."\";\n";
	}
	?>
	</select><br /><br />
	<input type='button' value='Ajouter' onclick='javascript:addRedirect();' />
	</td>
</tr>
</table>
<div style='width:100%;background:#2e72a3;color:#ffffff;border-bottom:1px solid #ffffff;padding:3px 0 3px 0;margin:10px 0 10px 0;'>
	<b><?php echo _("Liste des liens à ajouter:");?></b>
</div>
<?php
$nbLinks = $linkCart->getNbLinks();
$linkCart->end();
if($nbLinks){
	while($link = $linkCart->previous()){
		echo "<b>".$modules->getName($link["type"])."</b><br />";
		echo Link::getSideInfos($link["type"],$link["id"],0);
		echo "<hr />";
	}
} else {
	echo "<div style='text-align:center;width:100%;'><i>"._("Aucun lien n'a été ajouté")."</i></div>";
}
?>
<script type='text/javascript'>
	<?php echo $jsCode; ?>
	function addRedirect(){
		selected_type = document.getElementById('new_link_target').options[document.getElementById('new_link_target').selectedIndex].value;
		if(selected_type==0) return;
		url = urls[selected_type];
		window.location=url;
	}
</script>
<?
include ("../html_footer_popup.php");
mysql_close($link);
?>