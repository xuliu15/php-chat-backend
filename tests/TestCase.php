<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;

class TestCase extends PHPUnit_TestCase
{
    /**
     * @return App
     */
    protected function getAppInstance(): App
    {
        // Create app
        $app = AppFactory::create();

        // Add middleware to parse JSON body
        $app->addBodyParsingMiddleware();

        // Add error middleware
        $app->addErrorMiddleware(true, true, true);

        // Add routes
        $app->get('/', function (Request $request, Response $response) {
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Chat API is running',
                'endpoints' => [
                    'GET /users' => 'Get all users',
                    'POST /users' => 'Create a new user',
                    'GET /groups' => 'Get all groups',
                    'POST /groups' => 'Create a new group',
                    'POST /messages' => 'Send a message',
                    'GET /groups/{id}/messages' => 'Get messages from a group'
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        });

        // Copy all routes from public/index.php
        require __DIR__ . '/../public/index.php';

        return $app;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $headers
     * @param array  $cookies
     * @param array  $serverParams
     * @return Request
     */
    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        $uri = new Uri('', '', 80, $path);
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }
}
