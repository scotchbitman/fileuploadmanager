<?php
/**
 *
 */
class FileUploadFormatErrorException extends FileUploadErrorException{
	
	public function __construct($code){
		parent::__construct(NULL,$code,'ERREUR FICHIER');
	}
}
?>