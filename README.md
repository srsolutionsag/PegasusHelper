## Installation

1. Install RESTPlugin; Branch feature/sr-app-routes  
Start at your ILIAS root directory 
```bash
mkdir -p Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/  
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/  
git clone https://github.com/studer-raimann/Ilias.RESTPlugin REST
cd REST
git checkout feature/sr-app-routes
```  
Update and activate the plugin in the ILIAS Plugin Administration

2. Install PegausHelper
Start at your ILIAS root directory 
```bash
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook/  
git clone https://github.com/studer-raimann/PegasusHelper.git  
```
Update and activate the plugin in the ILIAS Plugin Administration

3. Get the API Secret
3.1 Go to the ILIAS Plugin Administration  
3.2 Choose the Action 'Configure' of the REST Plugin  
3.3 Click the Button 'Start Administration Panel'  
3.4 Click the Button 'Manage API-Keys and Authorization Schemes'  
3.5 Click the Button 'Modify' at the row 'ilias_pegasus'  

## Contact
studer + raimann ag  
Farbweg 9  
3400 Burgdorf  
Switzerland 

info@studer-raimann.ch  
www.studer-raimann.ch  
