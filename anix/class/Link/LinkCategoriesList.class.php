<?php
class LinkCategoriesList implements Iterator {
	private $categories=array();
	private $idLanguage = 0;
	private $pointer=0;

	//DB TABLES
	private $TBL_links_category;
	private $TBL_links_info_category;

	function __construct($idLanguage){
		global $TBL_links_category, $TBL_links_info_category;
		$this->TBL_links_category = $TBL_links_category;
		$this->TBL_links_info_category = $TBL_links_info_category;
		$this->idLanguage=$idLanguage;
		$this->pointer=0;
		$this->load();
	}

	private function load(){
		$link = dbConnect();
		$requestStr = "";
		$requestStr.= "SELECT `id`,`name` FROM `$this->TBL_links_category`,`$this->TBL_links_info_category` WHERE 1 ";
		$requestStr.= "AND `$this->TBL_links_info_category`.`id_language`='$this->idLanguage' ";
		$requestStr.= "AND `$this->TBL_links_info_category`.`id_category`=`$this->TBL_links_category`.`id` ";
		$request = request($requestStr,$link);
		while($category = mysql_fetch_object($request)){
			$this->categories[]=new LinkCategory($category->id,$category->name);
		}
		mysql_close($link);
	}

	public function rewind(){
		$this->pointer=0;
	}
	/**
	 * Return the current category inside the iterator
	 *
	 * @return LinkCategory
	 */
	public function current(){
		return $this->categories[$this->pointer];
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
		return ($this->pointer>=0 && $this->pointer<count($this->categories));
	}
}
?>