<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\MessageRepository;

class Messages {
    public function __construct(private MessageRepository $messageRepository) {

    }
    public function createMessage(Request $request, Response $response): Response {
        $body = $request->getParsedBody();
        $message = $this->messageRepository->createMessage($body);
        $body=json_encode(['message' => 'Message created successfully', 'data' => $message]);
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getMessages(Request $request, Response $response): Response {
        $body = $request->getParsedBody();
        $messages = $this->messageRepository->getMessagesbyGroupId($body['groupId']);
        $body=json_encode(['message' => 'Messages fetched successfully', 'data' => $messages]);
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    }
}