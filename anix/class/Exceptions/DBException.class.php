<?php
class ExceptionDBError extends Exception {
	private $mySqlErrNo;
	private $mySqlErr;

	function __construct($message,$mySqlErrNo, $mySqlErr){
		parent::__construct($message);
		$this->mySqlErr = $mySqlErr;
		$this->mySqlErrNo = $mySqlErrNo;
	}

	public function getDBErrNo(){
		return $this->mySqlErrNo;
	}

	public function getDBErr(){
		return $this->mySqlErr;
	}
}
?>