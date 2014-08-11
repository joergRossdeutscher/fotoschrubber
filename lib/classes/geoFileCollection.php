<?php

/**
 * Class geoFileCollection
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
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



