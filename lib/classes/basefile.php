<?php

class basefile {
	var $folder	= "";
	var $filename = "";

	function absoluteName( $differentName="" ) {
		if( strlen($differentName)==0 )	$differentName = $this->filename;
		return $this->folder . '/' . $differentName;
	}
}

