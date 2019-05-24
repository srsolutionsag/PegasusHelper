<?php

function init() {
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

function printCheck($str, $result) {
    list($pass, $info, $mandatory) = $result;
    ($pass) ? printGood("OK   ") : (($mandatory) ? printBad("FAIL ") : printWarn("WARN "));
    print $str;
    if(isset($info)) print "     info: " . $info;
}

function strVersionToArray($version) {
    return array_map("intval", explode(".", $version));
}

function arrayVersionToStr($version) {
    return implode(".", $version);
}