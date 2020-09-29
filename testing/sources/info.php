<?php

/**
 * collection of information for tests, about the current installation
 */

function getInfo()
{
    $info = [];

    $ilDB_handle = initIlDB();
    $info["TestScript"] = getTestScriptInfo($ilDB_handle);
    $info["Connectivity"] = getConnectivityInfo();
    $info["ILIAS"] = getILIASInfo();
    $info["REST"] = getPluginInfo("REST", "rest", $ilDB_handle);
    $info["PegasusHelper"] = getPluginInfo("PegasusHelper", "sragpegasushelper", $ilDB_handle);
    closeIlDB($ilDB_handle);

    return $info;
}

function getTestScriptInfo($ilDB_handle)
{
    $testScript_info = [];

    $location = "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/testing";
    $testScript_info["correct_working_directory"] = isset($GLOBALS["ilias"]) || (substr(getcwd(), -strlen($location)) === $location);
    $testScript_info["ilDB_connection"] = isset($ilDB_handle);

    preg_match("/^\d+(\.\d+)*/", phpversion(), $match);
    $testScript_info["php_version"] = $match[0];

    $testScript_info["available"] = true;
    return $testScript_info;
}

function getConnectivityInfo()
{
    $connectivity_info = [];
    $err_msg = "WARNING unable to get some Information about the connectivity";

    try {
        $host = parse_ini_file(getRootIlias() . "/ilias.ini.php", true)["server"]["http_path"];
        $url_rest = $host . "/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST";

        $url_login = $url_rest . "/apps/admin";
        $connectivity_info["rest_login"] = httpLoggedRequest($url_login);
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" . $e->getMessage() . "\n");
    }

    $external_host = "https://test.studer-raimann.ch/pegasus-ilias53-php7"; // TODO setup backend (summarize urls in constants-file?)
    try {
        $connectivity_info["external_url"] = httpLoggedRequest($external_host);
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" . $e->getMessage() . "\n");
    }

    $external_script = $external_host . "/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/testing/external/run.php";
    $client = "default"; // TODO get client id
    try {
        $urlTestScript = $external_script . "?host=" . urlencode($host);
        $urlTestScript .= "&client_id=" . urlencode($client);
        $connectivity_info["external_testing"] = httpLoggedRequest($urlTestScript);
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" . $e->getMessage() . "\n");
    }

    return $connectivity_info;
}

function getILIASInfo()
{
    $ilias_info = [];
    $err_msg = "WARNING unable to get some Information about ILIAS";

    try {
        include_once getRootIlias() . "/include/inc.ilias_version.php";
        $ilias_info["version"] = ILIAS_VERSION_NUMERIC;

        $ilias_info["available"] = true;
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" . $e->getMessage() . "\n");
        $ilias_info["available"] = false;
    }

    try {
        $ilias_info["ilias_ini"] = parse_ini_file(getRootIlias() . "/ilias.ini.php", true);
        $ilias_info["ilias_ini"]["available"] = true;
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" . $e->getMessage() . "\n");
        $ilias_info["ilias_ini"]["available"] = false;
    }

    return $ilias_info;
}

function getPluginInfo($plugin_dir, $plugin_id, $ilDB_handle)
{
    $plugin_info = [];
    $err_msg = "WARNING unable to get some Information about plugin " . $plugin_dir;

    try {
        include getRootPlugins() . "/" . $plugin_dir . "/plugin.php";
        $plugin_info["version"] = $version;
        $plugin_info["ilias_min_version"] = $ilias_min_version;
        $plugin_info["ilias_max_version"] = $ilias_max_version;

        $plugin_info["available"] = true;
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" . $e->getMessage() . "\n");
        $plugin_info["available"] = false;
    }

    try {
        $query = "SELECT last_update_version, active, db_version FROM ilias.il_plugin WHERE plugin_id = '" . $plugin_id . "'";
        $result = queryAndFetchIlDB($ilDB_handle, $query);
        if (!$result) {
            $query = "SELECT last_update_version, active, db_version FROM il_plugin WHERE plugin_id = '" . $plugin_id . "'";
            $result = queryAndFetchIlDB($ilDB_handle, $query);
        }

        $plugin_info += ["ilDB" => $result];
        $plugin_info["ilDB"]["available"] = (bool) $result;
    } catch (Exception $e) {
        addToLog("\n" . $err_msg . "\n" . $e->getMessage() . "\n");
        $plugin_info["ilDB"]["available"] = false;
    }

    return $plugin_info;
}
