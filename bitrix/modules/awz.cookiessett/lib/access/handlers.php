<?php
namespace Awz\Cookiessett\Access;

use Bitrix\Main\Application;
use Bitrix\Main\UserGroupTable;

class Handlers {

    public static function OnAfterUserUpdate(&$arFields)
    {

        if(class_exists('\AwzAcl')){
            return \AwzAcl::OnAfterUserUpdate($arFields);
        }

    }

}