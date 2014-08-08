<?php

/**
 * Class geoFileCollection
 *
 * This Software is the property of Joerg Rossdeutscher and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.zeichenwege.de
 * @copyright (C) Joerg Rossdeutscher 2014
 * @author    Joerg Rossdeutscher <joerg.rossdeutscher _AT_ zeichenwege.de>
 */
class geoFileCollection extends fileCollection
{

    /**
     * @param imgFileCollection $imgFiles
     */
    function applyGeoCollectionToImgCollection(imgFileCollection $imgFiles)
    {
        foreach ($this->file as $geoFile) {
            $geoFile->applyGeoToImgCollection($imgFiles);
        }
    }
}

