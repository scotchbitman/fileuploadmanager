<?php
	class FileUploadMimeTypeErrorException extends FileUploadErrorException{
		public function __construct($code){
			parent::__construct(NULL,$code,"ERREUR TYPE MIME");
			$this->message = "Fichier invalide";
		}
	}
?>