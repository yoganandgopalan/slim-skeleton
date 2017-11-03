<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

$app = new Slim\App([
    'env' => 'dev',
    'root_dir' => dirname(__DIR__),
    'settings' => ['displayErrorDetails' => true]
]);
$container = $app->getContainer();



$container['config'] = require __DIR__ . '/../app/config/config.php';
/*
$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        // log error here
        return $c['response']->withStatus(500)
                             ->withHeader('Content-Type', 'text/html')
                             ->write('Something went wrong on our side!');
    };
};
$container['phpErrorHandler'] = function ($c) {
    return function ($request, $response, $error) use ($c) {
        // log error here
        return $c['response']->withStatus(500)
                             ->withHeader('Content-Type', 'text/html')
                             ->write('Something went wrong on our side (again)!');
    };
};

$app->get('/exception', function ($req, $res, $args) {
    // errorHandler will trap this Exception
    throw new Exception("An error happened here");
});


$app->get('/php7', function ($req, $res, $args) {
    $x = function (int $x) {
        return $x;
    };

    // phpErrorHandler wil trap this Error
    $x('test');
});

$app->get('/warning', function ($req, $res, $args) {
    $x = UNDEFINED_CONSTANT;
});
*/

require __DIR__ . '/../app/dependencies.php';

require __DIR__ . '/../app/handlers.php';

require __DIR__ . '/../app/middleware.php';

require __DIR__ . '/../app/controllers.php';

require __DIR__ . '/../app/routes.php';

$app->run();
