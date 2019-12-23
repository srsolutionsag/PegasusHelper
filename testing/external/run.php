<?php

include_once "../includefile.php";

try {
    $log = performTest();
    setResponse($log);
} catch (Exception $e) {
    setResponse("", 500);
}

/**
 * runs the test(s) and creates a log with the results
 *
 * @return mixed an array containing the info of the test(s)
 */
function performTest() {
    $clientId = $_GET["client_id"];
    $host = $_GET["host"];
    $api = $host . "/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php";
    $url = $api . "/v1/tests/routesAccess?client_id=" . $clientId;

    $log = httpLoggedRequest($url, "GET", ["dat" => "test"], []);
    $log["host"] = 'https://'.$_SERVER['HTTP_HOST']."//".$_SERVER['REQUEST_URI'];

    return $log;
}

/**
 * sets the response headers and the body as a JSON
 *
 * @param string $bodyArr the body as an array
 * @param int $code the status code
 */
function setResponse($bodyArr, $code = 200) {
    header_remove();

    http_response_code($code);
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    header("Content-Type: application/json");
    $status = array(
        200 => "200 OK",
        400 => "400 Bad Request",
        500 => "500 Internal Server Error"
    );
    header("Status: " . $status[$code]);

    echo json_encode($bodyArr);
}