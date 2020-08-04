<?php

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class ilPegasusHelperConfigGUI
 */
final class ilPegasusHelperConfigGUI extends ilPluginConfigGUI {

    /**
     * @var \ilPegasusHelperPlugin
     */
    var $pl;

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

    public function __construct() {
        $this->pl = new ilPegasusHelperPlugin();
    }

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
        $ilTabs->addSubTab(
            "id_general",
            $this->pl->txt("tab_general"),
            $ilCtrl->getLinkTarget($this, "general")
        );
        $ilTabs->addSubTab(
            "id_theme",
            $this->pl->txt("tab_app_theme"),
            $ilCtrl->getLinkTarget($this, "theme")
        );
        $ilTabs->addSubTab(
            "id_statistics",
            $this->pl->txt("tab_statistics"),
            $ilCtrl->getLinkTarget($this, "statistics")
        );
        $ilTabs->addSubTab(
            "id_testing",
            $this->pl->txt("tab_testing"),
            $ilCtrl->getLinkTarget($this, "testing")
        );

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
            case "statistics":
                $ilTabs->setSubTabActive("id_statistics");
                $tpl->setContent($this->getTokenStatisticsHtml());
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

            $formApiUser->setTitle($this->pl->txt("form_api_secret"));
            $gui = new ilNonEditableValueGUI($api_key);
            $gui->setValue($api_secret);
            $formApiUser->addItem($gui);
        }

        return $formApiUser->getHTML();
    }

    /**
     * html with token statistics
     * @return string
     */
    protected function getTokenStatisticsHtml() {
        $formTokensStatistics = new ilPropertyFormGUI();
        $formTokensStatistics->setTitle($this->pl->txt("form_token_statistics"));

        // get number of accesses for different durations
        $differences = [
            $this->pl->txt("txt_month") => 30,
            $this->pl->txt("txt_quarter") => 90,
            $this->pl->txt("txt_semester") => 180
        ];

        foreach($differences as $label => $dd) {
            global $ilDB;
            $sql = "SELECT COUNT(*) FROM ui_uihk_rest_refresh WHERE datediff(NOW(), created) < $dd";
            $set = $ilDB->query($sql);
            $counts = current($ilDB->fetchAssoc($set));

            $gui = new ilNonEditableValueGUI($label);
            $gui->setValue($counts);
            $formTokensStatistics->addItem($gui);
        }

        return $formTokensStatistics->getHTML();
    }

    /**
     * form for dynamic coloring
     * @return string
     */
    protected function getColorFormHtml() {
        global $ilDB, $ilCtrl;

        $form = new ilPropertyFormGUI();
        $form->setTitle($this->pl->txt("form_coloring"));
        $form->setFormAction($ilCtrl->getFormAction($this));
        $form->addCommandButton("theme_reset_colors", $this->pl->txt("button_reset"));
        $form->addCommandButton("theme_save_colors", $this->pl->txt("button_save"));

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
        $themeExample = new ilNonEditableValueGUI($this->pl->txt("txt_preview"), "", true);
        $themeExample->setInfo($this->pl->txt("txt_info_preview"));
        $themeExample->setValue($tpl->get());
        $form->addItem($themeExample);

        // primary color
        require_once("./Services/Form/classes/class.ilColorPickerInputGUI.php");
        $primaryInput = new ilColorPickerInputGUI($this->pl->txt("txt_primary_color"), "primary_color");
        $primaryInput->setInfo($this->pl->txt("txt_info_primary_color"));
        $primaryInput->setValue($primaryColor);
        $form->addItem($primaryInput);

        // contrast color
        require_once("./Services/Form/classes/class.ilRadioGroupInputGUI.php");
        require_once("./Services/Form/classes/class.ilRadioOption.php");
        $contrastInput = new ilRadioGroupInputGUI($this->pl->txt("txt_contrast"), "contrast_color");
        $contrastInput->setInfo($this->pl->txt("txt_info_contrast"));
        $contrastInput->addOption(new ilRadioOption($this->pl->txt("button_white"), 1));
        $contrastInput->addOption(new ilRadioOption($this->pl->txt("button_black"), 0));
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
        $form->setTitle($this->pl->txt("form_icon"));
        $form->setFormAction($ilCtrl->getFormAction($this));
        $form->addCommandButton("theme_reset_icons", $this->pl->txt("button_reset"));
        $form->addCommandButton("theme_save_icons", $this->pl->txt("button_save"));

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
        $tableLegend->setTitle($this->pl->txt("tests_txt_legend"));
        require_once __DIR__ . "/class.ilPegasusTestingStatus.php";
        $dataLegend = [
            [
                "status" => ilPegasusTestingStatus::T_STATUS_OK,
                "test" => $this->pl->txt("tests_txt_passed"),
                "info" => ""
            ],
            [
                "status" => ilPegasusTestingStatus::T_STATUS_FAIL,
                "test" => $this->pl->txt("tests_txt_failed"),
                "info" => $this->pl->txt("tests_txt_failed_info")
            ],
            [
                "status" => ilPegasusTestingStatus::T_STATUS_WARN,
                "test" => $this->pl->txt("tests_txt_warning"),
                "info" => $this->pl->txt("tests_txt_warning_info")
            ],
            [
                "status" => ilPegasusTestingStatus::T_STATUS_INCOMPLETE,
                "test" => $this->pl->txt("tests_txt_aborted"),
                "info" => ""
            ]
        ];
        $tableLegend->setData($dataLegend);

        // table with internal tests
        $tableInt = new ilPegasusTestingTableGUI($this, $this->pl->txt("tests_txt_name"));
        $tableInt->setTitle($this->pl->txt("tests_txt_tests"));
        require_once __DIR__ . "/class.ilPegasusTesting.php";
        $tableInt->setData((new ilPegasusHelperTesting())->run("internal"));

        // table with external tests, run if $external is true
        $tableExt = new ilPegasusTestingTableGUI($this, $this->pl->txt("tests_txt_name"));
        $tableExt->setTitle($this->pl->txt("tests_txt_external_tests"));
        if($external) {
            require_once __DIR__ . "/class.ilPegasusTesting.php";
            $tableExt->setData((new ilPegasusHelperTesting())->run("external"));
        }
        $tableExt->setFormAction($ilCtrl->getFormAction($this));
        $tableExt->addCommandButton(
            "testing_run_external_tests",
            $this->pl->txt("tests_txt_run_external_tests")
        );

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
            ilUtil::sendFailure($this->pl->txt("msg_coloring_not_saved"), true);
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
        ilUtil::sendSuccess($this->pl->txt("msg_coloring_saved"), true);
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
        ilUtil::sendSuccess($this->pl->txt("msg_coloring_reset"), true);
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
                            $msgSuccess .= $this->pl->txt("msg_icon_saved_pre") . $category . $this->pl->txt("msg_icon_saved_post");
                        }
                    } else {
                        if($result->getName()) {
                            $msgFail = $this->pl->txt("msg_icon_not_uploaded");
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
            ilUtil::sendFailure($this->pl->txt("msg_icons_not_saved"), true);
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
        ilUtil::sendSuccess($this->pl->txt("msg_icons_reset"), true);
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