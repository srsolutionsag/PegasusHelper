<?php

namespace SRAG\PegasusHelper\container\provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SRAG\PegasusHelper\authentication\DefaultUserTokenAuthenticator;
use SRAG\PegasusHelper\authentication\UserTokenAuthenticator;

/**
 * Class AuthenticationProvider
 *
 * @package SRAG\PegasusHelper\container\provider
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class AuthenticationProvider implements ServiceProviderInterface
{

    /**
     * @inheritDoc
     */
    public function register(Container $pimple)
    {
        $pimple[UserTokenAuthenticator::class] = function ($c) {
            return new DefaultUserTokenAuthenticator();
        };
    }
}
