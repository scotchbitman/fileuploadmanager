<?php
/**
 *	Classe de gestion des erreurs d'upload
 *
 *	@author Alexandre Blieux
 *	@since 14/02/2014
 *	@version 0.1
 */
class FileUploadTransferErrorException extends FileUploadErrorException{

	/**
	 *
	 */
	public function __construct($code){
		parent::__construct(NULL,$code,'ERREUR TRANSFERT');
		self::getReason();
	}
	
	/**
	 *
	 */
	private function getReason(){
		switch(self::getCode()){
		case 1: // idem case 2
		case 2: $msg = 'La taille du fichier est trop importante';break;
		case 3: $msg = 'Le fichier n\'a été que partiellement téléchargé';break;
		case 4: $msg = 'Aucun fichier sélectionné';break;
		case 6: $msg = 'Un dossier temporaire est manquant';break;
		case 7: $msg = 'Echec de l\'écriture du fichier sur le disque';break;
		case 8: $msg = 'L\'envoi du fichier a été arrêté';break;
		}
		
		$this->message = $msg;
	}
}
?>