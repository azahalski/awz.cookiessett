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
        $request = Application::getInstance()->getContext()->getRequest();
        if($request->isAdminSection()) return;
        if($request->isPost()) return;
        if($request->isAjaxRequest()) return;

        $dsbl_get = explode(",", Option::get("awz.cookiessett", 'DSBL_GET', '', SITE_ID));

        if(!empty($dsbl_get)){
            foreach($dsbl_get as $prm){
                $key_get = trim($prm);
                if($key_get && $request->get($key_get))
                    return;
            }
        }

        if(
            Option::get("awz.cookiessett", 'SHOW', 'N', SITE_ID)==="Y" &&
            mb_strpos(mb_substr($content,-20), '</body>')!==false
        ){
            $curPage = $request->getRequestUri();
            if ($arExcluded = explode("\n", Option::get("awz.cookiessett", 'DSBL_REJ', '', SITE_ID))) {
                foreach ($arExcluded as $exc) {
                    if(!trim($exc) || strlen(trim($exc))<3) continue;
                    try{
                        if (preg_match($exc, $curPage)) {
                            return;
                        }
                    }catch (\Exception $e){

                    }
                }
            }

            global $APPLICATION;
            ob_start();
            $strParams = Option::get("awz.cookiessett", 'PARAMS', '', SITE_ID);
            $strArParams = unserialize(
                $strParams,
                ['allowed_classes'=>false]
            );
            $strArParams["INLINE_STYLES"]="Y";
            $strArParams["SITE_ID"] = SITE_ID;
            if(!is_array($strArParams)) $strArParams = ['COMPONENT_TEMPLATE'=>".default"];
            $APPLICATION->IncludeComponent("awz:cookies.sett",".default",
                $strArParams, null, array("HIDE_ICONS"=>"Y")
            );
            $html = ob_get_contents();
            $html = preg_replace("/(\s+)/is"," ", $html);
            $html = str_replace(["\n","\t","\r"],"", $html);
            $html = preg_replace("/\s?([:;{>}=])\s?/is","$1", $html);

            ob_end_clean();
            $contentAr = explode('</body>',$content);
            $contentAr[count($contentAr)-2] .= "\n".$html."\n";
            $content = implode('</body>',$contentAr);
        }
    }

}