<?php

class commandline {

	var $maxLengthOfCommand=0;

	function __construct() {
		if(get_parent_class()) parent::__construct();
		$this->setMaxLengthOfCommand();
	}

	function setMaxLengthOfCommand() {
		$length	= shell_exec( 'getconf ARG_MAX' );
		$length	=preg_replace('/\D/uis' , "", $length);
		if( ! $length ) 	die("Cannot find out max length of command line.\n");
		$this->maxLengthOfCommand = $length;
	}

	function shellExecute( $cmd ) {
		if( ! is_array($cmd) ) $cmd[0] = $cmd;
		$ret = array();
		foreach( $cmd as $c ) {
			$ret[] = shell_exec( $c );
		}
		return $ret;
	}
}
