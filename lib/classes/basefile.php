<?php

/**
 * Class basefile
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
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

