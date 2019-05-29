<?php

/**
 * helper-functions to perform the tests
 */

function init() {
    global $root_ilias, $root_plugins;
    $root_ilias = "../../../../../../../../";
    $root_plugins = $root_ilias . "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/";

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

function runTests($testsList, $info) {
    foreach ($testsList as $category) {
        print "\n" . $category["title"] . "\n";

        foreach ($category["tests"] as $test) {
            try {
                $result = $test["fn"]($info);
                printTest($test["msg"], $result);
            } catch (Exception $e) {
                printBad("\nERROR when running test '" . $test["msg"] . "'\n" .  $e->getMessage() . "\n");
            }
        }
    }
}

function printTest($str, $result) {
    list($pass, $msg, $mandatory) = $result;

    if ($pass) printGood("OK   ");
    elseif ($mandatory) printBad("FAIL ");
    else printWarn("WARN ");

    print $str . (isset($msg) ? " >> " . $msg : "") . "\n";
}

function printGood($str) {
    global $cli_color;
    print $cli_color["green"] . $str . $cli_color["black"];
}

function printWarn($str) {
    global $cli_color;
    print $cli_color["yellow"] . $str . $cli_color["black"];
}

function printBad($str) {
    global $cli_color;
    print $cli_color["red"] . $str . $cli_color["black"];
}

function strVersionToArray($version) {
    return array_map("intval", explode(".", $version));
}

function arrayVersionToStr($version) {
    return implode(".", $version);
}