<?php

/**
 * performs tests for troubleshooting the installation of the REST- and PegasusHelper-plugins
 * run the script as "php run.php" from the commandline
 *
 * @author Marc Schneiter <msc@studer-raimann.ch>
 */

include_once "auxiliaries.php";
include_once "sources.php";
include_once "tests.php";

print "Diagnostics for REST- and PegasusHelper-plugins\n";
print "===============================================\n";

init();

print "\n> Gathering information for tests...\n";

$info = getInfo();

print "\n> Running tests...\n";

$testsList = [
    [
        "title" => "0) general",
        "tests" => [
            [
                "msg" => "location where script is run",
                "fn" => "testExecutionDirectory"
            ],
            [
                "msg" => "location of REST-plugin",
                "fn" => "testRESTDirectory"
            ],
            [
                "msg" => "location of PegasusHelper-plugin",
                "fn" => "testPegasusHelperDirectory"
            ],
            [
                "msg" => "connection to ILIAS-database",
                "fn" => "testIlBDConnection"
            ]
        ]
    ],
    [
        "title" => "1) ILIAS",
        "tests" => [
            [
                "msg" => "version",
                "fn" => "testILIASVersion"
            ],
            [
                "msg" => "https redirects",
                "fn" => "testILIASRedirectStatement"
            ]
        ]
    ],
    [
        "title" => "2) REST-plugin",
        "tests" => [
            [
                "msg" => "version",
                "fn" => "testRESTVersion"
            ],
            [
                "msg" => "compatible ilias-version",
                "fn" => "testRESTkMinMaxVersion"
            ],
            [
                "msg" => "entry in ilias-database",
                "fn" => "testRESTInIlDB"
            ],
            [
                "msg" => "ilias-database version",
                "fn" => "testRESTDbVersion"
            ],
            [
                "msg" => "active",
                "fn" => "testRESTPluginActive"
            ]
        ]
    ],
    [
        "title" => "3) PegasusHelper-plugin",
        "tests" => [
            [
                "msg" => "version",
                "fn" => "testPegasusHelperVersion"
            ],
            [
                "msg" => "compatible ilias-version",
                "fn" => "testPegasusHelperMinMaxVersion"
            ],
            [
                "msg" => "entry in ilias-database",
                "fn" => "testPegasusHelperInIlDB"
            ],
            [
                "msg" => "ilias-database version",
                "fn" => "testPegasusHelperDbVersion"
            ],
            [
                "msg" => "active",
                "fn" => "testPegasusHelperPluginActive"
            ]
        ]
    ]
];

runTests($testsList, $info);

finalize();