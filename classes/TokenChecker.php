<?php

require_once ('./Services/Link/classes/class.ilLink.php');
require_once ('./Services/Authentication/classes/class.ilSession.php');
require_once ('./Services/User/classes/class.ilObjUser.php');
require_once ('./Services/Utilities/classes/class.ilUtil.php');

/**
 * Class TokenChecker
 *
 * @author  Nicolas Märchy <nm@studer-raimann.ch>
 * @version 0.0.1
 *
 */
class TokenChecker {

	const USR_ID_GLOBAL = 'AccountId';
	const USR_ID_AUTHSESSION = '_authsession_user_id';
	const USR_ID_REGISTERED = 'registered';
	const USR_ID_USERNAME = 'username';


	private $userId;
	private $refId;
	private $token;

	/**
	 * @return boolean true if this handler needs to handle the request, otherwise false
	 */
	function isHandler() {

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
	function execute() {

		$link = ilLink::_getLink($this->refId);


		global $ilUser;



		ilSession::setClosingContext(ilSession::SESSION_CLOSE_EXPIRE);

		try {
			session_destroy();
		} catch(Exception $e) {
		}

		session_start();

		$ilUser->setId($this->userId);
		$ilUser->read();

		$session =& $_SESSION["_auth__authhttp".md5(CLIENT_ID)];
		$session[self::USR_ID_GLOBAL] = $ilUser->getId();
		$session[self::USR_ID_AUTHSESSION] = $ilUser->getId();
		$session[self::USR_ID_REGISTERED] = true;
		$session[self::USR_ID_USERNAME] = $ilUser->getLogin();

//		echo $link;
//		die();
//		ilUtil::redirect($link);
//
		header("Location: $link");
//		exit();

	}
}