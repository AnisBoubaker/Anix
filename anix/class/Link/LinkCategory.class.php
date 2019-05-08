<?php
class LinkCategory{
	private $id=0;
	private $name = "";

	//DB TABLES
	private $TBL_links_category;
	private $TBL_links_info_category;

	function __construct($id=0){
		global $TBL_links_category,$TBL_links_info_category;
		$this->id = $id;
		$this->TBL_links_category = $TBL_links_category;
		$this->TBL_links_info_category = $TBL_links_info_category;
		if($this->id!=0) $this->load();
	}

	private function load(){
		global $used_language_id;
		$link = dbConnect();
		$requestStr = "SELECT `$this->TBL_links_category`.`id`,`$this->TBL_links_info_category`.`name` ";
		$requestStr.= "FROM (`$this->TBL_links_category`,`$this->TBL_links_info_category`)";
		$requestStr.= "WHERE 1 ";
		$requestStr.= "AND `$this->TBL_links_category`.`id`='".$this->id."' ";
		$requestStr.= "AND `$this->TBL_links_info_category`.`id_language`='$used_language_id' ";
		$requestStr.= "AND `$this->TBL_links_info_category`.`id_category`=`$this->TBL_links_category`.`id` ";
		$request=request($requestStr,$link);
		if(!mysql_num_rows($request)) throw new ExceptionDBItemNotFound(_("La catégorie de liens spécifiée n'existe pas."));
		$cat = mysql_fetch_object($request);
		$this->name = $cat->name;
		mysql_close($link);
	}

	public function getId(){
		return $this->id;
	}

	public function getName(){
		return $this->name;
	}

	public function setName($name){
		$this->name = $name;
	}
}
?>