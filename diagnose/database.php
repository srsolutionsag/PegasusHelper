<?php

/**
 * code to access the ILIAS-database
 */

function initIlDB() {
    try {
        list($host, $username, $password, $database) = getClientInfo();
        $ilDB_handle = new mysqli($host, $username, $password, $database);
        if ($ilDB_handle->connect_errno) $ilDB_handle = NULL;
    } catch (Exception $e) {
        printBad("\nERROR when connecting to ILIAS-database\n" .  $e->getMessage() . "\n");
        $ilDB_handle = NULL;
    }

    return $ilDB_handle;
}

function getClientInfo() {
    global $root_ilias;
    $clients_info = parse_ini_file($root_ilias . "ilias.ini.php", TRUE)["clients"];
    $clients = glob($root_ilias . $clients_info["path"] . "/*", GLOB_ONLYDIR);
    $client = count($clients) == 1 ? $clients_info["default"] : makeClientSelection($clients);

    $path = $root_ilias . $clients_info["path"] . "/" . $client . "/" . $clients_info["inifile"];
    $client_ini = parse_ini_file($path, TRUE);
    $db = $client_ini["db"];

    return [$db["host"], $db["user"], $db["pass"], $db["name"]];
}

function makeClientSelection($clients) {
    print "your ILIAS-installation seems to have multiple clients\n";
    for ($i = 0; $i < count($clients); $i++) {
        $tmp = explode("/", $clients[$i]);
        $clients[$i] = end($tmp);
        print $i . ") " . $clients[$i] . "\n";
    }

    print "please enter the number of the client for which you want to run the tests: ";
    for($i = 0; $i < 3; $i++) {
        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));
        if(ctype_digit($line))
            return $clients[(int) $line];
        print "please enter a number for one of the clients as listed above: ";
    }

    print "\nselecting client '" . $clients[0] . "' by default";
    return $clients[0];
}

function queryAndFetchIlDB($ilDB_handle, $query) {
    if (!isset($ilDB_handle)) return NULL;

    $result = $ilDB_handle->query($query);
    return $result ? $result->fetch_assoc() : NULL;
}

function closeIlDB($ilDB_handle) {
    if (!isset($ilDB_handle)) return;

    $ilDB_handle->close();
}