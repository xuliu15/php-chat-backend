<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\UserRepository;

class Users {
    public function __construct(private UserRepository $userRepository) {

    }
    public function createUser(Request $request, Response $response): Response {
        $body = $request->getParsedBody();
        $user = $this->userRepository->createUser($body['username']);
        $body=json_encode(['message' => 'User created successfully', 'data' => $user]);
        $response->getBody()->write($body);
        return $response->withStatus(201);
    }

}       