<?php

class fileCollection {

	var $file	= array();

	function getFilesInFolder( $makeType , $folder , $regexp ) {
		$filenames = scandir($folder);
		foreach( $filenames as $filename ) {
			if(
				preg_match($regexp , $filename) &&
				is_file( "$folder/$filename" )
			) {
				$tmp	= new $makeType;
				$tmp->folder	= $folder;
				$tmp->filename	= $filename;
				$this->file[]	= $tmp;
				unset( $tmp );
			}
		}
		return $this;
	}
}
