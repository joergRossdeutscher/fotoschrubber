<?php

/**
 * Class sqliteDb
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
 *
 * @link      http://www.zeichenwege.de
 * @copyright (C) Joerg Rossdeutscher 2014
 * @author    Joerg Rossdeutscher <joerg.rossdeutscher _AT_ zeichenwege.de>
 */
class sqliteDb extends SQLite3
{
    /**
     * @var
     */
    protected $dbFile;

    /**
     *
     */
    function __construct($dbFile)
    {
        $this->dbFile = $dbFile;
        $this->openDb();
    }

    /**
     * @param string $dbFile
     */
    function openDb($dbFile = "")
    {
        $dbFile = $dbFile ? $dbFile : $this->dbFile;
        $this->open($dbFile);
        return $this;
    }

    function closeDb()
    {
        $this->close();
    }

}
