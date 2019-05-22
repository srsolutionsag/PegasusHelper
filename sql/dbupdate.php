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