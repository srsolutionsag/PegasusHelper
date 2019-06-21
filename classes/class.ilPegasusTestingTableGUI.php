<?php
include_once ('./Services/Table/classes/class.ilTable2GUI.php');
include_once "class.ilPegasusTesting.php";

/**
 * Table for Pegasus Testing Results
 */
class ilPegasusTestingTableGUI extends ilTable2GUI {

    public function __construct($a_parent_obj, $primary_column_name) {
        parent::__construct($a_parent_obj);

        $this->setEnableHeader(true);
        $this->disable('sort');
        $this->disable('numinfo');
        $this->setLimit(100);
        $this->setRowTemplate("tpl.pegasus_testing_row.html", "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes");

        $this->addColumn("","status","5%");
        $this->addColumn("<b>" . ucfirst($primary_column_name) . "</b>","test", "30%");
        $this->addColumn("<b>Info</b>","info");
    }

    /**
     * Fill a single data row.
     */
    protected function fillRow($a_set) {
        if(isset($a_set["category"])) {
            $this->tpl->setCurrentBlock("category");
            $this->tpl->setVariable("TXT_CATEGORY", ucfirst($a_set["category"]));
            $this->tpl->parseCurrentBlock();
        } else {
            $this->tpl->setCurrentBlock("test");
            list($img_path, $img_info) = $this->getStatusImg($a_set["status"]);
            $this->tpl->setVariable("IMG_PATH", $img_path);
            $this->tpl->setVariable("IMG_INFO", ucfirst($img_info));
            $this->tpl->setVariable("TXT_TEST", ucfirst($a_set["test"]));
            $this->tpl->setVariable("TXT_INFO", ucfirst($a_set["info"]));
            $this->tpl->parseCurrentBlock();
        }
    }

    private function getStatusImg($status) {
        require_once __DIR__ . "/class.ilPegasusTestingStatus.php";
        return [ilPegasusTestingStatus::getStatusImagePath($status), ilPegasusTestingStatus::getStatusText($status)];
    }
}