<?php

require_once __DIR__ . '/BaseHandler.php';

/**
 * Class LoginPageManager handles the display of a specific Login Page
 *
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @version 1.0.0
 *
 */
final class LoginPageManager extends BaseHandler {


	/**
	 * Checks if the {@code target} GET parameter is set
	 * and if its marked for login_page of ILIAS Pegasus.
	 *
	 * @return boolean true if this handler needs to handle the request, otherwise false
	 */
	private function isHandler() {

		if (strcmp($_GET['target'], 'ilias_app_login_page') === 0) {
			return true;
		}

		return false;
	}

	public function handle() {
		if(!$this->isHandler())
			$this->next();
		else {
			$script = ILIAS_HTTP_PATH."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/templates/pegasus_login_page.html";
			header("Location: ".$script);
			exit();
		}
	}
}