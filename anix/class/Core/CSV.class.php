<?php
class CSV{
	private $fileName;
	private $csvData = "";

	public function __construct($fileName){
		$this->fileName = $fileName;
	}


	/**
	 * Add a new line to the CSV file
	 *
	 * @param array $data
	 */
	public function addLine($data){
		foreach($data as $column){
			$this->csvData.=$column."\t";
		}
		$this->csvData.="\r\n";
	}

	public function write($folder){
		$fh = fopen($folder.$this->fileName, 'w');
		fwrite($fh, $this->csvData);
		fclose($fh);
	}
}
?>