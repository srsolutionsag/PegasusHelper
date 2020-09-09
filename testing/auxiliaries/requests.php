<?php

/**
 * code for html-requests
 */

/**
 * invoke a http-request and return both a summary of the request, as well as the response
 *
 * @param $url
 * @param string $method
 * @param array $bodyArr
 * @param array $headerArr
 * @return mixed
 */
function httpLoggedRequest($url, $method = "GET", $bodyArr = [], $headerArr = [])
{
    $headerArr += [count($bodyArr) ? "Content-Type: application/json" : "User-Agent: srag (testing script)"];

    $options = array("http" => array(
        "method" => $method,
        "header" => implode("\r\n", $headerArr),
        "ignore_errors" => true
    ));
    if (count($bodyArr)) {
        $options["content"] = json_encode($bodyArr);
    }

    $response = file_get_contents($url, false, stream_context_create($options));

    $log["request"]["options"] = $options;
    $log["request"]["url"] = $url;
    $log["response"]["body"] = json_decode($response, true);
    $log["response"]["headers"] = $http_response_header;

    foreach ($log["response"]["headers"] as $h) {
        if (preg_match('/HTTP.*([0-9]{3})/', $h, $matches)) {
            $log["response"]["status"] = intval($matches[1]);
        }
    }

    return $log;
}
