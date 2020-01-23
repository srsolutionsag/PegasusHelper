<?php

/**
 * code to access the ILIAS-database
 */

function initIlDB() {
    try {
        if(isset($GLOBALS["ilias"])) {
            $ilDB_handle = $GLOBALS["ilDB"];
        } else {
            if(!function_exists('mysqli_init') && !extension_loaded('mysqli'))
                throw new Exception("implementation of mysqli not found");

            list($host, $username, $password, $database) = getClientInfo();
            $ilDB_handle = new mysqli($host, $username, $password, $database);
            if($ilDB_handle->connect_errno) throw new Exception($ilDB_handle->connect_error);
        }
    } catch (Exception $e) {
        addToLog("\nWARNING unable to connect to ILIAS-database\n" .  $e->getMessage() . "\n");
        $ilDB_handle = NULL;
    }

    return $ilDB_handle;
}

function getClientInfo() {
    $clients_info = parse_ini_file(getRootIlias() . "/ilias.ini.php", true)["clients"];
    $clients = glob(getRootIlias() . "/" . $clients_info["path"] . "/*", GLOB_ONLYDIR);
    if(count($clients) === 0) throw new Exception("Unable to get client information from '/ilias.ini.php'");
    $client = count($clients) === 1 ? $clients_info["default"] : makeClientSelection($clients);

    $path = getRootIlias() . "/" . $clients_info["path"] . "/" . $client . "/" . $clients_info["inifile"];
    $client_ini = parse_ini_file($path, true);
    $db = $client_ini["db"];

    return [$db["host"], $db["user"], $db["pass"], $db["name"]];
}

function makeClientSelection($clients) {
    printNormal("your ILIAS-installation seems to have multiple clients\n");
    for($i = 0; $i < count($clients); $i++) {
        $tmp = explode("/", $clients[$i]);
        $clients[$i] = end($tmp);
        printNormal($i . ") " . $clients[$i] . "\n");
    }

    printNormal("please enter the number of the client for which you want to run the tests: ");
    for($i = 0; $i < 3; $i++) {
        $handle = fopen("php://stdin","r");
        $line = fgets($handle);
        addToLog($line);
        $line = trim($line);
        if(ctype_digit($line))
            return $clients[(int) $line];
        printNormal("please enter a number for one of the clients as listed above: ");
    }

    printNormal("\nselecting client '" . $clients[0] . "' by default");
    return $clients[0];
}

function queryAndFetchIlDB($ilDB_handle, $query) {
    if(!isset($ilDB_handle)) return NULL;

    $result = $ilDB_handle->query($query);
    if($result) {
        if(isset($GLOBALS["ilias"])) $result = (array) $ilDB_handle->fetchAssoc($result);
        else $result = $result->fetch_assoc();
    }

    return $result;
}

function closeIlDB($ilDB_handle) {
    if(isset($ilDB_handle) && !isset($GLOBALS["ilias"]))
        $ilDB_handle->close();
}