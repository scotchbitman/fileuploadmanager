<?php
	class FileUploadExtensionErrorException extends FileUploadErrorException{
		public function __construct($code){
			parent::__construct(NULL,$code,"ERREUR EXTENSION");
			$this->message = 'Extension interdite';
		}
	}
?>