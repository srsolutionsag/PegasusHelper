<?php

require_once __DIR__ . "/../bootstrap.php";

include_once __DIR__ . "/../testing/tests.php";
include_once __DIR__ . "/../testing/database.php";
include_once __DIR__ . "/../testing/sourcesInfo.php";
include_once __DIR__ . "/../testing/sourcesTarget.php";

final class ilPegasusHelperTesting {

    public function __construct() {

    }

    public function run() {
        global $root_ilias, $root_plugins;
        $root_ilias = "./";
        $root_plugins = $root_ilias . "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/";

        $info = $this->getInfo();
        $targetInfo = $this->getTargetInfo($info);
        $testsList = getTestsList("ilias");

        $results = [];
        $category_no = 0;
        foreach ($testsList as $category) {
            $results[$category_no] = ["title" => $category["title"]];

            $test_no = 0;
            foreach ($category["tests"] as $test) {
                $title = $test["msg"];
                try {
                    list($pass, $description, $mandatory) = $test["fn"]($info, $targetInfo);
                    $completed = true;
                } catch (Exception $e) {
                    $pass = false;
                    $mandatory = false;
                    $description = "";
                    $completed = false;
                }
                $results[$category_no]["tests"][$test_no] = [$title, $description, $this->getTestStatus($pass, $mandatory, $completed)];
                $test_no++;
            }
            $category_no++;
        }

        return $results;
    }

    public function getInfo() {
        $info = getInfo("ilias");
        $info["TestScript"]["correct_working_directory"] = true;
        return $info;
    }

    private function getTargetInfo($info) {
        $targetInfo = getTargetInfo($info);
        return $targetInfo;
    }

    private function getTestStatus($pass, $mandatory, $completed) {
        if(!$completed) return TestStatus::T_STATUS_INCOMPLETE;
        if($pass) return TestStatus::T_STATUS_OK;
        if($mandatory) return TestStatus::T_STATUS_FAIL;
        return TestStatus::T_STATUS_WARN;
    }

}

abstract class TestStatus {
    const T_STATUS_OK = ilLPStatus::LP_STATUS_COMPLETED_NUM;
    const T_STATUS_WARN = ilLPStatus::LP_STATUS_IN_PROGRESS_NUM;
    const T_STATUS_FAIL = ilLPStatus::LP_STATUS_FAILED_NUM;
    const T_STATUS_INCOMPLETE = ilLPStatus::LP_STATUS_NOT_ATTEMPTED_NUM;
}