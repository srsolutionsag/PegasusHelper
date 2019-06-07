<?php

require_once __DIR__ . '/../bootstrap.php';

require_once __DIR__ . "/class.ilPegasusHelperTesting.php";

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
		$sql = "SELECT api_secret FROM ui_uihk_rest_client WHERE api_key = '$api_key'";
		$set = $ilDB->query($sql);
		while ($rec = $ilDB->fetchAssoc($set)) {
			$api_secret = $rec['api_secret'];
		}


		$formApiUser = new ilPropertyFormGUI();
		$formApiUser->setTitle('ILIAS Pegasus API User');
        $this->addIlNonEditableValueGUI($api_key, $api_secret, $formApiUser);

        $formTestsLegend = new ilPropertyFormGUI();
        $formTestsLegend->setTitle('Legend for tests');

        $this->addIlNonEditableValueGUI("<b>Symbol</b>",
			"Description", $formTestsLegend);
        $this->addIlNonEditableValueGUI($this->getStatusImg(TestStatus::T_STATUS_OK),
			"Test passed", $formTestsLegend);
        $this->addIlNonEditableValueGUI($this->getStatusImg(TestStatus::T_STATUS_FAIL),
			"Test failed: This problem must be solved for the Pegasus App to work", $formTestsLegend);
        $this->addIlNonEditableValueGUI($this->getStatusImg(TestStatus::T_STATUS_WARN),
			"Test resulted in a warning: Some setups cannot work properly without removing the corresponding problem", $formTestsLegend);
        $this->addIlNonEditableValueGUI($this->getStatusImg(TestStatus::T_STATUS_INCOMPLETE),
			"It was not possible to run the Test, no conclusions can be drawn", $formTestsLegend);

        $formTests = new ilPropertyFormGUI();
        $formTests->setTitle('Tests');

        $results = (new ilPegasusHelperTesting())->run();
        foreach ($results as $category) {
            $this->addIlNonEditableValueGUI("<br/><b>" . $category["title"] . "</b>", "", $formTests);

            foreach ($category["tests"] as $testResult) {
                list($title, $description, $status) = $testResult;
                $this->addIlNonEditableValueGUI( ucfirst($title) . " " . $this->getStatusImg($status), ucfirst($description), $formTests);
            }
        }

        // TODO remove $info = (new ilPegasusHelperTesting())->getInfo();
		$tpl->setContent($formApiUser->getHTML() . "<br/>" . $formTests->getHTML() . "<br/>" . $formTestsLegend->getHTML()); // TODO remove  . "</br>" . str_replace("\n", "<br/>", print_r($info, true)));

	}

	private function getStatusImg($status) {
		return ilUtil::img(ilLearningProgressBaseGUI::_getImagePathForStatus($status));
	}

	private function addIlNonEditableValueGUI($title, $value, $formGUI) {
        $gui = new ilNonEditableValueGUI($title);
        $gui->setValue($value);
        $formGUI->addItem($gui);
	}
}

?>
