<?php

/**
 * tests to be performed are implemented here and must be added to $testsList in the getTestSuite-function below
 *
 * template for a test-function:
 *
 * function testNAME($info, $targetInfo, $suite) {
 *     // compute test based on $info, $targetInfo, $suite
 *     return [$pass, $msg, $mandatory, $complete];
 * }
 *
 */

/**
 * creates a collection of tests without HTTP requests
 *
 * @param $context int for TestingContext
 * @return TestSuite
 */
function getInternalTestSuite($context) {
    $suite = new TestSuite("Internal Testing", $context);

    $general = new TestCategory("General");
    $general->addTests([
        new Test("location where script is run", "testWorkingDirectory", true, [TestingContext::C_CLI]),
        new Test("location of REST-plugin", "testRESTDirectory"),
        new Test("location of PegasusHelper-plugin", "testPegasusHelperDirectory"),
        new Test("connection to ILIAS-database", "testIlBDConnection", false, [TestingContext::C_CLI]),
        new Test("compatible PHP-version", "testPhpVersion", false)
    ]);
    $suite->addCategories($general);

    $ilias = new TestCategory("ILIAS");
    $ilias->addTests([
        new Test("compatible ILIAS-version", "testILIASVersion"),
        new Test("https redirects", "testILIASRedirectStatement", false)
    ]);
    $suite->addCategories($ilias);

    $rest = new TestCategory("REST-plugin");
    $rest->addTests([
        new Test("version", "testRESTVersion", false),
        new Test("compatible ILIAS-version", "testRESTkMinMaxVersion"),
        new Test("entry in ILIAS-database", "testRESTInIlDB"),
        new Test("plugin-updates in ILIAS", "testRESTLastUpdateVersion", false),
        new Test("ILIAS-database version", "testRESTDbVersion"),
        new Test("active", "testRESTPluginActive")
    ]);
    $suite->addCategories($rest);

    $pegasusHelper = new TestCategory("PegasusHelper-plugin");
    $pegasusHelper->addTests([
        new Test("working REST-installation", "testPegasusHelperRESTInstallation"),
        new Test("version", "testPegasusHelperVersion", false),
        new Test("compatible ILIAS-version", "testPegasusHelperMinMaxVersion"),
        new Test("entry in ILIAS-database", "testPegasusHelperInIlDB"),
        new Test("plugin-updates in ILIAS", "testPegasusHelperLastUpdateVersion", false),
        new Test("ILIAS-database version", "testPegasusHelperDbVersion"),
        new Test("active", "testPegasusHelperPluginActive")
    ]);
    $suite->addCategories($pegasusHelper);

    return $suite;
}

/**
 * creates a collection of tests with HTTP requests
 *
 * @param $context int for TestingContext
 * @return TestSuite
 */
function getExternalTestsSuite($context) {
    $suite = new TestSuite("External Testing", $context);

    $pegasusHelper = new TestCategory("Accessing Resources");
    $pegasusHelper->addTests([
        new Test("external URL", "testExternalUrl", false),
        new Test("REST login", "testRESTLoginConnection", false)
    ]);
    $suite->addCategories($pegasusHelper);

    $pegasusHelper = new TestCategory("External Script");
    $pegasusHelper->addTests([
        new Test("successful run", "testRESTExternalTestScriptComplete", false),
        new Test("body transmitted", "testRESTExternalTestScriptTransmitted", false)
    ]);
    $suite->addCategories($pegasusHelper);

    return $suite;
}

// 0 General

function testWorkingDirectory($info, $targetInfo, $suite) {
    $pass = $info["TestScript"]["correct_working_directory"];
    $msg = $pass ? "" : "the script must be run from [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/testing";
    return completeTestResult($pass, $msg);
}

function testRESTDirectory($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["correct_working_directory"]) return failTestFromMissingInfo("wrong working directory");
    $pass = file_exists(getRootPlugins() . "/REST");
    $msg = $pass ? "" : "REST must be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST";
    return completeTestResult($pass, $msg);
}

function testPegasusHelperDirectory($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["correct_working_directory"]) return failTestFromMissingInfo("wrong working directory");
    $pass = file_exists(getRootPlugins() . "/PegasusHelper");
    $msg = $pass ? "" : "PegasusHelper must be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper";
    return completeTestResult($pass, $msg);
}

function testIlBDConnection($info, $targetInfo, $suite) {
    $pass = $info["TestScript"]["ilDB_connection"];
    $msg = $pass ? "succeeded" : "connection to ILIAS-database failed, some tests cannot be performed";
    return completeTestResult($pass, $msg);
}

function testPhpVersion($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["php_version"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["TestScript"]["php_version"], $targetInfo["TestScript"]["php_version"]);
    return completeTestResult($pass, $msg);
}

// 1 ILIAS

function testILIASVersion($info, $targetInfo, $suite) {
    if(!$info["ILIAS"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) = testMinMaxVersion($info["ILIAS"]["version"], $targetInfo["ILIAS"]["min_version"], $targetInfo["ILIAS"]["max_version"]);
    return completeTestResult($pass, $msg);
}

function testILIASRedirectStatement($info, $targetInfo, $suite) {
    if(!$info["ILIAS"]["ilias_ini"]["available"]) return failTestFromMissingInfo();
    $pass = isset($info["ILIAS"]["ilias_ini"]["server"]["http_path"]);
    $msg = $pass ? "" : "if requests to ILIAS are redirected to https, then the file ilias.ini.php must be configured accordingly";
    return completeTestResult($pass, $msg);
}

// 2 REST

function testRESTVersion($info, $targetInfo, $suite) {
    if(!$info["REST"]["available"]) return failTestFromMissingInfo();
    if(!$targetInfo["REST"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["REST"]["version"], $targetInfo["REST"]["version"], "the latest version is [TARGET] and the one used here is [VERSION]");
    return completeTestResult($pass, $msg);
}

function testRESTkMinMaxVersion($info, $targetInfo, $suite) {
    if(!$info["ILIAS"]["available"]) return failTestFromMissingInfo();
    if(!$info["REST"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testMinMaxVersion($info["ILIAS"]["version"], $info["REST"]["ilias_min_version"], $info["REST"]["ilias_max_version"]);
    return completeTestResult($pass, $msg);
}

function testRESTInIlDB($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    list($pass, $msg) =  testInIlDB($info["REST"]);
    if(!$pass) failTestFromMissingInfo($msg); // TODO this may fail with installed plugins (mysql user?)
    return completeTestResult($pass, $msg);
}

function testRESTLastUpdateVersion($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["REST"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$info["REST"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["REST"]["ilDB"]["last_update_version"], $info["REST"]["version"], "some plugin-updates in ILIAS are not installed");
    return completeTestResult($pass, $msg);
}

function testRESTDbVersion($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["REST"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$targetInfo["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["REST"]["ilDB"]["db_version"], $targetInfo["REST"]["ilDB"]["db_version"]);
    return completeTestResult($pass, $msg);
}

function testRESTPluginActive($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["REST"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    list($pass, $msg) =  testPluginActive($info["REST"]["ilDB"]);
    return completeTestResult($pass, $msg);
}

// 3 PegasusHelper

function testPegasusHelperVersion($info, $targetInfo, $suite) {
    if(!$info["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    if(!$targetInfo["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["PegasusHelper"]["version"], $targetInfo["PegasusHelper"]["version"], "the latest version is [TARGET] and the one used here is [VERSION]");
    return completeTestResult($pass, $msg);
}

function testPegasusHelperMinMaxVersion($info, $targetInfo, $suite) {
    if(!$info["ILIAS"]["available"]) return failTestFromMissingInfo();
    if(!$info["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testMinMaxVersion($info["ILIAS"]["version"], $info["PegasusHelper"]["ilias_min_version"], $info["PegasusHelper"]["ilias_max_version"]);
    return completeTestResult($pass, $msg);
}

function testPegasusHelperRESTInstallation($info, $targetInfo, $suite) {
    $pass = true;
    $pass_non_mandatory = true;
    $missing_info = false;
    foreach ($suite->getCategoryByTitle("REST-plugin")->tests as $test) {
        if($test->result->state === ResultState::R_FAIL) {
            if ($test->mandatory) $pass = false;
            else $pass_non_mandatory = false;
        }
        if($test->result->state === ResultState::R_MISSING_INFO || $test->result->state === ResultState::R_ERROR) {
            $missing_info = true;
        }
    }

    if(!$pass) return completeTestResult(false, "some tests failed for the REST-plugin");
    if(!$pass_non_mandatory) return failTestFromMissingInfo("a test for the REST-plugin that is not mandatory has failed");
    if($missing_info) return failTestFromMissingInfo("a test for the REST-plugin has not been completed");
    return completeTestResult(true, "");
}

function testPegasusHelperInIlDB($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    list($pass, $msg) =  testInIlDB($info["PegasusHelper"]);
    if(!$pass) failTestFromMissingInfo($msg); // TODO this may fail with installed plugins (mysql user?)
    return completeTestResult($pass, $msg);
}

function testPegasusHelperLastUpdateVersion($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$info["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["PegasusHelper"]["ilDB"]["last_update_version"], $info["PegasusHelper"]["version"], "some plugin-updates in ILIAS are not installed");
    return completeTestResult($pass, $msg);
}

function testPegasusHelperDbVersion($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$targetInfo["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["PegasusHelper"]["ilDB"]["db_version"], $targetInfo["PegasusHelper"]["ilDB"]["db_version"]);
    return completeTestResult($pass, $msg);
}

function testPegasusHelperPluginActive($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    list($pass, $msg) =  testPluginActive($info["PegasusHelper"]["ilDB"]);
    return completeTestResult($pass, $msg);
}

// 4 External

function testExternalUrl($info, $targetInfo, $suite) {
    if(!$info["Connectivity"]["external_url"]["response"]["status"]) return failTestFromMissingInfo();
    list($pass, $msg) = testHttpResponseStatus($info["Connectivity"]["external_url"]["response"]["status"]);
    return completeTestResult($pass, $msg);
}

function testRESTLoginConnection($info, $targetInfo, $suite) {
    if(!$info["Connectivity"]["rest_login"]["response"]["status"]) return failTestFromMissingInfo();
    list($pass, $msg) = testHttpResponseStatus($info["Connectivity"]["rest_login"]["response"]["status"]);
    return completeTestResult($pass, $msg);
}

function testRESTExternalTestScriptComplete($info, $targetInfo, $suite) {
    if(!$info["Connectivity"]["external_testing"]) return failTestFromMissingInfo();
    list($pass, $msg) = testHttpResponseStatus($info["Connectivity"]["external_testing"]["response"]["status"]);
    return completeTestResult($pass, $msg);
}

function testRESTExternalTestScriptTransmitted($info, $targetInfo, $suite) {
    if(!$info["Connectivity"]["external_testing"]["response"]["body"]["response"]) return failTestFromMissingInfo();
    $pass = isset($info["Connectivity"]["external_testing"]["response"]["body"]["response"]["body"]["request"]["body"]["dat"]);
    $msg = ""; // TODO better failing message
    return completeTestResult($pass, $msg);
}

// * Multiple

/**
 * @param $v string
 * @param $v_min string
 * @param $v_max string
 * @return array
 */
function testMinMaxVersion($v, $v_min, $v_max) {
    $v_arr = strVersionToArray($v);
    $v_min_arr = strVersionToArray($v_min);
    $v_max_arr = strVersionToArray($v_max);

    $pass = (arrayVersionSmallerThan($v_min_arr, $v_arr, true)) &&
        (arrayVersionSmallerThan($v_arr, $v_max_arr, true));
    $msg = $pass ? "version " . $v : "the version " . $v . " is not contained in " . $v_min . " and " . $v_max;
    return [$pass, $msg];
}

/**
 * Checks for versions wether $version >= $target. the custom message $msg_fail uses tags [TARGET] and [VERSION]
 *
 * @param $version string
 * @param $target string
 * @param $msg_fail string
 * @return array
 */
function testVersionIs($version, $target, $msg_fail = "version must be [TARGET] but is [VERSION]") {
    $pass = arrayVersionSmallerThan(strVersionToArray($target), strVersionToArray($version), true);
    $msg = $pass ? "version " . $version : str_replace("[VERSION]", $version, str_replace("[TARGET]", $target, $msg_fail));
    return [$pass, $msg];
}

function testInIlDB($plugin_info) {
    $pass = $plugin_info["ilDB"]["available"];
    $msg = $pass ? "" : "the plugin must be installed";
    return [$pass, $msg];
}

function testPluginActive($plugin_info) {
    $pass = (bool) $plugin_info["active"];
    $msg = $pass ? "" : "the plugin must be activated";
    return [$pass, $msg];
}

function testHttpResponseStatus($status) {
    $pass = $status == 200;
    $msg = $pass ? "" : "received status $status";
    return [$pass, $msg];
}