<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\GroupRepository;

class Groups {
    public function __construct(private GroupRepository $groupRepository) {

    }
    public function createGroup(Request $request, Response $response): Response {
        $body = $request->getParsedBody();
        $group = $this->groupRepository->createGroup($body['name']);
        $body=json_encode(['message' => 'Group created successfully', 'data' => $group]);
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }    

    public function showGroup(Request $request, Response $response, string $groupId): Response {
        $group = $request->getAttribute('group');
        $body=json_encode(['message' => 'Group fetched successfully', 'data' => $group]);
        $response->getBody()->write($body);
        return $response;
    }


    
}