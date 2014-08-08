<?php

/**
 * Class commandline
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
