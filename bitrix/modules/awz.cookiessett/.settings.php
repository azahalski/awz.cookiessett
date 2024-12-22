<?php
return [
    'ui.entity-selector' => [
        'value' => [
            'entities' => [
                [
                    'entityId' => 'awzcookiessett-user',
                    'provider' => [
                        'moduleId' => 'awz.cookiessett',
                        'className' => '\\Awz\\Cookiessett\\Access\\EntitySelectors\\User'
                    ],
                ],
                [
                    'entityId' => 'awzcookiessett-group',
                    'provider' => [
                        'moduleId' => 'awz.cookiessett',
                        'className' => '\\Awz\\Cookiessett\\Access\\EntitySelectors\\Group'
                    ],
                ],
            ]
        ],
        'readonly' => true,
    ]
];