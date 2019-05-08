<?php
class Messenger{
	public $nbMessages = 0;
	public $nbWarnings = 0;
	public $nbErrors = 0;

	private $messages = array();

	private $currentError = 0;
	private $currentMessage = 0;
	private $currentWarning = 0;

	function AnixMessenger(){
		$this->messages=array();
		$this->messages["erros"]=array();
		$this->messages["messages"]=array();
		$this->messages["warnings"]=array();
	}

	public function addError($message){
		$this->nbErrors++;
		$this->messages["errors"][]=$message;
	}

	public function addWarning($message){
		$this->nbWarnings++;
		$this->messages["warnings"][]=$message;
	}

	public function addMessage($message){
		$this->nbMessages++;
		$this->messages["messages"][]=$message;
	}

	public function getError(){
		if($this->currentError<$this->nbErrors) return $this->messages["errors"][$this->currentError++];
		else $this->currentError=0;

		return false;

	}

	public function getWarning(){
		if($this->currentWarning<$this->nbWarnings) return $this->messages["warnings"][$this->currentWarning++];
		else $this->currentWarning=0;

		return false;
	}

	public function getMessage(){
		if($this->currentMessage<$this->nbMessages) return $this->messages["messages"][$this->currentMessage++];
		else $this->currentMessage=0;

		return false;
	}

	public function hasMessages(){
		return ($this->nbErrors || $this->nbMessages || $this->nbWarnings);
	}

	public function convertOldErrors($messages,$errno,$errMessage){
		//parse the $message against <br />s
		$oldMessages = explode("<br />",$messages);
		foreach ($oldMessages as $message) {
			if($message!="") $this->addMessage($message);
		}
		//parse the $errMessage against <br />s
		$oldErrors = explode("<br />",$errMessage);
		foreach ($oldErrors as $error) {
			if($error!="") $this->addError($error);
		}
	}
}

?>