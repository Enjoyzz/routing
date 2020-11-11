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
];
