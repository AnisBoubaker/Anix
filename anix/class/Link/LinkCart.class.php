<?php
class LinkCart implements Iterator {
	private $categoryId=0;
	private $category = null;
	private $fromModule = 0;
	private $fromId = 0;

	private $modules = null;

	//Poisition inside the Cart links (iterator)
	private $pointer=0;

	function __construct($category=0,$linkFrom=0,$linkFromId=0){
		$this->modules = new LinkModulesList();
		if($category!=0 && $linkFrom!="" && $linkFromId!=0){
			//check the category
			$categoryObj = new LinkCategory($category);
			if(!$this->modules->isAllowed($linkFrom)) throw new ExceptionAnixError(_("Ce type de lien n'est pas supporté."));
			//check if item existense
			Link::getSideInfos($linkFrom,$linkFromId);
			$_SESSION["linkCart"]=array();
			$_SESSION["linkCart"]["category"]=$category;
			$_SESSION["linkCart"]["from"]=$linkFrom;
			$_SESSION["linkCart"]["fromId"]=$linkFromId;
		} elseif(!isset($_SESSION["linkCart"]) || !isset($_SESSION["linkCart"]["category"]) || !isset($_SESSION["linkCart"]["from"]) || !isset($_SESSION["linkCart"]["fromId"])){
			throw new ExceptionAnixError(_("Panier de liens non initialisé."));
		}
		$this->categoryId = $_SESSION["linkCart"]["category"];
		$this->fromModule = $_SESSION["linkCart"]["from"];
		$this->fromId = $_SESSION["linkCart"]["fromId"];
		$this->category = new LinkCategory($this->categoryId);
	}

	public function getFromModule(){
		return $this->fromModule;
	}

	public function getFromId(){
		return $this->fromId;
	}

	public function getCategoryName(){
		return $this->category->getName();
	}

	public function getCategoryId(){
		return $this->categoryId;
	}

	function addLink($linkType,$linkTo){
		if(!$this->modules->isAllowed($linkType)){
			throw new ExceptionAnixError(_("Type de lien non supporté."));
		}
		if(!isset($_SESSION["linkCart"]["links"])) $_SESSION["linkCart"]["links"]=array();
		//Check if already added
		$found=false;
		foreach ($_SESSION["linkCart"]["links"] as $presentLink){
			if($presentLink["type"]==$linkType && $presentLink["id"]==$linkTo){
				throw new ExceptionAnixError(_("Vous avez déjà ajouté ce lien."));
			}
		}

		//check if we already have the link in the objects links (not in cart)
		$previousLinks = new LinkList($this->fromModule,$this->fromId, $this->categoryId);
		$toAdd = new Link();
		$toAdd->setFrom($this->fromModule,$this->fromId);
		$toAdd->setTo($linkType,$linkTo);
		if($previousLinks->exists($toAdd)) throw new ExceptionAnixError(_("Le lien que vous tentez d'ajouter a déjà été défini pour cet objet. Vous n'avez pas besoin de l'ajouter à nouveau."));

		$_SESSION["linkCart"]["links"][]=array("type"=>$linkType,"id"=>$linkTo);
	}

	public function getNbLinks(){
		if (!isset($_SESSION["linkCart"]["links"])) return 0;
		return count($_SESSION["linkCart"]["links"]);
	}

	public function rewind(){
		$this->pointer=0;
	}

	public function end(){
		if(isset($_SESSION["linkCart"]["links"])){
			$this->pointer=count($_SESSION["linkCart"]["links"]);
		}
	}

	public function current(){
		if($this->pointer!=0) return $_SESSION["linkCart"]["links"][$this->pointer-1];
		return null;
	}

	public function key(){
		return $this->pointer;
	}

	public function next(){
		if(isset($_SESSION["linkCart"]["links"][$this->pointer])){
			$this->pointer++;
			return $_SESSION["linkCart"]["links"][$this->pointer-1];
		}
		return null;
	}

	public function previous(){
		if($this->pointer==0) return null;
		$this->pointer--;
		return $_SESSION["linkCart"]["links"][$this->pointer];
	}

	public function valid(){
		return $this->pointer<count($_SESSION["linkCart"]["links"]);
	}

	public function commitCart(){
		foreach($_SESSION["linkCart"]["links"] as $toAdd){
			$link = new Link();
			$link->setCategory($this->categoryId);
			$link->setFrom($this->fromModule,$this->fromId);
			$link->setTo($toAdd["type"],$toAdd["id"]);
			$link->save();
		}
	}
}
?>