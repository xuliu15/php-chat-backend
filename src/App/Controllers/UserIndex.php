<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\UserRepository;

class UserIndex {
    public function __construct(private UserRepository $userRepository) {

    }
    public function __invoke(Request $request, Response $response): Response {
        $users = $this->userRepository->getUsers();
        $body=json_encode(['message' => 'Users fetched successfully', 'data' => $users]);
        $response->getBody()->write($body);
        return $response;
    }
}   