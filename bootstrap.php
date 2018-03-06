<?php
/**
 * File bootstrap.php
 *
 * @author  Nicolas Schäfli <ns@studer-raimann.ch>
 */

use SRAG\PegasusHelper\container\PegasusHelperContainer;

require_once __DIR__ . '/vendor/autoload.php';
PegasusHelperContainer::bootstrap();