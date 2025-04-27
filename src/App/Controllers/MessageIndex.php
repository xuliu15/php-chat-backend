<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\MessageRepository;

class MessageIndex {
    public function __construct(private MessageRepository $messageRepository) {

    }
    public function __invoke(Request $request, Response $response): Response {
        $messages = $this->messageRepository->getMessagesbyGroupId($request->getAttribute('groupId'));
        $body=json_encode(['message' => 'Messages fetched successfully', 'data' => $messages]);
        $response->getBody()->write($body);
        return $response;
    }
}   