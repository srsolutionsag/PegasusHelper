<#1>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	$rest = new RestSetup();
	$rest->setupClient();

?>
<#2>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/TokenType.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/TokenParam.php";

	$token = new TokenParam(3600000, TokenType::ACCESS_TOKEN);

	$rest = new RestSetup();
	$rest->configTTL($token);
?>
<#3>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/TokenType.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/TokenParam.php";

	$token = new TokenParam(4500000, TokenType::REFRESH_TOKEN);

	$rest = new RestSetup();
	$rest->configTTL($token);
?>
<#4>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$route = new RouteParam("/v1/ilias-app/desktop", "GET");

	$rest = new RestSetup();
	$rest->addRoute($route);
?>
<#5>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$route = new RouteParam("/v1/ilias-app/desktop", "OPTIONS");

	$rest = new RestSetup();
	$rest->addRoute($route);
?>
<#6>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$route = new RouteParam("/v1/ilias-app/objects/:refId", "GET");

	$rest = new RestSetup();
	$rest->addRoute($route);
?>
<#7>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$route = new RouteParam("/v1/ilias-app/objects/:refId", "OPTIONS");

	$rest = new RestSetup();
	$rest->addRoute($route);
?>
<#8>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$route = new RouteParam("/v1/ilias-app/files/:refId", "GET");

	$rest = new RestSetup();
	$rest->addRoute($route);
?>
<#9>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$route = new RouteParam("/v1/ilias-app/files/:refId", "OPTIONS");

	$rest = new RestSetup();
	$rest->addRoute($route);
?>
<#10>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$route = new RouteParam("/v1/files/:id", "GET");

	$rest = new RestSetup();
	$rest->addRoute($route);
?>
<#11>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$route = new RouteParam("/v1/files/:id", "OPTIONS");

	$rest = new RestSetup();
	$rest->addRoute($route);
?>
<#12>
<?php

	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RestSetup.php";
	require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/rest/RouteParam.php";

	$routes = array(
		new RouteParam("/v2/ilias-app/desktop", "GET"),
		new RouteParam("/v2/ilias-app/desktop", "OPTIONS"),

		new RouteParam("/v2/ilias-app/objects/:refId", "GET"),
		new RouteParam("/v2/ilias-app/objects/:refId", "OPTIONS"),

		new RouteParam("/v2/ilias-app/files/:refId", "GET"),
		new RouteParam("/v2/ilias-app/files/:refId", "OPTIONS"),

		new RouteParam("/v2/ilias-app/auth-token", "GET"),
		new RouteParam("/v2/ilias-app/auth-token", "OPTIONS")
	);

	$rest = new RestSetup();

	foreach ($routes as $route) {
		$rest->addRoute($route);
	}
?>
<#13>
<?php
require_once "./Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/classes/entity/UserToken.php";
	UserToken::updateDB();
?>