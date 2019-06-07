<?php

include_once "auxiliaries.php";

/**
 * tests to be performed are implemented here and must be added to the tests-list in "run.php"
 *
 * template for a test-function:
 *
 * function testNAME($info, $targetInfo) {
 *     // compute test based on $info, $targetInfo
 *     return [$pass, $msg, $mandatory];
 * }
 *
 */

// 0 general

function testWorkingDirectory($info, $targetInfo) {
    $pass = $info["TestScript"]["correct_working_directory"];
    $msg = $pass ? NULL : "the script must be run from [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/testing";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testRESTDirectory($info, $targetInfo) {
    if(!$info["TestScript"]["correct_working_directory"]) return [false, "wrong working directory", false];

    global $root_plugins;
    $pass = file_exists($root_plugins . "/REST");
    $msg = $pass ? NULL : "REST must be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testPegasusHelperDirectory($info, $targetInfo) {
    if(!$info["TestScript"]["correct_working_directory"]) return [false, "wrong working directory", false];

    global $root_plugins;
    $pass = file_exists($root_plugins . "/PegasusHelper");
    $msg = $pass ? NULL : "PegasusHelper must be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testIlBDConnection($info, $targetInfo) {
    $pass = $info["TestScript"]["ilDB_connection"];
    $msg = $pass ? NULL : "connection to ILIAS-database failed, some tests cannot be performed";
    $mandatory = false;

    return [$pass, $msg, $mandatory];
}

// 1 ILIAS

function testILIASVersion($info, $targetInfo) {
    if(!$info["ILIAS"]["available"]) return failTestFromMissingInfo();
    return testMinMaxVersion($info["ILIAS"]["version"], $targetInfo["ILIAS"]["min_version"], $targetInfo["ILIAS"]["max_version"]);
}

function testILIASRedirectStatement($info, $targetInfo) {
    if(!$info["ILIAS"]["ilias_ini"]["available"]) return failTestFromMissingInfo();

    $pass = isset($info["ILIAS"]["ilias_ini"]["server"]["http_path"]);
    $msg = $pass ? NULL : "if requests to ILIAS are redirected to https, then the file ilias.ini.php must be configured accordingly";
    $mandatory = false;

    return [$pass, $msg, $mandatory];
}

// 2 REST

function testRESTVersion($info, $targetInfo) {
    if(!$info["REST"]["available"]) return failTestFromMissingInfo();
    if(!$targetInfo["REST"]["available"]) return failTestFromMissingInfo();
    return testVersionIs($info["REST"]["version"], $targetInfo["REST"]["version"]);
}

function testRESTkMinMaxVersion($info, $targetInfo) {
    if(!$info["ILIAS"]["available"]) return failTestFromMissingInfo();
    if(!$info["REST"]["available"]) return failTestFromMissingInfo();
    return testMinMaxVersion($info["ILIAS"]["version"], $info["REST"]["ilias_min_version"], $info["REST"]["ilias_max_version"]);
}

function testRESTInIlDB($info, $targetInfo) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    return testInIlDB( $info["REST"]);
}

function testRESTLastUpdateVersion($info, $targetInfo) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["REST"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$info["REST"]["available"]) return failTestFromMissingInfo();
    return testVersionIs($info["REST"]["ilDB"]["last_update_version"], $info["REST"]["version"], "some plugin-updates in ilias are not installed");
}

function testRESTDbVersion($info, $targetInfo) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["REST"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$targetInfo["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo();
    return testVersionIs($info["REST"]["ilDB"]["db_version"], $targetInfo["REST"]["ilDB"]["db_version"]);
}

function testRESTPluginActive($info, $targetInfo) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["REST"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    return testPluginActive($info["REST"]["ilDB"]);
}

// 3 PegasusHelper

function testPegasusHelperVersion($info, $targetInfo) {
    if(!$info["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    if(!$targetInfo["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    return testVersionIs($info["PegasusHelper"]["version"], $targetInfo["PegasusHelper"]["version"]);
}

function testPegasusHelperMinMaxVersion($info, $targetInfo) {
    if(!$info["ILIAS"]["available"]) return failTestFromMissingInfo();
    if(!$info["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    return testMinMaxVersion($info["ILIAS"]["version"], $info["PegasusHelper"]["ilias_min_version"], $info["PegasusHelper"]["ilias_max_version"]);
}

function testPegasusHelperInIlDB($info, $targetInfo) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    return testInIlDB($info["PegasusHelper"]);
}

function testPegasusHelperLastUpdateVersion($info, $targetInfo) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$info["PegasusHelper"]["available"]) return failTestFromMissingInfo();
    return testVersionIs($info["PegasusHelper"]["ilDB"]["last_update_version"], $info["PegasusHelper"]["version"], "some plugin-updates in ilias are not installed");
}

function testPegasusHelperDbVersion($info, $targetInfo) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    if(!$targetInfo["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo();
    return testVersionIs($info["PegasusHelper"]["ilDB"]["db_version"], $targetInfo["PegasusHelper"]["ilDB"]["db_version"]);
}

function testPegasusHelperPluginActive($info, $targetInfo) {
    if(!$info["TestScript"]["ilDB_connection"]) return failTestFromMissingInfo("connection to ILIAS-database required");
    if(!$info["PegasusHelper"]["ilDB"]["available"]) return failTestFromMissingInfo("plugin is not (correctly) installed");
    return testPluginActive($info["PegasusHelper"]["ilDB"]);
}

// * multiple

function testMinMaxVersion($v, $v_min, $v_max) {
    $V = strVersionToArray($v);
    $V_MIN = strVersionToArray($v_min);
    $V_MAX = strVersionToArray($v_max);

    $pass = ($V_MIN <= $V) && ($V <= $V_MAX);
    $msg = $pass ? $v : "the version " . $v . " is not contained in " . $v_min . " and " . $v_max;
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testVersionIs($version, $target, $custom_msg_fail = NULL) {
    $pass = $version === $target;
    $msg = $pass ? $version : ($custom_msg_fail ? $custom_msg_fail : "version must be " . $target . " but is " . $version);
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testInIlDB($plugin_info) {
    $pass = $plugin_info["ilDB"]["available"];
    $msg = $pass ? NULL : "the plugin must be installed";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testPluginActive($plugin_info) {
    $pass = (bool) $plugin_info["active"];
    $msg = $pass ? NULL : "the plugin must be activated";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}