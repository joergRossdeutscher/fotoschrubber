<?php

/**
 * Class imgFileCollection
 *
 * This Software is the property of Joerg Rossdeutscher and
 * licensed under GPL v3. See http://www.gnu.org/licenses/gpl-3.0
 *
 *
 * @link      http://www.zeichenwege.de
 * @copyright (C) Joerg Rossdeutscher 2014
 * @author    Joerg Rossdeutscher <joerg.rossdeutscher _AT_ zeichenwege.de>
 */
class imgFileCollection extends fileCollection
{

    /**
     *
     */
    function renameFiles()
    {

        foreach ($this->file as $file) {
            if (preg_match('/^_MG/uis', $file->filename)) {
                $file->newFilename = preg_replace('/^_MG/uis', 'IMG', $file->filename);
            }
        }

        foreach ($this->file as $file) {
            if ($file->newFilename) {
                if (file_exists($file->absoluteName($file->newFilename))) {
                    die("Fatal error!\n'" . $file->newFilename . "' already exists!\n");
                }

                rename(
                    $file->absoluteName(),
                    $file->absoluteName($file->newFilename)
                );

                $file->filename = $file->newFilename;
            }

            unset($file->newFilename);

        }
    }

    /**
     * @param $makeType
     * @param $folder
     */
    function getFilesWithoutGeotags($makeType, $folder)
    {
        $csvTempFile = tempnam(sys_get_temp_dir(), 'fotoschrubber');

        $cmd =
            "exiftool " .
            "-n " . # Exact coordinates instead rounded degrees
            # Output fields:
            "-gpslatitude -gpslongitude -FileModificationDateTime -GPSTimeStamp " .
            "-GPSDateStamp -CreateDate -DateTimeOriginal -GPSDateTime " .
            # Format:
            "-csv " .
            # Recursive
            "-r " .
            # Shut up
            "-quiet " .

            # Folder with images
            "{$folder} " .

            "> $csvTempFile";

        `$cmd`;
        $fileList = csvToArray($csvTempFile);

        if (file_exists($csvTempFile)) {
            unlink($csvTempFile) || die("Cannot delete tempfile.\n");
        }

        foreach ($fileList as $file) {
            if (
            @$file['GPSLatitude'] == '' &&
                @$file['GPSLongitude'] == ''
            ) {
                /*
                                if (
                                   @$file['GPSDateStamp'] == '' &&
                                    @$file['GPSTimeStamp'] == ''
                                ) {
                                    $file['GPSDateTimeStamp'] = $file['GPSDateStamp'] . ' ' . $file['GPSTimeStamp'];
                                    unset($file['GPSDateStamp']);
                                    unset($file['GPSTimeStamp']);
                                }
                 */
                $tmp = new $makeType;
#                foreach (array('GPSDateTimeStamp', 'CreateDate', 'DateTimeOriginal', 'GPSDateTime') as $field) {
                foreach (array('CreateDate', 'DateTimeOriginal') as $field) {
                    $day = @dayFromDate($file[$field]);
                    if ($day) {
                        $tmp->GpsTimeStamps[] = $day;
                    }
                }
                if (count($tmp->GpsTimeStamps)) {
                    $tmp->GpsTimeStamps = array_unique($tmp->GpsTimeStamps);
                    $tmp->folder = dirname($file['SourceFile']);
                    $tmp->filename = basename($file['SourceFile']);
                    $this->file[] = $tmp;
                }
                unset($tmp);

            }
        }
    }

    /**
     *
     */
    function getListofGpsDates()
    {
        $date = array();

        foreach ($this->file as $file) {
            $date = array_merge($date, $file->GpsTimeStamps);
        }

        $date = array_unique($date);
        return $date;
    }

    function applyPlainCoordinatesToImgCollection($coordinate)
    {

        $this->executeExiftoolShellCommand(
             'exiftool ' .
             '-overwrite_original ' .
             '-preserve ' .
             '-exif:gpslatitude=' . $coordinate->latitude . ' ' .
             '-exif:gpslongitude=' . $coordinate->longitude . ' '
        );

    }

    /**
     * @param $commandExiftool
     * @param imgFileCollection $imgFiles
     */
    function executeExiftoolShellCommand($commandExiftool)
    {

        $shell = new commandline;
        $maxLengthOfCommand = $shell->maxLengthOfCommand;

        $commandline = array();
        $line = 0;

        foreach ($this->file as $fileList) {
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
            $commandline[$i] = $commandExiftool . ' ' . $commandline[$i];
        }

        $shell->shellExecute($commandline);

    }

}
