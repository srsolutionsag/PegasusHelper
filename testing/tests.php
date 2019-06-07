<?php

include_once "auxiliaries.php";

/**
 * tests to be performed are implemented here and must be added to $testsList in the getTestsList-function below
 *
 * template for a test-function:
 *
 * function testNAME($info, $targetInfo) {
 *     // compute test based on $info, $targetInfo
 *     return [$pass, $msg, $mandatory];
 * }
 *
 */

function getTestsList($context) {
    $testsList = [
        [
            "title" => "General",
            "tests" => [
                [
                    "msg" => "location where script is run",
                    "fn" => "testWorkingDirectory",
                    "run" => ["cli" => true, "ilias" => false]
                ],
                [
                    "msg" => "location of REST-plugin",
                    "fn" => "testRESTDirectory",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "location of PegasusHelper-plugin",
                    "fn" => "testPegasusHelperDirectory",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "connection to ILIAS-database",
                    "fn" => "testIlBDConnection",
                    "run" => ["cli" => true, "ilias" => false]
                ]
            ]
        ],
        [
            "title" => "ILIAS",
            "tests" => [
                [
                    "msg" => "version",
                    "fn" => "testILIASVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "https redirects",
                    "fn" => "testILIASRedirectStatement",
                    "run" => ["cli" => true, "ilias" => true]
                ]
            ]
        ],
        [
            "title" => "REST-plugin",
            "tests" => [
                [
                    "msg" => "version",
                    "fn" => "testRESTVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "compatible ilias-version",
                    "fn" => "testRESTkMinMaxVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "entry in ilias-database",
                    "fn" => "testRESTInIlDB",
                    "run" => ["cli" => true, "ilias" => false]
                ],
                [
                    "msg" => "plugin-updates in ilias",
                    "fn" => "testRESTLastUpdateVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "ilias-database version",
                    "fn" => "testRESTDbVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "active",
                    "fn" => "testRESTPluginActive",
                    "run" => ["cli" => true, "ilias" => true]
                ]
            ]
        ],
        [
            "title" => "PegasusHelper-plugin",
            "tests" => [
                [
                    "msg" => "version",
                    "fn" => "testPegasusHelperVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "compatible ilias-version",
                    "fn" => "testPegasusHelperMinMaxVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "entry in ilias-database",
                    "fn" => "testPegasusHelperInIlDB",
                    "run" => ["cli" => true, "ilias" => false]
                ],
                [
                    "msg" => "plugin-updates in ilias",
                    "fn" => "testPegasusHelperLastUpdateVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "ilias-database version",
                    "fn" => "testPegasusHelperDbVersion",
                    "run" => ["cli" => true, "ilias" => true]
                ],
                [
                    "msg" => "active",
                    "fn" => "testPegasusHelperPluginActive",
                    "run" => ["cli" => true, "ilias" => true]
                ]
            ]
        ]
    ];

    $no_category = 0;
    foreach ($testsList as $category) {
        $no_test = 0;
        foreach ($category["tests"] as $test) {
            if(!$test["run"][$context]) {
                unset($testsList[$no_category]["tests"][$no_test]);
            }
            $no_test++;
        }
        $no_category++;
    }

    return $testsList;
}

// 0 general

function testWorkingDirectory($info, $targetInfo) {
    $pass = $info["TestScript"]["correct_working_directory"];
    $msg = $pass ? "correct" : "the script must be run from [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/testing";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testRESTDirectory($info, $targetInfo) {
    if(!$info["TestScript"]["correct_working_directory"]) return [false, "wrong working directory", false];

    global $root_plugins;
    $pass = file_exists($root_plugins . "/REST");
    $msg = $pass ? "correct" : "REST must be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testPegasusHelperDirectory($info, $targetInfo) {
    if(!$info["TestScript"]["correct_working_directory"]) return [false, "wrong working directory", false];

    global $root_plugins;
    $pass = file_exists($root_plugins . "/PegasusHelper");
    $msg = $pass ? "correct" : "PegasusHelper must be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testIlBDConnection($info, $targetInfo) {
    $pass = $info["TestScript"]["ilDB_connection"];
    $msg = $pass ? "succeeded" : "connection to ILIAS-database failed, some tests cannot be performed";
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
    $msg = $pass ? "present" : "if requests to ILIAS are redirected to https, then the file ilias.ini.php must be configured accordingly";
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
    $msg = $pass ? "version " . $v : "the version " . $v . " is not contained in " . $v_min . " and " . $v_max;
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testVersionIs($version, $target, $custom_msg_fail = NULL) {
    $pass = $version === $target;
    $msg = $pass ? "version " . $version : ($custom_msg_fail ? $custom_msg_fail : "version must be " . $target . " but is " . $version);
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testInIlDB($plugin_info) {
    $pass = $plugin_info["ilDB"]["available"];
    $msg = $pass ? "found entry" : "the plugin must be installed";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}

function testPluginActive($plugin_info) {
    $pass = (bool) $plugin_info["active"];
    $msg = $pass ? "yes" : "the plugin must be activated";
    $mandatory = true;

    return [$pass, $msg, $mandatory];
}