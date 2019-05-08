<?php
/**
 * This class encapsulates values of a field based on their language.
 *
 * @author Anis Boubaker
 * @package Core
 * @version $Revision: 56 $
 */
class MultiLangualValue{
	/**
	 * Database handler
	 *
	 * @var DatabaseHandler
	 */
	private $dbh;
	/**
	 * List of used languages
	 *
	 * @var LanguageList
	 */
	private $languages;
	private $values;

	public function __construct(){
		global $dbh, $languages;
		$this->dbh = $dbh;
		$this->languages = $languages;
		$this->values=array();
	}

	/**
	 * Adds a language dependant value
	 *
	 * @param Language $language
	 * @param unknown_type $value
	 */
	public function setValue($language, $value){
		if($language instanceof Language){
			//We sent the language instance
			$this->values[$language->getId()]=$value;
		} elseif($this->languages->isAvailable($language)) {
			//We got the language ID as param
			$this->values[$language]=$value;
		}
	}

	/**
	 * Return the value for a given language
	 *
	 * @param Language $language
	 * @return unknown_type
	 */
	public function getValue($language){
		if($language instanceof Language){
			//We sent the language instance
			if(isset($this->values[$language->getId()])) return $this->values[$language->getId()];
			//if the value does not exist
			return "";
		} else {
			//We got the language ID as param
			if(isset($this->values[$language])) return $this->values[$language];
			//if the value does not exist
			return "";
		}
	}

	public function prependToAllValues($prepend){
		foreach($this->values as $key=>$value){
			$this->values[$key]=$prepend.$value;
		}
	}

	public function appendToAllValues($append){
		foreach($this->values as $key=>$value){
			$this->values[$key]=$value.$append;
		}
	}
}
?>