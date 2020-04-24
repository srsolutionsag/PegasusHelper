<?php

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class ilPegasusHelperConfigGUI
 *
 * @ilCtrl_Calls ilPegasusHelperConfigGUI: ilPegasusHelperTestingGUI, ilPegasusHelperThemingGUI, ilPegasusHelperGeneralGUI
 */
final class ilPegasusHelperConfigGUI extends ilPluginConfigGUI {

    function executeCommand() {
        global $ilTabs, $ilCtrl;
        $ilTabs->addSubTab("id_general", "General", $ilCtrl->getLinkTarget($this, "general"));
        $ilTabs->addSubTab("id_theme", "App Theme", $ilCtrl->getLinkTarget($this, "theme"));
        $ilTabs->addSubTab("id_testing", "Testing", $ilCtrl->getLinkTarget($this, "testing"));

        $nextClass = $ilCtrl->getNextClass($this);

        switch ($nextClass) {
            case "ilpegasushelpertestinggui":
                $ilTabs->setSubTabActive("id_testing");
                include_once("class.ilPegasusHelperTestingGUI.php");
                $qui = new ilPegasusHelperTestingGUI();
                $ilCtrl->forwardCommand($qui);
                break;
            case "ilpegasushelpertheminggui":
                $ilTabs->setSubTabActive("id_theme");
                include_once("class.ilPegasusHelperThemingGUI.php");
                $qui = new ilPegasusHelperThemingGUI();
                $ilCtrl->forwardCommand($qui);
                break;
            case "ilpegasushelpergeneralgui":
            default:
                $ilTabs->setSubTabActive("id_general");
            include_once("class.ilPegasusHelperGeneralGUI.php");
                $qui = new ilPegasusHelperGeneralGUI();
                $ilCtrl->forwardCommand($qui);
                break;
        }
    }

    public function performCommand($cmd) {}

}

?>
