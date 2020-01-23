<?php

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class ilPegasusHelperConfigGUI
 */
final class ilPegasusHelperConfigGUI extends ilPluginConfigGUI {

    /**
     * invoked by parent
     * @param $cmd string
     */
    public function performCommand($cmd) {
        global $ilTabs, $ilCtrl, $tpl;
        $ilTabs->addSubTab("id_general", "General", $ilCtrl->getLinkTarget($this, "configure"));
        $ilTabs->addSubTab("id_testing", "Testing", $ilCtrl->getLinkTarget($this, "testing"));

        switch ($cmd) {
            case "saveColor":
                $this->saveColor();
                break;
            case "testing":
                $ilTabs->setSubTabActive("id_testing");
                $tpl->setContent($this->getTestsTableHtml());
                break;
            case "testing_run_external_tests":
                $ilTabs->setSubTabActive("id_testing");
                $tpl->setContent($this->getTestsTableHtml(true));
                break;
            case "configure":
            default:
                $ilTabs->setSubTabActive("id_general");
                $tpl->setContent($this->getApiSecretFormHtml() . $this->getColorFormHtml());
                break;
        }
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

        $primaryColor = "04427e";
        $contrastColor = 1;
        $sql = "SELECT * FROM ui_uihk_pegasus_theme";
        $set = $ilDB->query($sql);
        if($set !== false) {
            while ($rec = $ilDB->fetchAssoc($set)) {
                $primaryColor = $rec["primary_color"];
                $contrastColor = $rec["contrast_color"];
            }
        }


        $formColor = new ilPropertyFormGUI();
        $formColor->setTitle("App Theme Coloring");
        $formColor->setFormAction($ilCtrl->getFormAction($this));
        // preview
        $thisDir = "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes";
        $tpl = new ilTemplate("tpl.theme_example.html", true, true, $thisDir);
        $tpl->setVariable('EX_TEXT', "&nbsp;ILIAS Pegasus&nbsp;");
        $tpl->setVariable('COLOR_PRIMARY', $primaryColor);
        $tpl->setVariable('COLOR_CONTRAST', $contrastColor ? "ffffff" : "000000");
        $themeExample = new ilNonEditableValueGUI("Preview", "", true);
        $themeExample->setInfo("The current theme");
        $themeExample->setValue($tpl->get());
        $formColor->addItem($themeExample);

        // primary color
        require_once("./Services/Form/classes/class.ilColorPickerInputGUI.php");
        $primaryInput = new ilColorPickerInputGUI("Primary color", "primary_color");
        $primaryInput->setInfo("The main color for the theme of the app");
        $primaryInput->setValue($primaryColor);
        $formColor->addItem($primaryInput);

        // contrast color
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
     *
     * @param bool $external
     * @return string
     */
    protected function getTestsTableHtml($external = false) {
        global $ilCtrl;
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

        // table with internal tests
        $tableInt = new ilPegasusTestingTableGUI($this, "Name");
        $tableInt->setTitle("Tests");
        require_once __DIR__ . "/class.ilPegasusTesting.php";
        $tableInt->setData((new ilPegasusHelperTesting())->run("internal"));

        // table with external tests, run if $external is true
        $tableExt = new ilPegasusTestingTableGUI($this, "Name");
        $tableExt->setTitle("External Tests");
        if($external) {
            require_once __DIR__ . "/class.ilPegasusTesting.php";
            $tableExt->setData((new ilPegasusHelperTesting())->run("external"));
        }
        $tableExt->setFormAction($ilCtrl->getFormAction($this));
        $tableExt->addCommandButton("testing_run_external_tests", "Run external tests");

        return $tableLegend->getHTML() . $tableInt->getHTML() . $tableExt->getHTML();
    }

    /**
     * save input from the color form
     */
    protected function saveColor() {
        global $ilDB, $ilCtrl;
        $primaryColor = $_POST["primary_color"];
        $contrastColor = $_POST["contrast_color"];

        if(!preg_match("/^[0-9a-fA-F]{6}$/", $primaryColor)) {
            ilUtil::sendFailure("App theme was not saved", true);
            $ilCtrl->redirect($this, "configure");
            return;
        }

        $values = array(
            "primary_color" => array("text", $primaryColor),
            "contrast_color" => array("integer", $contrastColor)
        );
        $where = array(
            "id" => array("integer", 1)
        );
        $ilDB->update("ui_uihk_pegasus_theme", $values, $where);

        ilUtil::sendSuccess("App theme saved successfully", true);
        $ilCtrl->redirect($this, "configure");
    }
}

?>
