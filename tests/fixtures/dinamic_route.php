<?php

return [
    [
        'pattern' => '<controller:(post|comment)>/<id:\d+>',
        'route' => '<controller>\view',
        'suffix' => '/',
    ],
    [
        'pattern' => '<class:(post|comment)>/<action:.+>/<id:\d+>',
        'route' => '<class>\<action>',
        'suffix' => '/',
        'defaults' => [
            'class' => 'post',
            'id' => 55
        ]
    ],
    [
        'pattern' => '<band>/<song>.html@<action>_<data_id:\d+>',
        'route' => '\Song\<action>',
        'callback' => [
            '<action>' => [
                'strtolower',
                'ucfirst'
            ]
        ],
        'suffix' => '',
    ],
    [
         'pattern' => '<band>/<song>@<act>_<id:\d+>',
        'route' => '\chords\Core\Song\<act>',
        'verb' => [
            'POST',
            'GET'
        ],
        'callback' => [
            '<act>' => [
                'ucfirst'
            ]
        ],
        'suffix' => '.php',
    ],    
    [
   
        'pattern' => 'admin/<controller>/<module>/<action>',
        'route' => '\<module>\<controller><action>',
        'callback' => [
            '<action>' => [
                'strtolower',
                'ucfirst'
            ],
            '<controller>' => [
                'strtolower',
                'ucfirst'
            ]
        ],
        'suffix' => '',
    ],
];
