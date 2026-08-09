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
            $disabledSetting = (string)Option::get("awz.cookiessett", 'DSBL_REJ', '', SITE_ID);

            if ($arExcluded = explode("\n", $disabledSetting)) {
                foreach ($arExcluded as $exc) {
                    $exc = trim($exc);
                    if (strlen($exc) < 3) {
                        continue;
                    }

                    // --- ЗАЩИТА ОТ ФЛАГА /e ---
                    // Ищем последний ограничитель и проверяем флаги после него
                    $lastDelimiterPos = strrpos($exc, $exc[0] ?? '/');
                    if ($lastDelimiterPos !== false) {
                        $modifiers = substr($exc, $lastDelimiterPos + 1);
                        // Если среди модификаторов есть 'e' (без учета регистра), блокируем строку
                        if (stripos($modifiers, 'e') !== false) {
                            continue;
                        }
                    }
                    // --------------------------

                    try {
                        // @ подавляет Warning при ошибках синтаксиса
                        @ini_set('pcre.backtrack_limit', 1000);
                        $match = @preg_match($exc, $curPage);
                        @ini_set('pcre.backtrack_limit', 1000000);
                        if ($match === false) {
                            continue;
                        }

                        // Если вернулся false — регулярка некорректна, пропускаем её
                        if ($match === false) {
                            continue;
                        }

                        if ($match > 0) {
                            return;
                        }
                    } catch (\Throwable $e) {
                        // Перехватывает CompileError в PHP 7/8, если регулярка сломала парсер
                        continue;
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