<?php

function checkILIASVersion($ilias) {
    $pass = $ilias["version"][0] == 5 && ($ilias["version"][1] == 3 || $ilias["version"][1] == 4);
    $info = ($pass) ? NULL : "supported ILIAS-versions are 5.3 or 5.4\n";
    $mandatory = true;

    return array($pass, $info, $mandatory);
}

function checkRESTVersion($rest) {
    $pass = $rest["version"] == [1, 0, 1];
    $info = ($pass) ? NULL : "REST-version must be 1.0.1\n";
    $mandatory = true;

    return array($pass, $info, $mandatory);
}

function checkPegasusHelperVersion($pegasusHelper) {
    $pass = $pegasusHelper["version"] == [1, 7, 3];
    $info = ($pass) ? NULL : "PegasusHelper-version must be 1.7.3\n";
    $mandatory = true;

    return array($pass, $info, $mandatory);
}

function checkMinMaxVersion($ilias, $plugin) {
    $v = $ilias["version"];
    $vmin = $plugin["ilias_min_version"];
    $vmax = $plugin["ilias_max_version"];

    $pass = ($vmin <= $v) && ($v <= $vmax);
    $info = ($pass) ? NULL : "the version " . arrayVersionToStr($v) . " is not contained in " . arrayVersionToStr($vmin) . " and " . arrayVersionToStr($vmax) . "\n";
    $mandatory = true;

    return array($pass, $info, $mandatory);
}

function checkRedirectStatement($code) {
    $code = explode("\n", $code);
    $pass = false;
    for ($i = 0; $i < count($code) - 1; $i++) {
        if(strpos($code[$i], "[server]") === 0 && strpos($code[$i + 1], "http_path") === 0) {
            $pass = true;
            break;
        }
    }

    $info = ($pass) ? NULL : "if requests to ILIAS are redirected to https, then the file 'ilias.ini.php' in the root-directory must contain the following two lines\n[server]\nhttp_path = \"https://YOUR.ILIAS-INSTALLATION.ORG\"" . "\n";
    $mandatory = false;

    return array($pass, $info, $mandatory);
}