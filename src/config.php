<?php

return [
    'inject' => [
        'enable'     => true,
        'namespaces' => [],
    ],
    'route'  => [
        'enable'      => true,
        'controllers' => [],
    ],
    'ignore' => [],
    'param' => [
        'enable'      => true,
        'bind' => \app\Param::class
    ],
    'rbac' => [
        'enable'      => true,
        'bind' => \app\Rbac::class
    ],
    'logger' => [
        'enable'      => true,
        'bind' => \app\Logger::class
    ],
    'jwt' => [
        'enable'      => true,
        'bind' => \app\Jwt::class
    ],
];
