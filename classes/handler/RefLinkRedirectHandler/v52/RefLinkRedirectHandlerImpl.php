<?php

namespace SRAG\PegasusHelper\handler\RefLinkRedirectHandler\v52;

use ilLink;
use ilObject2;
use ilUtil;
use SRAG\PegasusHelper\authentication\UserTokenAuthenticator;
use SRAG\PegasusHelper\handler\BaseHandler;
use SRAG\PegasusHelper\handler\RefLinkRedirectHandler\RefLinkRedirectHandler;

/**
 * Class TokenChecker handles a specific link to log in
 * the user with a token and redirect him to the wanted page.
 *
 * @author  Nicolas Märchy <nm@studer-raimann.ch>
 * @version 1.1.0
 *
 */
final class RefLinkRedirectHandlerImpl extends BaseHandler implements RefLinkRedirectHandler {

	private static $self_call;

	private $userId;
	private $refId;
	private $view;
	private $token;
	/**
	 * @var UserTokenAuthenticator $authenticator
	 */
	private $authenticator;


	/**
	 * TokenChecker constructor.
	 *
	 * @param UserTokenAuthenticator $authenticator
	 */
	public function __construct(UserTokenAuthenticator $authenticator) { $this->authenticator = $authenticator; }


	public function handle() {
		if(!$this->isHandler())
			parent::next();
		else
			$this->execute();
	}


	/**
	 * @return boolean true if this handler needs to handle the request, otherwise false
	 */
	public function isHandler() {

		$matches = [];

		if (preg_match("/^ilias_app_auth\|(\d+)\|(\d+)\|(.+)\|(.+)$/", $_GET['target'], $matches) === 0) {
			return false;
		}

		$this->userId = $matches[1];
		$this->refId = $matches[2];
		$this->view = $matches[3];
		$this->token = $matches[4];

		return true;
	}


	/**
	 * Checks the request to an valid token,
	 * logs in and redirects the user to the wanted page.
	 *
	 * If the user is already logged in, the user will be redirected.
	 *
	 * The token will always be deleted.
	 */
	public function execute() {

		$this->authenticator->authenticate($this->userId, $this->token);
		$this->redirect();
	}


	/**
	 * Redirects the user to the wanted page.
	 * The wanted page is determined by the ref_id
	 * and the view.
	 *
	 * * view 'default': goto the ref_id
	 * * view 'timeline': goto the timeline of the ref_id
	 *
	 * This methods redirects only once per request to avoid recursive calls.
	 */
	private function redirect() {

		if (!self::$self_call) {
			self::$self_call = true;

			switch ($this->view) {
				case "default":
					$link = ilLink::_getLink($this->refId);
					ilUtil::redirect($link);
					break;
				case "timeline":

					global $ilCtrl;

					$type = ilObject2::_lookupType($this->refId, true);

					$ilCtrl->initBaseClass("ilrepositorygui");
					$ilCtrl->setParameterByClass("ilnewstimelinegui", "ref_id", $this->refId);
					$ilCtrl->setParameterByClass("ilnewstimelinegui", "cmd", "show");

					if ($type === "crs") {
						$link = $ilCtrl->getLinkTargetByClass(["ilrepositorygui", "ilobjcoursegui", "ilnewstimelinegui"]);
						ilUtil::redirect(ilUtil::_getHttpPath(). "/ilias.php" . htmlspecialchars_decode($link));
					} else if ($type === "grp") {
						$link = $ilCtrl->getLinkTargetByClass(["ilrepositorygui", "ilobjgroupgui", "ilnewstimelinegui"]);
						ilUtil::redirect(ilUtil::_getHttpPath(). "/ilias.php" . htmlspecialchars_decode($link));
					}
					break;
			}
		}
	}
}