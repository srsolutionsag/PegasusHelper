<#1>
<?php
/**
 * @var ilPluginAdmin $ilPluginAdmin
 */
global $ilPluginAdmin;
//Check Preconditions
if(!$ilPluginAdmin->isActive(IL_COMP_PLUGIN, 'UIComponent', 'uihk', $questionData['REST'])) {
    ilUtil::sendFailure('Please install the ILIAS REST Plugin first!');
}
?>