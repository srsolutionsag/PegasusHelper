<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

use SRAG\PegasusHelper\container\PegasusHelperContainer;
use SRAG\PegasusHelper\handler\ChainRequestHandler;
use SRAG\PegasusHelper\handler\ExcludedHandler\ExcludedHandler;
use SRAG\PegasusHelper\handler\LoginPageHandler\LoginPageManager;
use SRAG\PegasusHelper\handler\NewsLinkRedirectHandler\NewsLinkRedirectHandler;
use SRAG\PegasusHelper\handler\OAuthManager\OAuthManager;
use SRAG\PegasusHelper\handler\OAuthManager\v52\OauthManagerImpl;
use SRAG\PegasusHelper\handler\RefLinkRedirectHandler\RefLinkRedirectHandler;
use SRAG\PegasusHelper\handler\RefLinkRedirectHandler\v52\RefLinkRedirectHandlerImpl;
use SRAG\PegasusHelper\handler\ResourceLinkHandler\ResourceLinkHandler;

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class ilPegasusHelperUIHookGUI handles different kind of requests,
 * that are needed for ILIAS Pegasus app.
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 * @author Martin Studer <ms@studer-raimann.ch>
 * @author Nicolas Märchy <nm@studer-raimann.ch>
 */
final class ilPegasusHelperUIHookGUI extends ilUIHookPluginGUI
{

    /**
     * @var ChainRequestHandler $handlers
     */
    private $handlers;

    /**
     * ilPegasusHelperUIHookGUI constructor.
     */
    public function __construct()
    {
        $this->handlers = PegasusHelperContainer::resolve(ExcludedHandler::class);
        $this->handlers->add(PegasusHelperContainer::resolve(OauthManager::class));
        $this->handlers->add(PegasusHelperContainer::resolve(RefLinkRedirectHandler::class));
        $this->handlers->add(PegasusHelperContainer::resolve(NewsLinkRedirectHandler::class));
        $this->handlers->add(PegasusHelperContainer::resolve(LoginPageManager::class));
        $this->handlers->add(PegasusHelperContainer::resolve(ResourceLinkHandler::class));
    }

    /**
     * Checks, if the request is a specific request of ILIAS Pegasus.
     * If its a specific request, the appropriate handler is called.
     *
     * @see OauthManagerImpl
     * @see RefLinkRedirectHandlerImpl
     *
     * If the {@link OauthManager->authenticate()} is executed, this
     * method will return the data for Oauth2 as a hidden input in the response body.
     *
     * If the {@link TokenChecker->execute()} is executed, the user will
     * be redirected the the appropriate page.
     *
     * @param       $a_comp
     * @param       $a_part
     * @param array $a_par
     *
     * @return array
     */
    public function getHTML($a_comp, $a_part, $a_par = [])
    {
        $this->handlers->handle();
        return parent::getHTML($a_comp, $a_part, $a_par);
    }
}
