<?php

/**
 * Class OauthManager handles an authentication when a user
 * logs in from ILIAS Pegasus app.
 *
 * @author  Nicolas Märchy <nm@studer-raimann.ch>
 * @version 1.0.0
 *
 */
class OauthManager {

	const API_KEY = 'ilias_pegasus';

	/**
	 * Checks if the {@code target} GET parameter is set
	 * and if its marked for Oauth of ILIAS Pegasus.
	 *
	 * @return boolean true if this handler needs to handle the request, otherwise false
	 */
	public function isHandler() {

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
	public function authenticate() {

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
	 */
	public static function createAccessToken($api_key) {
		global $ilUser;
		$appDirectory = './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/RESTController/';
		require_once($appDirectory . 'RESTController.php');
		\RESTController\RESTController::registerAutoloader();
		/*
		 * the RESTController needs to be initialized, because of its constructor,
		 * which performs several operations to prepare ILIAS
		 */
		$restController = new \RESTController\RESTController();
		$client = \RESTController\core\oauth2_v2\Common::CheckApiKey($api_key);
		$userId = $ilUser->getId();
		$withRefresh = $client->getKey('refresh_resource_owner');
		$iliasClient = $_COOKIE['ilClientId'];
		$oauthData = \RESTController\core\oauth2_v2\Common::GetResponse($api_key, $userId, $iliasClient, null, $withRefresh);

		return $oauthData;
	}

	/**
	 * Executes a curl request to get the client id.
	 *
	 * @param $access_token string a valid access token for ILIAS REST
	 * @return string|boolean false, if no client id is found, otherwise the client id
	 */
	public static function getRestClientId($access_token) {
		global $ilIliasIniFile;
		$ch = curl_init();
		$HOST = $ilIliasIniFile->readVariable('server', 'http_path');

		curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v1/clients");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));

		$result = curl_exec($ch);
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