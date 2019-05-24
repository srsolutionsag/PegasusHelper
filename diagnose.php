<?php

include_once "diagnose_tools.php";
include_once "diagnose_checks.php";

init();

print "Diagnostics for REST- and PegasusHelper-plugins\n";

$ilias = [];
print "\n1) ILIAS\n";
try {
    include_once "../../../../../../../include/inc.ilias_version.php";

    $ilias["version"] = strVersionToArray(ILIAS_VERSION_NUMERIC);
    printCheck("version " . ILIAS_VERSION . "\n", checkILIASVersion($ilias));

    try {
        $code = file_get_contents("../../../../../../../ilias.ini.php");
    } catch (Exception $e) {
        $code = "";
    }
    printCheck("https redirects\n", checkRedirectStatement($code));

} catch (Exception $e) {
    printBad("\nERROR when diagnosing ILIAS\n" .  $e->getMessage() . "\n");
}

$rest = [];
print "\n2) REST-plugin\n";
try {
    include_once "plugin.php";

    $rest["version"] = strVersionToArray($version);
    $rest["ilias_min_version"] = strVersionToArray($ilias_min_version);
    $rest["ilias_max_version"] = strVersionToArray($ilias_max_version);
    printCheck("version " . $version . "\n", checkRESTVersion($rest));
    printCheck("ilias-version between " . $ilias_min_version . " and " . $ilias_max_version . "\n", checkMinMaxVersion($ilias, $rest));

} catch (Exception $e) {
    printBad("\nERROR when diagnosing REST-plugin\n" .  $e->getMessage() . "\n");
}

$pegasusHelper = [];
print "\n3) PegasusHelper-plugin\n";
try {
    include_once "../REST/plugin.php";

    $pegasusHelper["version"] = strVersionToArray($version);
    $pegasusHelper["ilias_min_version"] = strVersionToArray($ilias_min_version);
    $pegasusHelper["ilias_max_version"] = strVersionToArray($ilias_max_version);
    printCheck("version " . $version . "\n", checkPegasusHelperVersion($pegasusHelper));
    printCheck("ilias-version between " . $ilias_min_version . " and " . $ilias_max_version . "\n", checkMinMaxVersion($ilias, $pegasusHelper));

} catch (Exception $e) {
    printBad("\nERROR when diagnosing PegasusHelper-plugin\n" .  $e->getMessage() . "\n");
}

finalize();