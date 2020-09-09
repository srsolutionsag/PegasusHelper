<?php

/**
 * helper-functions to perform the tests in the cli
 */

function init()
{
    global $printLog;
    $printLog = [];

    global $cli_color;
    $cli_color["black"] = "\033[0m";
    $cli_color["red"] = "\033[1;31m";
    $cli_color["green"] = "\033[1;32m";
    $cli_color["yellow"] = "\033[1;33m";

    set_error_handler(function ($severity, $message, $file, $line) {
        throw new \ErrorException($message, $severity, $severity, $file, $line);
    });
}

function finalize()
{
    restore_error_handler();
}

/**
 * @param $suite TestSuite
 */
function printResults($suite)
{
    printNormal("\n" . $suite->title . "\n");
    $no_category = 0;
    foreach ($suite->categories as $category) {
        printNormal("\n" . $no_category . ") " . $category->title . "\n");

        foreach ($category->tests as $test) {
            printTest($test);
        }
        $no_category++;
    }
}

/**
 * @param $test Test
 */
function printTest($test)
{
    $pass = $test->result->state === ResultState::R_PASS;
    if ($pass) {
        printGood("OK   ");
    } elseif ($test->mandatory && $test->result->state === ResultState::R_FAIL) {
        printBad("FAIL ");
    } else {
        printWarn("WARN ");
    }

    $description = $test->result->description;
    printNormal($test->description . (!$pass && isset($description) ? " >> " . $description : "") . "\n");
}

function printNormal($str)
{
    print $str;
    addToLog($str);
}

function printGood($str)
{
    global $cli_color;
    print $cli_color["green"] . $str . $cli_color["black"];
    addToLog($str);
}

function printWarn($str)
{
    global $cli_color;
    print $cli_color["yellow"] . $str . $cli_color["black"];
    addToLog($str);
}

function printBad($str)
{
    global $cli_color;
    print $cli_color["red"] . $str . $cli_color["black"];
    addToLog($str);
}

function addToLog($str)
{
    global $printLog;
    if (isset($GLOBALS["ilias"]) || !isset($printLog)) {
        return;
    }
    array_push($printLog, $str);
}

function writeLog($info, $targetInfo)
{
    global $printLog;

    $logFile = "results.log";
    file_put_contents($logFile, $printLog);
    file_put_contents($logFile, "\n[INFO]\n", FILE_APPEND);
    file_put_contents($logFile, print_r($info, true), FILE_APPEND);
    file_put_contents($logFile, "\n[TARGET]\n", FILE_APPEND);
    file_put_contents($logFile, print_r($targetInfo, true), FILE_APPEND);
}
