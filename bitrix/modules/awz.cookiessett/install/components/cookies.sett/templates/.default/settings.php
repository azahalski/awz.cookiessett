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
            <div class="awz_cookies_sett__detail-checkbox-wrapper-42">
                <input class="awz_cookies_mode1" name="awz_cookies_mode1" value="Y" id="awz_cookies_mode1" type="checkbox" checked="checked" readonly="readonly" autocomplete="off">
                <label class="cbx dsbl" for="awz_cookies_mode1" onclick="return false;"></label>
            </div>
            <span class="awz_cookies_sett__detail-title"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT1')?></span>
            <span class="awz_cookies_sett__detail-descr"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT1_DESC')?></span>
        </span>
    </div>
    <div class="awz_cookies_sett__detail-row">
        <span class="awz_cookies_sett__detail-checkbox-text">
            <div class="awz_cookies_sett__detail-checkbox-wrapper-42">
                <input class="awz_cookies_mode2" name="awz_cookies_mode2" value="Y" id="awz_cookies_mode2" type="checkbox"<?if($arResult['awz_cookies_mode2']=='Y'){?> checked="checked"<?}?> autocomplete="off">
                <label class="cbx" for="awz_cookies_mode2"></label>
            </div>
            <span class="awz_cookies_sett__detail-title"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT2')?></span>
            <span class="awz_cookies_sett__detail-descr"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT2_DESC')?></span>
        </span>
    </div>
    <div class="awz_cookies_sett__detail-row">
        <span class="awz_cookies_sett__detail-checkbox-text">
            <div class="awz_cookies_sett__detail-checkbox-wrapper-42">
                <input class="awz_cookies_mode3" name="awz_cookies_mode3" value="Y" id="awz_cookies_mode3" type="checkbox"<?if($arResult['awz_cookies_mode3']=='Y'){?> checked="checked"<?}?> autocomplete="off">
                <label class="cbx" for="awz_cookies_mode3"></label>
            </div>
            <span class="awz_cookies_sett__detail-title"><?=Loc::getMessage('AWZ_COOKIES_SETT_CMP_SECT3')?></span>
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
