<?php
class Review{
	private $id=0;
	private $idProduct=0;
	private $idCustomer=0;
	private $idLanguage=0;
	private $review = "";
	private $score=-1;
	private $reviewerName = "";
	private $reviewerEmail = "";
	private $reviewDate="";
	private $moderated;

	//DB TABLES
	private $TBL_catalogue_review;
	private $TBL_ecommerce_customer;

	const MAX_SCORE = 5;

	function __construct($id=0, $idProduct=0, $idCustomer=0){
		global $TBL_catalogue_review,$TBL_ecommerce_customer;
		$this->id = $id;
		$this->TBL_catalogue_review = $TBL_catalogue_review;
		$this->TBL_ecommerce_customer = $TBL_ecommerce_customer;
		if($this->id!=0) $this->load();
		else {
			$this->idProduct=$idProduct;
			$this->idCustomer=$idCustomer;
		}
	}

	private function load(){
		$link = dbConnect();
		$requestStr = "SELECT `$this->TBL_catalogue_review`.*,`$this->TBL_ecommerce_customer`.`id` as customer, `$this->TBL_ecommerce_customer`.`greating`,`$this->TBL_ecommerce_customer`.`firstname`,`$this->TBL_ecommerce_customer`.`lastname`,`$this->TBL_ecommerce_customer`.`email` as customer_email ";
		$requestStr.= "FROM (`$this->TBL_catalogue_review`)";
		$requestStr.= "LEFT JOIN `$this->TBL_ecommerce_customer` ON (`$this->TBL_ecommerce_customer`.`id`=`$this->TBL_catalogue_review`.`id_customer`) ";
		$requestStr.= "WHERE 1 ";
		$requestStr.= "AND `$this->TBL_catalogue_review`.`id`='".$this->id."' ";
		$request=request($requestStr,$link);
		if(!mysql_num_rows($request)) throw new ExceptionDBItemNotFound(_("L'avis de produit spécifié n'existe pas."));
		$review = mysql_fetch_object($request);
		$this->idProduct = $review->id_product;
		$this->idCustomer = $review->id_customer;
		$this->idLanguage = $review->id_language;
		$this->review = $review->review;
		$this->score = $review->score;
		if($review->customer!=null){
			$this->reviewerName = $review->greating." ".$review->firstname." ".$review->lastname;
			$this->reviewerEmail = $review->customer_email;
		} else {
			$this->reviewerName = $review->reviewer_name;
			$this->reviewerEmail = $review->reviewer_email;
		}
		$this->reviewDate = $review->review_date;
		$this->moderated = $review->moderated=="Y";
		mysql_close($link);
	}

	public function getId(){
		return $this->id;
	}

	public function getIdProduct(){
		return $this->idProduct;
	}

	public function getIdLanguage(){
		return $this->idLanguage;
	}

	public function getIdCustomer(){
		return $this->idCustomer;
	}

	public function getCustomerName(){
		return $this->reviewerName;
	}

	public function getCustomerEmail(){
		return $this->reviewerEmail;
	}

	public function getReview(){
		return $this->review;
	}

	public function getScore(){
		return $this->score;
	}

	public function getReviewDate(){
		return $this->reviewDate;
	}

	public function isModerated(){
		return $this->moderated;
	}

	public function setId($id){
		if(!$this->id) $this->id = $id;
		else throw new ExceptionAnixEngineError(_("Un identifiant a déjà été défini pour cet avis."));
	}

	public function setIdProduct($id){
		$this->idProduct = $id;
	}

	public function setIdCustomer($id){
		$this->idCustomer = $id;
	}

	public function setIdLanguage($id){
		$this->idLanguage=$id;
	}

	public function setReview($review){
		$this->review=$review;
	}

	public function setScore($score){
		$this->score=$score;
	}

	public function setCustomerName($name){
		$this->reviewerName=$name;
	}

	public function acceptReview(){
		$this->moderated=true;
	}

	public function save(){
		if(!$this->idProduct) throw new ExceptionAnixEngineError(_("Le produit n'a pas été spcécifié pour l'avis."));
		if(!$this->idCustomer && $this->reviewerName=="") throw new ExceptionAnixEngineError(_("Le client n'a pas été spcécifié pour l'avis."));
		if($this->score==-1) throw new ExceptionAnixEngineError(_("La note n'a pas été spcécifiée pour l'avis."));
		$link = dbConnect();
		if(!$this->id){
			//Insert the review
			$requestStr = "INSERT INTO `$this->TBL_catalogue_review` (`id_product`,`id_customer`,`id_language`,`review`,`score`) VALUES (";
			$requestStr.= "'$this->idProduct','$this->idCustomer','$this->idLanguage','$this->review','$this->score'";
			$requestStr.= ")";
			request($requestStr,$link);
			$this->id = mysql_insert_id($link);
		} else {
			//update the review
			$requestStr = "UPDATE `$this->TBL_catalogue_review` SET ";
			$requestStr.= "`id_product`='$this->idProduct',";
			$requestStr.= "`id_customer`='$this->idCustomer',";
			$requestStr.= "`id_language`='$this->idLanguage',";
			$requestStr.= "`reviewer_name`='$this->reviewerName',";
			$requestStr.= "`reviewer_email`='$this->reviewerEmail',";
			$requestStr.= "`review`='$this->review',";
			$requestStr.= "`score`='$this->score',";
			$requestStr.= "`moderated`='".($this->moderated?"Y":"N")."',";
			$requestStr.= "`review_date`='$this->reviewDate' ";
			$requestStr.= "WHERE `id`='$this->id'";
			request($requestStr,$link);
		}
		mysql_close($link);
	}

	public function delete(){
		if(!$this->id) throw new ExceptionAnixEngineError(_("L'avis produit n'a pas été initialisé."));
		$link = dbConnect();
		$requestStr = "DELETE FROM `$this->TBL_catalogue_review` WHERE `id`='$this->id'";
		request($requestStr,$link);
		mysql_close($link);
	}
}
?>