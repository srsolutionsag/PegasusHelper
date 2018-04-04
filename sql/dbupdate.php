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

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/ilias-app/desktop", "GET");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#5>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/ilias-app/desktop", "OPTIONS");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#6>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/ilias-app/objects/:refId", "GET");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#7>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/ilias-app/objects/:refId", "OPTIONS");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#8>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/ilias-app/files/:refId", "GET");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#9>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/ilias-app/files/:refId", "OPTIONS");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#10>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/files/:id", "GET");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#11>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$route = new SRAG\PegasusHelper\rest\RouteParam("/v1/files/:id", "OPTIONS");

$rest = new SRAG\PegasusHelper\rest\RestSetup();
$rest->addRoute($route);
?>
<#12>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$routes = array(
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/desktop", "GET"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/desktop", "OPTIONS"),

	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/objects/:refId", "GET"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/objects/:refId", "OPTIONS"),

	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/files/:refId", "GET"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/files/:refId", "OPTIONS"),

	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/auth-token", "GET"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/auth-token", "OPTIONS")
);

$rest = new SRAG\PegasusHelper\rest\RestSetup();

foreach ($routes as $route) {
	$rest->addRoute($route);
}
?>
<#13>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';
SRAG\PegasusHelper\entity\UserToken::updateDB();
?>
<#14>
<?php
require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';

$routes = [
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/learnplace/:objectId", "GET"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/learnplace/:objectId", "OPTIONS"),

	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/learnplace/:objectId/journal-entries", "GET"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/learnplace/:objectId/journal-entries", "OPTIONS"),

	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/learnplace/:objectId/journal-entry", "POST"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/learnplace/:objectId/journal-entry", "OPTIONS"),

	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/learnplace/:objectId/blocks", "GET"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/learnplace/:objectId/blocks", "OPTIONS"),
];

$rest = new SRAG\PegasusHelper\rest\RestSetup();

foreach ($routes as $route) {
	$rest->addRoute($route);
}
?>
<#15>
<?php

require_once './Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/bootstrap.php';
$routes = [
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/news", "GET"),
	new SRAG\PegasusHelper\rest\RouteParam("/v2/ilias-app/news", "OPTIONS"),
];

$rest = new SRAG\PegasusHelper\rest\RestSetup();

foreach ($routes as $route) {
	$rest->addRoute($route);
}
?>
