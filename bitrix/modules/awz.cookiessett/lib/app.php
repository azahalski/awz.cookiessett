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
     * Технические cookies
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
    const ASSEPT_ALL = 30;

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
        if(!$siteId){
            $siteId = Application::getInstance()->getContext()->getSite();
        }
        $this->siteId = (string) $siteId;
        $request = Application::getInstance()->getContext()->getRequest();
        $session = Application::getInstance()->getSession();
        $cookieMask = $request->getCookie(self::SESSION_KEY);
        if(!$cookieMask) $cookieMask = $session->get(self::SESSION_KEY);
        if($cookieMask) $this->isEmpty = false;
        $this->set($cookieMask);
    }

    /**
     * @param string $siteId
     * @return App
     */
    public static function getInstance(string $siteId=''): App
    {
        if(!$siteId) $siteId = self::NO_SITE_ID;
        if(!isset(self::$_instances[$siteId])){
            self::$_instances[$siteId] = new self($siteId);
        }
        return self::$_instances[$siteId];
    }

    public function getAllMask(){
        return [
            self::USER_REQUIRE,
            self::USER_TECH,
            self::MARKET_TECH,
            self::MARKET_EXT
        ];
    }

    /**
     * Установка маски cookies
     *
     * @param $mask
     * @return App
     */
    public function set($mask = self::USER_REQUIRE): App
    {
        $this->user_perm = $mask;
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
        if($mask === self::ASSEPT_ALL){
            $this->user_perm = self::ASSEPT_ALL;
        }else{
            foreach($this->getAllMask() as $m){
                if(!$this->check($m) && ($m & $mask)){
                    $this->user_perm = $this->user_perm | $m;
                }
            }
        }
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
        if($mask === self::ASSEPT_ALL){
            $this->user_perm = 0;
        }else{
            foreach($this->getAllMask() as $m){
                if($this->check($m) && ($m & $mask)){
                    $this->user_perm &= ~ $mask;
                }
            }
        }
        return $this;
    }

    /**
     * Текущая маска cookies
     *
     * @return int
     */
    public function get(): int
    {
        return $this->user_perm;
    }

    /**
     * Проверка прав
     *
     * @param $mask
     * @return bool
     */
    public function check($mask = self::ASSEPT_ALL):bool
    {
        return ($this->get() & $mask);
    }

    public function save(){
        $context = Application::getInstance()->getContext();
        $session = Application::getInstance()->getSession();
        $session->set(self::SESSION_KEY, $this->get());
        if($this->check(self::USER_REQUIRE)){
            $cookie = new Cookie(self::SESSION_KEY, $this->get());
            $cookie->setDomain($context->getServer()->getHttpHost())
                ->setPath('/')->setSecure(false)->setHttpOnly(false);
            $context->getResponse()->addCookie($cookie);
        }
        $this->isEmpty = false;
    }

    public function isEmpty(){
        return $this->isEmpty;
    }

}