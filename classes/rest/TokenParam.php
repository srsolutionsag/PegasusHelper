<?php

/**
 * Class TokenParam
 *
 * @author  nmaerchy
 * @date    10.10.17
 * @version 0.0.1
 *
 */
class TokenParam {

	/**
	 * @var int
	 */
	private $ttl;
	/**
	 * @var string
	 */
	private $type;


	/**
	 * TokenParam constructor.
	 *
	 * @param int    $ttl
	 * @param string $type
	 */
	public function __construct($ttl, $type) {
		$this->ttl = $ttl;
		$this->type = $type;
	}


	/**
	 * @return int
	 */
	public function getTTL() {
		return $this->ttl;
	}


	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
}