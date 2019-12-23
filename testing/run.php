<?php

/**
 * performs tests for troubleshooting the installation of the REST- and PegasusHelper-plugins
 * run the script as "php run.php" from the commandline
 *
 * @author Marc Schneiter <msc@studer-raimann.ch>
 */

include_once "includefile.php";

// setup

init();

printNormal("Diagnostics for REST- and PegasusHelper-plugins\n");
printNormal("===============================================\n");

printNormal("do you want to run external tests [y/n]? ");
$handle = fopen("php://stdin","r");
$line = fgets($handle);
addToLog($line);
$line = trim($line);
$external = strlen($line) ? $line[0] === "y" : false;
printNormal("will " . ($external ? "" : "not ") . "run external tests\n");

// collect info

printNormal("\n> Gathering information for tests...\n");

$info = getInfo();
$targetInfo = getTargetInfo($info);

// run tests

printNormal("\n> Running tests...\n");

$suite = getInternalTestSuite(TestingContext::C_CLI);
$suite->run($info, $targetInfo);
printResults($suite);

if($external) {
    $suite = getExternalTestsSuite(TestingContext::C_CLI);
    $suite->run($info, $targetInfo);
    printResults($suite);
}

// write log

if($info["TestScript"]["correct_working_directory"]) {
    printNormal("\n> Write log-file 'results.log' in 'PegasusHelper/testing/' ...\n");
    writeLog($info, $targetInfo);
}

// finalize

finalize();