<?php

namespace SRAG\PegasusHelper\container;

use ILIAS\DI\Container;
use SRAG\PegasusHelper\container\exception\DependencyResolutionException;
use SRAG\PegasusHelper\container\provider\AuthenticationProvider;
use SRAG\PegasusHelper\container\provider\Ilias52RequestHandlerProvider;
use SRAG\PegasusHelper\container\provider\Ilias53RequestHandlerProvider;
use SRAG\PegasusHelper\container\provider\Ilias6RequestHandlerProvider;

/**
 * Class PegasusHelperContainer
 *
 * @package SRAG\PegasusHelper\container
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class PegasusHelperContainer {
	/**
	 * @var Container $container
	 */
	private static $container;


	/**
	 * Bootstraps the plugin dependency container, with all service providers.
	 * This method requires an registered autoloader and
	 * the already bootstrapped ILIAS DI container.
	 *
	 * @return void
	 */
	public static function bootstrap() {
		static::$container = $GLOBALS['DIC'];

		static::$container->register(new AuthenticationProvider());
		if (version_compare(ILIAS_VERSION_NUMERIC, '6.0', '>=')) {
            static::$container->register(new Ilias6RequestHandlerProvider());
        }
		else if(version_compare(ILIAS_VERSION_NUMERIC, '5.3', '>=')) {
			static::$container->register(new Ilias53RequestHandlerProvider());
		}
		else if(version_compare(ILIAS_VERSION_NUMERIC, '5.2', '>=')) {
			static::$container->register(new Ilias52RequestHandlerProvider());
		}
		else {
			throw new DependencyResolutionException('The pegasus helper plugin has no provider for the current ILIAS version.');
		}
	}


	/**
	 * @param string $class
	 * @return object
	 */
	public static function resolve($class) {
		if(!static::$container->offsetExists($class))
			throw new DependencyResolutionException("The class \"$class\" was not found.");

		return static::$container[$class];
	}
}