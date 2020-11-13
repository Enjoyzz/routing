<?php

return [
    [
        'pattern' => 'books/<cat_id:\d+>',
        'route' => 'Books',
        'suffix' => '/',
    ],
    [
        'pattern' => 'digits/<id:\d+>',
        'route' => 'Digits',
        'defaults' => [
            'id' => '1'
        ],
        'suffix' => '.html'
    ],
    [
        'pattern' => 'musics/<category:.*>/<sort:asc|desc>',
        'route' => 'Music',
        'suffix' => '/',
        'defaults' => [
            'sort' => 'desc'
        ]
    ],
    [
        'pattern' => 'encode/<text:.*>',
        'route' => 'Encode\True',
        'encodeParams' => true
    ],
    [
        'pattern' => 'encode_f/<text:.*>',
        'route' => 'Encode\False',
        'encodeParams' => false
    ],
    [
        'pattern' => 's',
        'route' => 'Search',
        'suffix' => '/',
        'host' => 'http://yandex.ru'
    ],
    [
        'pattern' => 'search',
        'route' => 'Search',
        'suffix' => '/',
    ]
];
