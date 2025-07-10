<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;

Loc::loadLanguageFile(__DIR__.'/template.php');
?>
<form id="awz_cookies_sett__detail-form">
<div class="awz_cookies_sett__detail">
    <div class="awz_cookies_sett__detail-row">
        <div class="awz_cookies_sett__detail-col">
        <div class="awz_cookies_sett__detail-alert">
            <?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_ALERT')?>
        </div>
        </div>
    </div>
    <div class="awz_cookies_sett__detail-row">
        <span class="awz_cookies_sett__detail-checkbox-text">
            <span class="awz_cookies_sett__detail-checkbox-top">
                <span class="awz_cookies_sett__detail-title"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT1')?></span>
                <label class="awz_cookies_sett__switch">
                    <input type="hidden" name="awz_cookies_mode1" value="Y">
                    <input disabled="disabled" type="checkbox" id="awz_cookies_mode1" name="awz_cookies_mode1-dsbl" value="Y" class="awz_cookies_mode1" type="checkbox" checked="checked" autocomplete="off">
                    <span class="awz_cookies_sett__slider round"></span>
                </label>
            </span>
            <span class="awz_cookies_sett__detail-descr"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT1_DESC')?></span>
        </span>
    </div>
    <div class="awz_cookies_sett__detail-row">
        <span class="awz_cookies_sett__detail-checkbox-text">
            <span class="awz_cookies_sett__detail-checkbox-top">
                <span class="awz_cookies_sett__detail-title"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT2')?></span>
                <label class="awz_cookies_sett__switch">
                    <input type="checkbox" id="awz_cookies_mode2" name="awz_cookies_mode2" value="Y" class="awz_cookies_mode2" type="checkbox"<?if($arResult['awz_cookies_mode2']=='Y'){?> checked="checked"<?}?> autocomplete="off">
                    <span class="awz_cookies_sett__slider round"></span>
                </label>
            </span>
            <span class="awz_cookies_sett__detail-descr"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT2_DESC')?></span>
        </span>
    </div>
    <div class="awz_cookies_sett__detail-row">
        <span class="awz_cookies_sett__detail-checkbox-text">
            <span class="awz_cookies_sett__detail-checkbox-top">
                <span class="awz_cookies_sett__detail-title"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT3')?></span>
                <label class="awz_cookies_sett__switch">
                    <input type="checkbox" id="awz_cookies_mode3" name="awz_cookies_mode3" value="Y" class="awz_cookies_mode3" type="checkbox"<?if($arResult['awz_cookies_mode3']=='Y'){?> checked="checked"<?}?> autocomplete="off">
                    <span class="awz_cookies_sett__slider round"></span>
                </label>
            </span>
            <span class="awz_cookies_sett__detail-descr"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT3_DESC')?></span>
        </span>
    </div>
    <div class="awz_cookies_sett__detail-row">
        <div class="awz_cookies_sett__detail-col">
            <button href="#" class="awz_cookies_sett__save" id="awz_cookies_sett__save">
                <?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SAVE')?>
            </button>
        </div>
    </div>
</div>
</form>
