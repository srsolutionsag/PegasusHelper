<?php

namespace SRAG\PegasusHelper\rest;

use ilDatabaseException;
use SRAG\PegasusHelper\handler\OAuthManager\v52\OauthManagerImpl;

/**
 * Class RestSetup
 *
 * @author  nmaerchy
 * @date    10.10.17
 * @version 0.0.1
 *
 */
class RestSetup {

	private $clientParams;

	private $host;

	/**
	 * RestSetup constructor.
	 */
	public function __construct() {

		// pattern: 'xxxx.xxxx-xx'
		$api_secret = $this->getRandString(4).".".$this->getRandString(4)."-".$this->getRandString(2);

		$this->clientParams = array( 'id' => null,
            'api_key' => "ilias_pegasus",
            'api_secret' => $api_secret,
            'grant_resource_owner' => 1,
            'refresh_authorization_code' => 1,
            'description' => 'ILIAS Pegasus App');

		global $ilIliasIniFile;
		$this->host = $ilIliasIniFile->readVariable('server', 'http_path');
	}


	public function setupClient() {

		$response = $this->post($this->host."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v1/clients", $this->clientParams);
		$this->handle($response);
	}


	/**
	 * @param $tokenParam TokenParam
	 */
	public function configTTL($tokenParam) {

		$uri = $this->host."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/config/" . $tokenParam->getType();
		$params = array( 'value' => strval($tokenParam->getTTL()));

		$response = $this->put($uri, $params);
		$this->handle($response);
	}

	/**
	 * @param $routeParams RouteParam
	 *
	 * @throws ilDatabaseException
	 */
	public function addRoute($routeParams) {

		// TODO: move this method to this class
		$oauthData = OauthManagerImpl::createAccessToken('apollon');

		// TODO: move this method to this class
		$rest_client_id = OauthManagerImpl::getRestClientId($oauthData['access_token']);
		if(!$rest_client_id) {
			throw new ilDatabaseException("REST Client ".OauthManagerImpl::API_KEY." is not configured");
		}

		$uri = $this->host."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/permission/".$rest_client_id;
		$params = array(
			'pattern' => $routeParams->getPattern(),
			'verb' => $routeParams->getVerb()
		);

		$response = $this->post($uri, $params);
		$this->handle($response);
	}



	/**
	 * @param int $length
	 * @return string
	 */
	private function getRandString($length = 0) {
		$characters = '123456789abcdefghijklmnopqrstuvwxyz';
		$randstring = '';
		for ($i = 0; $i < $length; $i++) {
			$randstring .= $characters[rand(0, strlen($characters))];
		}
		return $randstring;
	}

	private function post($uri, $params) {

		// TODO: move this method to this class
		$oauthData = OauthManagerImpl::createAccessToken('apollon');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_POST, true );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params );

		return curl_exec($ch);
	}

	private function put($uri, $params) {

		// TODO: move this method to this class
		$oauthData = OauthManagerImpl::createAccessToken('apollon');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
		return curl_exec($ch);
	}

	private function handle($response) {

		$arr_result = json_decode($response, true);
		if($arr_result['error'] || $arr_result['message']) {
			throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
		}
	}
}