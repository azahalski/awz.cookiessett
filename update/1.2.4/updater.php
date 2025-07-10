<?
$moduleId = "awz.cookiessett";
if(IsModuleInstalled($moduleId)) {
    $updater->CopyFiles(
        "install/components",
        "components/awz"
    );
}