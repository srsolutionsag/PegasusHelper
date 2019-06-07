<?php

/**
 * helper-functions to perform the tests
 */

function init() {
    global $root_ilias, $root_plugins;
    $root_ilias = "../../../../../../../../";
    $root_plugins = $root_ilias . "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/";

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

function finalize() {
    restore_error_handler();
}

function runTests($testsList, $info, $targetInfo) {
    foreach ($testsList as $category) {
        printNormal("\n" . $category["title"] . "\n");

        foreach ($category["tests"] as $test) {
            try {
                $result = $test["fn"]($info, $targetInfo);
                printTest($test["msg"], $result);
            } catch (Exception $e) {
                printBad("\nERROR when running test '" . $test["msg"] . "'\n" .  $e->getMessage() . "\n");
            }
        }
    }
}

function writeLog($info, $targetInfo) {
    global $printLog;
    $logFile = "results.log";

    file_put_contents($logFile, "== CLI_OUTPUT ==\n\n");
    file_put_contents($logFile, $printLog, FILE_APPEND);

    file_put_contents($logFile, "\n\n== INFO ==\n\n", FILE_APPEND);
    file_put_contents($logFile, print_r($info, true), FILE_APPEND);

    file_put_contents($logFile, "\n\n== TARGET_INFO ==\n\n", FILE_APPEND);
    file_put_contents($logFile, print_r($targetInfo, true), FILE_APPEND);
}

function printTest($str, $result) {
    list($pass, $msg, $mandatory) = $result;

    if ($pass) printGood("OK   ");
    elseif ($mandatory) printBad("FAIL ");
    else printWarn("WARN ");

    printNormal($str . (isset($msg) ? " >> " . $msg : "") . "\n");
}

function printNormal($str) {
    print $str;
    addToLog($str);
}

function printGood($str) {
    global $cli_color;
    print $cli_color["green"] . $str . $cli_color["black"];
    addToLog($str);
}

function printWarn($str) {
    global $cli_color;
    print $cli_color["yellow"] . $str . $cli_color["black"];
    addToLog($str);
}

function printBad($str) {
    global $cli_color;
    print $cli_color["red"] . $str . $cli_color["black"];
    addToLog($str);
}

function addToLog($str) {
    global $printLog;
    array_push($printLog, $str);
}

function strVersionToArray($version) {
    return array_map("intval", explode(".", $version));
}

function arrayVersionToStr($version) {
    return implode(".", $version);
}

function higherStrVersion($v1, $v2) {
    return strVersionToArray($v1) > strVersionToArray($v2) ? $v1 : $v2;
}

function lowerStrVersion($v1, $v2) {
    return strVersionToArray($v1) < strVersionToArray($v2) ? $v1 : $v2;
}

function failTestFromMissingInfo($msg = NULL) {
    return [false, "lacking information to perform test" . ($msg ? ": " . $msg : ""), false];
}