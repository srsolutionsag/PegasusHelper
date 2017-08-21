<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Services/UIComponent/classes/class.ilUIHookPluginGUI.php');
require_once(__DIR__ . '/TokenChecker.php');
require_once(__DIR__ . '/OauthManager.php');

/**
 * Class ilPegasusHelperUIHookGUI
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


	function getHTML($a_comp, $a_part, $a_par = array()) {

		switch (true) {
			case $this->outhManager->isHandler():
				$data = $this->outhManager->authenticate();
				$encodedData = implode('|||', $data);
				$out = '<input type="hidden" name="data" id="data" value="' . $encodedData . '">';
				echo $out;
				die();
				break;
			case $this->tokenChecker->isHandler():
				break;
			default:
				return parent::getHTML($a_comp, $a_part, $a_par);
		}
	}


    /**
     * User has a valid session, create an access token
     *
     * @param $api_key
     * @return array
     */
    public static function createAccessToken($api_key) {
        return OauthManager::createAccessToken($api_key);
    }


    /**
     * @param $access_token
     * @return mixed
     */
    public static function getRestClientId($access_token) {
        return OauthManager::getRestClientId($access_token);
    }
}