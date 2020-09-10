<?php

namespace SRAG\PegasusHelper\handler\RefLinkRedirectHandler\v54;

use ilLink;
use ilObject2;
use ilUtil;
use SRAG\PegasusHelper\authentication\UserTokenAuthenticator;
use SRAG\PegasusHelper\handler\BaseHandler;
use SRAG\PegasusHelper\handler\RefLinkRedirectHandler\RefLinkRedirectHandler;
use ilCtrl;

/**
 * Class TokenChecker handles a specific link to log in
 * the user with a token and redirect him to the wanted page.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 * @version 1.1.0
 *
 */
final class RefLinkRedirectHandlerImpl extends BaseHandler implements RefLinkRedirectHandler
{
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
     * @var ilCtrl $ctrl
     */
    private $ctrl;

    /**
     * TokenChecker constructor.
     * @param UserTokenAuthenticator $authenticator
     * @param ilCtrl                 $ctrl
     */
    public function __construct(UserTokenAuthenticator $authenticator, ilCtrl $ctrl)
    {
        $this->authenticator = $authenticator;
        $this->ctrl = $ctrl;
    }


    public function handle()
    {
        if (!$this->isHandler()) {
            parent::next();
        } else {
            $this->execute();
        }
    }


    /**
     * @return boolean true if this handler needs to handle the request, otherwise false
     */
    public function isHandler()
    {
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
    public function execute()
    {
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
    private function redirect()
    {
        if (!self::$self_call) {
            self::$self_call = true;

            switch ($this->view) {
                case "default":
                    $link = ilLink::_getLink($this->refId);
                    $this->ctrl->redirectToURL($link);
                    break;
                case "timeline":
                    $type = ilObject2::_lookupType($this->refId, true);

                    $this->ctrl->initBaseClass("ilrepositorygui");
                    $this->ctrl->setParameterByClass("ilnewstimelinegui", "ref_id", $this->refId);
                    $this->ctrl->setParameterByClass("ilnewstimelinegui", "cmd", "show");

                    if ($type === "crs") {
                        $link = $this->ctrl->getLinkTargetByClass(["ilrepositorygui", "ilobjcoursegui", "ilnewstimelinegui"]);
                        $this->ctrl->redirectToURL(ilUtil::_getHttpPath() . "/" . htmlspecialchars_decode($link));
                    } elseif ($type === "grp") {
                        $link = $this->ctrl->getLinkTargetByClass(["ilrepositorygui", "ilobjgroupgui", "ilnewstimelinegui"]);
                        $this->ctrl->redirectToURL(ilUtil::_getHttpPath() . "/" . htmlspecialchars_decode($link));
                    }
                    break;
            }
        }
    }
}
