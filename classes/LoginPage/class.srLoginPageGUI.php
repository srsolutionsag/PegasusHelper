<?php
/**
 * GUI-Class srLoginPageGUI displays a specific Pegasus Login-Page
 *
 * @author Martin Studer <ms@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy srLoginPageGUI: ilUIPluginRouterGUI
 */
class srLoginPageGUI {

	protected $tpl;
	protected $ctrl;
	protected $pl;
	protected $pltrp;
	protected $toolbar;
	protected $tabs;


	function __construct() {
		global $tpl, $ilCtrl;

		$this->tpl = $tpl;
		$this->tpl->getStandardTemplate();

		$this->ctrl = $ilCtrl;
echo "sdfsdf"; exit;
		/*$this->pl = ilProductPortfolioPlugin::getInstance();
		$this->pltrp = ilTrainingProgramPlugin::getInstance();
		$this->lng = $lng;
		$this->tpl = $tpl;
		$this->tpl->getStandardTemplate();


		$this->toolbar = $ilToolbar;
		$this->tabs = $ilTabs;*/
	}


	public function executeCommand() {
		$cmd = $this->ctrl->getCmd();
		echo "sdfsdsssf"; exit;

		switch ($cmd) {
			case 'show':
			default:
				$this->show();
				break;
		}

		//$this->tpl->show();

		$this->tpl->show();exit;
	}


	public function show() {

		$this->tpl->setContent("Sie werden in Kürze eingeloggt");
	}
}

?>
