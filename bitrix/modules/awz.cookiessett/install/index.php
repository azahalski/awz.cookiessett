<?php
use Bitrix\Main\Localization\Loc,
    Bitrix\Main\EventManager,
    Bitrix\Main\ModuleManager,
    Bitrix\Main\Application;

Loc::loadMessages(__FILE__);

class awz_cookiessett extends CModule
{
	var $MODULE_ID = "awz.cookiessett";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

    public function __construct()
	{
        $arModuleVersion = [];

        include(__DIR__.'/version.php');

		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

        $this->MODULE_NAME = Loc::getMessage("AWZ_COOKIESSETT_MODULE_NAME");
        $this->MODULE_DESCRIPTION = Loc::getMessage("AWZ_COOKIESSETT_MODULE_DESCRIPTION");
		$this->PARTNER_NAME = Loc::getMessage("AWZ_COMPANY_NAME");
		$this->PARTNER_URI = "https://zahalski.dev/";

		return true;
	}

	function DoInstall()
	{
		RegisterModule($this->MODULE_ID);
	}

	function DoUninstall()
	{
		UnRegisterModule($this->MODULE_ID);
	}
	
	function InstallDB() {
		return true;
	}

    function UnInstallDB()
    {
        return true;
    }

}