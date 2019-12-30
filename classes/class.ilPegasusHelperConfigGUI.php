<?php

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class ilPegasusHelperConfigGUI
 */
final class ilPegasusHelperConfigGUI extends ilPluginConfigGUI {

    static $ICON_CATEGORIES =  [
        "course",
        "folder",
        "group",
        "file",
        "learningplace",
        "link"
    ];

    /**
     * invoked by parent
     * @param $cmd string
     */
    public function performCommand($cmd) {
        global $ilTabs, $ilCtrl, $tpl;
        $ilTabs->addSubTab("id_general", "General", $ilCtrl->getLinkTarget($this, "general"));
        $ilTabs->addSubTab("id_theme", "App Theme", $ilCtrl->getLinkTarget($this, "theme"));
        $ilTabs->addSubTab("id_testing", "Testing", $ilCtrl->getLinkTarget($this, "testing"));

        switch ($cmd) {
            case "testing":
                $ilTabs->setSubTabActive("id_testing");
                $tpl->setContent($this->getTestsTableHtml());
                break;
            case "testing_run_external_tests":
                $ilTabs->setSubTabActive("id_testing");
                $tpl->setContent($this->getTestsTableHtml(true));
                break;
            case "theme":
            case "theme_reset_icons":
                $ilTabs->setSubTabActive("id_theme");
                $tpl->setContent($this->getColorFormHtml() . $this->getIconsForm()->getHTML());
                break;
            case "theme_save_colors":
                $this->saveColors();
                break;
            case "theme_reset_colors":
                $this->resetColors();
                break;
            case "theme_save_icons":
                $this->saveIcons();
            case "general":
            default:
                $ilTabs->setSubTabActive("id_general");
                $tpl->setContent($this->getApiSecretFormHtml());
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

        $form = new ilPropertyFormGUI();
        $form->setTitle("Coloring");
        $form->setFormAction($ilCtrl->getFormAction($this));
        $form->addCommandButton("theme_reset_colors", "Reset");
        $form->addCommandButton("theme_save_colors", "Save");

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

        // preview
        $thisDir = "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes";
        $tpl = new ilTemplate("tpl.theme_example.html", true, true, $thisDir);
        $tpl->setVariable('EX_TEXT', "&nbsp;ILIAS Pegasus&nbsp;");
        $tpl->setVariable('COLOR_PRIMARY', $primaryColor);
        $tpl->setVariable('COLOR_CONTRAST', $contrastColor ? "ffffff" : "000000");
        $themeExample = new ilNonEditableValueGUI("Preview", "", true);
        $themeExample->setInfo("The current theme");
        $themeExample->setValue($tpl->get());
        $form->addItem($themeExample);

        // primary color
        require_once("./Services/Form/classes/class.ilColorPickerInputGUI.php");
        $primaryInput = new ilColorPickerInputGUI("Primary color", "primary_color");
        $primaryInput->setInfo("The main color for the theme of the app");
        $primaryInput->setValue($primaryColor);
        $form->addItem($primaryInput);

        // contrast color
        require_once("./Services/Form/classes/class.ilRadioGroupInputGUI.php");
        require_once("./Services/Form/classes/class.ilRadioOption.php");
        $contrastInput = new ilRadioGroupInputGUI("Contrast color", "contrast_color");
        $contrastInput->setInfo("The color of text in the app, which should be chosen dependent on the primary color");
        $contrastInput->addOption(new ilRadioOption("White", 1));
        $contrastInput->addOption(new ilRadioOption("Black", 0));
        $contrastInput->setValue($contrastColor);
        $form->addItem($contrastInput);

        return $form->getHTML();
    }

    /**
     * TODO desc
     */
    function getIconsForm() {
        global $ilCtrl;

        $form = new ilPropertyFormGUI();
        $form->setTitle("Icons");
        $form->setFormAction($ilCtrl->getFormAction($this));
        $form->addCommandButton("theme_reset_icons", "Reset");
        $form->addCommandButton("theme_save_icons", "Save");

        foreach(ilPegasusHelperConfigGUI::$ICON_CATEGORIES as $category) {
            // current item
            $thisDir = "Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes";
            $tpl = new ilTemplate("tpl.icon.html", true, true, $thisDir);
            $tpl->setVariable("SRC", $thisDir . "/../templates/images/icon_" . $category . ".svg");
            $tpl->setVariable("SIZE", 45); // TODO how to set size? find better method to embed icon
            $icon = new ilNonEditableValueGUI(ucfirst($category), "", true);
            $icon->setValue($tpl->get());
            $form->addItem($icon);
            // upload form
            $fileUpload = new ilFileInputGUI("", "post_icon_" . $category);
            $fileUpload->setSuffixes(["svg"]);
            $form->addItem($fileUpload);
        }

        return $form;
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
     * save input from the colors form
     */
    protected function saveColors() {
        global $ilDB, $ilCtrl;
        $primaryColor = $_POST["primary_color"];
        $contrastColor = $_POST["contrast_color"];

        if(!preg_match("/^[0-9a-fA-F]{6}$/", $primaryColor)) {
            ilUtil::sendFailure("App theme was not saved", true);
            $ilCtrl->redirect($this, "theme");
            return;
        }

        $values = array(
            "primary_color"  => array("text", $primaryColor),
            "contrast_color" => array("integer", $contrastColor),
            'timestamp'      => array('integer', time())
        );
        $where = array(
            "id" => array("integer", 1)
        );
        $ilDB->update("ui_uihk_pegasus_theme", $values, $where);

        ilUtil::sendSuccess("App coloring saved successfully", true);
        $ilCtrl->redirect($this, "theme");
    }

    /**
     * reset colors to default values
     */
    protected function resetColors() {
        global $ilDB, $ilCtrl;

        $values = array(
            "primary_color"  => array("text", "4a668b"),
            "contrast_color" => array("integer", 1),
            'timestamp'      => array('integer', time())
        );
        $where = array(
            "id" => array("integer", 1)
        );
        $ilDB->update("ui_uihk_pegasus_theme", $values, $where);

        ilUtil::sendSuccess("App coloring reset successfully", true);
        $ilCtrl->redirect($this, "theme");
    }

    protected function saveIcons() {
        global $ilCtrl;

        ////////////////////////////////

        global $tpl;
        $form = $this->getIconsForm();
        if(!$form->checkInput()) {
            $tpl->setContent($form->getHTML());
            ilUtil::sendInfo("no input", true);
        } else {
            $debug = "found input</br>";
            foreach(ilPegasusHelperConfigGUI::$ICON_CATEGORIES as $category) {
                $key = "post_icon_" . $category;
                $file = $form->getInput($key);
                $importer = new ilOrgUnitSimpleUserImport();
                try {
                    $importer->simpleUserImport($file["tmp_name"]);
                    $debug .= "ok: " . $key . "</br>";
                } catch(Exception $e) {
                    $debug .= "err: " . $key . "</br>";
                }

                if (!$importer->hasErrors() AND !$importer->hasWarnings()) {
                    $stats = $importer->getStats();
                    $debug .= $stats['created'] . " - " . $stats['removed'] . "</br>";
                }
                if ($importer->hasWarnings()) {
                    $debug .= "WARN ";
                    foreach ($importer->getWarnings() as $warning)
                        $debug .= $warning['lang_var'] . ' (Import ID: ' . $warning['import_id'] . ')<br>';
                }
                if ($importer->hasErrors()) {
                    $debug .= "ERR ";
                    foreach ($importer->getErrors() as $warning)
                        $debug .= $warning['lang_var'] . ' (Import ID: ' . $warning['import_id'] . ')<br>';
                }
            }
            ilUtil::sendInfo($debug, true);
        }

        ////////////////////////////////

        ilUtil::sendSuccess("App icons saved successfully", true);
        $ilCtrl->redirect($this, "theme");
    }
}

?>
