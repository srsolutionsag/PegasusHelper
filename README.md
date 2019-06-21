# PegasusHelper
The pegasus helper is a small helper plugin for ILIAS which is required to
operate the ILIAS Pegasus mobile application.

The plugin enables the mobile application to:
- Login
- Download files from ILIAS except FileObjects
- Open ILIAS pages
- Open personal news of the user
- Configure required REST plugin routes and client



## Requirements
* Version: ILIAS 5.2 or 5.3
* PHP 5.5.9 or PHP 7

## Installation

### 1. Install RESTPlugin
Branch feature/sr-app-routes  
Start at your ILIAS root directory 
```bash
mkdir -p Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/  
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/  
git clone https://github.com/studer-raimann/Ilias.RESTPlugin REST
cd REST
git checkout feature/sr-app-routes
```  
Update and activate the plugin in the ILIAS Plugin Administration.

### 2. Install PegausHelper
Start at your ILIAS root directory 
```bash
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/  
git clone https://github.com/studer-raimann/PegasusHelper.git  
```
Update and activate the plugin in the ILIAS Plugin Administration.

### 3. Get the API Secret  
- Go to the ILIAS Plugin Administration  
- Choose the Action 'Configure' of the REST Plugin  
- Click the Button 'Start Administration Panel'  
- Click the Button 'Manage API-Keys and Authorization Schemes'  
- Click the Button 'Modify' at the row 'ilias_pegasus'  

## Testing
If the installation of the REST- or PegasusHelper-plugin as described above fails, the test-script in the directory 'testing' may provide useful information

Start at your ILIAS root directory
```bash
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper/testing/
php run.php
```
The script prints out feedback from the tests and writes a log-file 'results.log' in 'PegasusHelper/testing/'

Also read through the paragraph 'Caveats' below

## Update

### 1. Update RESTPlugin
Branch feature/sr-app-routes
Start at your ILIAS root directory

```bash
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/REST
git pull
```
Update / activate the plugin in the ILIAS Plugin Administration.

### 2. Update PegasusHelper
Start at your ILIAS root directory 

```bash
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/PegasusHelper
git pull
```

Update / activate the plugin in the ILIAS Plugin Administration.

## Caveats
### ILIAS http path

#### Description
If the host address of ILIAS is configured with http but requests to ILIAS are
redirected to https, the plugin migration of the PegasusHelper will fail.

The PegasusHelper configures the REST plugin while updating to ensure that all
routes are as expected by the Pegasus mobile application. In order to configure the REST
plugin the PegasusHelper adds all routes to the REST plugin with local http POST requests.

The redirect will transform the POST request to a GET request which is not understood by
the REST plugin which leads to the migration error of the PegasusHelper.

#### Solution
To ensure that no https redirects are done, the configuration in the ilias.ini.php has to
be adjusted as shown in the example below.

The ilias.ini.php is located in the root directory of the ILIAS installation.
```text
[server]
http_path = "https://your.ilias-installation.org"
```

## Versioning
We use SemVer for versioning. For the versions available, see the tags on this repository.

## License
This project is licensed under the GNU GPLv3 License - see the LICENSE.md file for details.

## Acknowledgments
[composer](https://getcomposer.org/)

## Contact

studer + raimann ag  
Farbweg 9  
3400 Burgdorf  
Switzerland

[info@studer-raimann.ch](mailto:info@studer-raimann.ch)  
<https://www.studer-raimann.ch>

