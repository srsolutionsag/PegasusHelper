<?php
/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */
require_once './Services/UIComponent/classes/class.ilUIHookPluginGUI.php';
require_once __DIR__ . '/ExcludedHandler.php';
require_once __DIR__ . '/TokenChecker.php';
require_once __DIR__ . '/OauthManager.php';
require_once __DIR__ . '/LoginPageManager.php';

/**
 * Class ilPegasusHelperUIHookGUI handles different kind of requests,
 * that are needed for ILIAS Pegasus app.
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 * @author Martin Studer <ms@studer-raimann.ch>
 * @author Nicolas Märchy <nm@studer-raimann.ch>
 */
final class ilPegasusHelperUIHookGUI extends ilUIHookPluginGUI
{

	/**
	 * @var BaseHandler $handlers
	 */
	private $handlers;

	/**
	 * ilPegasusHelperUIHookGUI constructor.
	 */
	public function __construct() {
		$this->handlers = new ExcludedHandler();
		$this->handlers->add(new OauthManager());
		$this->handlers->add(new TokenChecker());
		$this->handlers->add(new LoginPageManager());
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

		$this->handlers->handle();
		return parent::getHTML($a_comp, $a_part, $a_par);
	}



}