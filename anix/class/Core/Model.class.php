<?php
/**
 * Model abstract class. Each specific model must extend this class.
 *
 * @author Anis Boubaker
 * @package Models
 * @version $Revision: 258 $
 */
abstract class Model{
	/**
	 * Database handler
	 *
	 * @var DatabaseHandler
	 */
	protected $dbh;
	/**
	 * List of used languages
	 *
	 * @var LanguageList
	 */
	protected $languages;
	/**
	 * Holds the application messenger
	 *
	 * @var Messenger
	 */
	protected $messenger;
	/**
	 * Form object associated to the Model
	 *
	 * @var Form
	 */
	protected $form;

	private $log;
	protected $defaultOnChangeScript;

	public function __construct(){
		global $dbh, $languages, $messenger, $defaultOnChangeScript;
		$this->dbh=$dbh;
		$this->languages=$languages;
		$this->messenger = $messenger;
		$this->log="";
		$this->defaultOnChangeScript=$defaultOnChangeScript;
	}

	abstract protected function load();

	abstract public function save();

	/**
	 * Creates a Form object associated with the model
	 *
	 * @param String $formId
	 * @return Form
	 */
	abstract public function getForm($formId);


	/**
	 * Populates attributes from the form values.
	 *
	 */
	abstract public function populate();

	/**
	 * Initialise multilingual fields by affecting a new MultiLangualValue instance to each field in the array param
	 *
	 * @param array $fields
	 */
	protected function initMultiLangualValues($fields){
		if(is_array($fields)){
			foreach($fields as $field){
				$this->$field = new MultiLangualValue();
			}
		} else {
			//if we ongly got one field
			$this->$fields = new MultiLangualValue();
		}
	}

	/**
	 * Sets a value in a form field depending on the language
	 *
	 * @param String $field
	 * @param FormField $formField
	 * @param Language $language
	 * @param FormSelector $selector
	 */
	protected function setMultiLangualValueInFormField($field, $formField, $language,$selector, $prepend="", $append=""){
		$formField->setValue($prepend.$this->$field->getValue($language).$append,$selector,$selector->getOptionIdByAppend($language->getShortName()));
	}


	public function setLog($log){
		$this->log = $log;
	}

	public function getLog(){
		return $this->log;
	}

	public function resetLog(){
		$this->log="";
	}

}
?>