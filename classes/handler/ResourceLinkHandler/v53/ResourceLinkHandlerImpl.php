<?php

namespace SRAG\PegasusHelper\handler\ResourceLinkHandler\v53;

use ILIAS\HTTP\Cookies\CookieFactory;
use ILIAS\HTTP\GlobalHttpState;
use ilWebAccessCheckerDelivery;
use SRAG\PegasusHelper\authentication\UserTokenAuthenticator;
use SRAG\PegasusHelper\handler\BaseHandler;
use SRAG\PegasusHelper\handler\ResourceLinkHandler\ResourceLinkHandler;

/**
 * Class ResourceLinkHandler
 *
 * Authenticates the user for ILIAS data resources.
 * For example ./data/default/xsrl_326/xsrl5a8b00e14abd06.46960540.mp4
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */
final class ResourceLinkHandlerImpl extends BaseHandler implements ResourceLinkHandler {

	private static $self_call = false;

	const GET_TOKEN = 'token';
	const GET_USER = 'user';
	const GET_TARGET = 'target';

	/**
	 * @var UserTokenAuthenticator $authenticator
	 */
	private $authenticator;
	/**
	 * @var CookieFactory $cookieFactory
	 */
	private $cookieFactory;
	/**
	 * @var GlobalHttpState $http
	 */
	private $http;


	/**
	 * NewsLinkRedirectHandler constructor.
	 *
	 * @param UserTokenAuthenticator $authenticator
	 * @param CookieFactory          $cookieFactory
	 * @param GlobalHttpState        $globalHttpState
	 */
	public function __construct(UserTokenAuthenticator $authenticator, CookieFactory $cookieFactory, GlobalHttpState $globalHttpState) {
		$this->authenticator = $authenticator;
		$this->cookieFactory = $cookieFactory;
		$this->http = $globalHttpState;
	}


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
		return
			array_key_exists('token', $_GET) &&
			array_key_exists('user', $_GET) &&
			array_key_exists('target', $_GET) &&
			filter_input(INPUT_GET, self::GET_TARGET, FILTER_SANITIZE_STRING) === 'ilias_app_resource';
	}


	/**
	 * Checks the request to an valid token,
	 * logs in and delegates the request to the ILIAS web access checker
	 * which delivers the file on its own.
	 *
	 * The token will always be deleted.
	 */
	public function execute() {

		//handle the request only one time
		if (!self::$self_call) {
			self::$self_call = true;

			//set CORS header
			$response = $this->http->response()
				->withHeader('Access-Control-Allow-Origin', '*')
				->withHeader('Access-Control-Allow-Headers', '*')
				->withHeader('Access-Control-Allow-Methods', 'GET')
				->withHeader('Access-Control-Max-Age', '600');

			$this->http->saveResponse($response);

			$token = filter_input(INPUT_GET, self::GET_TOKEN, FILTER_SANITIZE_STRING);
			$user = filter_input(INPUT_GET, self::GET_USER, FILTER_VALIDATE_INT, [ "min_range" => 0, "default" => 0 ]);
			$this->authenticator->authenticate($user, $token);
			$this->invokeWebAccessChecker();
		}
	}


	/**
	 * Invokes the ILIAS web access checker.
	 * The WAC will finish the delivary process by it self no
	 * further action is possible after this method.
	 */
	private function invokeWebAccessChecker() {
		require_once('./Services/WebAccessChecker/classes/class.ilWebAccessCheckerDelivery.php');
		ilWebAccessCheckerDelivery::run($this->http, $this->cookieFactory);
	}
}