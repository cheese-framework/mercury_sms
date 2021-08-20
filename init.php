<?php

/** @noinspection PhpUnhandledExceptionInspection */

use App\Core\Helper;
use Autoload\Loader;

ob_start();
session_start();
session_regenerate_id(TRUE);

require_once __DIR__ . "/Autoload/Loader.php";
require_once __DIR__ . '/vendor/autoload.php';

Loader::init(__DIR__);

require_once 'config.php';

// set error and exceptions catcher

function errorHandler($level, $message, $file, $line)
{
    throw new ErrorException($message, 0, $level, $file, $line);
}

function exceptionHandler($exception)
{
    if (SHOW_ERROR_DETAIL) {
        echo "<h1>An error occurred</h1>";
        echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
        echo "<p>'" . $exception->getMessage() . "'</p>";
        echo "<p>In file: '" . $exception->getFile() . "' on line <b>" . $exception->getLine() . "</b></p>";
        $fileContents = file($exception->getFile());
        $line = $exception->getLine();
        $temp = $line . "|<b class='text-danger'> ></b>" . $fileContents[$line - 1];
        $rand = rand(0, 2);
        for ($i = $line; $i < $line + $rand; $i++) {
            $temp .= $i + 1 . "| " . $fileContents[$i];
        }
        Helper::prettify($temp);
    } else {
        echo "<h1>An error occurred</h1>";
        echo "<p>Please try again later.</p>";
    }
    exit();
}

set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
