<?php

class imgFileCollection extends fileCollection {

	function renameFiles() {

		foreach ($this->file as $file ) {
			if( preg_match( '/^_MG/uis' , $file->filename ) ) {
				$file->newFilename	= preg_replace('/^_MG/uis' , 'IMG' , $file->filename );
			}
		}

		foreach ($this->file as $file ) {
			if( $file->newFilename ) {
				if( file_exists( $file->absoluteName($file->newFilename) ) ) {
					die("Fatal error!\n'" . $file->newFilename . "' already exists!\n");
				}

				rename(
					$file->absoluteName() ,
					$file->absoluteName($file->newFilename)
				);
			
				$file->filename	= $file->newFilename;
			}
			
			unset( $file->newFilename );

		}
	}
}
