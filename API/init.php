<?php

use Autoload\Loader;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset: UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Autoload/Loader.php";

Loader::init(__DIR__ . "/../");

include_once __DIR__ . "/../config.php";

// set error and exceptions catcher

function errorHandler($level, $message, $file, $line)
{
    throw new ErrorException($message, 0, $level, $file, $line);
}

function exceptionHandler($exception)
{
    if (SHOW_ERROR_DETAIL) {
        $error =  "<h1>An error occurred</h1>";
        $error .= "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
        $error .= "<p>'" . $exception->getMessage() . "'</p>";
        $error .= "<p>In file: '" . $exception->getFile() . "' on line <b>" . $exception->getLine() . "</b></p>";
        $fileContents = file($exception->getFile());
        $line = $exception->getLine();
        $temp = $line . "|<b class='text-danger'> ></b>" . $fileContents[$line - 1];
        $rand = rand(0, 2);
        for ($i = $line; $i < $line + $rand; $i++) {
            $temp .= $i + 1 . "| " . $fileContents[$i];
        }
        $error .= $temp;
    } else {
        $error = "<h1>An error occurred</h1>";
        $error .= "<p>Please try again later.</p>";
    }
    http_response_code(503);
    echo json_encode($error);
    exit();
}


function loadInput()
{
    $data = json_decode(file_get_contents("php://input"), true);
    return $data;
}

set_error_handler('errorHandler');
set_exception_handler('exceptionHandler');
