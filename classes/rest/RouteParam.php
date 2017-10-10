<?php

/**
 * Class RouteParam
 *
 * @author  nmaerchy
 * @date    10.10.17
 * @version 0.0.1
 *
 */
class RouteParam {

	private $pattern;
	private $verb;

	/**
	 * RouteParam constructor.
	 *
	 * @param $pattern
	 * @param $verb
	 */
	public function __construct($pattern, $verb) {
		$this->pattern = $pattern;
		$this->verb = $verb;
	}


	/**
	 * @return mixed
	 */
	public function getPattern() {
		return $this->pattern;
	}


	/**
	 * @return mixed
	 */
	public function getVerb() {
		return $this->verb;
	}
}