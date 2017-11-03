<?php

use App\Core\Rest\Router as RestRouter;

$router = new RestRouter($container['router'], $container['config']['rest']);

/**
 * CORS Pre-flight request
 */
$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

/**
 * Authentication
 */
$app->group('/api', function () use ($container) {
    $this->post('/register', 'security.auth.controller:register')->setName('register');
    $this->post('/login', 'security.auth.controller:login')->setName('login');
    $this->post('/auth/refresh', 'security.auth.controller:refresh')->setName('jwt.refresh');
    $this->get('/users/me', 'security.auth.controller:me')
        ->add($container['auth.middleware']())
        ->setName('users.me');

/*
$options = [
     'key' => 'id',
      'requirement' => '[0-9]+',
      'singular' => 'log'
 ];
	$this->cget('logs', 'controller.log:getLogs')
		->add($container['auth.middleware']())
		->setName('get_log');
*/


/** Logs **/
/*
    $this->get('/logs', 'controller.log:getLog')
        ->add($container['auth.middleware']())
        ->setName('get_logs');

    $this->cget('/logs', 'controller.log:getLogs')
        ->add($container['auth.middleware']())
        ->setName('get_log');

    $this->post('/logs', 'controller.log:postLog')
        ->add($container['auth.middleware']())
        ->setName('post_log');
*/
/** Logs End **/

    $this->get('/app', function ($request, $response) {
    return 'Welcome To App';
    })->add($container['auth.middleware']());
});

$options = [
	'key' => 'id',
	'requirement' => '[0-9]+',
	'singular' => 'log'
 ];

$router->CRUD('logs', 'controller.log', [$container['auth.middleware']()],$options);

$app->get('/', 'core.controller:root')->setName('root');


/**
 *         URL          |           CONTROLLER            |     ROUTE
 * ---------------------|---------------------------------|----------------
 * GET /logs        | LogController:getLog    | get_logs
 * GET /logs/{id}   | LogController:getLogs   | get_log
 * POST /logs       | LogController:postLog   | post_log
 * PUT /logs/{id}   | LogController:putLog    | put_log
 * DELETE /log/{id} | LogController:deleteLog | delete_log
 */
//$router->CRUD('logs', 'LogController',[$container['auth.middleware']()]);

// OR

// $router->cget('logs', 'LogController');
// $router->get('logs', 'LogController');
// $router->post('logs', 'LogController');
// $router->put('logs', 'LogController');
// $router->delete('logs', 'LogController');

// With options
/**
 * $options = [
 *      'key' => 'id',
 *      'requirement' => '[0-9]+',
 *      'singular' => 'log'
 * ];
 *
 * $router->CRUD('logs', 'LogController', [], $options);
 *
 * OR
 *
 * $router->get('logs', 'LogController', $options);
 * ...
 */

/***********************************************************/
/* -------------------- SUB RESOURCES -------------------- */
/***********************************************************/

/**
 *                        URL                         |                   CONTROLLER                  |        ROUTE
 * ---------------------------------------------------|-----------------------------------------------|------------------------
 * GET /logs/{log_id}/comments                | LogCommentController:getLogComments   | get_log_comments
 * GET /logs/{log_id}/comments/{comment_id}   | LogCommentController:getLogComment    | get_log_comment
 * POST /logs/{log_id}/comments               | LogCommentController:postLogComment   | post_log_comment
 * PUT /logs/{log_id}/comments/{comment_id}   | LogCommentController:putLogComment    | put_log_comment
 * DELETE /log/{log_id}/comments/{comment_id} | LogCommentController:deleteLogComment | delete_log_comment
 */
//$router->subCRUD('logs', 'comments', 'LogCommentController');

// OR

// $router->cgetSub('logs', 'comments', 'LogController');
// $router->getSub('logs', 'comments', 'LogController');
// $router->postSub('logs', 'comments', 'LogController');
// $router->putSub('logs', 'comments', 'LogController');
// $router->deleteSub('logs', 'comments', 'LogController');

// With options
/**
 * $options = [
 *      'parent_key' => 'log_id',
 *      'parent_requirement' => '[0-9]+',
 *      'sub_key' => 'comment_id',
 *      'sub_requirement' => '[0-9]+',
 *      'parent_singular' => 'log',
 *      'sub_singular' => 'comment'
 * ];
 *
 * $router->subCRUD('logs', 'comments', 'LogCommentController', [], $options);
 *
 * OR
 *
 * $router->getSub('logs', 'comments', 'LogController', $options);
 * ...
 */
