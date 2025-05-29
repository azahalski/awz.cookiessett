<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\UI\Extension;
use Bitrix\Main\SiteTable;
use Awz\Cookiessett\Access\AccessController;

Loc::loadMessages(__FILE__);
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/awz/cookies.sett/templates/.default/.parameters.php');
global $APPLICATION;
$module_id = "awz.cookiessett";
if(!Loader::includeModule($module_id)) return;
Extension::load('ui.sidepanel-content');
$request = Application::getInstance()->getContext()->getRequest();
$APPLICATION->SetTitle(Loc::getMessage('AWZ_COOKIESSETT_OPT_TITLE'));

if($request->get('IFRAME_TYPE')==='SIDE_SLIDER'){
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
require_once('lib/access/include/moduleright.php');
CMain::finalActions();
die();
}

if(!AccessController::isViewSettings())
$APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$siteRes = SiteTable::getList(['select'=>['LID','NAME'],'filter'=>['ACTIVE'=>'Y']])->fetchAll();
$context = Application::getInstance()->getContext();
$request = $context->getRequest();

if ($request->getRequestMethod()==='POST' && AccessController::isEditSettings() && $request->get('Update'))
{
    $shows = $request->get('SHOW');
    if(!is_array($shows)) $shows = [];
    $PARAMS = $request->get('PARAMS');
    if(!is_array($PARAMS)) $PARAMS = [];
    foreach($siteRes as $arSite){
		if(!isset($PARAMS[$arSite['LID']])) continue;
        if(!isset($shows[$arSite['LID']]) || !$shows[$arSite['LID']]) {
            $shows[$arSite['LID']] = 'N';
        }
        Option::set($module_id, 'SHOW', $shows[$arSite['LID']], $arSite['LID']);
        Option::set($module_id, 'PARAMS', serialize($PARAMS[$arSite['LID']]), $arSite['LID']);
    }
}

$aTabs = array();

$aTabs[] = array(
"DIV" => "edit1",
"TAB" => Loc::getMessage('AWZ_COOKIESSETT_OPT_SECT1'),
"ICON" => "vote_settings",
"TITLE" => Loc::getMessage('AWZ_COOKIESSETT_OPT_SECT1')
);

$saveUrl = $APPLICATION->GetCurPage(false).'?mid='.htmlspecialcharsbx($module_id).'&lang='.LANGUAGE_ID.'&mid_menu=1';
$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
<style>.adm-workarea option:checked {background-color: rgb(206, 206, 206);}</style>
<form method="POST" action="<?=$saveUrl?>" id="FORMACTION">
    <?
    $tabControl->BeginNextTab();
    Extension::load("ui.alerts");
    ?>
    <tr>
        <td colspan="2">
            <div class="ui-alert ui-alert-primary">
                    <span class="ui-alert-message">
                        <?=Loc::getMessage('AWZ_COOKIESSETT_OPT_SHOW_DESC')?>
                    </span>
            </div>
            <textarea style="background: #ffffff;padding:10px;width:100%;height:100px;">
<?='<?'?>$APPLICATION->IncludeComponent("awz:cookies.sett",".default",
    Array(
        "COMPONENT_TEMPLATE" => ".default"
    ),
    null, array("HIDE_ICONS"=>"Y")
);<?='?>'?>
                </textarea>
        </td>
    </tr>
    <?
    $currentSite = $request->get('SITE_ID') ? str_replace($saveUrl.'&SITE_ID=','',$request->get('SITE_ID')) : current($siteRes)['LID'];
    ?>
    <tr>
        <td>
            <?=Loc::getMessage('AWZ_COOKIESSETT_OPT_SITE_ID')?>
        </td>
        <td>
            <select name="SITE_ID" onchange="window.location.href=this.value;">
                <?foreach($siteRes as $arSite){?>
                    <option value="<?=$saveUrl?>&SITE_ID=<?=$arSite['LID']?>"<?if($arSite['LID']==$currentSite){?> selected="selected"<?}?>>
                        [<?=$arSite['LID']?>] - <?=$arSite['NAME']?>
                    </option>
                <?}?>
            </select>
        </td>
    </tr>
    <?

    foreach($siteRes as $arSite){
        if($currentSite!=$arSite['LID']) continue;
        $valParams = unserialize(Option::get($module_id, "PARAMS", "N",$arSite['LID']),['allowed_classes'=>false]);
        if(!is_array($valParams)){
            $valParams = [
                'COMPONENT_TEMPLATE'=>".default",
                "LINK"=>"/",
                "BUTTON_OK"=>Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_OK_DEF'),
                "BUTTON_NO"=>Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_NO_DEF'),
                "BUTTON_SETT"=>Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_SETT_DEF'),
                "LINK_ANCOR"=>Loc::getMessage('AWZ_COOKIES_SETT_PARAM_LINK_ANCOR_DEF'),
                "MSG"=>Loc::getMessage('AWZ_COOKIES_SETT_PARAM_MSG_DEF'),
                "CSS"=>[]
            ];
        }
        ?>
        <tr class="heading">
            <td colspan="2">
                <b>[<?=$arSite['LID']?>] - <?=$arSite['NAME']?></b>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <table>
                    <td>
                        <table>
                            <tr>
                                <td style="width:20%;"><?=Loc::getMessage('AWZ_COOKIESSETT_OPT_SHOW')?></td>
                                <td>
                                    <?$val = Option::get($module_id, "SHOW", "N",$arSite['LID']);?>
                                    <input type="checkbox" value="Y" name="SHOW[<?=$arSite['LID']?>]" <?if ($val=="Y") echo "checked";?>>
                                </td>
                            </tr>

                            <?
                            $styleFile = new \Bitrix\Main\IO\File($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/awz/cookies.sett/templates/.default/style.css');
                            ?>
                            <?if($styleFile->isExists()){
                                \CJSCore::init(['jquery3','color_picker']);
                                ?>
                                <tr class="awz_cookies_sett__admin-<?=$arSite['LID']?>">
                                    <td style="width:20%;"></td>
                                    <td class="awz_cookies_sett__admin">
                                        <?
                                        $arParams = $valParams;
                                        ?>
                                        <style><?=$styleFile->getContents()?></style>
                                        <style id="awz_cookies_sett__style"><?foreach(\Awz\CookiesSett\Helper::STYLES as $code){
                        $code = mb_strtoupper($code);
    if(!$arParams[$code]) continue;
    $type = 'background';
    if(strpos($code,'__COLOR')!==false) $type = 'color';
    ?><?if($code == 'AWZ_COOKIES_SETT__COLOR2_HOVER'){?>a<?}?>.<?=str_replace('_hover',':hover',mb_strtolower($code))?>{<?=$type?>:<?=$arParams[$code]?>;}
                                            <?}?>
                                        </style>
                                        <?include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/awz/cookies.sett/templates/.default/message.php')?>
                                        <script>
                                            var awz_calc_css = function(val){
                                                if(val){
                                                    var val_ar = val.split(',');
                                                    $('.awz-inp-color').each(function(){
                                                        $(this).val(val_ar.shift());
                                                    });
                                                }
                                                if($('input[name="PARAMS[<?=$arSite['LID']?>][BUTTON_OK]"]').val()){
                                                    $('.awz_cookies_sett__admin-<?=$arSite['LID']?> #awz_cookies_sett__all').html(
                                                        $('input[name="PARAMS[<?=$arSite['LID']?>][BUTTON_OK]"]').val()
                                                    ).show();
                                                }else{
                                                    $('.awz_cookies_sett__admin-<?=$arSite['LID']?> #awz_cookies_sett__all').hide();
                                                }

                                                if($('input[name="PARAMS[<?=$arSite['LID']?>][BUTTON_NO]"]').val()){
                                                    $('.awz_cookies_sett__admin-<?=$arSite['LID']?> #awz_cookies_sett__all_decline').html(
                                                        $('input[name="PARAMS[<?=$arSite['LID']?>][BUTTON_NO]"]').val()
                                                    ).show();
                                                }else{
                                                    $('.awz_cookies_sett__admin-<?=$arSite['LID']?> #awz_cookies_sett__all_decline').hide();
                                                }

                                                if($('input[name="PARAMS[<?=$arSite['LID']?>][BUTTON_SETT]"]').val()){
                                                    $('.awz_cookies_sett__admin-<?=$arSite['LID']?> #awz_cookies_sett__settings').html(
                                                        $('input[name="PARAMS[<?=$arSite['LID']?>][BUTTON_SETT]"]').val()
                                                    ).show();
                                                }else{
                                                    $('.awz_cookies_sett__admin-<?=$arSite['LID']?> #awz_cookies_sett__settings').hide();
                                                }

                                                var link = '';
                                                if($('input[name="PARAMS[<?=$arSite['LID']?>][LINK]"]').val() && $('input[name="PARAMS[<?=$arSite['LID']?>][LINK_ANCOR]"]').val()){
                                                    link = '<a class="awz_cookies_sett__agr_link awz_cookies_sett__color2" href="'+$('input[name="PARAMS[<?=$arSite['LID']?>][LINK]"]').val()+'">'+$('input[name="PARAMS[<?=$arSite['LID']?>][LINK_ANCOR]"]').val()+'</a>';
                                                }

                                                if($('textarea[name="PARAMS[<?=$arSite['LID']?>][MSG]"]').val()){
                                                    $('.awz_cookies_sett__admin-<?=$arSite['LID']?> #awz_cookies_sett__msg').html(
                                                        $('textarea[name="PARAMS[<?=$arSite['LID']?>][MSG]"]').val().replace('#LINK#',link)
                                                    );
                                                }

                                                var str_style = '';
                                                <?foreach(\Awz\CookiesSett\Helper::STYLES as $code){
                                                $code = mb_strtoupper($code);
                                                $classCss = '.'.str_replace('_hover',':hover',mb_strtolower($code));
                                                if($code == 'AWZ_COOKIES_SETT__COLOR2_HOVER') $classCss = 'a'.$classCss;
                                                $type = 'background';
                                                if(strpos($code,'__COLOR')!==false) $type = 'color';
                                                ?>
                                                str_style += '<?=$classCss?>{<?=$type?>:'+$('input[name="PARAMS[<?=$arSite['LID']?>][<?=$code?>]"]').val()+'}';
                                                <?}?>
                                                $('#awz_cookies_sett__style').html(str_style);
                                            };
                                            $(document).on('keyup', '.awz_cookies_sett__admin-<?=$arSite['LID']?> .awz--load-html-js', function(){
                                                awz_calc_css();
                                            });
                                            $(document).on('change', '.awz_cookies_sett__admin-<?=$arSite['LID']?> .awz--load-html-js', function(){
                                                awz_calc_css();
                                            });

                                            var awz_picker = new BX.ColorPicker({
                                                bindElement: null,
                                                defaultColor: "#000000",
                                                popupOptions: {
                                                    offsetTop: 10,
                                                    offsetLeft: 10,
                                                    angle: true,
                                                    events: {
                                                        onPopupClose: function() {
                                                            awz_calc_css();
                                                        },
                                                        onPopupShow: function() {
                                                            awz_calc_css();
                                                        }
                                                    }
                                                }
                                            });
                                            function awz_onButtonClick(event)
                                            {
                                                event.preventDefault();
                                                var target = event.target;
                                                var input = target.previousElementSibling;
                                                awz_picker.open({
                                                    selectedColor: BX.type.isNotEmptyString(input.value) ? input.value : null,
                                                    bindElement: target,
                                                    onColorSelected: awz_onColorSelected.bind(input)
                                                });
                                            }

                                            function awz_onColorSelected(color, picker)
                                            {
                                                this.value = color;
                                                awz_calc_css();
                                            }

                                        </script>
                                    </td>
                                </tr>
                            <?}?>

                            <tr class="awz_cookies_sett__admin-<?=$arSite['LID']?>">
                                <td style="width:20%;"><?=Loc::getMessage('AWZ_COOKIES_SETT_PARAM_LINK')?></td>
                                <td>
                                    <input class="awz--load-html-js" type="text" value="<?=$valParams['LINK']?>" name="PARAMS[<?=$arSite['LID']?>][LINK]">
                                </td>
                            </tr>
                            <tr class="awz_cookies_sett__admin-<?=$arSite['LID']?>">
                                <td style="width:20%;"><?=Loc::getMessage('AWZ_COOKIES_SETT_PARAM_LINK_ANCOR')?></td>
                                <td>
                                    <input class="awz--load-html-js" type="text" value="<?=$valParams['LINK_ANCOR']?>" name="PARAMS[<?=$arSite['LID']?>][LINK_ANCOR]">
                                </td>
                            </tr>
                            <tr class="awz_cookies_sett__admin-<?=$arSite['LID']?>">
                                <td style="width:20%;"><?=Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_OK')?></td>
                                <td>
                                    <input class="awz--load-html-js" type="text" value="<?=$valParams['BUTTON_OK']?>" name="PARAMS[<?=$arSite['LID']?>][BUTTON_OK]">
                                </td>
                            </tr>
                            <tr class="awz_cookies_sett__admin-<?=$arSite['LID']?>">
                                <td style="width:20%;"><?=Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_NO')?></td>
                                <td>
                                    <input class="awz--load-html-js" type="text" value="<?=$valParams['BUTTON_NO']?>" name="PARAMS[<?=$arSite['LID']?>][BUTTON_NO]">
                                </td>
                            </tr>
                            <tr class="awz_cookies_sett__admin-<?=$arSite['LID']?>">
                                <td style="width:20%;"><?=Loc::getMessage('AWZ_COOKIES_SETT_PARAM_BUTTON_SETT')?></td>
                                <td>
                                    <input class="awz--load-html-js" type="text" value="<?=$valParams['BUTTON_SETT']?>" name="PARAMS[<?=$arSite['LID']?>][BUTTON_SETT]">
                                </td>
                            </tr>
                            <tr class="awz_cookies_sett__admin-<?=$arSite['LID']?>">
                                <td style="width:20%;"><?=Loc::getMessage('AWZ_COOKIES_SETT_PARAM_MSG')?></td>
                                <td>
                                    <textarea class="awz--load-html-js" cols="40" rows="4" name="PARAMS[<?=$arSite['LID']?>][MSG]"><?=trim($valParams['MSG'])?></textarea>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td style="width:20%;"><?=Loc::getMessage('AWZ_COOKIESSETT_OPT_THEME')?></td>
                                <td>
                                    <select id="awz_theme_preset" onchange="awz_calc_css(this.value);">
                                        <option value="">-</option>
                                        <option value="#171241,#ffffff,#ffe40e,#f1d600,#ffe40e,#171241,#ffffff,#ffe40e,#171241"><?=Loc::getMessage('AWZ_COOKIESSETT_OPT_THEME_DEFAULT')?></option>
                                        <option value="#ef3000,#ffffff,#ffe40e,#f1d600,#ffe40e,#000000,#ffffff,#ffe40e,#000000"><?=Loc::getMessage('AWZ_COOKIESSETT_OPT_THEME_RED')?></option>
                                        <option value="#0083D1,#ffffff,#ffe40e,#f1d600,#ffe40e,#000000,#ffffff,#ffe40e,#000000"><?=Loc::getMessage('AWZ_COOKIESSETT_OPT_THEME_BLUE')?></option>
                                        <option value="#33b111,#ffffff,#ffe40e,#f1d600,#ffe40e,#000000,#ffffff,#ffe40e,#000000"><?=Loc::getMessage('AWZ_COOKIESSETT_OPT_THEME_GREEN')?></option>
                                        <option value="#F9A91D,#ffffff,#fff893,#ffe40e,#fff893,#000000,#ffffff,#ffffff,#000000"><?=Loc::getMessage('AWZ_COOKIESSETT_OPT_THEME_YELOW')?></option>
                                        <option value="#ffe4e4,#ffffff,#fffadc,#dc2f2f,#f85151,#000000,#000000,#000000,#ffffff"><?=Loc::getMessage('AWZ_COOKIESSETT_OPT_THEME_PINK')?></option>
                                    </select>
                                </td>
                            </tr>
                            <?
                            $defColors = \Awz\CookiesSett\Helper::COLORS;
                            $defStyles = \Awz\CookiesSett\Helper::STYLES;
                            foreach($defStyles as $key=>$code){
                                $code = mb_strtoupper($code);
                                $colorVal = $arParams[$code] ? $arParams[$code] : $defColors[$key];
                                ?>
                                <tr class="awz_cookies_sett__admin-<?=$arSite['LID']?>">
                                    <td style="width:50%;">
                                        <?=Loc::getMessage('AWZ_COOKIES_SETT_PARAM_'.$code)?>
                                    </td>
                                    <td>
                                        <input size="7" class="awz--load-html-js awz-inp-color" type="text" value="<?=$colorVal?>" name="PARAMS[<?=$arSite['LID']?>][<?=$code?>]"><a href="#" class="" id="PARAMS_<?=$arSite['LID']?>_CSS_<?=$code?>" style="display:inline-block;margin-left:5px;margin-top:3px;border-radius:10px;background:<?=$colorVal?>;padding:5px 15px;">
                                            ...
                                        </a>
                                        <script>BX.bind(BX("PARAMS_<?=$arSite['LID']?>_CSS_<?=$code?>"), "click", awz_onButtonClick);</script>
                                    </td>
                                </tr>
                                <?
                            }
                            ?>
                        </table>
                    </td>
                </table>
            </td>
        </tr>




        <?
    }
    ?>
    <?
    $tabControl->Buttons();
    ?>
    <input <?if (!AccessController::isEditSettings()) echo "disabled" ?> type="submit" class="adm-btn-green" name="Update" value="<?=Loc::getMessage('AWZ_COOKIESSETT_OPT_L_BTN_SAVE')?>" />
    <input type="hidden" name="Update" value="Y" />
    <?if(AccessController::isViewRight()){?>
        <button class="adm-header-btn adm-security-btn" onclick="BX.SidePanel.Instance.open('<?=$saveUrl?>');return false;">
            <?=Loc::getMessage('AWZ_COOKIESSETT_OPT_SECT2')?>
        </button>
    <?}?>
    <?$tabControl->End();?>
</form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");