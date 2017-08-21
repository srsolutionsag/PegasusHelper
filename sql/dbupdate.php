<#1>
<?php
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    /**
     * @param int $length
     * @return string
     */
    function getRandString($length = 0) {
        $characters = '123456789abcdefghijklmnopqrstuvwxyz';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }
    //'xxxx.xxxx-xx'
    $api_secret = getRandString(4).".".getRandString(4)."-".getRandString(2);

    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $params = array( 'id' => null,
                            'api_key' => "ilias_pegasus",
                            'api_secret' => $api_secret,
                            'cert_serial' => null,
                            'cert_issuer' => null,
                            'cert_subject' => null,
                            'redirect_uri' => null,
                            'consent_message' => null,
                            'client_credentials_userid' => 0,
                            'grant_client_credentials' => 1,
                            'grant_authorization_code' => 1,
                            'grant_implicit' => 1,
                            'grant_resource_owner' => 1,
                            'refresh_authorization_code' => 1,
                            'refresh_resource_owner' => 1,
                            'grant_bridge' => null,
                            'ips' => null,
                            'users' => null,
                            'scopes' => null,
                            'description' => 'ILIAS Pegasus App');
	global $ilIliasIniFile;
	$HOST = $ilIliasIniFile->readVariable('server', 'http_path');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v1/clients");
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
    $result = curl_exec($ch);

    $arr_result = json_decode($result, true);
    if($arr_result['error'] || curl_error($ch)) {
        throw new ilDatabaseException($arr_result['error']['message']);
    }

?>
<#2>
<?php
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $params = array( 'value' => '3600000');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, ilUtil::_getHttpPath()."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/config/access_token_ttl");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
    $result = curl_exec($ch);
    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>
<#3>
<?php
	global $ilIliasIniFile;
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $params = array( 'value' => '4500000');
    $ch = curl_init();
	$HOST = $ilIliasIniFile->readVariable('server', 'http_path');
    curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/config/refresh_token_ttl");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($params));
    $result = curl_exec($ch);
    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>
<#4>
<?php
    global $ilias, $ilIliasIniFile;

    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $rest_client_id = ilPegasusHelperUIHookGUI::getRestClientId($oauthData['access_token']);

    if(!$rest_client_id) {
        throw new ilDatabaseException("REST Client ".ilPegasusHelperUIHookGUI::API_KEY." is not configured");
    }


    $params = array('pattern' => '/v1/ilias-app/desktop',
                    'verb' => 'GET');
    $ch = curl_init();
	$HOST = $ilIliasIniFile->readVariable('server', 'http_path');
    curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/permission/".$rest_client_id);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
    $result = curl_exec($ch);

    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>
<#5>
<?php
    global $ilias, $ilIliasIniFile;
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $rest_client_id = ilPegasusHelperUIHookGUI::getRestClientId($oauthData['access_token']);
    if(!$rest_client_id) {
        throw new ilDatabaseException("REST Client ".ilPegasusHelperUIHookGUI::API_KEY." is not configured");
    }

    $params = array('pattern' => '/v1/ilias-app/desktop',
        'verb' => 'OPTIONS');
    $ch = curl_init();
	$HOST = $ilIliasIniFile->readVariable('server', 'http_path');
    curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/permission/".$rest_client_id);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
    $result = curl_exec($ch);

    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>
<#6>
<?php
    global $ilias, $ilIliasIniFile;
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $rest_client_id = ilPegasusHelperUIHookGUI::getRestClientId($oauthData['access_token']);
    if(!$rest_client_id) {
        throw new ilDatabaseException("REST Client ".ilPegasusHelperUIHookGUI::API_KEY." is not configured");
    }

    $params = array('pattern' => '/v1/ilias-app/objects/:refId',
        'verb' => 'GET');
    $ch = curl_init();
	$HOST = $ilIliasIniFile->readVariable('server', 'http_path');
    curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/permission/".$rest_client_id);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
    $result = curl_exec($ch);

    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>
<#7>
<?php
    global $ilias, $ilIliasIniFile;
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $rest_client_id = ilPegasusHelperUIHookGUI::getRestClientId($oauthData['access_token']);
    if(!$rest_client_id) {
        throw new ilDatabaseException("REST Client ".ilPegasusHelperUIHookGUI::API_KEY." is not configured");
    }

    $params = array('pattern' => '/v1/ilias-app/objects/:refId',
                    'verb' => 'OPTIONS');
    $ch = curl_init();
	$HOST = $ilIliasIniFile->readVariable('server', 'http_path');
    curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/permission/".$rest_client_id);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
    $result = curl_exec($ch);

    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>
<#8>
<?php
    global $ilias, $ilIliasIniFile;
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $rest_client_id = ilPegasusHelperUIHookGUI::getRestClientId($oauthData['access_token']);
    if(!$rest_client_id) {
        throw new ilDatabaseException("REST Client ".ilPegasusHelperUIHookGUI::API_KEY." is not configured");
    }

    $params = array('pattern' => '/v1/ilias-app/files/:refId',
                    'verb' => 'GET');
    $ch = curl_init();
	$HOST = $ilIliasIniFile->readVariable('server', 'http_path');
    curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/permission/".$rest_client_id);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
    $result = curl_exec($ch);

    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>
<#9>
<?php
    global $ilias;
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $rest_client_id = ilPegasusHelperUIHookGUI::getRestClientId($oauthData['access_token']);
    if(!$rest_client_id) {
        throw new ilDatabaseException("REST Client ".ilPegasusHelperUIHookGUI::API_KEY." is not configured");
    }

    $params = array('pattern' => '/v1/ilias-app/files/:refId',
                    'verb' => 'OPTIONS');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, ilUtil::_getHttpPath()."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/permission/".$rest_client_id);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
    $result = curl_exec($ch);

    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>
<#10>
<?php
    global $ilias, $ilIliasIniFile;
    require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/class.ilPegasusHelperUIHookGUI.php";
    $oauthData = ilPegasusHelperUIHookGUI::createAccessToken('apollon');

    $rest_client_id = ilPegasusHelperUIHookGUI::getRestClientId($oauthData['access_token']);
    if(!$rest_client_id) {
        throw new ilDatabaseException("REST Client ".ilPegasusHelperUIHookGUI::API_KEY." is not configured");
    }

    $params = array('pattern' => '/v1/files/:id',
        'verb' => 'GET');
    $ch = curl_init();
	$HOST = $ilIliasIniFile->readVariable('server', 'http_path');
    curl_setopt($ch, CURLOPT_URL, $HOST."/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST/api.php/v2/admin/permission/".$rest_client_id);
    curl_setopt($ch, CURLOPT_POST, true );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $oauthData['access_token']));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params );
    $result = curl_exec($ch);

    $arr_result = json_decode($result, true);
    if($arr_result['error'] || $arr_result['message']) {
        throw new ilDatabaseException($arr_result['message'].' '.$arr_result['error']['message']);
    }
?>