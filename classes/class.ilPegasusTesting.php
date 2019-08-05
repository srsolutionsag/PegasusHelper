<?php

require_once __DIR__ . "/../bootstrap.php";
include_once __DIR__ . "/../testing/includefile.php";

/**
 * Class ilPegasusHelperTesting runs the test-suite for the PegasusHelper- and REST-plugins
 */
final class ilPegasusHelperTesting {

    public function run() {
        $set_global_ilias = !isset($GLOBALS["ilias"]);
        if($set_global_ilias) $GLOBALS["ilias"] = true;

        $info = getInfo();
        $targetInfo = getTargetInfo($info);
        $suite = getTestSuite(TestingContext::C_ILIAS);
        $suite->run($info, $targetInfo);

        if($set_global_ilias) unset($GLOBALS["ilias"]);
        return $this->getResults($suite);
    }

    /**
     * @param $suite TestSuite
     */
    private function getResults($suite) {
        $results = [];
        foreach ($suite->categories as $category) {
            array_push($results, ["category" => $category->title]);

            foreach ($category->tests as $test) {
                array_push($results, [
                    "test" => $test->description,
                    "info" => $test->result->description,
                    "status" => $this->getTestStatus($test->result->state, $test->mandatory)
                ]);
            }
        }

        return $results;
    }

    /**
     * @param $state ResultState
     * @param $mandatory boolean
     */
    private function getTestStatus($state, $mandatory) {
        if($state === ResultState::R_PASS) return ilPegasusTestingStatus::T_STATUS_OK;
        if($state === ResultState::R_FAIL && $mandatory) return ilPegasusTestingStatus::T_STATUS_FAIL;
        if($state === ResultState::R_MISSING_INFO || $state === ResultState::R_ERROR) return ilPegasusTestingStatus::T_STATUS_INCOMPLETE;
        return ilPegasusTestingStatus::T_STATUS_WARN;
    }

}