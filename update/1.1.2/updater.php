<?
$moduleId = "awz.cookiessett";
if(IsModuleInstalled($moduleId)) {
    $updater->CopyFiles(
        "install/components/cookies.sett",
        "components/awz/cookies.sett",
        true,
        true
    );
}