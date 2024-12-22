<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

$arTemplateParameters = [
    'LINK'=>[
        "NAME" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_LINK'),
        "TYPE" => "STRING",
        "MULTIPLE" => "N",
        "DEFAULT" => "/",
        "PARENT" => "VISUAL",
    ],
    'LINK_ANCOR'=>[
        "NAME" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_LINK_ANCOR'),
        "TYPE" => "STRING",
        "MULTIPLE" => "N",
        "DEFAULT" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_LINK_ANCOR_DEF'),
        "PARENT" => "VISUAL",
    ],
    'BUTTON_OK'=>[
        "NAME" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_OK'),
        "TYPE" => "STRING",
        "MULTIPLE" => "N",
        "DEFAULT" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_OK_DEF'),
        "PARENT" => "VISUAL",
    ],
    'BUTTON_NO'=>[
        "NAME" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_NO'),
        "TYPE" => "STRING",
        "MULTIPLE" => "N",
        "DEFAULT" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_NO_DEF'),
        "PARENT" => "VISUAL",
    ],
    'BUTTON_SETT'=>[
        "NAME" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_SETT'),
        "TYPE" => "STRING",
        "MULTIPLE" => "N",
        "DEFAULT" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_SETT_DEF'),
        "PARENT" => "VISUAL",
    ],
    'MSG'=>[
        "NAME" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_MSG'),
        "TYPE" => "STRING",
        "MULTIPLE" => "N",
        "DEFAULT" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_MSG_DEF'),
        "PARENT" => "VISUAL",
    ]
];

foreach(\Awz\CookiesSett\Helper::STYLES as $key=>$code) {
    $code = mb_strtoupper($code);
    $arTemplateParameters[$code] = [
        "NAME" => Loc::getMessage('AWZ_COOKIES_SETT_PARAM_'.$code),
        "TYPE" => "COLORPICKER",
        "MULTIPLE" => "N",
        "DEFAULT" => \Awz\CookiesSett\Helper::COLORS[$key],
        "PARENT" => "VISUAL",
    ];
}
