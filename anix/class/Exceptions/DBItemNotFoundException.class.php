<?php
class ExceptionDBItemNotFound extends Exception {
	function __construct($message){
		parent::__construct($message);
	}
}
?>