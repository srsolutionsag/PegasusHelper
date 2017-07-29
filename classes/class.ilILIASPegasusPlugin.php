<?php
include_once('./Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php');

/**
 * Class ilILIASPegasusPlugin
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ilILIASPegasusPlugin extends ilUserInterfaceHookPlugin
{

    /**
     * @var ilILIASPegasusPlugin
     */
    protected static $instance;


    /**
     * @return ilILIASPegasusPlugin
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
        return 'ILIASPegasus';
    }
}
