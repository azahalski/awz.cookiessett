<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

use Awz\AutForm\CodesTable;
use Awz\AutForm\Events;
use Awz\AutForm\Helper;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Context;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Errorable;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\Security;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Security\Random;
use Bitrix\Main\Service\GeoIp\Manager;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserConsent\Agreement;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\UserTable;
use Bitrix\Main\Application;
use Bitrix\Sale\Internals\OrderPropsValueTable;
use Awz\CookiesSett\App as CookieApp;

Loc::loadMessages(__FILE__);

class AwzCookiesSettComponent extends CBitrixComponent implements Controllerable, Errorable
{
    /** @var ErrorCollection */
    protected $errorCollection;

    /** @var  Bitrix\Main\HttpRequest */
    protected $request;

    /** @var Context $context */
    protected $context;

    public $arParams = array();
    public $arResult = array();

    public $userGroups = array();

    /**
     * Ajax actions
     *
     * @return array[][]
     */
    public function configureActions(): array
    {
        return [
            'getSett' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf()
                ],
            ],
            'allow' => [
                'prefilters' => [
                    new ActionFilter\HttpMethod([
                        ActionFilter\HttpMethod::METHOD_POST
                    ]),
                    new ActionFilter\Csrf()
                ],
            ],
        ];
    }

    /**
     * Signed params
     *
     * @return string[]
     */
    protected function listKeysSignedParameters(): array
    {
        return [
            'COMPONENT_TEMPLATE',
            'SITE_ID'
        ];
    }

    /**
     * Create default component params
     *
     * @param array $arParams параметры
     * @return array
     */
    public function onPrepareComponentParams($arParams): array
    {
        $this->errorCollection = new ErrorCollection();
        $this->arParams = &$arParams;

        return $arParams;
    }

    /**
     * Show public component
     *
     * @throws LoaderException
     */
    public function executeComponent()
    {
        if(!$this->isRequiredModule())
        {
            ShowError(Loc::getMessage('AWZ_COOKIES_SETT_MODULE_NOT_INSTALL'));
            return;
        }
        $app = CookieApp::getInstance();
        $this->arResult['SHOW_MESSAGE'] = $app->isEmpty() ? 'Y' : 'N';

        if($app->isEmpty()){
            $this->arResult['awz_cookies_mode2'] = 'Y';
            $this->arResult['awz_cookies_mode3'] = 'Y';
        }else{
            $this->arResult['awz_cookies_mode1'] = $app->check(CookieApp::USER_REQUIRE) ? 'Y' : 'N';
            $this->arResult['awz_cookies_mode2'] = $app->check(CookieApp::USER_TECH) ? 'Y' : 'N';
            $this->arResult['awz_cookies_mode3'] = $app->check(CookieApp::MARKET_EXT) ? 'Y' : 'N';
        }

        if($this->arParams['TEMPLATE_FILE']){
            $this->includeComponentTemplate($this->arParams['TEMPLATE_FILE']);
        }else{
            $this->includeComponentTemplate('');
        }
    }

    public function allowAction()
    {
        if(!$this->isRequiredModule()) return '';
        $app = CookieApp::getInstance($this->arParams['SITE_ID']);
        $request = Application::getInstance()->getContext()->getRequest();
        if($request->get('awz_cookies_mode_all')=='Y'){
            $app->set(CookieApp::ACCEPT_ALL);
        }else{
            $app->set(CookieApp::USER_DECLINE);
            if($request->get('awz_cookies_mode1')=='Y'){
                $app->add(CookieApp::USER_REQUIRE);
            }
            if($request->get('awz_cookies_mode2')=='Y'){
                $app->add(CookieApp::USER_TECH);
            }
            if($request->get('awz_cookies_mode3')=='Y'){
                $app->add(CookieApp::MARKET_EXT);
            }
        }

        $app->save();
    }

    public function getSettAction(){

        if(!$this->isRequiredModule()) return '';
        global $APPLICATION;
        ob_start();

        $APPLICATION->IncludeComponent(
            "awz:cookies.sett",
            $this->arParams['COMPONENT_TEMPLATE'],
            Array(
                "COMPONENT_TEMPLATE" => $this->arParams['COMPONENT_TEMPLATE'],
                "TEMPLATE_FILE"=>"settings"
            )
        );

        $html = ob_get_contents();
        ob_end_clean();
        return $html;

    }

    /**
     * Добавление ошибки
     *
     * @param string|Error $message
     * @param int|string $code
     */
    public function addError($message, $code=0)
    {
        if($message instanceof Error){
            $this->errorCollection[] = $message;
        }elseif(is_string($message)){
            $this->errorCollection[] = new Error($message, $code);
        }
    }

    /**
     * Массив ошибок
     *
     * Getting array of errors.
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errorCollection->toArray();
    }

    /**
     * Getting once error with the necessary code.
     *
     * @param string|int $code Code of error.
     * @return Error|null
     */
    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    /**
     * проверка установки обязательных модулей
     *
     * @return bool
     * @throws LoaderException
     */
    public function isRequiredModule(): bool
    {
        if(!Loader::includeModule('awz.cookiessett')){
            $this->addError(Loc::getMessage('AWZ_COOKIES_SETT_MODULE_NOT_INSTALL'), 'system');
            return false;
        }
        return true;
    }

}
