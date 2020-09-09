<?php

namespace SRAG\PegasusHelper\container\provider;

use ilCtrl;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SRAG\PegasusHelper\authentication\UserTokenAuthenticator;
use SRAG\PegasusHelper\handler\ExcludedHandler\ExcludedHandler;
use SRAG\PegasusHelper\handler\ExcludedHandler\v52\ExcludedHandlerImpl;
use SRAG\PegasusHelper\handler\LoginPageHandler\LoginPageManager;
use SRAG\PegasusHelper\handler\LoginPageHandler\v52\LoginPageManagerImpl;
use SRAG\PegasusHelper\handler\NewsLinkRedirectHandler\NewsLinkRedirectHandler;
use SRAG\PegasusHelper\handler\NewsLinkRedirectHandler\v52\NewsLinkRedirectHandlerImpl;
use SRAG\PegasusHelper\handler\OAuthManager\OAuthManager;
use SRAG\PegasusHelper\handler\OAuthManager\v52\OauthManagerImpl;
use SRAG\PegasusHelper\handler\RefLinkRedirectHandler\RefLinkRedirectHandler;
use SRAG\PegasusHelper\handler\RefLinkRedirectHandler\v52\RefLinkRedirectHandlerImpl;
use SRAG\PegasusHelper\handler\ResourceLinkHandler\ResourceLinkHandler;
use SRAG\PegasusHelper\handler\ResourceLinkHandler\v52\ResourceLinkHandlerImpl;

/**
 * Class PegasusHelperIlias52Provider
 *
 * @package SRAG\PegasusHelper\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class Ilias52RequestHandlerProvider implements ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple[ExcludedHandler::class] = $pimple->factory(function ($c) {
            return new ExcludedHandlerImpl();
        });

        $pimple[LoginPageManager::class] = $pimple->factory(function ($c) {
            return new LoginPageManagerImpl();
        });

        $pimple[NewsLinkRedirectHandler::class] = $pimple->factory(function ($c) {
            return new NewsLinkRedirectHandlerImpl($c[UserTokenAuthenticator::class], $c[ilCtrl::class]);
        });

        $pimple[OAuthManager::class] = $pimple->factory(function ($c) {
            return new OauthManagerImpl();
        });

        $pimple[RefLinkRedirectHandler::class] = $pimple->factory(function ($c) {
            return new RefLinkRedirectHandlerImpl($c[UserTokenAuthenticator::class]);
        });

        $pimple[ResourceLinkHandler::class] = $pimple->factory(function ($c) {
            return new ResourceLinkHandlerImpl($c[UserTokenAuthenticator::class]);
        });
    }
}
