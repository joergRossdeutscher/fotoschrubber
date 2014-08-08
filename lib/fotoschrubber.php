<?php


spl_autoload_register(
/**
 * @param $class
 */

    function ($class) {
        require_once __DIR__ . "/classes/{$class}.php";
    }
);


/**
 * @param $date
 * @return string
 */
function dayFromDate($date)
{
    if ($date > 0) {
        $parseDate = date_parse($date);
        $parseDate['month'] = substr('00' . $parseDate['month'], -2);
        $parseDate['day'] = substr('00' . $parseDate['day'], -2);
        return ($parseDate['year'] . '/' . $parseDate['month'] . '/' . $parseDate['day']);
    }

    return false;
}


/**
 * @param string $filename
 * @param string $delimiter
 * @return array|bool
 */
function csvToArray($filename = '', $delimiter = ',')
{
    /**
     * Convert a comma separated file into an associated array.
     * The first row should contain the array keys.
     *
     * Example:
     *
     * @param string $filename Path to the CSV file
     * @param string $delimiter The separator used in the file
     * @return array
     * @link http://gist.github.com/385876
     * @author Jay Williams <http://myd3.com/>
     * @copyright Copyright (c) 2010, Jay Williams
     * @license http://www.opensource.org/licenses/mit-license.php MIT License
     */
    if (!file_exists($filename) || !is_readable($filename)) {
        return false;
    }
    $header = null;
    $data = array();
    if (($handle = fopen($filename, 'r')) !== false) {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            if (!$header) {
                $header = $row;
            } else {
                $data[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    }
    return $data;
}
