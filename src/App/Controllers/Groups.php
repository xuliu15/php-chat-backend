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
        return $response->withStatus(201);
    }    

    public function showGroup(Request $request, Response $response, string $groupId): Response {
        $group = $request->getAttribute('group');
        $body=json_encode(['message' => 'Group fetched successfully', 'data' => $group]);
        $response->getBody()->write($body);
        return $response;
    }

    public function showGroupMessages(Request $request, Response $response, string $groupId): Response {
        $group = $request->getAttribute('group');
        $body=json_encode(['message' => 'Group messages fetched successfully', 'data' => $group]);
        $response->getBody()->write($body);
        return $response;
    }

    public function createMessage(Request $request, Response $response): Response {
        try {
            $data = $request->getParsedBody();
            
            if (!isset($data['userId']) || !isset($data['groupId']) || !isset($data['content'])) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'User ID, Group ID and content are required'
                ]));
                return $response->withStatus(400);
            }

            $message = $this->groupRepository->createGroupMessage(
                (int)$data['groupId'],
                (int)$data['userId'],
                $data['content']
            );

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $message
            ]));
            return $response->withStatus(201);
        } catch (\RuntimeException $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(400);
        }
    }

    public function getGroupMessages(Request $request, Response $response, string $groupId): Response {
            $messages = $this->groupRepository->getGroupMessages($groupId);
            $body=json_encode(['message' => 'Group messages fetched successfully', 'data' => $messages]);
            $response->getBody()->write($body);
            return $response->withStatus(200);
    }

    public function joinGroup(Request $request, Response $response): Response {
        try {
            $data = $request->getParsedBody();
            
            if (!isset($data['userId']) || !isset($data['groupId'])) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'User ID and Group ID are required'
                ]));
                return $response->withStatus(400);
            }

            $result = $this->groupRepository->joinGroup(
                (int)$data['groupId'],
                (int)$data['userId']
            );

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Successfully joined the group',
                'data' => $result
            ]));
            return $response->withStatus(200);
        } catch (\RuntimeException $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(400);
        }
    }
}