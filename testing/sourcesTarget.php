<?php

/**
 * collection of target-information for tests
 */

function getTargetInfo($info) {
    return [
        "ILIAS" => getILIASTargetInfo($info),
        "REST" => getPluginTargetInfo("REST", "1.7.3"),
        "PegasusHelper" => getPluginTargetInfo("PegasusHelper", "1.0.2")
    ];
}

function getILIASTargetInfo($info) {
    $ilias_target = [];
    $err_msg = "WARNING unable to get some Information about ILIAS";

    try {
        $ilias_target["min_version"] = higherStrVersion($info["REST"]["ilias_min_version"], $info["PegasusHelper"]["ilias_min_version"]);
        $ilias_target["max_version"] = lowerStrVersion($info["REST"]["ilias_max_version"], $info["PegasusHelper"]["ilias_max_version"]);

        $ilias_target["available"] = true;
    } catch (Exception $e) {
        printBad("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $ilias_target["available"] = false;
    }

    return $ilias_target;
}

function getPluginTargetInfo($plugin_dir, $version) {
    $plugin_target = [];
    $err_msg = "WARNING unable to get some Information about plugin " . $plugin_dir;

    $plugin_target["version"] = $version;
    $plugin_target["available"] = true;

    try {
        $plugin_target["ilDB"]["db_version"] = getTargetDbVersion($plugin_dir);
        $plugin_target["ilDB"]["available"] = true;
    } catch (Exception $e) {
        printBad("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $plugin_target["ilDB"]["available"] = false;
    }

    return $plugin_target;
}

function getTargetDbVersion($plugin_dir) {
    global $root_plugins;
    $dbupdate_lines = file($root_plugins . $plugin_dir . "/sql/dbupdate.php");

    // go through file-content and search for last occurrence of <#x>
    $version = "0";
    $regs = array();
    foreach($dbupdate_lines as $line) {
        if(preg_match('/^\<\#([0-9]+)>/', $line, $regs)) {
            $version = $regs[1];
        }
    }

    return $version;
}