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
            case 'saveColor':
                $this->saveColor();
                break;
            case 'configure':
                $this->showConfig();
                break;
        }
    }

    public function showConfig() {
        global $tpl;
        $tpl->setContent($this->getApiSecretFormHtml() . $this->getColorFormHtml() . $this->getTestsTableHtml());
    }

    /**
     * html of form for API-secret
     * @return string
     */
    protected function getApiSecretFormHtml() {
        global $ilDB;
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

        return $formApiUser->getHTML();
    }

    /**
     * html of form for dynamic coloring
     * @return string
     */
    protected function getColorFormHtml() {
        global $ilDB, $ilCtrl;

        $sql = "SELECT * FROM ui_uihk_pegasus_theme";
        $set = $ilDB->query($sql);
        while ($rec = $ilDB->fetchAssoc($set)) {
            $primaryColor = $rec["primary_color"];
            $contrastColor = $rec["contrast_color"];
        }

        $formColor = new ilPropertyFormGUI();
        $formColor->setTitle("App Theme Coloring");
        $formColor->setFormAction($ilCtrl->getFormAction($this));

        require_once("./Services/Form/classes/class.ilColorPickerInputGUI.php");
        $primaryInput = new ilColorPickerInputGUI("Primary color", "primary_color");
        $primaryInput->setInfo("The main color for the theme of the app");
        $primaryInput->setValue($primaryColor);
        $formColor->addItem($primaryInput);

        require_once("./Services/Form/classes/class.ilRadioGroupInputGUI.php");
        require_once("./Services/Form/classes/class.ilRadioOption.php");
        $contrastInput = new ilRadioGroupInputGUI("Contrast color", "contrast_color");
        $contrastInput->setInfo("The color of text in the app, which should be chosen dependent on the primary color");
        $contrastInput->addOption(new ilRadioOption("White", 1));
        $contrastInput->addOption(new ilRadioOption("Black", 0));
        $contrastInput->setValue($contrastColor);
        $formColor->addItem($contrastInput);

        $formColor->addCommandButton("saveColor", "Save");

        return $formColor->getHTML();
    }

    /**
     * html of legend for tests
     * @return string
     */
    protected function getTestsTableHtml() {
        include_once __DIR__ . "/class.ilPegasusTestingTableGUI.php";
        $tableLegend = new ilPegasusTestingTableGUI($this, "Status");
        $tableLegend->setTitle("Legend for Tests");
        require_once __DIR__ . "/class.ilPegasusTestingStatus.php";
        $dataLegend = [
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
        $tableLegend->setData($dataLegend);

        $tableTests = new ilPegasusTestingTableGUI($this, "Name");
        $tableTests->setTitle("Tests");
        require_once __DIR__ . "/class.ilPegasusTesting.php";
        $tableTests->setData((new ilPegasusHelperTesting())->run());

        return $tableLegend->getHTML() . $tableTests->getHTML();
    }

    /**
     * save input from the color form
     */
    protected function saveColor() {
        global $ilDB, $ilCtrl;

        //ilUtil::sendFailure("App theme coloring was not saved", true); TODO checks, catching of errors, inform user ect

        $primaryColor = $_POST["primary_color"];
        $contrastColor = $_POST["contrast_color"];

        $values = array(
            "primary_color" => array("text", $primaryColor),
            "contrast_color" => array("integer", $contrastColor)
        );
        $where = array(
            "id" => array("integer", 1)
        );
        $ilDB->update("ui_uihk_pegasus_theme", $values, $where);

        ilUtil::sendSuccess("App theme coloring saved successfully", true);
        $ilCtrl->redirect($this, "configure");
    }
}

?>
