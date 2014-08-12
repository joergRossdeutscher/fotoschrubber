<?php

/**
 * Class Preferences
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
 *
 * @link      http://www.zeichenwege.de
 * @copyright (C) Joerg Rossdeutscher 2014
 * @author    Joerg Rossdeutscher <joerg.rossdeutscher _AT_ zeichenwege.de>
 */
class Preferences
{
    public $userDirHome;
    public $userDirApplication;
    public $userDirCaches;
    public $userDirCachesGoogle;
    public $userDirPreferences;
    public $googleCookie;

    /**
     *
     */
    function __construct()
    {
        $this->userDirHome = $this->getHomeDir();
        $this->userDirApplication = $this->userDirHome . "/Library/Application Support/Fotoschrubber";
        $this->userDirCaches = $this->userDirHome . "/Library/Caches/Fotoschrubber";
        $this->userDirCachesGoogle = $this->userDirCaches . "/Google";
        $this->userDirPreferences = $this->userDirHome . "/Library/Preferences/fotoschrubber";
        $this->googleCookie = $this->userDirPreferences . "/cookie.txt";

        $this->userDirForceExist($this->userDirApplication);
        $this->userDirForceExist($this->userDirCaches);
        $this->userDirForceExist($this->userDirCachesGoogle);
        $this->userDirForceExist($this->userDirPreferences);
    }


    /**
     * @return string
     */
    function getHomeDir()
    {
        $home = @getenv("HOME");
        if ($home != "" && is_dir($home)) {
            return $home;
        }

        $home = @$_ENV['HOME'];
        if ($home != "" && is_dir($home)) {
            return $home;
        }

        $home = @$_SERVER['HOME'];
        if ($home != "" && is_dir($home)) {
            return $home;
        }

        $home = realpath('~');
        if ($home != "" && is_dir($home)) {
            return $home;
        }

        die('Cannot find homedir');
    }

    /**
     * @param $dir
     */
    function userDirForceExist($dir)
    {
        $walk = '';
        foreach (explode('/', $dir) as $part) {
            $walk .= "{$part}/";
            if ($walk != '/') {
                if (!is_dir($walk)) {
                    if (mkdir($walk, 0700) !== true) {
                        die("Cannot create dir {$walk}\n");
                    }
                }
            }
        }
    }

    /**
     * @param $file
     */
    function editGoogleCookie($file = "")
    {
        if ($file == "") {
            $file = $this->googleCookie;
        }
        if(! file_exists($file)) {
            touch($file);
        }
        `open -e '$file'`;
    }
}
