<?php
class Link{
	private $id;
	private $category;
	private $fromModule;
	private $fromItem;
	private $toModule;
	private $toItem;

	private $ordering;

	private $modules = null;

	//DB TABLES
	private $TBL_links_link;


	function __construct($id=0,$link=0){
		global $TBL_links_link;
		$this->id=0;
		$this->category=0;
		$this->fromModule="";
		$this->fromItem=0;
		$this->toModule=0;
		$this->toItem=0;
		$this->ordering=0;
		$this->TBL_links_link=$TBL_links_link;
		$this->modules = new LinkModulesList();
		if($id!=0){
			$this->load($id,$link);
		}
	}

	/*public static function isSupportedLink($type=""){
		$supportedLinks=array(
			"CatalogueProduct"=>array("friendlyName"=>_("[Catalogue] Produit"),"module"=>"catalogue","type"=>"item","addLinkURL"=>"./addCatalogueProductLink.php"),
			"CatalogueCategory"=>array("friendlyName"=>_("[Catalogue] Catégorie"),"module"=>"catalogue","type"=>"category","addLinkURL"=>"./addCatalogueCategoryLink.php")
		);

		if($type=="") return $supportedLinks;

		if(isset($supportedLinks[$type])) return $supportedLinks[$type];
		else return FALSE;
	}*/

	private function load($id){
		global $TBL_links_link;
		$link = dbConnect();
		//The link ID could not be 0
		if(!$id) throw new ExceptionDBItemNotFound(_("Le lien spécifié n'existe pas."));
		//DB request
		$requestStr = "SELECT * FROM `$this->TBL_links_link` WHERE `id`='$id'";
		$request = request($requestStr,$link);
		if(mysql_errno($link)){
			mysql_close($link);
			throw new ExceptionDBError(_("La base de données a signalé une requête erronée"));
		}

		//if the link was not found, throw an exception
		if(!mysql_num_rows($request)){
			mysql_close($link);
			throw new ExceptionDBItemNotFound(_("Le lien spécifié n'existe pas."));
		}
		$row = mysql_fetch_object($request);
		//Fill in the object attributes
		$this->id=$row->id;
		$this->category = $row->id_category;
		$this->fromModule = $row->from_module;
		$this->fromItem = $row->from_item;
		$this->toModule = $row->to_module;
		$this->toItem = $row->to_item;
		$this->ordering = $row->ordering;
		//close the DB connection if opened inside the function
		mysql_close($link);
	}

	private function getMaxOrdering($link){
		$requestStr = "SELECT MAX(`ordering`) as maxordering FROM `$this->TBL_links_link` WHERE `id_category`='$this->category' AND `from_module`='$this->fromModule' AND `from_item`='$this->fromItem'";
		$request = request($requestStr,$link);
		if(!mysql_num_rows($request)) return 0;
		$tmp = mysql_fetch_object($request);
		return $tmp->maxordering;
	}

	public function save(){
		//connect to DB if connection link not given
		$insideLink = dbConnect();
		if($this->category==0) throw new ExceptionAnixError(_("La catégorie n'est pas valide. Le lien n'a pas été sauvegardé."));
		if($this->fromModule==0) throw new ExceptionAnixError(_("Le module de l'élément initial n'est pas valide. Le lien n'a pas été sauvegardé."));
		if($this->fromItem==0) throw new ExceptionAnixError(_("L'identifiant de l'élément initial n'est pas valide. Le lien n'a pas été sauvegardé."));
		if($this->toModule==0) throw new ExceptionAnixError(_("Le module de l'élément de destination n'est pas valide. Le lien n'a pas été sauvegardé."));
		if($this->toItem==0) throw new ExceptionAnixError(_("L'identifiant de l'élément de destination n'est pas valide. Le lien n'a pas été sauvegardé."));
		if($this->id){ //The link exists in DB, Update it
			$requestStr = "UPDATE `$this->TBL_links_link` SET ";
			$requestStr.= "`id_category`='$this->category',";
			$requestStr.= "`from_module`='$this->fromModule',";
			$requestStr.= "`from_item`='$this->fromItem',";
			$requestStr.= "`to_module`='$this->toModule',";
			$requestStr.= "`to_item`='$this->toItem',";
			$requestStr.= "`ordering`='$this->ordering' ";
			$requestStr.= "WHERE `id`='$this->id'";
		} else { //The link does not exist in DB, insert it
			$ordering = $this->getMaxOrdering($insideLink)+1;
			$requestStr = "INSERT INTO `$this->TBL_links_link` (`id_category`,`from_module`,`from_item`,`to_module`,`to_item`,`ordering`) VALUES (";
			$requestStr.= "'$this->category',";
			$requestStr.= "'$this->fromModule',";
			$requestStr.= "'$this->fromItem',";
			$requestStr.= "'$this->toModule',";
			$requestStr.= "'$this->toItem',";
			$requestStr.= "'$ordering'";
			$requestStr.= ")";
		}
		request($requestStr,$insideLink);
		if(mysql_errno($insideLink)){
			mysql_close($insideLink);
			throw new ExceptionDBError(_("La base de données a signalé une requête erronée"),mysql_errno($insideLink),mysql_error($insideLink));
		}
		mysql_close($insideLink);
	}

	public function moveDown(){
		if(!$this->id) throw new ExceptionAnixEngineError(_("Le lien n'a pas été identifié."));

		$dbLink = dbConnect();
		$maxOrdering = $this->getMaxOrdering($dbLink);
		if($this->ordering==$maxOrdering) throw new ExceptionAnixError("Ce lien est déjà au plus bas niveau.");
		//Update the link on the bottom of this one
		request("UPDATE `$this->TBL_links_link` SET `ordering`=`ordering`-1 WHERE `id_category`='$this->category' AND `from_module`='$this->fromModule' AND `from_item`='$this->fromItem' AND `ordering`='".($this->ordering+1)."'",$dbLink);
		//Move the link up
		$this->ordering++;
		$this->save();
		mysql_close($link);
	}

	public function moveUp(){
		$dbLink = dbConnect();
		if(!$this->id) throw new ExceptionAnixEngineError(_("Le lien n'a pas été identifié."));
		if($this->ordering==1) throw new ExceptionAnixError("Ce lien est déjà au plus haut niveau.");
		//Update the link on top of this one
		request("UPDATE `$this->TBL_links_link` SET `ordering`=`ordering`+1 WHERE `id_category`='$this->category' AND `from_module`='$this->fromModule' AND `from_item`='$this->fromItem' AND `ordering`='".($this->ordering-1)."'",$dbLink);
		//Move the link up
		$this->ordering--;
		$this->save();
		mysql_close($link);
	}

	public function delete(){
		//The link ID could not be 0
		if(!$this->id) throw new ExceptionAnixEngineError(_("Le lien n'a pas été identifié."));
		//connect to DB if connection link not given
		$insideLink = dbConnect();
		//Delete the row from the DB
		$requestStr = "DELETE FROM `$this->TBL_links_link` WHERE `id`='$this->id'";
		$request = request($requestStr,$insideLink);
		if(mysql_errno($insideLink)){
			throw new ExceptionDBError(_("La base de données a signalé une requête erronée"));
		}
		if(!mysql_affected_rows($insideLink)){
			throw new ExceptionDBItemNotFound(_("Le lien a déjà été supprimé."));
		}
		//Move up the links
		$requestStr = "UPDATE `$this->TBL_links_link` SET `ordering`=`ordering`-1 WHERE `id_category`='$this->category' AND `from_module`='$this->fromModule' AND `from_item`='$this->fromItem' AND `ordering`>'$this->ordering'";
		request($requestStr,$insideLink);
		//close the DB connection if opened inside the function
		mysql_close($insideLink);
	}

	//get a link side information
	static function getSideInfos($type,$id,$link=0){
		global $used_language_id;
		global $TBL_catalogue_info_products,$TBL_catalogue_info_categories;

		$modules = new LinkModulesList();

		//if(!isset(self::$supportedLinks[$type])) return FALSE;
		//if(self::isSupportedLink($type)===FALSE) return FALSE;
		if(!$modules->isAllowed($type)) return FALSE;
		if(!$id) throw new ExceptionDBItemNotFound(_("L'élément spécifié n'existe pas."));
		$return="";

		//connect to DB if connection link not given
		if(!$link) $insideLink = dbConnect();
		else $insideLink=$link;

		$code = $modules->getModuleCode($type);

		if($code=="CatalogueProduct"){
			$requestStr = "SELECT `name` FROM `$TBL_catalogue_info_products` WHERE `id_product`='$id' AND `id_language`='$used_language_id'";
			//echo $requestStr;
			$request = request($requestStr,$insideLink);
			if(!mysql_num_rows($request)) throw new ExceptionDBItemNotFound(_("L'élément spécifié n'existe pas."));
			$row = mysql_fetch_object($request);
			$return = $row->name;
		}

		if($code=="CatalogueCategory"){
			$requestStr = "SELECT `name` FROM `$TBL_catalogue_info_categories` WHERE `id_catalogue_cat`='$id' AND `id_language`='$used_language_id'";
			//echo $requestStr;
			$request = request($requestStr,$insideLink);
			if(!mysql_num_rows($request)) throw new ExceptionDBItemNotFound(_("L'élément spécifié n'existe pas."));
			$row = mysql_fetch_object($request);
			$return = $row->name;
		}

		if(!$link) mysql_close($insideLink);
		return $return;
	}

	public function isEqual(Link $link){
		return ($this->fromModule==$link->fromModule
				&& $this->fromItem==$link->fromItem
				&& $this->toModule==$link->toModule
				&& $this->toItem==$link->toItem);
	}

	public function getId(){
		return $this->id;
	}

	public function setFrom($module,$id){
		$this->fromModule = $module;
		$this->fromItem = $id;
	}

	public function setTo($module,$id){
		$this->toModule = $module;
		$this->toItem = $id;
	}

	public function setCategory($idCategory){
		try{
			$tmp = new LinkCategory($idCategory);
			$this->category=$idCategory;
		} catch (ExceptionDBItemNotFound $e){
			throw new ExceptionAnixError(_("La catégorie spécifiée n'existe pas."));
		}
	}

	public function getCategoryId(){
		return $this->category;
	}

	public function getFromModule(){
		return $this->fromModule;
	}

	public function getFromItem(){
		return $this->fromItem;
	}

	public function getToModule(){
		return $this->toModule;
	}

	public function getToItem(){
		return $this->toItem;
	}

	public function getToInfos(){
		return Link::getSideInfos($this->toModule,$this->toItem);
	}

	/**
	 * Deletes all the links related to an item
	 *
	 * @param int $module The module ID
	 * @param int $item The item Id
	 */
	public static function deleteAllLinks($module,$item){
		global $TBL_links_link;
		$dbLink = dbConnect();
		$request = request("SELECT `id` FROM `$TBL_links_link` WHERE (`from_module`='$module' AND `from_item`='$item') OR (`to_module`='$module' AND `to_item`='$item')",$dbLink);
		while($row = mysql_fetch_object($request)){
			$link = new Link($row->id);
			$link->delete();
		}
	}
}
?>