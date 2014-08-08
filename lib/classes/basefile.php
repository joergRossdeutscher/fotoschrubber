<?php

/**
 * Class basefile
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
class basefile
{
    /**
     * @var string
     */
    var $folder = "";
    /**
     * @var string
     */
    var $filename = "";

    /**
     * @param string $differentName
     * @return string
     */
    function absoluteName($differentName = "")
    {
        if (strlen($differentName) == 0) {
            $differentName = $this->filename;
        }
        return $this->folder . '/' . $differentName;
    }
}

