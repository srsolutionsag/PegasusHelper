<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Services/UIComponent/classes/class.ilUIHookPluginGUI.php');
require_once(__DIR__ . '/TokenChecker.php');
require_once(__DIR__ . '/OauthManager.php');

/**
 * Class ilPegasusHelperUIHookGUI handles different kind of requests,
 * that are needed for ILIAS Pegasus app.
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 * @author Martin Studer <ms@studer-raimann.ch>
 * @author Nicolas Märchy <nm@studer-raimann.ch>
 */
class ilPegasusHelperUIHookGUI extends ilUIHookPluginGUI
{
    const API_KEY = 'ilias_pegasus';

    private $tokenChecker;
    private $outhManager;

	/**
	 * ilPegasusHelperUIHookGUI constructor.
	 */
	public function __construct() {
		$this->tokenChecker = new TokenChecker();
		$this->outhManager = new OauthManager();
	}

	/**
	 * Checks, if the request is a specific request of ILIAS Pegasus.
	 * If its a specific request, the appropriate handler is called.
	 *
	 * @see OauthManager
	 * @see TokenChecker
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
	public function getHTML($a_comp, $a_part, $a_par = array()) {

		switch (true) {
			case $this->isExcluded():
				return parent::getHTML($a_comp, $a_part, $a_par);
			case $this->outhManager->isHandler():
				$data = $this->outhManager->authenticate();
				$encodedData = implode('|||', $data);
				$out = '<input type="hidden" name="data" id="data" value="' . $encodedData . '">';
				echo $out;
				die();
				break;
			case $this->tokenChecker->isHandler():
				$this->tokenChecker->execute();
				break;
			default:
				return parent::getHTML($a_comp, $a_part, $a_par);
		}
	}

	private function isExcluded() {

		return !(isset($_GET['target'])
			&& preg_match("/^ilias_app.*$/", $_GET['target']) === 1);
	}


    /**
     * Delegates to {@link OauthManager::createAccessToken}.
     * This method is only needed by the dbupdate.
     *
     * @param $api_key string the api key for the REST request
     *
     * @return array the resulting data
     */
    public static function createAccessToken($api_key) {
        return OauthManager::createAccessToken($api_key);
    }


    /**
     * Delegates to {@link OauthManager::getRestClientId}.
     * This method is only needed by the dbupdate.
     *
     * @param $access_token string a valid access token for ILIAS REST
     * @return string|boolean false, if no client id is found, otherwise the client id
     */
    public static function getRestClientId($access_token) {
        return OauthManager::getRestClientId($access_token);
    }
}