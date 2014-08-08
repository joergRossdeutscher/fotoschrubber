<?php

class geoFileCollection extends fileCollection {

	function applyGeoCollectionToImgCollection( imgFileCollection $imgFiles ) {
		foreach( $this->file as $geoFile ) {
			$geoFile->applyGeoToImgCollection( $imgFiles );
		}
	}
}

