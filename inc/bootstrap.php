<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', 'On');
setlocale(LC_MONETARY, 'de_AT');

/* very simple class autoload */
spl_autoload_register(function ($class) {
    $filename = __DIR__ . '/../lib/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    if (file_exists($filename))
        include($filename);
});

/** DataManager Injection **/
$mode = 'pdo';
switch (mb_strtolower($mode)) {
    case 'pdo' : 
        $class = 'PDO';
        break;
    default: 
        $class = 'mock';
        break;
}

require_once(__DIR__ . '/../lib/Data/DataManager_' . $class . '.php');