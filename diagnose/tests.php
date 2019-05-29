<?php

include_once "auxiliaries.php";

/**
 * tests to be performed are implemented here and must be added to the tests-list in "run.php"
 *
 * template for a test-function:
 *
 * function testNAME($info) {
 *     // compute test based on $info
 *     return [$pass, $msg, $mandatory];
 * }
 *
 */

// 0 general

function testExecutionDirectory($info) {
    global $root_ilias;
    $pass = file_exists($root_ilias . "/ilias.php");
    $msg = $pass ? NULL : "the script must be run from './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper'";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testRESTDirectory($info) {
    global $root_plugins;
    $pass = file_exists($root_plugins . "/REST");
    $msg = $pass ? NULL : "REST must be located at './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST'";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testPegasusHelperDirectory($info) {
    global $root_plugins;
    $pass = file_exists($root_plugins . "/PegasusHelper");
    $msg = $pass ? NULL : "REST must be located at './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper'";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testIlBDConnection($info) {
    $pass = $info["ilDB"];
    $msg = $pass ? NULL : "connection to ILIAS-database failed, some tests cannot be performed";
    $mandatory = false;

    return [$pass, $msg, $mandatory];
}

// 1 ILIAS

function testILIASVersion($info) {
    $v = strVersionToArray($info["ILIAS"]["version"]);
    $pass = $v[0] == 5 && ($v[1] == 3 || $v[1] == 4);
    $msg = $pass ? $info["ILIAS"]["version"] : "supported ILIAS-versions are 5.3 or 5.4";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testILIASRedirectStatement($info) {
    $pass = isset($info["ILIAS"]["server"]["http_path"]);
    $msg = $pass ? NULL : "if requests to ILIAS are redirected to https, then the file 'ilias.ini.php' must be configured accordingly";
    $mandatory = false;

    return [$pass, $msg, $mandatory];
}

// 2 REST

function testRESTVersion($info) {
    return testVersionIs($info["REST"]["version"], "1.7.3");
}

function testRESTkMinMaxVersion($info) {
    return testMinMaxVersion($info["ILIAS"], $info["REST"]);
}

function testRESTInIlDB($info) {
    return testInIlDB($info["REST"], $info["ilDB"]);
}

function testRESTDbVersion($info) {
    return testBdVersion($info["REST"], "11", $info["ilDB"]);
}

function testRESTPluginActive($info) {
    return testPluginActive($info["REST"], $info["ilDB"]);
}

// 3 PegasusHelper

function testPegasusHelperVersion($info) {
    return testVersionIs($info["PegasusHelper"]["version"], "1.0.1");
}

function testPegasusHelperMinMaxVersion($info) {
    return testMinMaxVersion($info["ILIAS"], $info["PegasusHelper"]);
}

function testPegasusHelperInIlDB($info) {
    return testInIlDB($info["PegasusHelper"], $info["ilDB"]);
}

function testPegasusHelperDbVersion($info) {
    return testBdVersion($info["PegasusHelper"], "15", $info["ilDB"]);
}

function testPegasusHelperPluginActive($info) {
    return testPluginActive($info["PegasusHelper"], $info["ilDB"]);
}

// * multiple

function testMinMaxVersion($ilias_info, $plugin_info) {
    $v = strVersionToArray($ilias_info["version"]);
    $vmin = strVersionToArray($plugin_info["ilias_min_version"]);
    $vmax = strVersionToArray($plugin_info["ilias_max_version"]);

    $pass = ($vmin <= $v) && ($v <= $vmax);
    $msg = $pass ? NULL : "the version '" . $ilias_info["version"] . "' is not contained in '" . $plugin_info["ilias_min_version"] . "' and '" . $plugin_info["ilias_max_version"] . "'";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testVersionIs($version, $target) {
    $pass = $version === $target;
    $msg = $pass ? $version : "version must be '" . $target . "' but is '" . $version . "'";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testInIlDB($plugin_info, $ilBD_connected) {
    $pass = isset($plugin_info["ilDB"]);
    $msg = $pass ? NULL : ($ilBD_connected ? "the plugin must be installed" : "test requires connection to ILIAS-database");
    $mandatory = $ilBD_connected;

    return [$pass, $msg, $mandatory];
}

function testBdVersion($plugin_info, $target, $ilBD_connected) {
    if(!isset($plugin_info["ilDB"])) {
        $pass = false;
        $msg = $ilBD_connected ? "the plugin must be installed" : "test requires connection to ILIAS-database";
        $mandatory = $ilBD_connected;
    } else {
        list($pass, $msg, $mandatory) = testVersionIs($plugin_info["ilDB"]["db_version"], $target);
    }

    return [$pass, $msg, $mandatory];
}

function testPluginActive($plugin_info, $ilBD_connected) {
    if(!isset($plugin_info["ilDB"])) {
        $pass = false;
        $msg = $ilBD_connected ? "the plugin must be installed" : "test requires connection to ILIAS-database";
        $mandatory = $ilBD_connected;
    } else {
        $pass = (bool) $plugin_info["ilDB"]["active"];
        $msg = $pass ? NULL : "the plugin must be activated";
        $mandatory = true;
    }

    return [$pass, $msg, $mandatory];
}