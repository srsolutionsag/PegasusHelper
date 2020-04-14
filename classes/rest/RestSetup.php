<?php

namespace SRAG\PegasusHelper\rest;

use Exception;
use ilDatabaseException;
use ClientConfiguration;
use SRAG\PegasusHelper\handler\OAuthManager\v52\OauthManagerImpl;

/**
 * Class RestSetup
 *
 * @author  nmaerchy, mschneiter
 * @date    09.04.20
 * @version 0.1.0
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

		$this->clientParams = array(
			'api_key' => "ilias_pegasus",
			'api_secret' => $api_secret,
			'grant_resource_owner' => 1,
			'refresh_resource_owner' => 1,
			'description' => 'ILIAS Pegasus App'
		);

		global $ilIliasIniFile;
		$this->host = $ilIliasIniFile->readVariable('server', 'http_path');
	}

    /**
     * setup the REST client for the plugin if it does not exist yet
     */
	public function setupClient() {
        $id = $this->getClientId();
        if(!isset($id)) ClientConfiguration::createClient($this->clientParams);
	}

    /**
     * delete the REST client for the plugin if it exists
     */
	public function deleteClient() {
	    // first try the old version of the uninstall process without any error messages
        try {
            $this->deleteClientOldVersionWithRequest();
            return;
        } catch (Exception $e) {}

        $id = $this->getClientId();
        if(isset($id)) ClientConfiguration::deleteClient($id);
    }

    /**
     * readout the id from the REST client of the plugin. returns null, if this client does not exist
     */
    private function getClientId() {
        $response = ClientConfiguration::getClients();

        foreach($response as $id => $client)
            if($client["api_key"] === $this->clientParams["api_key"])
                return intval($id);

        return null;
    }

	/**
	 * @param $tokenParam TokenParam
	 */
	public function configTTL($tokenParam) {
        ClientConfiguration::setClientConfig($tokenParam->getType(), strval($tokenParam->getTTL()));
	}

    /**
     * @param $routeParams RouteParam
     *
     * @throws \RESTController\libs\Exceptions\Database
     * @throws \ilException
     * @throws ilDatabaseException
     * @throws \RESTController\core\oauth2_v2\Exceptions\InvalidRequest
     */
	public function addRoute($routeParams) {
        $oauthData = OauthManagerImpl::createAccessToken('apollon');
		$rest_client_id = OauthManagerImpl::getRestClientId($oauthData['access_token']);
		if(!$rest_client_id) {
			throw new ilDatabaseException("REST Client ".OauthManagerImpl::API_KEY." is not configured");
		}

		ClientConfiguration::addClientPermission($rest_client_id, $routeParams->getPattern(), $routeParams->getVerb());
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

    /**
     * for uninstalling the PegasusHelper with an outdated REST version
     * delete the REST client for the plugin if it exists
     */
    public function deleteClientOldVersionWithRequest() {
        $id = $this->getClientId();
        if(isset($id)) {
            $oauthData = OauthManagerImpl::createAccessToken('apollon');

            $uri = $this->host . "/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v1/clients/" . strval($id);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
            $response = curl_exec($ch);
            if (isset($response)) {
                $arr_result = json_decode($response, true);
                if($arr_result['error'] || $arr_result['message']) {
                    throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
                }
            }
        }
    }

}