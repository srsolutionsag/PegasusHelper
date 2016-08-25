<?php
include_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');

/**
 * Class ilILIASAppPlugin
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ilILIASAppPlugin extends ilUserInterfaceHookPlugin
{

    /**
     * @var ilILIASAppPlugin
     */
    protected static $instance;


    /**
     * @return ilILIASAppPlugin
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
        return 'ILIASApp';
    }
}
