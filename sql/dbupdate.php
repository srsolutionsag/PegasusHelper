<#1>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->setupClient();

?>
<#2>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$token = new SRAG\PegasusHelper\rest\TokenParam(3600000, SRAG\PegasusHelper\rest\TokenType::ACCESS_TOKEN);

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->configTTL($token);
?>
<#3>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$token = new SRAG\PegasusHelper\rest\TokenParam(4500000, SRAG\PegasusHelper\rest\TokenType::REFRESH_TOKEN);

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->configTTL($token);
?>
<#4>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/files/:id", "GET");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#5>
<?php
$error_if_not_existing = false;
$ilDB->dropTable("ui_uihk_pegasus_theme", $error_if_not_existing);

$fields = array(
    'id' => array(
        'type'    => 'integer',
        'length'  => 4,
        'notnull' => true
    ),
    'primary_color'  => array(
        'type'        => 'text',
        'length'      => 10,
        'fixed'       => true,
        'notnull'     => true
    ),
    'contrast_color' => array(
        'type'        => 'integer',
        'length'      => 4,
        'notnull'     => true
    )
);
$ilDB->createTable('ui_uihk_pegasus_theme', $fields, true);

$ilDB->addPrimaryKey('ui_uihk_pegasus_theme', array('id'));
$ilDB->manipulate('ALTER TABLE ui_uihk_pegasus_theme CHANGE id id INT NOT NULL AUTO_INCREMENT');

global $ilLog;
$ilLog->write('Plugin PegasusHelper -> DB-Update #5: Created ui_uihk_pegasus_theme.');
?>
<#6>
<?php
$ilDB->insert('ui_uihk_pegasus_theme', array(
    'primary_color'             => array('text', '4a668b'),
    'contrast_color'            => array('integer', 1)
));

global $ilLog;
$ilLog->write('Plugin PegasusHelper -> DB-Update #6: Filled ui_uihk_pegasus_theme.');
?>
<#7>
<?php
$ilDB->addTableColumn("ui_uihk_pegasus_theme", "timestamp",
    array(
        "type" => "integer",
        "length" => 8,
        "notnull" => true
    )
);
?>
<#8>
<?php
$values = array(
    "timestamp" => array("integer", time())
);
$where = array(
    "id" => array("integer", 1)
);

$ilDB->update("ui_uihk_pegasus_theme", $values, $where);
?>
<#9>
<?php
ilPegasusHelperConfigGUI::copyDefaultIcons();
?>
