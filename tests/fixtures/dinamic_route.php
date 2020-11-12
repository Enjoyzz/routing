<?php

return [
    [
        'pattern' => '<controller:(post|comment)>/<id:\d+>',
        'route' => '<controller>\\\view',
        'suffix' => '/',
    ],
    [
        'pattern' => '<class:(post|comment)>/<action:.+>/<id:\d+>',
        'route' => '<class>\\\<action>',
        'suffix' => '/',
        'defaults' => [
            'class' => 'post',
            'id' => 55
        ]
    ],
    [
        'name' => 'song - vlessons',
        'pattern' => '<band>/<song>.html@<action>_<data_id:\d+>',
        'route' => '\Song\\\<action>',
        'callback' => [
            '<action>' => [
                'strtolower',
                'ucfirst'
            ]
        ],
        'suffix' => '',
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
