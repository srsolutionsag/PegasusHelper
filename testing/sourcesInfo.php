<?php

include_once "database.php";

/**
 * collection of information for tests, about the current installation
 */

function getInfo($context) {
    $info = [];

    $ilDB_handle = initIlDB($context);
    $info["TestScript"] = getTestScriptInfo($ilDB_handle);
    $info["ILIAS"] = getILIASInfo();
    $info["REST"] = getPluginInfo("REST", "rest", $ilDB_handle, $context);
    $info["PegasusHelper"] = getPluginInfo("PegasusHelper", "sragpegasushelper", $ilDB_handle, $context);
    closeIlDB($ilDB_handle, $context);

    return $info;
}

function getTestScriptInfo($ilDB_handle) {
    $testScript_info = [];

    $location = "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/testing";
    $testScript_info["correct_working_directory"] = substr(getcwd(), -strlen($location)) === $location;
    $testScript_info["ilDB_connection"] = isset($ilDB_handle);

    $testScript_info["available"] = true;
    return $testScript_info;
}

function getILIASInfo() {
    global $root_ilias;
    $ilias_info = [];
    $err_msg = "WARNING unable to get some Information about ILIAS";

    try {
        include_once $root_ilias . "include/inc.ilias_version.php";
        $ilias_info["version"] = ILIAS_VERSION_NUMERIC;

        $ilias_info["available"] = true;
    } catch (Exception $e) {
        printBad("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $ilias_info["available"] = false;
    }

    try {
        $ilias_info["ilias_ini"] = parse_ini_file($root_ilias . "ilias.ini.php");
        $ilias_info["ilias_ini"]["available"] = true;
    } catch (Exception $e) {
        printBad("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $ilias_info["ilias_ini"]["available"] = false;
    }

    return $ilias_info;
}

function getPluginInfo($plugin_dir, $plugin_id, $ilDB_handle, $context) {
    global $root_plugins;
    $plugin_info = [];
    $err_msg = "WARNING unable to get some Information about plugin " . $plugin_dir;

    try {
        include $root_plugins . $plugin_dir . "/plugin.php";
        $plugin_info["version"] = $version;
        $plugin_info["ilias_min_version"] = $ilias_min_version;
        $plugin_info["ilias_max_version"] = $ilias_max_version;

        $plugin_info["available"] = true;
    } catch (Exception $e) {
        printBad("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $plugin_info["available"] = false;
    }

    try {
        $query = "SELECT last_update_version, active, db_version FROM ilias.il_plugin WHERE plugin_id = '" . $plugin_id . "'";
        $plugin_info += ["ilDB" => queryAndFetchIlDB($ilDB_handle, $query, $context)];

        $plugin_info["ilDB"]["available"] = isset($plugin_info["ilDB"]);
    } catch (Exception $e) {
        printBad("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $plugin_info["ilDB"]["available"] = false;
    }

    return $plugin_info;
}