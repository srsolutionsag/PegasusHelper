<?php

namespace SRAG\PegasusHelper\handler\OAuthManager\v52;

use Exception;
use ilException;
use ilObjUser;
use ilUtil;
use RESTController\core\oauth2_v2\Common;
use RESTController\RESTController;
use SRAG\PegasusHelper\handler\BaseHandler;
use SRAG\PegasusHelper\handler\ChainRequestHandler;

/**
 * Class OauthManager handles an authentication when a user
 * logs in from ILIAS Pegasus app.
 *
 * @author  Nicolas Märchy <nm@studer-raimann.ch>
 * @version 1.0.0
 *
 */
final class OauthManagerImpl extends BaseHandler implements ChainRequestHandler {

	const API_KEY = 'ilias_pegasus';


	public function handle() {
		if($this->isHandler()) {
			$data = $this->authenticate();
			$encodedData = implode('|||', $data);
			$out = '<input type="hidden" name="data" id="data" value="' . $encodedData . '">';
			echo $out;
			die();
		}
		parent::next();
	}

	/**
	 * Checks if the {@code target} GET parameter is set
	 * and if its marked for Oauth of ILIAS Pegasus.
	 *
	 * @return boolean true if this handler needs to handle the request, otherwise false
	 */
	private function isHandler() {

		global $ilUser;

		if ($_GET['target'] != 'ilias_app_oauth2') {
			return false;
		}

		return ($ilUser->getId() > 0 && $ilUser->getId() != ANONYMOUS_USER_ID);
	}


	/**
	 * Authenticates the user and returns data for Oauth2.
	 * The data contains:
	 * [<user_id>, "<username>", "<access_token>", "<refresh_token>"]
	 *
	 * e.g.
	 * [1, "mmuster", "1454NRSM156trs4Nn54rNN45N5654R4R4N541RMN", "46554N5RN654RTN56N56RN4RT4DNR4R4S4"]
	 *
	 * @return array the resulting data
	 */
	private function authenticate() {

		try {
			/** @var $ilUser ilObjUser */
			global $ilUser;

			$oauthData = self::createAccessToken(self::API_KEY);

			$data = array(
				$ilUser->getId(),
				$ilUser->getLogin(),
				isset($oauthData['access_token']) ? $oauthData['access_token'] : '',
				isset($oauthData['refresh_token']) ? $oauthData['refresh_token'] : '',
			);

			return $data;

		} catch (Exception $e) {
		}
	}


	/**
	 * Creates an access token by interacting with ILIAS REST plugin.
	 * The resulting data contains:
	 * [
	 *  "access_token" => "<access_token>",
	 *  "refresh_token" => "<refresh_token>"
	 * ]
	 *
	 * @param $api_key string the api key for the REST request
	 *
	 * @return array the resulting data
	 *
	 * @throws \RESTController\core\oauth2_v2\Exceptions\InvalidRequest
	 */
	public static function createAccessToken($api_key) {
		global $ilUser;
		$restControllerFilePath = './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/RESTController/RESTController.php';
		require_once($restControllerFilePath);
		RESTController::registerAutoloader();
		/*
		 * the RESTController needs to be initialized, because of its constructor,
		 * which performs several operations to prepare ILIAS
		 */
		new RESTController();
		$client = Common::CheckApiKey($api_key);
		$userId = $ilUser->getId();
		$withRefresh = $client->getKey('refresh_resource_owner');
		$iliasClient = $_COOKIE['ilClientId'];
		$oauthData = Common::GetResponse($api_key, $userId, $iliasClient, null, $withRefresh);

		return $oauthData;
	}

	/**
	 * Executes a curl request to get the client id.
	 *
	 * @param $access_token string a valid access token for ILIAS REST
	 * @return string|boolean false, if no client id is found, otherwise the client id
     * @throws ilException throws when the request to the REST api fails. The given error code corresponds to the {@see curl_errno()}.
	 */
	public static function getRestClientId($access_token) {
		global $ilIliasIniFile;
		$ch = curl_init();
		$HOST = $ilIliasIniFile->readVariable('server', 'http_path');

		curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v1/clients");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $access_token, 'Content-Type: text/plain']);

		$result = curl_exec($ch);
		if ($result === false) {
			$cError = curl_error($ch);
			$errorNumber = curl_errno($ch);
			curl_close($ch);
			throw new ilException("Failed to fetch rest client id: Code: '$errorNumber' with message: '$cError'", $cError);
		}
		curl_close($ch);
		$arr_result = json_decode($result, true);

		foreach($arr_result as $result) {
			if($result['api_key'] == self::API_KEY) {
				return $result['id'];
			}
		}

		ilUtil::sendFailure('API KEY '.self::API_KEY.' not Found', true);
		return false;
	}
}