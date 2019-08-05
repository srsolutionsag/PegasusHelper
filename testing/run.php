<?php

/**
 * performs tests for troubleshooting the installation of the REST- and PegasusHelper-plugins
 * run the script as "php run.php" from the commandline
 *
 * @author Marc Schneiter <msc@studer-raimann.ch>
 */

include_once "includefile.php";

init();

printNormal("Diagnostics for REST- and PegasusHelper-plugins\n");
printNormal("===============================================\n");
printNormal("\n> Gathering information for tests...\n");

$info = getInfo();
$targetInfo = getTargetInfo($info);

printNormal("\n> Running tests...\n");

$suite = getTestSuite(TestingContext::C_CLI);
$suite->run($info, $targetInfo);
printResults($suite);

if($info["TestScript"]["correct_working_directory"]) {
    printNormal("\n> Write log-file 'results.log' in 'PegasusHelper/testing/' ...\n");
    writeLog($info, $targetInfo);
}

finalize();