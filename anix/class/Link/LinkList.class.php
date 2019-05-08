<?php
class LinkList implements Iterator {
	private $nbLinks = 0;
	private $links = array();
	private $module = 0;
	private $itemId = 0;
	private $linkCategoryId = 0;

	private $modules = null;

	//array that keep the position of the by category iterator
	private $iteratorCounter=array();
	//set the cateory on which we want to iterate
	private $iteratorCategory=0;


	function __construct($module, $itemId, $linkCategoryId=0){
		$this->nbLinks = 0;
		$this->links=array();
		$this->modules = new LinkModulesList();
		if($module==0 || !$this->modules->isAllowed($module)) throw new ExceptionAnixEngineError("Ce type de lien n'est pas supporté.");
		if($itemId==0) throw new ExceptionAnixError("L'identifiant de l'objet transmis est invalide.");
		$this->itemId = $itemId;
		$this->module = $module;
		$this->linkCategoryId = $linkCategoryId;
		$this->load();
	}

	/**
	 * Loads the list of links from the DB
	 *
	 */
	private function load(){
		global $TBL_links_link;
		$dbLink = dbConnect();
		$requestStr = "";
		$requestStr.="SELECT * FROM `$TBL_links_link` WHERE 1 ";
		$requestStr.="AND `from_module`='$this->module' ";
		$requestStr.="AND `from_item`='$this->itemId' ";
		if($this->linkCategoryId!=0) $requestStr.="AND `id_category`='$this->linkCategoryId' ";
		$requestStr.="ORDER BY `id_category`,`ordering`";
		$request=request($requestStr,$dbLink);
		if(mysql_errno($dbLink)) throw new ExceptionDBError("Une erreur s'est produite lors de la lecture des liens en base de donnée.");
		while($link = mysql_fetch_object($request)){
			if(!isset($this->links[$link->id_category])) $this->links[$link->id_category]=array();
			$this->links[$link->id_category][]=new Link($link->id,$link);
		}
		mysql_close($dbLink);
	}

	/**
	 * Returns true if the given category has links
	 *
	 * @param Integer $idCategory
	 * @return boolean
	 */
	public function categoryHasLinks($idCategory){
		return isset($this->links[$idCategory]);
	}

	public function getNbLinks($idCategory){
		if($this->categoryHasLinks($idCategory)) return count($this->links[$idCategory]);
		return 0;
	}

	/**
	 * Returns an array containing all the links for a specified links category
	 *
	 * @param INTEGER $idCategory
	 * @return Array
	 */
	public function getLinksArray($idCategory){
		if($this->categoryHasLinks()) return $this->links[$idCategory];
		else return null;
	}

	public function setIteratorCategory($idCategory){
		if(isset($this->links[$idCategory])) $this->iteratorCategory=$idCategory;
	}

	/**
	 * Initialise the iterator for the given links category
	 *
	 * @param Integer $idCategory
	 */
	private function iteratorInit(){
		if(!isset($this->iteratorCounter[$this->iteratorCategory])) $this->iteratorCounter[$this->iteratorCategory]=0;
	}

	/**
	 * Resets the iterator position for a given category
	 *
	 * @param unknown_type $idCategory
	 */
	public function rewind(){
		if($this->iteratorCategory && $this->categoryHasLinks($this->iteratorCategory)){
			$this->iteratorInit();
			$this->iteratorCounter[$this->iteratorCategory]=0;
		}
	}

	/**
	 * Iterate to the next Link for a given category
	 *
	 * @return Link
	 */
	public function current(){
		if(!$this->iteratorCategory) throw new ExceptionAnixEngineError(_("1. La catégorie de lien n'a pas été spécifiée pour l'itération."));
		if(!$this->categoryHasLinks($this->iteratorCategory)) return null;
		$this->iteratorInit();
		if(isset($this->links[$this->iteratorCategory][$this->iteratorCounter[$this->iteratorCategory]])){
			return $this->links[$this->iteratorCategory][$this->iteratorCounter[$this->iteratorCategory]];
		}
	}

	public function next(){
		if(!$this->iteratorCategory) throw new ExceptionAnixEngineError(_("2. La catégorie de lien n'a pas été spécifiée pour l'itération."));
		$this->iteratorCounter[$this->iteratorCategory]++;
	}

	/**
	 * Iterate to the previous Link for a given category
	 *
	 * @param Integer $idCategory
	 * @return Link
	 */
	public function previous(){
		if(!$this->iteratorCategory) throw new ExceptionAnixEngineError(_("3. La catégorie de lien n'a pas été spécifiée pour l'itération."));
		if($this->iteratorCounter[$this->iteratorCategory]>0) $this->iteratorCounter[$this->iteratorCategory]--;
	}

	public function key(){
		if(!$this->iteratorCategory) throw new ExceptionAnixEngineError(_("4. La catégorie de lien n'a pas été spécifiée pour l'itération."));
		return $this->iteratorCounter[$this->iteratorCategory];
	}

	public function valid(){
		if(!$this->iteratorCategory) throw new ExceptionAnixEngineError(_("5. La catégorie de lien n'a pas été spécifiée pour l'itération."));
		return (isset($this->links[$this->iteratorCategory][$this->iteratorCounter[$this->iteratorCategory]]));
	}

	/**
	 * Returns true if the same link as $link exists in the current iterator.
	 * This is helpful when we are adding a new link and want to check if there is an already
	 * set link to the same object.
	 * If the link category ID has been set, we only check if the the same link exists for that particular category.
	 *
	 * @param Link $link
	 * @return boolean
	 */
	public function exists(Link $link){
		foreach($this->links as $idCat=>$linksCat){
			if($this->linkCategoryId==0 || $idCat==$this->linkCategoryId){
				foreach($linksCat as $setLink){
					if($setLink->isEqual($link)) return true;
				}
			}
		}
	}

}

?>