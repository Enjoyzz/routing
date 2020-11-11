<?php

return [
    [
        'pattern' => '',
        'route' => '\Core\Index',
        'suffix' => '/',
    ],
    [
        'pattern' => 'login',
        'route' => '\Core\Signin',
    ],
    [
        'pattern' => 'test',
        'route' => '\Core\Test',
        'suffix' => '.html',
    ],
];
