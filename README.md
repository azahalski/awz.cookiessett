# AWZ: Согласие о cookies [awz.cookiessett]

### [Установка модуля](https://github.com/zahalski/cookiessett/tree/main/docs/install.md)


<!-- desc-start -->

Модуль содержит Api и компонент для запроса разрешения на использование cookies (куки) для CMS 1c-Битрикс. 
Соответствует ФЗ-152 и позволяет опционально настроить отклонение или настройку по типам куки.

После установки и настройки модуля AWZ: Согласие о cookies на сайте выводится окно с информацией об использовании cookies, а также с возможностью его настройки.

**Модуль содержит битовые маски для вариантов cookies (куки):**
* Технические cookies
* Функциональные cookies
* Аналитические cookies
* Другие cookies

Битовые маски = возможности проверки настроек куки в любой комбинации логики "И" и "ИЛИ" (&, |)

**Для версии php 7.0 - 8.2:** подключается альтернативная (старая) страница настроек модуля.

**Поддерживаемые редакции CMS Битрикс:**<br>
«Старт», «Стандарт», «Малый бизнес», «Бизнес», «Корпоративный портал», «Энтерпрайз», «Интернет-магазин + CRM»

<!-- desc-end -->

<!-- dev-start -->

## Документация для разработчиков

```php
use Awz\CookiesSett\App as CookieApp;
if(\Bitrix\Main\Loader::includeModule('awz.cookiessett')){

	$app = CookieApp::getInstance();
	if($app->check(CookieApp::USER_TECH)){
		//разрешены функциональные
	}
	if($app->check(CookieApp::MARKET_EXT)){
		//разрешены маркетинговые
	}
	if($app->check(CookieApp::USER_TECH & CookieApp::MARKET_EXT)){
		//разрешены маркетинговые и функциональные
	}
	if($app->check(CookieApp::USER_TECH | CookieApp::MARKET_EXT)){
		//разрешены маркетинговые или функциональные
	}
	if($app->isEmpty()){
		//пользователь еще не выбрал согласие или отмену
	}

}
```

### Пример подключения компонента в footer шаблона (используя настройки с модуля для текущего сайта)

/include_areas/cookies.php

```php
<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?php
use Bitrix\Main\Config\Option;
$strParams = Option::get("awz.cookiessett", 'PARAMS', '', SITE_ID);
$strArParams = unserialize(
    $strParams,
    ['allowed_classes'=>false]
);
if(!is_array($strArParams)) $strArParams = ['COMPONENT_TEMPLATE'=>".default"];
$strArParams['SITE_ID'] = SITE_ID;
$APPLICATION->IncludeComponent("awz:cookies.sett",".default",
    $strArParams, null, array("HIDE_ICONS"=>"Y")
);
?>
```

footer.php шаблона сайта

```php
<?
$APPLICATION->IncludeComponent("bitrix:main.include", ".default", Array(
    "AREA_FILE_SHOW" => "file",
    "PATH" => "/include_areas/cookies.php",
    "EDIT_TEMPLATE" => "",
), false, array("HIDE_ICONS" => "N"));
?>
</body>
</html>
```

### ссылка на настройки cookies

id = `awz_cookies_sett__settings_custom`

```html
<p><a class="btn btn-primary" id="awz_cookies_sett__settings_custom">Настроить</a></p>
```

также можно получить доступ к глобальному объекту
window.AwzCookiesSettComponent_Instances и вызвать, настройки, отклонение или подтверждение с любого места.

```js
/* пример обработчика на jquery обрабатывающего ссылку href="#awz_cookies" и вызывающего настройки */
$(document).on('click', 'a[href="#awz_cookies"]', function(e){
    e.preventDefault();
    var k;
    for(k in window.AwzCookiesSettComponent_Instances){
        window.AwzCookiesSettComponent_Instances[k].settings();
        return false;
    }
});
```

### пример подключения аналитики

```php
<?php
$showCounters = false;
if(\Bitrix\Main\Loader::includeModule('awz.cookiessett')) {
    $app = \Awz\CookiesSett\App::getInstance();
    if($app->check(\Awz\CookiesSett\App::MARKET_EXT)){
        $showCounters = true;
    }
}
if($showCounters){
    ?>
    <script type="text/javascript">код счетчика</script>
    <?
}
```

<!-- dev-end -->

<!-- cl-start -->
## История версий

https://github.com/zahalski/cookiessett/blob/master/CHANGELOG.md

<!-- cl-end -->

