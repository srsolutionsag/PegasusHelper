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
 * @param $context int for TestingContext
 * @return TestSuite
 */
function getTestSuite($context) {
    $suite = new TestSuite($context);

    $general = new TestCategory("General");
    $general->addTests([
        new Test("location where script is run", "testWorkingDirectory", true, [TestingContext::C_CLI]),
        new Test("location of REST-plugin", "testRESTDirectory"),
        new Test("location of PegasusHelper-plugin", "testPegasusHelperDirectory"),
        new Test("connection to ILIAS-database", "testIlBDConnection", false, [TestingContext::C_CLI])
    ]);
    $suite->addCategories($general);

    $ilias = new TestCategory("ILIAS");
    $ilias->addTests([
        new Test("compatible ilias-version", "testILIASVersion"),
        new Test("https redirects", "testILIASRedirectStatement", false)
    ]);
    $suite->addCategories($ilias);

    $rest = new TestCategory("REST-plugin");
    $rest->addTests([
        new Test("version", "testRESTVersion", false),
        new Test("compatible ilias-version", "testRESTkMinMaxVersion"),
        new Test("entry in ilias-database", "testRESTInIlDB"),
        new Test("plugin-updates in ilias", "testRESTLastUpdateVersion"),
        new Test("ilias-database version", "testRESTDbVersion"),
        new Test("active", "testRESTPluginActive")
    ]);
    $suite->addCategories($rest);

    $pegasusHelper = new TestCategory("PegasusHelper-plugin");
    $pegasusHelper->addTests([
        new Test("working REST-installation", "testPegasusHelperRESTInstallation"),
        new Test("version", "testPegasusHelperVersion", false),
        new Test("compatible ilias-version", "testPegasusHelperMinMaxVersion"),
        new Test("entry in ilias-database", "testPegasusHelperInIlDB"),
        new Test("plugin-updates in ilias", "testPegasusHelperLastUpdateVersion"),
        new Test("ilias-database version", "testPegasusHelperDbVersion"),
        new Test("active", "testPegasusHelperPluginActive")
    ]);
    $suite->addCategories($pegasusHelper);

    return $suite;
}

// 0 general

function testWorkingDirectory($info, $targetInfo, $suite) {
    $pass = $info["TestScript"]["correct_working_directory"];
    $msg = $pass ? "correct" : "the script must be run from [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/testing";
    return completeTestResult($pass, $msg);
}

function testRESTDirectory($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["correct_working_directory"]) return failTestFromMissingInfo("wrong working directory");
    $pass = file_exists(getRootPlugins() . "/REST");
    $msg = $pass ? "correct" : "REST must be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST";
    return completeTestResult($pass, $msg);
}

function testPegasusHelperDirectory($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["correct_working_directory"]) return failTestFromMissingInfo("wrong working directory");
    $pass = file_exists(getRootPlugins() . "/PegasusHelper");
    $msg = $pass ? "correct" : "PegasusHelper must be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper";
    return completeTestResult($pass, $msg);
}

function testIlBDConnection($info, $targetInfo, $suite) {
    $pass = $info["TestScript"]["ilDB_connection"];
    $msg = $pass ? "succeeded" : "connection to ILIAS-database failed, some tests cannot be performed";
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
    $msg = $pass ? "present" : "if requests to ILIAS are redirected to https, then the file ilias.ini.php must be configured accordingly";
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
    list($pass, $msg) =  testInIlDB( $info["REST"]);
    return completeTestResult($pass, $msg);
}

function testRESTLastUpdateVersion($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["REST"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$info["REST"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["REST"]["ilDB"]["last_update_version"], $info["REST"]["version"], "some plugin-updates in ilias are not installed");
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
    return completeTestResult(true, "working");
}

function testPegasusHelperInIlDB($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    list($pass, $msg) =  testInIlDB($info["PegasusHelper"]);
    return completeTestResult($pass, $msg);
}

function testPegasusHelperLastUpdateVersion($info, $targetInfo, $suite) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$info["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    list($pass, $msg) =  testVersionIs($info["PegasusHelper"]["ilDB"]["last_update_version"], $info["PegasusHelper"]["version"], "some plugin-updates in ilias are not installed");
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

// * multiple

/**
 * @param $v string
 * @param $v_min string
 * @param $v_max string
 */
function testMinMaxVersion($v, $v_min, $v_max) {
    $v_arr = strVersionToArray($v);
    $v_min_arr = strVersionToArray($v_min);
    $v_max_arr = strVersionToArray($v_max);

    $pass = ($v_min_arr <= $v_arr) && ($v_arr <= $v_max_arr);
    $msg = $pass ? "version " . $v : "the version " . $v . " is not contained in " . $v_min . " and " . $v_max;
    return [$pass, $msg];
}

/**
 * Checks for versions wether $version >= $target. the custom message $msg_fail uses tags [TARGET] and [VERSION]
 *
 * @param $version string
 * @param $target string
 * @param $msg_fail string
 */
function testVersionIs($version, $target, $msg_fail = "version must be [TARGET] but is [VERSION]") {
    $pass = strVersionToArray($version) >= strVersionToArray($target);
    $msg = $pass ? "version " . $version : str_replace("[VERSION]", $version, str_replace("[TARGET]", $target, $msg_fail));
    return [$pass, $msg];
}

function testInIlDB($plugin_info) {
    $pass = $plugin_info["ilDB"]["available"];
    $msg = $pass ? "found entry" : "the plugin must be installed";
    return [$pass, $msg];
}

function testPluginActive($plugin_info) {
    $pass = (bool) $plugin_info["active"];
    $msg = $pass ? "yes" : "the plugin must be activated";
    return [$pass, $msg];
}