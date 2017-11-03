<?php

$controllers = [
    'core.controller' => 'App\Core\Controller\CoreController',
    'security.auth.controller' => 'App\Security\Controller\AuthController',
    'controller.log' => 'App\Controllers\LogController'
];

foreach ($controllers as $key => $class) {
    $container[$key] = function ($container) use ($class) {
        return new $class($container);
    };
}
