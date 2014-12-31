<?php
	if(!empty($_FILES)){
	
		// A VOIR POUR L'IMPORT DES EXCEPTIONS...
		require_once 'module/except/FileUploadErrorException.class.php';
		require_once 'module/except/FileUploadExtensionErrorException.class.php';
		require_once 'module/except/FileUploadFormatErrorException.class.php';
		require_once 'module/except/FileUploadMimeTypeErrorException.class.php';
		require_once 'module/except/FileUploadTransferErrorException.class.php';
	
		require_once 'module/FileUploadManager.class.php';
		
		$up = new FileUploadManager(
			array(
				'extensions' => array('pdf','csv'),
				'target_path' => 'files/'
			)
		);
		
		try{
			$up->checkTransferredFiles();
			// $up->record();
			
			// DEBUG
			echo '<pre>';
			print_r($up->getUpload());
			echo '</pre>';
			
		}catch(FileUploadErrorException $e){
			echo $e->getMessage();
		}
	}
	
	require 'views/layout.php';
?>