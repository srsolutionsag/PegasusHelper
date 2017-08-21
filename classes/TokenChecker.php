<?php

require_once('./Services/Link/classes/class.ilLink.php');
require_once('./Services/Authentication/classes/class.ilSession.php');
require_once('./Services/User/classes/class.ilObjUser.php');
require_once('./Services/Utilities/classes/class.ilUtil.php');

/**
 * Class TokenChecker
 *
 * @author  Nicolas Märchy <nm@studer-raimann.ch>
 * @version 0.0.1
 *
 */
class TokenChecker {

	private $userId;
	private $refId;
	private $token;


	/**
	 * @return boolean true if this handler needs to handle the request, otherwise false
	 */
	public function isHandler() {

		if ($_SERVER['SCRIPT_NAME'] != "/goto.php") {
			return false;
		}

		$matches = array();

		if (preg_match("/^ilias_app_auth\|(\d+)\|(\d+)\|(.+)$/", $_GET['target'], $matches) === 0) {
			return false;
		}

		$this->userId = $matches[1];
		$this->refId = $matches[2];
		$this->token = $matches[3];

		return true;
	}


	/**
	 * Checks the request to an valid token and redirects
	 * the user to the wanted page.
	 */
	public function execute() {
		static $self_call;

		global $DIC;
		/**
		 * @var $ilAuthSession ilAuthSession
		 */
		$ilAuthSession = $DIC['ilAuthSession'];
		$ilAuthSession->init();
		$ilAuthSession->regenerateId();
		$ilAuthSession->setUserId($this->userId);
		$ilAuthSession->setAuthenticated(true, $this->userId);

		if (!$self_call) {
			$self_call = true;
			$link = ilLink::_getLink($this->refId);
			ilUtil::redirect($link);
		}
	}
}