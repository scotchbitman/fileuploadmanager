<?php
/**
 *
 */
class FileUploadErrorException extends Exception{

	protected $title;

	public function __construct($message,$code,$title){
		parent::__construct($message,$code);
		$this->title = $title;
	}
}
?>