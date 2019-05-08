<?php
class ReviewsList implements Iterator {
	private $reviews=array();
	private $idProduct = 0;
	private $pointer=0;

	private $nbUnmoderatedReviews=0;

	//DB TABLES
	private $TBL_catalogue_review;

	function __construct($idProduct=0){
		global $TBL_catalogue_review,$TBL_ecommerce_curtomer;
		$this->TBL_catalogue_review = $TBL_catalogue_review;
		$this->idProduct=$idProduct;
		$this->pointer=0;
		$this->reviews=array();
		if($this->idProduct) $this->loadByProduct();
	}

	private function loadByProduct(){
		$link = dbConnect();
		$requestStr = "SELECT `$this->TBL_catalogue_review`.`id` ";
		$requestStr.= "FROM (`$this->TBL_catalogue_review`) ";
		$requestStr.= "WHERE 1 ";
		$requestStr.= "AND `$this->TBL_catalogue_review`.`id_product`='".$this->idProduct."' ";
		$requestStr.= "ORDER BY `$this->TBL_catalogue_review`.`review_date` DESC";
		$request=request($requestStr,$link);
		$count = 0;
		while($review = mysql_fetch_object($request)){
			$this->reviews[$count] = new CatalogueReview($review->id);
			if(!$this->reviews[$count]->isModerated()){
				$this->nbUnmoderatedReviews++;
			}
			$count++;
		}
		mysql_close($link);
	}

	public function loadNotModerated(){
		$this->reviews=array();
		$link = dbConnect();
		$requestStr = "SELECT `$this->TBL_catalogue_review`.`id` ";
		$requestStr.= "FROM (`$this->TBL_catalogue_review`) ";
		$requestStr.= "WHERE 1 ";
		$requestStr.= "AND `$this->TBL_catalogue_review`.`moderated`='N' ";
		$requestStr.= "ORDER BY `$this->TBL_catalogue_review`.`review_date` ";
		$request=request($requestStr,$link);
		while($review = mysql_fetch_object($request)){
			$this->reviews[] = new CatalogueReview($review->id);
			$this->nbUnmoderatedReviews++;
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
		return $this->reviews[$this->pointer];
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
		return ($this->pointer>=0 && $this->pointer<count($this->reviews));
	}

	public function isAllowed($idModule){
		foreach($this->reviews as $module){
			if($module["id"]==$idModule) return true;
		}
		return false;
	}

	public function getNbReviews(){
		return count($this->reviews);
	}

	public function getNbUnmoderatedReviews(){
		return $this->nbUnmoderatedReviews;
	}
}
?>