<?php



spl_autoload_register(
/**
 * @param $class
 */

    function ($class) {
        $classFile = str_replace('\\', '/', $class);
        require_once __DIR__ . "/classes/{$classFile}.php";
    }
);

require_once(__DIR__ . '/global_functions.php');

$preferences = new Preferences;
