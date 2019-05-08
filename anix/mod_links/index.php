<?
include ("../config.php");
include ("./module_config.php");
$link = dbConnect();
$delete=false;
if(isset($_REQUEST["action"])){
	$action=$_REQUEST["action"];
} else {
	$ANIX_messages->addError(_("Aucune action n'a été définie."));
}
$modules = new LinkModulesList($used_language_id);

if($action=="addLink"){
	if(!$ANIX_messages->nbErrors && isset($_REQUEST["linkCat"])){
		$linkCat=$_REQUEST["linkCat"];
	} else {
		$ANIX_messages->addError(_("Type de lien non spécifié."));
	}
	if(!$ANIX_messages->nbErrors && isset($_REQUEST["linkFrom"])){
		$linkFrom=$_REQUEST["linkFrom"];
	} else {
		$ANIX_messages->addError(_("Origine du lien non spécifiée."));
	}
	if(!$ANIX_messages->nbErrors && isset($_REQUEST["id"])){
		$linkFromId=$_REQUEST["id"];
	} else {
		$ANIX_messages->addError(_("Identifiant de l'élément non spécifié."));
	}
	if(!$modules->isAllowed($_REQUEST["linkFrom"])) $ANIX_messages->addError(_("Ce type de lien n'est pas supporté."));
	if(!$ANIX_messages->nbErrors) try{
		$linkCart = new LinkCart($linkCat,$linkFrom,$linkFromId);
		header("Location: ./addLink.php");
		exit();
	} catch(Exception $e){
		$ANIX_messages->addError($e->getMessage());
	}
}
if($action=="error"){
	$ANIX_messages->addError(_("Une erreur s'est produite. Veuillez fermer cette fenêtre et ré-essayer de nouveau."));
}
?>
<?
//$title = _("Anix - Ajout d'un lien");
//title is handled in module_config
$title=_("Erreur de paramètres");
include("../html_header_popup.php");
setTitleBar($title);
$button=array();
//$buttons[]=array("type"=>"validate","link"=>"javascript:formSubmit(document.getElementById('main_form'));");
$buttons[]=array("type"=>"cancel","link"=>"javascript:window.close();");
printButtons($buttons);
?>
<?
include ("../html_footer_popup.php");
mysql_close($link);
?>