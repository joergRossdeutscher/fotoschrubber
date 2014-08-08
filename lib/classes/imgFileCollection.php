<?php

/**
 * Class imgFileCollection
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
                $file['GPSLatitude'] == '' &&
                $file['GPSLongitude'] == ''
            ) {
                $file['GPSDateTimeStamp'] = $file['GPSDateStamp'] . ' ' . $file['GPSTimeStamp'];
                unset($file['GPSDateStamp']);
                unset($file['GPSTimeStamp']);

                $tmp = new $makeType;
                foreach (array('GPSDateTimeStamp', 'CreateDate', 'DateTimeOriginal', 'GPSDateTime') as $field) {
                    $day = dayFromDate($file[$field]);
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
    function getListofGpsDates() {
        $date = array();

        foreach( $this->file as $file) {
            $date = array_merge($date , $file->GpsTimeStamps);
        }

        $date = array_unique($date);
        return $date;
    }
}
