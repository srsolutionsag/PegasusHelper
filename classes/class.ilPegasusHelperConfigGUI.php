<?php

require_once __DIR__ . '/../bootstrap.php';

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

        // form for API-secret
        $formApiUser = new ilPropertyFormGUI();
		if($ilDB->tableExists("ui_uihk_rest_client")) {
            $api_key = 'ilias_pegasus';
            $sql = "SELECT api_secret FROM ui_uihk_rest_client WHERE api_key = '$api_key'";
            $set = $ilDB->query($sql);
            while ($rec = $ilDB->fetchAssoc($set)) {
                $api_secret = $rec['api_secret'];
            }

            $formApiUser->setTitle('ILIAS Pegasus API User');
            $gui = new ilNonEditableValueGUI($api_key);
            $gui->setValue($api_secret);
            $formApiUser->addItem($gui);
        }


        // legend for tests
        include_once __DIR__ . "/class.ilPegasusTestingTableGUI.php";
        $table_legend = new ilPegasusTestingTableGUI($this, "Status");
        $table_legend->setTitle("Legend for Tests");
        require_once __DIR__ . "/class.ilPegasusTestingStatus.php";
        $data_legend = [
            [
                "status" => ilPegasusTestingStatus::T_STATUS_OK,
                "test" => "test passed",
                "info" => ""
            ],
            [
                "status" => ilPegasusTestingStatus::T_STATUS_FAIL,
                "test" => "test failed",
                "info" => "a failed test indicates that the Pegasus App cannot operate correctly, the corresponding problem must be solved"
            ],
            [
                "status" => ilPegasusTestingStatus::T_STATUS_WARN,
                "test" => "test resulted in a warning",
                "info" => "depending on the setup of ILIAS, a test resulting in a warning may indicate a problem"
            ],
            [
                "status" => ilPegasusTestingStatus::T_STATUS_INCOMPLETE,
                "test" => "it was not possible to run the test",
                "info" => ""
            ]
        ];
        $table_legend->setData($data_legend);

        // table with tests
		$table = new ilPegasusTestingTableGUI($this, "Name");
        $table->setTitle("Tests");
        require_once __DIR__ . "/class.ilPegasusTesting.php";
        $table->setData((new ilPegasusHelperTesting())->run());

		$tpl->setContent($formApiUser->getHTML() . $table_legend->getHTML() . $table->getHTML());
	}
}

?>
