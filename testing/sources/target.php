<?php

/**
 * collection of target-information for tests
 */

function getTargetInfo($info) {
    return [
        "TestScript" => getTestScriptTargetInfo(),
        "ILIAS" => getILIASTargetInfo($info),
        "REST" => getPluginTargetInfo("REST", "Ilias.RESTPlugin"),
        "PegasusHelper" => getPluginTargetInfo("PegasusHelper", "PegasusHelper")
    ];
}

function getTestScriptTargetInfo() {
    $testScript_target = [];

    $testScript_target["php_version"] = "7";

    return $testScript_target;
}

function getILIASTargetInfo($info) {
    $ilias_target = [];
    $err_msg = "WARNING unable to get some Information about ILIAS";

    try {
        $ilias_target["min_version"] = higherStrVersion($info["REST"]["ilias_min_version"], $info["PegasusHelper"]["ilias_min_version"]);
        $ilias_target["max_version"] = lowerStrVersion($info["REST"]["ilias_max_version"], $info["PegasusHelper"]["ilias_max_version"]);

        $ilias_target["available"] = true;
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $ilias_target["available"] = false;
    }

    return $ilias_target;
}

function getPluginTargetInfo($plugin_dir, $repo) {
    $plugin_target = [];
    $err_msg = "WARNING unable to get some Information about plugin " . $plugin_dir;

    try {
        $plugin_target["version"] = getTargetVersion($repo);
        $plugin_target["available"] = true;
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $plugin_target["available"] = false;
    }

    try {
        $plugin_target["ilDB"]["db_version"] = getTargetDbVersion($plugin_dir);
        $plugin_target["ilDB"]["available"] = true;
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" .  $e->getMessage() . "\n");
        $plugin_target["ilDB"]["available"] = false;
    }

    return $plugin_target;
}

function getTargetDbVersion($plugin_dir) {
    $dbupdate_lines = file(getRootPlugins() . "/" . $plugin_dir . "/sql/dbupdate.php");

    // go through file-content and search for last occurrence of <#x>
    $version = "0";
    foreach($dbupdate_lines as $line) {
        if(preg_match('/^\<\#([0-9]+)>/', $line, $matches)) {
            $version = $matches[1];
        }
    }

    return $version;
}

function getTargetVersion($repo) {
    $tags = httpLoggedRequest("https://api.github.com/repos/studer-raimann/{$repo}/tags")["response"]["body"];

    foreach ($tags as $tag) {
        try {
            $version = tagToVersion($tag["name"]);
            if(isset($version)) return $version;
        } catch (Exception $e) {}
    }

    throw new Exception("Unable to get version via api.github.com");
}

/**
 * Parse a tag-string, such as 'v1.7.3-srag' to a version-string, such as '1.7.3'
 * The method finds the first occurrence of a pattern N.M.(...).K where N, M, K are integers
 *
 * @param $tag string
 * @return string|null
 */
function tagToVersion($tag) {
    $version = NULL;
    $copy = false;
    for($i = 0; $i < strlen($tag); $i++) {
        $char = $tag[$i];
        // start copying when a number is found
        if(!$copy && is_numeric($char) && !isset($version)) {
            $copy = true;
            $version = "";
        }
        // stop copying when a char that is not a number or '.' is found
        if($copy && !(is_numeric($char) || $char === ".")) $copy = false;

        if($copy) $version .= $char;
    }

    if($version === "") return NULL;

    return $version;
}