<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;

$arComponentDescription = array(
	"NAME" => Loc::getMessage("AWZ_COOKIES_SETT_PARAM_DESCR_NAME"),
	"DESCRIPTION" => Loc::getMessage("AWZ_COOKIES_SETT_PARAM_DESCR_NAME"),
	"ICON" => "/images/cookies.gif",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "awz",
		"NAME" => Loc::getMessage("AWZ_COOKIES_SETT_PARAM_DESCR_GROUP"),
		"CHILD" => array(
			"ID" => "awzuser",
			"NAME" => Loc::getMessage("AWZ_COOKIES_SETT_PARAM_DESCR_GROUP_USER")
		)
	),
);
