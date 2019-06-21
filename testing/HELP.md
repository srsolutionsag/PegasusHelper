# Reference for Test-Suite

This file contains instructions for troubleshooting the installation of the REST- and PegasusHelper-plugins, based on the results of the test-suite

## General Tests

### Location where script is run (General)

The test-script was not run from the correct working-directory

> 0. Follow the [instructions for running the tests in CLI](../README.md/#testing)

### Location of REST-plugin (General)

This test fails, if the folder containing the REST-plugin does not exist. The Plugin MUST be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/

> 0. If the PegasusHelper-plugin is already installed, then uninstall it
> 0. Follow the [installation instructions for the REST-Plugin](../README.md/#1-install-restplugin)
> 0. [Install the PegasusHelper-Plugin](../README.md/#2-install-pegaushelper)

### Location of PegasusHelper-plugin (General)

This test fails, if the folder that should contain the PegasusHelper-plugin does not exist. The Plugin MUST be located at [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/

> 0. Follow the [installation instructions for the REST-Plugin](../README.md/#1-install-restplugin)

### Location of REST-plugin (General)

This test fails, if the folder that should contain the REST-plugin does not exist

> 0. Follow the [installation instructions for the REST-Plugin](../README.md/#1-install-restplugin)


## ILIAS Tests

### Version (ILIAS)

Your ILIAS-installation is not compatible with either the REST- or PegasusHelper-plugin, in which case it is not possible to setup the Pegasus-App

> 0. Install a version of ILIAS that is compatible with both the REST- and the PegasusHelper-plugins

### Https redirects (ILIAS)

TODO

## REST-plugin Tests

### Version (REST)

You need the correct Version of this plugin

> 0. If the PegasusHelper-plugin is already installed, then uninstall it
> 0. Remove the directory 'REST' in [YOUR_ILIAS]/Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/
> 0. Follow the [installation instructions for the REST-Plugin](../README.md/#1-install-restplugin)
> 0. Optional: Run the test-suite in CLI to check that the installation of REST is ok
> 0. [Install the PegasusHelper-Plugin](../README.md/#2-install-pegaushelper)

### Compatible ILIAS-version (REST)

See [Version (ILIAS)](#version-ilias)

### Entry in ilias-database (REST)

The Plugin is either not installed correctly, or it not installed at all

Solution 1:

> 0. If the PegasusHelper-plugin is already installed, then uninstall it
> 0. In ILIAS, navigate to 'Administration' -> 'Plugins'
> 0. If you dont see an entry called 'REST', continue with solution 2
> 0. Otherwise, click 'Actions' -> 'Install' in the entry 'REST', and wait until the page has reloaded
> 0. You should see a (green) message confirming the installation
> 0. In the entry 'REST', click 'Actions' -> 'Activate' and wait until the page has reloaded
> 0. You should see a (green) message confirming the activation
> 0. [Install the PegasusHelper-Plugin](../README.md/#2-install-pegaushelper)

Solution 2:

> 0. If the PegasusHelper-plugin is already installed, then uninstall it
> 0. Follow the [installation instructions for the REST-Plugin](../README.md/#1-install-restplugin)
> 0. Optional: Run the test-suite in CLI to check that the installation of REST is ok
> 0. [Install the PegasusHelper-Plugin](../README.md/#2-install-pegaushelper)

### Plugin-updates in ilias (REST)

TODO

### Ilias-database version (REST)

TODO

### Active (REST)

TODO

## PegasusHelper-plugin Tests

### Version (PegasusHelper)

TODO

### Compatible ILIAS-version (PegasusHelper)

TODO

### Entry in ilias-database (PegasusHelper)

TODO

### Plugin-updates in ilias (PegasusHelper)

TODO

### Ilias-database version (PegasusHelper)

TODO

### Active (PegasusHelper)

TODO