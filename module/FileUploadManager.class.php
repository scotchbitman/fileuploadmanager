<?php
	/**	
	 *	Classe FileUploadManager
	 *	@author Alexandre BLIEUX
	 *	@version 0.1
	 */
	class FileUploadManager{
		
		protected $config = array(), $upload = array();
		
		// CONSTRUCTEUR
		public function __construct($config){
			$this->config["extensions"] = $config['extensions'];
			$this->config["mime_types"] = self::selectMimeTypes(
				$config['extensions']
			);
			$this->config["target_path"] = $config['target_path'];
		}
		
		/**
		 *	Méthode renvoyant le tableau contenant les infos sur
		 *	les fichiers téléchargés. Après invocation de la méthode
		 *	record(), chaque tableau de fichier contenus dans ce tableau
		 *	comportent deux champs supplémentaires générés lors de cette
		 *	étape : "url", "extension"
		 */
		public function getUpload(){return $this->upload;}
		
		/**
		 *	Méthode de chargement des types mime en fonction des formats
		 *	tolérés pour l'upload.
		 */
		private static function loadMimeTypes(){
			return json_decode(
				file_get_contents('module/config/mime_types.json'), true
			);
		}
		
		/**
		 *	Méthode de sélection des types mimes tolérés.
		 */
		private static function selectMimeTypes($extensions){
			$all = self::loadMimeTypes();
			$selected = array();
			foreach($extensions as $ext) $selected[] = $all[$ext];
			return $selected;
		}
		
		/**
		 *	Méthode générale de vérification des fichiers transférés.
		 *	Elle est chargée de lancer les différents contrôles à tour
		 *	de rôle.
		 */
		public function checkTransferredFiles(){
			self::normalize(); 
			self::checkTransfer();
			self::checkExtensions();
		}
		
		/**
		 *	Méthode permettant de récupérer un tableau de fichiers
		 *	uniforme uploadés quelquesoit le cas :
		 *	--> Upload d'un fichier unique à partir d'un champ file unique.
		 *	--> Upload de plusieurs fichiers à partir d'un champ file unique.
		 *	--> Upload d'un fichier unique à partir de plusieurs champs file 
		 *		(un fichier par champ).
		 *	--> Upload de plusieurs fichiers à partir de plusieurs champs file 
		 *		(plusieurs fichiers par champ).
		 */
		private function normalize(){
			foreach($_FILES as $key=>$val){
				if(self::hasMultipleFiles($_FILES[$key])){
					for($i = 0; $i < count($_FILES[$key]['name']); $i++){
						$this->upload[] = array(
							'name' => $_FILES[$key]['name'][$i],
							'type' => $_FILES[$key]['type'][$i],
							'tmp_name' => $_FILES[$key]['tmp_name'][$i],
							'error' => $_FILES[$key]['error'][$i],
							'size' => $_FILES[$key]['size'][$i]
						);
					}
				}else{
					$this->upload[] = $_FILES[$key];
				}
			}
		}
		
		/**
		 *	Méthode permettant de lever une exception si un fichier
		 *	a subi une erreur de transfert. --> blocage de tout l'upload.
		 */
		private function checkTransfer(){
			foreach($this->upload as $file){
				if($file['error'] != UPLOAD_ERR_OK) 
				throw new FileUploadTransferErrorException($file['error']);
			}
		}
		
		/**
		 *	Méthode permettant de retourner un tableau contenant
		 *	les types mime des fichiers uploadés.
		 */
		private static function getMimeTypes($files){
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			foreach($files as $file)
			$types_mime[] = finfo_file($finfo, $file['tmp_name']);
			finfo_close($finfo);
			return $types_mime;
		}
		
		/**
		 *	Méthode permettant de contrôler si les types mime
		 *	des fichiers uploadés sont acceptés. Ceci est en rapport
		 *	étroit avec les extensions tolérées.
		 */
		private function checkMimeType(){
			$types_mime = self::getMimeTypes($this->upload);
			foreach($types_mime as $ind=>$type_mime){
				if(!in_array($type_mime,$this->config['mime_types']))
				throw new FileUploadMimeTypeErrorException(1);
			}
		}
		
		/**
		 *	Permet de récupérer les extensions des fichiers téléchargés.
		 */
		private static function getExtensions($files){
			foreach($files as $file)
			$file_extensions[] = trim(strrchr($file['name'], '.'),'.');
			return $file_extensions;
		}
		
		/**
		 *	Méthode permettant de contrôler si les extensions des fichiers
		 *	téléchargés sont autorisées.
		 */
		private function checkExtensions(){
			$extensions	= self::getExtensions($this->upload);
			foreach($extensions as $extension){
				if(!in_array(
					strtolower($extension), 
					$this->config['extensions']
				))
				throw new FileUploadExtensionErrorException(1);
			}
		}
		
		/**
		 *	Méthode permettant de vérifier si un champ input file 
		 *	contient de multiples fichiers.
		 */
		private static function hasMultipleFiles($field){
			if(is_array($field['name'])) return true;
			return false;
		}
		
		/**
		 *	Méthode permettant de créer l'arborescence contenant
		 *	les fichiers téléchargés.
		 */
		private static function createFolderTree($path){
			if (!is_dir($path)) mkdir($path, 0777, true);
		}
		
		/**
		 *	Méthode servant à générer un nom aléatoire pour le fichier.
		 */
		private static function renameFile($ext){
			return date('Ymd-H-i-s-').md5(uniqid(rand(), true)).'.'.$ext;
		}
		
		/**
		 *	Méthode permettant de lancer l'enregistrement.
		 *	--> Création de l'arborescence cible
		 *	--> On renomme ensuite les fichiers de manière aléatoire.
		 *	--> On lance l'enregistrement.
		 */
		public function record(){
			
			$ext = self::getExtensions($this->upload);
			$path = $this->config['target_path'];
			self::createFolderTree($path);
			
			for($i = 0; $i < count($this->upload); $i++){
				
				$rel_link = $path.self::renameFile($ext[$i]);
				$this->upload[$i]['url'] = $rel_link;
				$this->upload[$i]['extension'] = $ext[$i];
				
				@move_uploaded_file(
					$this->upload[$i]['tmp_name'],
					$rel_link
				);
			}
		}
	}
?>