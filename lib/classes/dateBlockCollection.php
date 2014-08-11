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
class dateBlockCollection
{

    var $dateBlock = array();

    /**
     * @param $days
     */
    function setDateBlocksFromDayList($days)
    {
        $oneDay = 60 * 60 * 24; // Length of a day in seconds

        $dayList = array_fill_keys($days, 1);

        # Add day before and day after to list.
        # That saves us from dealing with timezones.
        foreach (array_keys($dayList) as $day) {
            $dayList[dayFromDate(date("Y/m/d", strtotime($day) - $oneDay))] = 1;
            $dayList[dayFromDate(date("Y/m/d", strtotime($day) + $oneDay))] = 1;
        }

        $dayList = array_keys($dayList);
        asort($dayList);
        $dayList = array_values($dayList);

        $tmp = Array();
        foreach ($dayList as $day) {
            $tmp[$day] = 1;
        }
        $dayList = $tmp;
        unset($tmp);


        # Combine days to blocks
        foreach (array_keys($dayList) as $day) {
            if (isset($dayList[$day])) {

                $changes = true;
                for ($daysInFuture = 1; $changes; $daysInFuture++) {
                    $nextDay = dayFromDate(date("Y/m/d", strtotime($day) + $daysInFuture * $oneDay));
                    if (isset($dayList[$nextDay])) {
                        unset($dayList[$nextDay]);
                        $dayList[$day]++;
                        $changes = true;
                    } else {
                        $changes = false;
                    }
                }
            }
        }
        $this->dateBlock = $dayList;
    }

    /**
     * @param imgFileCollection $imgFile
     */
    function tagImagesDateblockwise(imgFileCollection $imgFileList)
    {
        foreach ($this->dateBlock as $startDate => $days) {
            $imageList = $this->getImagesInDateblock($imgFileList, $startDate, $days);

            $googleKml = new geoFile;
            $googleKml->downloadGoogleGeoFile($startDate, $days);
            $googleKml->applyGeoToImgCollection($imageList);

        }
    }

    /**
     * @param imgFileCollection $imgFileList
     * @param $startDate
     * @param $days
     * @return array|imgFileCollection
     */
    function getImagesInDateblock(imgFileCollection $imgFileList, $startDate, $days)
    {
        $retImgFile = new imgFileCollection;
        foreach ($imgFileList->file as $imgFile) {
            if ($this->imgLiesWithinDateblock($imgFile, $startDate, $days)) {
                $retImgFile->file[] = $imgFile;
            }
        }
        return $retImgFile;
    }

    /**
     * @param imgFile $imgFile
     * @param string $startDate
     * @param int $days
     */
    function imgLiesWithinDateblock(imgFile $imgFile, $startDate, $days)
    {
        $found = false;
        foreach ($imgFile->GpsTimeStamps as $fileDay) {
            for ($day = 0; $day <= $days; $day++) {
                $thisDay = date("Y/m/d", strtotime("$startDate + {$day} day"));
                if ($thisDay == $fileDay) {
                    $found = true;
                    continue 2;
                }
            }
        }
        return $found;
    }
}