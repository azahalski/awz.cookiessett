<?php
namespace Awz\CookiesSett;

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;

class App {

    const SESSION_KEY = 'AWZ_CK_MASK';

    /**
     * если не определен сайт, константа для конструктора
     */
    const NO_SITE_ID = 'all';

    /**
     * Полный запрет cookies
     */
    const USER_DECLINE = 1;

    /**
     * Технические cookies, обязательно для сохранения маски прав в cookies
     */
    const USER_REQUIRE = 2;

    /**
     * Функциональные cookies
     */
    const USER_TECH = 4;

    /**
     * Другие cookies, не передаются 3-м лицам
     */
    const MARKET_TECH = 8;

    /**
     * Аналитические cookies, могут передаваться 3-м лицам
     */
    const MARKET_EXT = 16;

    /**
     * Все cookies, без ограничения
     */
    const ACCEPT_ALL = 31;

    protected string $siteId;

    private int $user_perm;
    private bool $isEmpty;
    private static array $_instances = [];

    /**
     * @param string $siteId
     */
    private function __construct(string $siteId)
    {
        $this->isEmpty = true;
        $this->user_perm = 0;
        $this->siteId = $siteId;
        $request = Application::getInstance()->getContext()->getRequest();
        $session = Application::getInstance()->getSession();
        $cookieMask = $request->getCookie(self::SESSION_KEY.'_'.$this->siteId);
        if(!$cookieMask) $cookieMask = $session->get(self::SESSION_KEY.'_'.$this->siteId);
        if($cookieMask) $this->isEmpty = false;
        $this->set($cookieMask);
    }

    /**
     * @param string $siteId
     * @return App
     */
    public static function getInstance(string $siteId=''): App
    {
        if(!$siteId){
            $siteId = Application::getInstance()->getContext()->getSite();
        }
        if(!$siteId) $siteId = self::NO_SITE_ID;
        if(!isset(self::$_instances[$siteId])){
            self::$_instances[$siteId] = new self($siteId);
        }
        return self::$_instances[$siteId];
    }

    /**
     * Установка маски cookies
     *
     * @param $mask
     * @return App
     */
    public function set($mask = self::USER_REQUIRE): App
    {
        $this->user_perm = $mask & self::ACCEPT_ALL;
        return $this;
    }

    /**
     * Добавление права доступа
     *
     * @param $mask
     * @return App
     */
    public function add($mask): App
    {
        $mask = $mask & self::ACCEPT_ALL;
        $this->user_perm = $this->user_perm | ($this->user_perm ^ $mask);
        return $this;
    }

    /**
     * Удаление права доступа
     *
     * @param $mask
     * @return App
     */
    public function delete($mask): App
    {
        $mask = $mask & self::ACCEPT_ALL;
        $this->user_perm &= ~ ($this->user_perm & $mask);
        return $this;
    }

    /**
     * Текущая маска cookies
     *
     * @return int
     */
    public function get(): int
    {
        return $this->user_perm & self::ACCEPT_ALL;
    }

    /**
     * Проверка прав
     *
     * @param $mask
     * @return bool
     */
    public function check($mask = self::ACCEPT_ALL):bool
    {
        return ($this->get() & $mask);
    }

    public function save(){
        $context = Application::getInstance()->getContext();
        $session = Application::getInstance()->getSession();
        $session->set(self::SESSION_KEY.'_'.$this->siteId, $this->get());
        if($this->check(self::USER_REQUIRE)){
            $context->getResponse()->allowPersistentCookies(true);
            $cookie = new Cookie(self::SESSION_KEY.'_'.$this->siteId, $this->get());
            $cookie->setPath('/');
            $context->getResponse()->addCookie($cookie);
        }else{
            $context->getResponse()->allowPersistentCookies(false);
            foreach ($context->getRequest()->getCookieList() as $cookieName=>$cookieValue){
                $cookie = new Cookie($cookieName, $cookieValue, -1);
                $cookie->setPath('/');
                $context->getResponse()->addCookie($cookie);
            }
        }
        $this->isEmpty = false;
    }

    public function isEmpty(){
        return $this->isEmpty;
    }

}