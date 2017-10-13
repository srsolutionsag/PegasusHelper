<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once('./Services/UIComponent/classes/class.ilUIHookPluginGUI.php');
require_once(__DIR__ . '/TokenChecker.php');
require_once(__DIR__ . '/OauthManager.php');
require_once(__DIR__ . '/LoginPageManager.php');
require_once(__DIR__ . '/LoginPage/class.srLoginPageGUI.php');

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

    private $tokenChecker;
    private $outhManager;
	private $loginPageManager;

	/**
	 * ilPegasusHelperUIHookGUI constructor.
	 */
	public function __construct() {
		$this->tokenChecker = new TokenChecker();
		$this->outhManager = new OauthManager();
		$this->loginPageManager = new LoginPageManager();
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
			case $this->loginPageManager->isHandler():
				return array("mode" => ilUIHookPluginGUI::REPLACE, "html" => "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/templates/pegasus_login_page.html");

				//$tpl->getStandardTemplate();
				//global $ilCtrl;
				//$ilCtrl->clearParameters($this);
				//$ilCtrl->setParameterByClass("srLoginPageGUI","target", '');
				//$ilCtrl->redirectByClass(array("ilUIPluginRouterGUI", "srLoginPageGUI"),"show");
				break;
			default:
				return parent::getHTML($a_comp, $a_part, $a_par);
		}
	}


	/**
	 * Checks the GET parameter {@code target} against a regex.
	 * The param has to start with 'ilias_app'.
	 *
	 * @return bool true, if the request can be excluded from handlers, otherwise false
	 */
	private function isExcluded() {

		return !(isset($_GET['target'])
			&& preg_match("/^ilias_app.*$/", $_GET['target']) === 1);
	}
}