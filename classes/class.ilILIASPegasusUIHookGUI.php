<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Services/UIComponent/classes/class.ilUIHookPluginGUI.php');

/**
 * Class ilILIASPegasusUIHookGUI
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ilILIASPegasusUIHookGUI extends ilUIHookPluginGUI
{

    public function gotoHook()
    {
        if (!$this->checkConditions()) {
            return false;
        }
        // User has a valid session, create an access token and redirect to app
        try {
            /** @var $ilUser ilObjUser */
            global $ilUser;
            $appDirectory = './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/RESTController/';
            require_once($appDirectory . 'RESTController.php');
            \RESTController\RESTController::registerAutoloader();
            $restController = new \RESTController\RESTController();
            $client = \RESTController\core\oauth2_v2\Common::CheckApiKey('ilias_pegasus');
            $userId = $ilUser->getId();
            $withRefresh = $client->getKey('refresh_resource_owner');
            $iliasClient = $_COOKIE['ilClientId'];
            $oauthData = \RESTController\core\oauth2_v2\Common::GetResponse('ilias_pegasus', $userId, $iliasClient, null, $withRefresh);
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
     * @return bool
     */
    protected function checkConditions()
    {
        global $ilUser;

        if (!isset($_GET['target'])) {
            return false;
        }
        if ($_GET['target'] != 'ilias_pegasus_oauth2') {
            return false;
        }

        return ($ilUser->getId() > 0 && $ilUser->getId() != ANONYMOUS_USER_ID);
    }

}