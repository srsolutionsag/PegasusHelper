<?php
require_once('class.ilPegasusHelperPlugin.php');
require_once('./Services/Component/classes/class.ilPluginConfigGUI.php');
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once ('./Services/Form/classes/class.ilNonEditableValueGUI.php');

/**
 * Class ilPegasusHelperConfigGUI
 */
final class ilPegasusHelperConfigGUI extends ilPluginConfigGUI {


	public function __construct() {

	}

	public function performCommand($cmd) {
		switch ($cmd) {
			case 'configure':
				$this->showConfig();
				break;
		}
	}

	public function showConfig() {
		global $ilDB, $tpl;

		$api_key =  'ilias_pegasus';
		$sql = "SELECT api_secret FROM ui_uihk_rest_client where api_key = '$api_key'";
		$set = $ilDB->query($sql);
		while ($rec = $ilDB->fetchAssoc($set)) {
			$api_secret = $rec['api_secret'];
		}


		$formGUI = new ilPropertyFormGUI();
		$formGUI->setTitle('ILIAS Pegasus API User');

		$apiuser = new ilNonEditableValueGUI();
		$apiuser->setTitle($api_key);
		$apiuser->setValue($api_secret);

		$formGUI->addItem($apiuser);

		$tpl->setContent($formGUI->getHTML());

	}
}
?>
