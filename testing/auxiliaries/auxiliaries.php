<?php

/**
 * helper-functions to perform the tests
 */

function getRootIlias() {
    return isset($GLOBALS["ilias"]) ? "." : "../../../../../../../..";
}

function getRootPlugins() {
    return getRootIlias() . "/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook";
}

function strVersionToArray($version) {
    return array_map("intval", explode(".", $version));
}

function arrayVersionToStr($version) {
    return implode(".", $version);
}

function higherStrVersion($v1, $v2) {
    return strVersionToArray($v1) > strVersionToArray($v2) ? $v1 : $v2;
}

function lowerStrVersion($v1, $v2) {
    return strVersionToArray($v1) < strVersionToArray($v2) ? $v1 : $v2;
}