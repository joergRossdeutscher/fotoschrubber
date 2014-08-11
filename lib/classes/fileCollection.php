<?php

/**
 * Class fileCollection
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
 *
 * @link      http://www.zeichenwege.de
 * @copyright (C) Joerg Rossdeutscher 2014
 * @author    Joerg Rossdeutscher <joerg.rossdeutscher _AT_ zeichenwege.de>
 */
class fileCollection
{

    /**
     * @var array
     */
    var $file = array();

    /**
     * @param $makeType
     * @param $folder
     * @param $regexp
     * @return $this
     */
    function getFilesInFolder($makeType, $folder, $regexp)
    {
        $filenames = scandir($folder);
        foreach ($filenames as $filename) {
            if (
                preg_match($regexp, $filename) &&
                is_file("$folder/$filename")
            ) {
                $tmp = new $makeType;
                $tmp->folder = $folder;
                $tmp->filename = $filename;
                $this->file[] = $tmp;
                unset($tmp);
            }
        }
        return $this;
    }

}
