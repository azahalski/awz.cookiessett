<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}
use Bitrix\Main\Application;
?>
<div class="awz_cookies_sett__message awz_cookies_sett__message_<?=mb_strtolower($arParams['FLOAT'])?> awz_cookies_sett__bg1 awz_cookies_sett__color2">
    <div class="awz_cookies_sett__container">
        <div class="awz_cookies_sett__row">
            <div class="awz_cookies_sett__col" id="awz_cookies_sett__msg">
                <?
                if($arParams['LINK'] && $arParams['LINK_ANCOR']){
                    $link = '<a class="awz_cookies_sett__agr_link awz_cookies_sett__color2" href="'.$arParams['LINK'].'">'.$arParams['LINK_ANCOR'].'</a>';
                }else{
                    $link = '';
                }
                $arParams['MSG'] = str_replace('#LINK#',$link,$arParams['MSG']);
                ?>
                <?=htmlspecialcharsBack($arParams['MSG'])?>
            </div>
            <div class="awz_cookies_sett__col awz_cookies_sett__col__right">
                <?if($arParams['BUTTON_OK']){?>
                    <span class="awz_cookies_sett__btn awz_cookies_sett__bg3 awz_cookies_sett__color3" id="awz_cookies_sett__all"><?=$arParams['BUTTON_OK']?></span>
                <?}?>
                <?if($arParams['BUTTON_SETT']){?>
                    <span class="awz_cookies_sett__btn awz_cookies_sett__bg2 awz_cookies_sett__color1" id="awz_cookies_sett__settings"><?=$arParams['BUTTON_SETT']?></span>
                <?}?>
                <?if($arParams['BUTTON_NO']){?>
                    <span class="awz_cookies_sett__btn awz_cookies_sett__bg2 awz_cookies_sett__color1" id="awz_cookies_sett__all_decline"><?=$arParams['BUTTON_NO']?></span>
                <?}?>
            </div>
        </div>
    </div>
</div>