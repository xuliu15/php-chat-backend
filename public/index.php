<?php

use App\Middleware\AddJsonResponseHeader;
use Slim\Factory\AppFactory;
use DI\Container;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Controllers\GroupIndex;
use App\Controllers\Groups;
use App\Controllers\Users;
use App\Controllers\UserIndex;
use App\Controllers\Messages;
use App\Controllers\MessageIndex;
use App\Middleware\GetGroups;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector=$app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();

$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');
$app->add(new AddJsonResponseHeader);

$app->group('/api', function (RouteCollectorProxy $group) {
        $group->get('/groups', GroupIndex::class);
        $group->post('/groups', [Groups::class, 'createGroup']);
        $group->post('/groups/join', [Groups::class, 'joinGroup']);
        $group->post('/users', [Users::class, 'createUser']);
        $group->get('/users', UserIndex::class);
        $group->post('/groups/messages', [Messages::class, 'createMessage']);
        $group->get('/groups/{groupId:\d+}/messages',[Groups::class, 'getGroupMessages'])->add(GetGroups::class);
});

// Run app
$app->run();