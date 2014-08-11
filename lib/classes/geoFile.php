<?php

/**
 * Class geoFile
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
 *
 * @link      http://www.zeichenwege.de
 * @copyright (C) Joerg Rossdeutscher 2014
 * @author    Joerg Rossdeutscher <joerg.rossdeutscher _AT_ zeichenwege.de>
 */
class geoFile extends basefile
{

    /**
     * @param imgFileCollection $imgFiles
     */
    function applyGeoToImgCollection(imgFileCollection $imgFiles)
    {

        $shell = new commandline;
        $maxLengthOfCommand = $shell->maxLengthOfCommand;

        $commandline = array();
        $line = 0;
        $commandExiftool = 'exiftool ' .
            '-overwrite_original ' .
            '-preserve ' .
            '-api GeoMaxExtSecs=4000 ' .
            '-geotag ' . escapeshellarg($this->absoluteName()) .
            ' ';
        foreach ($imgFiles->file as $fileList) {
            $file = $fileList->absoluteName();

            if (!isset($commandline[$line])) {
                $commandline[$line] = "";
            }

            $imgFileName = escapeshellarg($file) . ' ';

            # 16 , because I am careful.
            if (mb_strlen($commandline[$line]) + mb_strlen($commandExiftool) + mb_strlen(
                    $imgFileName
                ) >= ($maxLengthOfCommand - 16)
            ) {
                $line++;
            }

            $commandline[$line] .= $imgFileName;
        }

        for ($i = 0; $i <= $line; $i++) {
            $commandline[$i] = $commandExiftool . $commandline[$i];
        }

        $shell->shellExecute($commandline);

    }

    /**
     * @param $startDay
     * @param $duration
     */
    function downloadGoogleGeoFile($startDay, $duration)
    {

        $folder = $GLOBALS['preferences']->userDirCachesGoogle;
        $filename = $folder . '/' . preg_replace('/[^0-9_\-]/uis', '_', "{$startDay}-{$duration}") . '.kml';

        $cookieFile = $GLOBALS['preferences']->userDirPreferences . '/cookie.txt';

        if (!file_exists($filename)) {
            $oneDay = 60 * 60 * 24; // Length of a day in seconds

            $startTime = strtotime($startDay);
            $endTime = $startTime + $oneDay * ($duration + 1);

            if ($startTime > time()) {
                $startTime = time();
            }
            if ($endTime > time()) {
                $endTime = time();
            }

            $locationURL = "https://maps.google.com/locationhistory/b/0/kml?" .
                "startTime=" . $startTime . "000&".
                "endTime=" . $endTime . "000";

            $cmd = "curl " .
                "-s " .
                "-b '{$cookieFile}'' " .
                "-o '{$filename}'' " .
                "'{$locationURL}'";

            `$cmd`;
        }

        $this->folder = $folder;
        $this->filename = basename($filename);
    }
}
