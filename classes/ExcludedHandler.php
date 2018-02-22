<?php

require_once __DIR__ . '/BaseHandler.php';

/**
 * Class ExcludedHandler
 *
 * The excluded handler checks whether the pegasus helper got excluded or not,
 * and interrupts the chain if necessary.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ExcludedHandler extends BaseHandler {

	public function handle() {

		//if not excluded call next request handler
		if(!$this->isExcluded())
			$this->next();
	}

	/**
	 * Checks the GET parameter {@code target} against a regex.
	 * The param has to start with 'ilias_app'.
	 *
	 * @return bool true, if the request can be excluded from handlers, otherwise false
	 */
	private function isExcluded() {

		return !(isset($_GET['target'])
			&& preg_match("/^ilias_app.*$/", $_GET['target']) === 1);
	}
}