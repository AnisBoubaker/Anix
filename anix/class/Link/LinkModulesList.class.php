<?php
class LinkModulesList implements Iterator {
	private $modules=array();
	private $idLanguage = 0;
	private $pointer=0;

	//DB TABLES
	private $TBL_links_module;
	private $TBL_links_info_module;

	function __construct($idLanguage=0){
		global $TBL_links_module, $TBL_links_info_module;
		$this->TBL_links_module = $TBL_links_module;
		$this->TBL_links_info_module = $TBL_links_info_module;
		$this->idLanguage=$idLanguage;
		$this->pointer=0;
		$this->load();
	}

	private function load(){
		$link = dbConnect();
		$requestStr = "";
		if($this->idLanguage) $requestStr.= "SELECT `id`,`name`,`code`,`icon_file`,`add_link_url` ";
		else $requestStr.= "SELECT DISTINCT `id`,'' as name,`code`,`icon_file`,`add_link_url` ";
		$requestStr.= "FROM `$this->TBL_links_module`,`$this->TBL_links_info_module` WHERE 1 ";
		if($this->idLanguage) $requestStr.= "AND `$this->TBL_links_info_module`.`id_language`='$this->idLanguage' ";
		$requestStr.= "AND `$this->TBL_links_info_module`.`id_module`=`$this->TBL_links_module`.`id` ";

		$request = request($requestStr,$link);
		while($module = mysql_fetch_object($request)){
			$this->modules[]=array("id"=>$module->id,"name"=>$module->name,"code"=>$module->code,"icon"=>$module->icon_file,"addLinkURL"=>$module->add_link_url);
		}
		mysql_close($link);
	}

	public function rewind(){
		$this->pointer=0;
	}
	/**
	 * Return the current module inside the iterator
	 *
	 * @return Array
	 */
	public function current(){
		return $this->modules[$this->pointer];
	}
	public function key(){
		return $this->pointer;
	}

	public function next(){
		$this->pointer++;
	}

	public function previous(){
		if($this->pointer>0) $this->pointer--;
	}

	public function valid(){
		return ($this->pointer>=0 && $this->pointer<count($this->modules));
	}

	public function isAllowed($idModule){
		foreach($this->modules as $module){
			if($module["id"]==$idModule) return true;
		}
		return false;
	}

	public function getModuleCode($idModule){
		foreach($this->modules as $module){
			if($module["id"]==$idModule) return $module["code"];
		}
		return false;
	}

	public function getName($idModule){
		foreach($this->modules as $module){
			if($module["id"]==$idModule) return $module["name"];
		}
		return false;
	}
}
?>