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
        "learningmodule",
        "link"
    ];
    static $ICON_WEB_DIR = "pegasushelper/theme/icons/";

    /**
     * copies the default icons to the directory for the synchronization with the app
     */
    static function copyDefaultIcons() {
        global $DIC;

        $customizingSourceDir = "global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/templates/icons/";
        foreach(ilPegasusHelperConfigGUI::$ICON_CATEGORIES as $category) {
            $fileName = "icon_$category.svg";
            $fsWeb = $DIC->filesystem()->web();
            $fsCustomizing = $DIC->filesystem()->customizing();
            if($fsWeb->has(ilPegasusHelperConfigGUI::$ICON_WEB_DIR . $fileName)) {
                $fsWeb->delete(ilPegasusHelperConfigGUI::$ICON_WEB_DIR . $fileName);
            }
            $fileStream = $fsCustomizing->readStream($customizingSourceDir . $fileName);
            $fsWeb->writeStream(ilPegasusHelperConfigGUI::$ICON_WEB_DIR . $fileName, $fileStream);
        }
    }

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
                break;
            case "theme_reset_icons":
                $this->resetIcons();
                break;
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
     * form for dynamic coloring
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
     * form for icons
     * @return ilPropertyFormGUI
     * @throws ilTemplateException
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
            $clientName = CLIENT_ID;
            $webDir = "data/$clientName/";
            $tpl = new ilTemplate("tpl.icon.html", true, true, $thisDir);
            $iconsDir = $webDir . ilPegasusHelperConfigGUI::$ICON_WEB_DIR;
            $tpl->setVariable("SRC", $iconsDir . "icon_$category.svg");
            $tpl->setVariable("SIZE", 45);
            $icon = new ilNonEditableValueGUI(ucfirst($category), "", true);
            $icon->setValue($tpl->get());
            $form->addItem($icon);
            // upload form
            $fileUpload = new ilFileInputGUI("", "post_icon_$category");
            $fileUpload->setSuffixes(["svg"]);
            $form->addItem($fileUpload);
        }

        return $form;
    }

    /**
     * html of legend for tests
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
            ilUtil::sendFailure("App coloring was not saved. The input for the primary color must be a 6-digit hex-color", true);
            $ilCtrl->redirect($this, "theme");
            return;
        }

        $values = array(
            "primary_color"  => array("text", $primaryColor),
            "contrast_color" => array("integer", $contrastColor)
        );
        $where = array(
            "id" => array("integer", 1)
        );
        $ilDB->update("ui_uihk_pegasus_theme", $values, $where);

        $this->updateTimestamp();
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
            "contrast_color" => array("integer", 1)
        );
        $where = array(
            "id" => array("integer", 1)
        );
        $ilDB->update("ui_uihk_pegasus_theme", $values, $where);

        $this->updateTimestamp();
        ilUtil::sendSuccess("App coloring reset successfully", true);
        $ilCtrl->redirect($this, "theme");
    }

    protected function saveIcons() {
        global $ilCtrl, $DIC;

        $form = $this->getIconsForm();
        if($form->checkInput()) {
            // manage upload
            if($DIC->upload()->hasBeenProcessed() !== true) {
                if(PATH_TO_GHOSTSCRIPT !== "") {
                    $DIC->upload()->register(new ilCountPDFPagesPreProcessors());
                }
            }
            $DIC->upload()->process();

            // for each category, set the new icon
            $msgSuccess = "";
            $msgFail = "";
            foreach(ilPegasusHelperConfigGUI::$ICON_CATEGORIES as $category) {
                $key = "post_icon_$category";
                $file = $form->getInput($key);

                foreach($DIC->upload()->getResults() as $result) {
                    if($result->getStatus()->getCode() === \ILIAS\FileUpload\DTO\ProcessingStatus::OK) {
                        $resultMatchesCategory = $file["tmp_name"] === $result->getPath();
                        if($resultMatchesCategory) {
                            $fileName = "icon_$category.svg";
                            if($DIC->filesystem()->web()->has(ilPegasusHelperConfigGUI::$ICON_WEB_DIR . $fileName)) {
                                $DIC->filesystem()->web()->delete(ilPegasusHelperConfigGUI::$ICON_WEB_DIR . $fileName);
                            }
                            $DIC->upload()->moveOneFileTo($result, ilPegasusHelperConfigGUI::$ICON_WEB_DIR, \ILIAS\FileUpload\Location::WEB, $fileName);
                            $msgSuccess .= "Icon for '$category' saved successfully<br/>";
                        }
                    } else {
                        if($result->getName()) {
                            $msgFail = "Problem when uploading file";
                        }
                    }
                }
            }
            $this->updateTimestamp();
            // user feedback
            if($msgSuccess) {
                ilUtil::sendSuccess($msgSuccess, true);
            }
            if($msgFail) {
                ilUtil::sendFailure($msgFail, true);
            }
        } else {
            ilUtil::sendFailure("Unable to upload icons", true);
        }

        $ilCtrl->redirect($this, "theme");
    }

    /**
     * reset icons to default values
     */
    protected function resetIcons() {
        global $ilCtrl;

        ilPegasusHelperConfigGUI::copyDefaultIcons();

        $this->updateTimestamp();
        ilUtil::sendSuccess("App icons reset successfully", true);
        $ilCtrl->redirect($this, "theme");
    }

    /**
     * sets the timestamp for the settings to the current time
     */
    private function updateTimestamp() {
        global $ilDB;

        $values = array(
            "timestamp" => array("integer", time())
        );
        $where = array(
            "id" => array("integer", 1)
        );
        $ilDB->update("ui_uihk_pegasus_theme", $values, $where);
    }
}

?>
