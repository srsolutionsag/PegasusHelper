<?php

/**
 * classes used to build the test-suite
 */

class TestSuite {
    /** @var TestCategory[] */
    public $categories = [];
    /** @var TestingContext */
    public $context;

    public function __construct($context, $categories = []) {
        $this->context = $context;
        $this->categories = $categories;
    }

    public function run($info, $target_info) {
        foreach ($this->categories as $category) {
            $category->run($info, $target_info, $this);
        }
    }

    public function addCategories($categories) {
        if(is_array($categories)) $this->categories = array_merge($this->categories, $categories);
        else array_push($this->categories, $categories);
    }

    public function getCategoryByTitle($title) {
        foreach ($this->categories as $category) {
            if($category->title === $title) return $category;
        }
        return NULL;
    }
}

class TestCategory {
    /** @var string */
    public $title;
    /** @var Test[] */
    public $tests = [];

    public function __construct($title, $tests = []) {
        $this->title = $title;
        $this->tests = $tests;
    }

    public function run($info, $target_info, $suite) {
        foreach ($this->tests as $key => $test) {
            if (in_array($suite->context, $test->contexts)) {
                $test->run($info, $target_info, $suite);
            } else {
                unset($this->tests[$key]);
            }
        }
        $this->tests = array_values($this->tests);
    }

    public function addTests($tests) {
        if(is_array($tests)) $this->tests = array_merge($this->tests, $tests);
        else array_push($this->tests, $tests);
    }
}

class Test {
    /** @var string */
    public $description;
    /** @var string */
    public $test_fn;
    /** @var boolean */
    public $mandatory;
    /** @var TestingContext[] */
    public $contexts;
    /** @var TestResult */
    public $result;

    public function __construct($description, $test_fn, $mandatory = true, $contexts = [TestingContext::C_CLI, TestingContext::C_ILIAS]) {
        $this->description = $description;
        $this->test_fn = $test_fn;
        $this->mandatory = $mandatory;
        $this->contexts = $contexts;
    }

    public function run($info, $target_info, $suite) {
        try {
            $this->result = call_user_func($this->test_fn, $info, $target_info, $suite);
        } catch (Exception $e) {
            $this->result = new TestResult(ResultState::R_ERROR, $e->getMessage());
        }
    }
}

class TestResult {
    /** @var ResultState */
    public $state;
    /** @var string */
    public $description;

    function __construct($state, $description) {
        $this->state = $state;
        $this->description = $description;
    }
}

abstract class ResultState {
    const R_PASS = 0;
    const R_FAIL = 1;
    const R_MISSING_INFO = 2;
    const R_ERROR = 3;
}

abstract class TestingContext {
    const C_CLI = 0;
    const C_ILIAS = 1;
}

function completeTestResult($pass, $msg) {
    return new TestResult($pass ? ResultState::R_PASS : ResultState::R_FAIL, $msg);
}

function failTestFromMissingInfo($msg = NULL) {
    $msg = "lacking information to perform test" . ($msg ? ": " . $msg : "");
    return new TestResult(ResultState::R_MISSING_INFO, $msg);
}