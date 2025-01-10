<?php
namespace Awz\CookiesSett;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

class Handlers {

    public static function OnPageStart(){
        $context = Application::getInstance()->getContext();
        if($context->getRequest()->isAdminSection()) return;
        if(
            Option::get("awz.cookiessett", 'SHOW', 'N', SITE_ID)==="Y"
        ){
            $strParams = Option::get("awz.cookiessett", 'PARAMS', '', SITE_ID);
            $strArParams = unserialize(
                $strParams,
                ['allowed_classes'=>false]
            );
            \CJSCore::Init(['ajax']);
            if($strArParams['BUTTON_SETT']){
                \CJSCore::Init(['popup']);
            }
        }
    }

    public static function OnEndBufferContent(&$content){
        $context = Application::getInstance()->getContext();
        if($context->getRequest()->isAdminSection()) return;
        if(
            Option::get("awz.cookiessett", 'SHOW', 'N', SITE_ID)==="Y" &&
            mb_strpos(mb_substr($content,-20), '</body>')!==false
        ){
            global $APPLICATION;
            ob_start();
            $strParams = Option::get("awz.cookiessett", 'PARAMS', '', SITE_ID);
            $strArParams = unserialize(
                $strParams,
                ['allowed_classes'=>false]
            );
            $strArParams["INLINE_STYLES"]="Y";
            if(!is_array($strArParams)) $strArParams = ['COMPONENT_TEMPLATE'=>".default"];
            $APPLICATION->IncludeComponent("awz:cookies.sett",".default",
                $strArParams, null, array("HIDE_ICONS"=>"Y")
            );
            $html = ob_get_contents();
            $html = preg_replace("/(\s+)/is"," ", $html);
            $html = str_replace(["\n","\t","\r"],"", $html);
            $html = preg_replace("/\s?([:;{>}=])\s?/is","$1", $html);

            ob_end_clean();
            $content = str_replace('</body>',$html."\n".'</body>',$content);
        }
    }

}