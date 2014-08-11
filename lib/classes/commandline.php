<?php

/**
 * Class commandline
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
 *
 * @link      http://www.zeichenwege.de
 * @copyright (C) Joerg Rossdeutscher 2014
 * @author    Joerg Rossdeutscher <joerg.rossdeutscher _AT_ zeichenwege.de>
 */
class commandline
{

    /**
     * @var int
     */
    var $maxLengthOfCommand = 0;

    /**
     *
     */
    function __construct()
    {
        if (get_parent_class()) {
            parent::__construct();
        }
        $this->setMaxLengthOfCommand();
    }

    /**
     *
     */
    function setMaxLengthOfCommand()
    {
        $length = shell_exec('getconf ARG_MAX');
        $length = preg_replace('/\D/uis', "", $length);
        if (!$length) {
            die("Cannot find out max length of command line.\n");
        }
        $this->maxLengthOfCommand = $length;
    }

    /**
     * @param $cmd
     * @return array
     */
    function shellExecute($cmd)
    {
        if (!is_array($cmd)) {
            $cmd[0] = $cmd;
        }
        $ret = array();
        foreach ($cmd as $c) {
            $ret[] = shell_exec($c);
        }
        return $ret;
    }
}
