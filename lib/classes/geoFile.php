<?php

class geoFile extends basefile {

	function applyGeoToImgCollection( imgFileCollection $imgFiles ) {

		$shell = new commandline;
		$maxLengthOfCommand = $shell->maxLengthOfCommand;

		$commandline = array();
		$line = 0;
		$commandExiftool =	'exiftool ' .
		                    '-overwrite_original ' .
		                    '-preserve ' .
		                    '-api GeoMaxExtSecs=4000 ' .
							'-geotag ' . escapeshellarg( $this->absoluteName() ) .
							' ';
		foreach( $imgFiles->file as $fileList ) {
			$file = $fileList->absoluteName();

			if( ! isset($commandline[$line]) ) {
				$commandline[$line] = "";
			}

			$imgFileName = escapeshellarg($file) . ' ';

			# 16 , because I am careful.
			if( mb_strlen($commandline[$line]) + mb_strlen($commandExiftool) + mb_strlen($imgFileName) >= ($maxLengthOfCommand - 16 ) ) {
				$line++;
			}

			$commandline[$line] .= $imgFileName;
		}

		for( $i=0 ; $i<=$line ; $i++ ) {
			$commandline[$i] = $commandExiftool . $commandline[$i];
		}

		$shell->shellExecute( $commandline );

	}

}
