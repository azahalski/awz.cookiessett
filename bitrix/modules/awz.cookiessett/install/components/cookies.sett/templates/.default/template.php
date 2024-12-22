<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;

Loc::loadLanguageFile(__DIR__.'/template.php');

/**
 * @var CBitrixComponentTemplate $this
 * @var string $componentPath
 * @var string $templateName
 * @var string $templateFolder
 * @var array $arParams
 * @var array $arResult
 */
\CJSCore::Init(['ajax']);
if($arParams['BUTTON_SETT']){
    \CJSCore::Init(['popup']);
}
$this->setFrameMode(true);
$randStr = 'awz_cookies_'.$this->randString();
$cmpId = 'awz_cmp_cookies_'.$this->randString();
/** @var \Bitrix\Main\Page\FrameBuffered $frame */
$frame = $this->createFrame($randStr, false)->begin();
$options = [
    'siteId'=>Application::getInstance()->getContext()->getSite(),
    'templateName'=>$this->getComponent()->getTemplateName(),
    'templateFolder'=>$templateFolder,
    'componentName'=>$this->getComponent()->getName(),
    'signedParameters'=>$this->getComponent()->getSignedParameters(),
    'cmpId'=>$cmpId,
    'lang'=>[]
];
if($arResult['SHOW_MESSAGE']==='Y'){
    include('message.php');
}
?>
<script type="text/javascript">
    <?if($arParams['INLINE_STYLES']=='Y'){?><?=file_get_contents(__DIR__.'/script.js')?><?}?>
    var <?=$cmpId?> = new window.AwzCookiesSettComponent(<?=CUtil::PHPToJSObject($options)?>);
</script>
<?
$frame->beginStub();
?><div id="<?=$randStr?>"></div>
<?
$frame->end();
?>
<style>
    <?if($arParams['INLINE_STYLES']=='Y'){?><?=file_get_contents(__DIR__.'/style.css')?><?}?>
    <?foreach(\Awz\CookiesSett\Helper::STYLES as $code){
    $code = mb_strtoupper($code);
    if(!$arParams[$code]) continue;
    $type = 'background';
    if(strpos($code,'__COLOR')!==false) $type = 'color';
    ?><?if($code == 'AWZ_COOKIES_SETT__COLOR2_HOVER'){?>a<?}?>.<?=str_replace('_hover',':hover',mb_strtolower($code))?>{<?=$type?>:<?=$arParams[$code]?>;}
    <?}?>
</style>
