<?php
include_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');

/**
 * Class ilPegasusHelper
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ilPegasusHelper extends ilUserInterfaceHookPlugin
{

    /**
     * @var ilPegasusHelper
     */
    protected static $instance;


    /**
     * @return ilPegasusHelper
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
}
