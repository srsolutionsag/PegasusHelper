<?php

/**
 * Class LoginPageManager handles the display of a specific Login Page
 *
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @version 1.0.0
 *
 */
class LoginPageManager {


	/**
	 * Checks if the {@code target} GET parameter is set
	 * and if its marked for login_page of ILIAS Pegasus.
	 *
	 * @return boolean true if this handler needs to handle the request, otherwise false
	 */
	public function isHandler() {

		global $ilUser;

		if ($_GET['target'] == 'ilias_app_login_page') {
			return true;
		}

		return false;
	}
}