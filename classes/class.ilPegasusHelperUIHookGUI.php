<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Services/UIComponent/classes/class.ilUIHookPluginGUI.php');

/**
 * Class ilPegasusHelperUIHookGUI
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 * @author Martin Studer <ms@studer-raimann.ch>
 */
class ilPegasusHelperUIHookGUI extends ilUIHookPluginGUI
{
    const API_KEY = 'ilias_pegasus';

    public function gotoHook()
    {
        if (!$this->checkConditions()) {
            return false;
        }
        // User has a valid session, create an access token and redirect to app
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
            $encodedData = implode('|||', $data);
            $out = '<input type="hidden" name="data" id="data" value="' . $encodedData . '">';
            echo $out;
            die();
        } catch (Exception $e) {
        }
    }


    /**
     * User has a valid session, create an access token
     *
     * @param $api_key
     * @return array
     */
    public static function createAccessToken($api_key) {
        global $ilUser;
        $appDirectory = './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/RESTController/';
        require_once($appDirectory . 'RESTController.php');
        \RESTController\RESTController::registerAutoloader();
        $restController = new \RESTController\RESTController();
        $client = \RESTController\core\oauth2_v2\Common::CheckApiKey($api_key);
        $userId = $ilUser->getId();
        $withRefresh = $client->getKey('refresh_resource_owner');
        $iliasClient = $_COOKIE['ilClientId'];
        $oauthData = \RESTController\core\oauth2_v2\Common::GetResponse($api_key, $userId, $iliasClient, null, $withRefresh);

        return $oauthData;
    }


    /**
     * @param $access_token
     * @return mixed
     */
    public static function getRestClientId($access_token) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, ilUtil::_getHttpPath()."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v1/clients");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $access_token));

        $result = curl_exec($ch);
        $arr_result = json_decode($result, true);

        foreach($arr_result as $result) {
            if($result['api_key'] == ilPegasusHelperUIHookGUI::API_KEY) {
                return $result['id'];
            }
        }

        ilUtil::sendFailure('API KEY '.ilPegasusHelperUIHookGUI::API_KEY.' not Found', true);
        return false;
    }


    /**
     * @return bool
     */
    protected function checkConditions()
    {
        global $ilUser;

        if (!isset($_GET['target'])) {
            return false;
        }
        if ($_GET['target'] != 'ilias_app_oauth2') {
            return false;
        }

        return ($ilUser->getId() > 0 && $ilUser->getId() != ANONYMOUS_USER_ID);
    }

}