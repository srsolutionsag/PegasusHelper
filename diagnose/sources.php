<?php

include_once "database.php";

/**
 * collection of the information that is used by the tests
 */

function getInfo() {
    $info = [];

    $ilDB_handle = initIlDB();
    $info["ilDB"] = isset($ilDB_handle);
    $info["ILIAS"] = getILIASInfo();
    $info["REST"] = getPluginInfo("REST", "rest", $ilDB_handle);
    $info["PegasusHelper"] = getPluginInfo("PegasusHelper", "sragpegasushelper", $ilDB_handle);
    closeIlDB($ilDB_handle);

    return $info;
}

function getILIASInfo() {
    global $root_ilias;
    $ilias_info = [];

    try {
        include_once $root_ilias . "include/inc.ilias_version.php";

        $ilias_info["version"] = ILIAS_VERSION_NUMERIC;
        $ilias_info["ilias_ini"] = parse_ini_file($root_ilias . "ilias.ini.php");
    } catch (Exception $e) {
        printBad("\nERROR when gathering Information about ILIAS\n" .  $e->getMessage() . "\n");
    }

    return $ilias_info;
}

function getPluginInfo($plugin_dir, $plugin_id, $ilDB_handle) {
    global $root_plugins;
    $plugin_info = [];

    try {
        include_once $root_plugins . $plugin_dir . "/plugin.php";

        $plugin_info["version"] = $version;
        $plugin_info["ilias_min_version"] = $ilias_min_version;
        $plugin_info["ilias_max_version"] = $ilias_max_version;

        $query = "SELECT last_update_version, active, db_version FROM ilias.il_plugin WHERE plugin_id = '" . $plugin_id . "'";
        $plugin_info += ["ilDB" => queryAndFetchIlDB($ilDB_handle, $query)];
    } catch (Exception $e) {
        printBad("\nERROR when gathering Information about plugin " . $plugin_dir . "\n" .  $e->getMessage() . "\n");
    }

    return $plugin_info;
}