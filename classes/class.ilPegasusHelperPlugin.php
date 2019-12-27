<?php

require_once __DIR__ . '/../bootstrap.php';

/**
 * Class ilPegasusHelperPlugin
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 * @author Martin Studer <ms@studer-raimann.ch>
 */
final class ilPegasusHelperPlugin extends ilUserInterfaceHookPlugin
{

    /**
     * @var ilPegasusHelperPlugin
     */
    protected static $instance;


    /**
     * @return ilPegasusHelperPlugin
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }


    /**
     * @return string
     */
    public function getPluginName()
    {
        return 'PegasusHelper';
    }

	/**
     * Before update processing
     */
    protected function beforeUpdate()
    {
        /**
         * @var ilPluginAdmin $ilPluginAdmin
         */
        global $ilPluginAdmin;
        if(!$ilPluginAdmin->isActive(IL_COMP_SERVICE, 'UIComponent', 'uihk', 'REST')) {
            ilUtil::sendFailure('Please install the ILIAS REST Plugin first!',true);
            return false;
        }
        return true;
    }

    /**
     * Before uninstall processing
     */
    protected function beforeUninstall() {
        try {
            global $ilDB;
            $ilDB->dropTable("ui_uihk_pegasus_theme", false);

            global $ilPluginAdmin;
            if($ilPluginAdmin->isActive(IL_COMP_SERVICE, 'UIComponent', 'uihk', 'REST')) {
                require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

                $rest = new SRAG\PegasusHelper\rest\RestSetup();
                $rest->deleteClient();
            }
            return true;
        } catch (Exception $e) {
            ilUtil::sendFailure("There was a problem when uninstalling the PegasuHelper plugin", true);
            return false;
        }
    }

}
