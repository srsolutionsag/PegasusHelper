<?php

namespace SRAG\PegasusHelper\handler\NewsLinkRedirectHandler\v52;

use ilCtrl;
use SRAG\PegasusHelper\authentication\UserTokenAuthenticator;
use SRAG\PegasusHelper\handler\BaseHandler;

/**
 * Class NewsLinkRedirectHandler
 *
 * The news link redirect handler, redirects to the news views of the
 * currently authenticated user.
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class NewsLinkRedirectHandlerImpl extends BaseHandler
{
    private static $self_call;

    private $userId;
    private $newsId;
    private $newsContext;
    private $token;
    /**
     * @var UserTokenAuthenticator $authenticator
     */
    private $authenticator;
    /**
     * @var ilCtrl $controlFlow
     */
    private $controlFlow;


    /**
     * NewsLinkRedirectHandler constructor.
     *
     * @param UserTokenAuthenticator $authenticator
     * @param ilCtrl                 $controlFlow
     */
    public function __construct(UserTokenAuthenticator $authenticator, ilCtrl $controlFlow)
    {
        $this->authenticator = $authenticator;
        $this->controlFlow = $controlFlow;
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

        if (preg_match("/^ilias_app_news\|(\d+)\|(\d+)\|(\d+)\|(.+)$/", $_GET['target'], $matches) === 0) {
            return false;
        }

        $this->userId = $matches[1];
        $this->newsId = $matches[2];
        $this->newsContext = $matches[3];
        $this->token = $matches[4];

        return true;
    }


    /**
     * Checks the request to an valid token,
     * logs in and redirects the user to the news page.
     *
     * The token will always be deleted.
     */
    public function execute()
    {
        $this->authenticator->authenticate($this->userId, $this->token);
        $this->redirect();
    }


    /**
     * Redirects the user to the personal news page, with
     * the correct news selected.
     *
     * This methods redirects only once per request to avoid recursive calls.
     */
    private function redirect()
    {
        if (!self::$self_call) {
            self::$self_call = true;

            $this->controlFlow->initBaseClass("ilPersonalDesktopGUI");
            $this->controlFlow->setTargetScript('ilias.php');
            $this->controlFlow->setParameterByClass("ilpdnewsblockgui", "news_id", $this->newsId);
            $this->controlFlow->setParameterByClass("ilpdnewsblockgui", "news_context", $this->newsContext);
            $this->controlFlow->setParameterByClass("ilpdnewsblockgui", "block_type", "pdnews");
            $this->controlFlow->setParameterByClass("ilpdnewsblockgui", "col_side", "left");

            $this->controlFlow->redirectByClass(["ilPersonalDesktopGUI", "ilColumnGUI", "ilpdnewsblockgui"], "showNews");
        }
    }
}
