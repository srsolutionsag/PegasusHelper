<?php

$internal_log = "request @ " . date("Y-m-d H:i:s") . PHP_EOL;
try {
    $internal_log .= "host   : " . $_GET["host"] . PHP_EOL;
    $internal_log .= "client : " . $_GET["client_id"] . PHP_EOL;
    $log = performTest();
    setResponse($log);
    $internal_log .= "RESULT : " . print_r($log, true) . PHP_EOL;
} catch (Exception $e) {
    $internal_log .= "ERROR  : " . $e->getMessage() . PHP_EOL;
    setResponse("", 500);
}
file_put_contents("check.log", $internal_log . PHP_EOL . PHP_EOL, FILE_APPEND);

/**
 * runs the test(s) and creates a log with the results
 *
 * @return mixed an array containing the info of the test(s)
 */
function performTest()
{
    $clientId = $_GET["client_id"];
    $host = $_GET["host"];
    $api = $host . "/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php";
    $url = $api . "/v1/tests/routesAccess?client_id=" . $clientId;

    $log = httpLoggedRequest($url, "GET", ["dat" => "test"], []);
    $log["host"] = "https://" . $_SERVER["HTTP_HOST"] . "//" . $_SERVER["REQUEST_URI"];

    return $log;
}

/**
 * sets the response headers and the body as a JSON
 *
 * @param string $bodyArr the body as an array
 * @param int $code the status code
 */
function setResponse($bodyArr, $code = 200)
{
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

function httpLoggedRequest($url, $method = "GET", $bodyArr = [], $headerArr = [])
{
    if ($method !== "GET" && $method !== "POST") {
        throw new Error("the argument \$method for httpLoggedRequest must be GET or POST");
    }
    $headerArr += [count($bodyArr) ? "Content-Type: application/json" : "User-Agent: srag (testing script)"];
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArr);
    if ($method === "POST") {
        curl_setopt($ch, CURLOPT_POST, true);
    }
    if (!count($bodyArr)) {
        curl_setopt($ch, CURLOPT_NOBODY, true);
    } else {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyArr));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    $log["info"] = curl_getinfo($ch);
    $log["errno"] = curl_errno($ch);
    $log["errmsg"] = curl_error($ch);
    $log["response"] = json_decode($response);

    curl_close($ch);
    return $log;
}
